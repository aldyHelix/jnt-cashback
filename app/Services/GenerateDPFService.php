<?
namespace App\Services;

use App\Models\CashbackSetting;
use App\Models\GlobalSumberWaybill;
use App\Models\Periode;
use App\Models\PeriodeDataJson;
use App\Models\PeriodeKlienPengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;
use Modules\Period\Models\Period;

class GenerateDPFService {

    public function runRekapGenerator($schema, $id){

        $this->createOrReplaceDPFPivot($schema, $id); //aman

        $this->generateMPDPFSumBiayaKirim($schema);
        $this->generateMPDPFReturSumBiayaKirim($schema);
        $this->generateMPDPFCountWaybill($schema);
        $this->generateMPDPFReturCountWaybill($schema);

        $this->cashbackRegulerDPF($schema);

        $this->cashbackKlienVIP($schema);

        $this->cashbackMarketplaceCod($schema);

        $this->cashbackMarketplaceNonCod($schema);

        $this->generateKlienPengirimanVIP($schema);

        $this->rekapDPF($schema);

        $this->rekapDPFDenda($schema);

         //tidak perlu summary grading karena data terlalu berat ketika view - to - view kalukulasi.
        //akan dibuatkan kalkulasi via json
        $this->saveToJson($schema, $id);

    }

    public function saveToJson($schema, $id){
        $periode = Periode::where('code', $schema)->first();

        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->get();
        $data_pivot = [];
        $data_pivot_mp = [];

        foreach($category as $cat) {
            $data_pivot[$cat->kode_kategori] = DB::table($schema.'.dpf_'.$cat->kode_kategori.'_count_sum')->get()->toArray();
        }

        //find periode data json
        $data_json = PeriodeDataJson::where('periode_id', $periode->id)->first();

        //save to periode
        $data_pivot_mp['dpf_mp_sum_biaya_kirim'] = DB::table($schema.'.dpf_mp_sum_biaya_kirim')->get()->toArray();
        $data_pivot_mp['dpf_mp_count_waybill'] = DB::table($schema.'.dpf_mp_count_waybill')->get()->toArray();
        $data_pivot_mp['dpf_mp_retur_sum_biaya_kirim'] = DB::table($schema.'.dpf_mp_retur_sum_biaya_kirim')->get()->toArray();
        $data_pivot_mp['dpf_mp_retur_count_waybill'] = DB::table($schema.'.dpf_mp_retur_count_waybill')->get()->toArray();

        $data_pivot_vip = DB::table($schema.'.dpf_rekap_klien_pengiriman_vip')->get()->toArray();

        $data_cashback_reguler = DB::table($schema.'.dpf_cashback_reguler');
        $data_cashback_marketplace_cod = DB::table($schema.'.dpf_cashback_marketplace_cod');
        $data_cashback_marketplace_non_cod = DB::table($schema.'.dpf_cashback_marketplace_non_cod');
        $data_cashback_klien_vip = DB::table($schema.'.dpf_cashback_klien_vip');
        $data_cashback_dpf = DB::table($schema.'.dpf_rekap_cashback_dpf');
        $data_cashback_denda = DB::table($schema.'.dpf_rekap_cashback_denda');

        $get_data_cashback_reguler = $data_cashback_reguler->get()->toArray();
        $get_data_cashback_marketplace_cod = $data_cashback_marketplace_cod->get()->toArray();
        $get_data_cashback_marketplace_non_cod = $data_cashback_marketplace_non_cod->get()->toArray();
        $get_data_cashback_klien_vip = $data_cashback_klien_vip->get()->toArray();
        $get_data_cashback_dpf = $data_cashback_dpf->get()->toArray();
        $get_data_cashback_denda = $data_cashback_denda->get()->toArray();

        $data_dpf = [];

        foreach($get_data_cashback_reguler as $key => $item){
            $data_dpf[] = [
                'kode_cp' => $item->kode_cp,
                'nama_cp' => $item->nama_cp,
                'total_cashback_reguler' => $item->total_cashback_reguler,
                'total_cashback_marketplace_cod' => $get_data_cashback_marketplace_cod[$key]->cashback_marketplace,
                'total_cashback_marketplace_non_cod' => $get_data_cashback_marketplace_non_cod[$key]->total_cashback_marketplace,
                'total_cashback_klien_vip' => $get_data_cashback_klien_vip[$key]->cashback_marketplace ?? 0,
                'total_cashback' => $item->total_cashback_reguler + $get_data_cashback_marketplace_non_cod[$key]->total_cashback_marketplace,
            ];
        }

        //update or create


        if($data_json) {
            $updated = $data_json->update([
                'dpf_pivot' => json_encode($data_pivot),
                'dpf_pivot_mp' => json_encode($data_pivot_mp),
                'dpf_pivot_vip' => json_encode($data_pivot_vip),
                'dpf_cashback_reguler' => json_encode($get_data_cashback_reguler),
                'dpf_cashback_marketplace_cod' => json_encode($get_data_cashback_marketplace_cod),
                'dpf_cashback_marketplace_non_cod' => json_encode($get_data_cashback_marketplace_non_cod),
                'dpf_cashback_klien_vip' => json_encode($get_data_cashback_klien_vip),
                'dpf_cashback_rekap' => json_encode($get_data_cashback_dpf),
                'dpf_cashback_rekap_denda' => json_encode($get_data_cashback_denda),
            ]);
        } else {
            PeriodeDataJson::create([
                'periode_id' => $periode->id,
                'dpf_pivot' => json_encode($data_pivot),
                'dpf_pivot_mp' => json_encode($data_pivot_mp),
                'dpf_pivot_vip' => json_encode($data_pivot_vip),
                'dpf_cashback_reguler' => json_encode($get_data_cashback_reguler),
                'dpf_cashback_marketplace_cod' => json_encode($get_data_cashback_marketplace_cod),
                'dpf_cashback_marketplace_non_cod' => json_encode($get_data_cashback_marketplace_non_cod),
                'dpf_cashback_klien_vip' => json_encode($get_data_cashback_klien_vip),
                'dpf_cashback_rekap' => json_encode($get_data_cashback_dpf),
                'dpf_cashback_rekap_denda' => json_encode($get_data_cashback_denda),
            ]);
        }
    }

    public function createOrReplaceDPFPivot($schema, $periode_id) {
        //get category
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->get();
        $query = "";
        // $schema = 'cashback_feb_2022'; //for debuging

        $setting_dpf_dp = [
            'BANTAR_KEMANG',
            'CITAYAM_PABUARAN',
            'CITEUREUP',
            'GUNUNG_BATU',
            'MAYOR_KH',
            'PAMOYANAN_BOGOR',
            'TAMAN_CIMANGGU',
            'IPB_DRAMAGA',
            'CIMAHPAR',
            'MARGAJAYA',
            'PAHLAWAN_SUKSES',
            'CIAMPEA',
        ];

        $dalam_zona = "or (data_mart.drop_point_outgoing = 'PAMOYANAN_BOGOR' and data_mart.kat = 'DALAM ZONASI')";

        $query .= "

            CREATE OR REPLACE VIEW dpf_all_count_sum AS
                SELECT DISTINCT (data_mart.drop_point_outgoing), COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'DPF' $dalam_zona)
                GROUP BY data_mart.drop_point_outgoing;

        ";

        $dp_dpf = implode(",", $setting_dpf_dp);

        foreach($category as $cat) {
            $sum_column = 'biaya_kirim';

            if($cat->kode_kategori == 'super') {
                $sum_column = 'total_biaya_setelah_diskon';
            }
            //get periode klien pengiriman
            $periode_klien_pengiriman = PeriodeKlienPengiriman::with('klien_pengiriman')->where(['periode_id' => $periode_id, 'category_id'=> $cat->id])->get()->pluck('klien_pengiriman.klien_pengiriman')->toArray();

            //get KAT
            $kat = "";
            //get metode pembayaran
            $kat = str_replace(";","' OR data_mart.kat = '",$cat->kat);
            $kat = "data_mart.kat = '".$kat."'";
            $metode_pembayaran = "";
            $metode_pembayaran = str_replace(";","' OR data_mart.metode_pembayaran = '",$cat->metode_pembayaran);
            $metode_pembayaran = str_replace("(blank)","",$metode_pembayaran);
            $metode_pembayaran = "data_mart.metode_pembayaran = '".$metode_pembayaran."'";

            if(count($periode_klien_pengiriman)){
                $klien_pengiriman = "";
                $klien_pengiriman = implode(";", $periode_klien_pengiriman);
                $klien_pengiriman = str_replace(";","', '",$klien_pengiriman);
                $klien_pengiriman = "'".$klien_pengiriman."'";
                $klien_pengiriman = str_replace("''","'',NULL ",$klien_pengiriman);
            }

            $query .= "
                CREATE OR REPLACE VIEW dpf_".$cat->kode_kategori."_count_sum AS
                    SELECT DISTINCT data_mart.drop_point_outgoing,
                        count(data_mart.no_waybill) AS count,
                        sum(data_mart.$sum_column) AS sum
                        FROM ".$schema.".data_mart
                    WHERE
                        ( data_mart.kat = 'DPF' $dalam_zona)
                    AND
                    ($metode_pembayaran)
                    AND
                    (data_mart.klien_pengiriman IN ( $klien_pengiriman ))
                    GROUP BY data_mart.drop_point_outgoing;
            ";
        }

        $this->checkAndRunSchema($schema, $query);

    }

    public function cashbackRegulerDPF($schema) {
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;
        $cashback_reguler_diskon = CashbackSetting::where('jenis_paket', 'REGULER')->first()->diskon;
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->orderBy('id', 'ASC')->get();
        $query = "";

        $joins = [];
        $select_column = [];
        $select_sum = [];

        $setting_dpf_dp = [
            'BANTAR_KEMANG',
            'CITAYAM_PABUARAN',
            'CITEUREUP',
            'GUNUNG_BATU',
            'MAYOR_KH',
            'PAMOYANAN_BOGOR',
            'TAMAN_CIMANGGU',
            'IPB_DRAMAGA',
            'CIMAHPAR',
            'MARGAJAYA',
            'PAHLAWAN_SUKSES',
            'CIAMPEA',
        ];

        foreach($category as $cat) {
            $joins[] = "LEFT JOIN $schema.dpf_".$cat->kode_kategori."_count_sum ON cp.drop_point_outgoing::text = dpf_".$cat->kode_kategori."_count_sum.drop_point_outgoing::text";

            $select_column[] = "COALESCE(dpf_".$cat->kode_kategori."_count_sum.sum, 0::bigint) AS biaya_kirim_".$cat->kode_kategori;
            $select_sum[] = "COALESCE(dpf_".$cat->kode_kategori."_count_sum.sum, 0::bigint)";
        }

        $joins = implode("\n", $joins);
        $select_column = implode(",\n", $select_column);
        $select_sum = implode("+\n", $select_sum);

        $dp_dpf = implode(",", $setting_dpf_dp);

        $total_biaya_dengan_ppn = "round(($select_sum) / $ppn::float)::bigint";
        $total_biaya_ppn_diskon = "round($total_biaya_dengan_ppn * 0.25)::bigint";

        $query .= "
        CREATE OR REPLACE VIEW dpf_cashback_reguler AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0::bigint) AS biaya_kirim_all,
                $select_column,
                ($select_sum) AS total_biaya_kirim,
                $total_biaya_dengan_ppn AS total_biaya_kirim_dikurangi_ppn,
                $total_biaya_ppn_diskon AS amount_discount_25,
                ($total_biaya_ppn_diskon) AS total_cashback_reguler
            FROM master_collection_point cp
                LEFT JOIN $schema.dpf_all_count_sum acs ON cp.drop_point_outgoing::text = acs.drop_point_outgoing::text
                $joins
            WHERE cp.grading_pickup = 'DPF';
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceCod($schema){
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;
        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;
        $bukalapak = "( COALESCE(sbk.bukalapak, 0) + COALESCE(sbk.bukaexpress, 0) + COALESCE(sbk.bukasend, 0) )";
        $bukalapak_ppn = "CAST( ROUND( ( $bukalapak ) / $ppn::float ) AS BIGINT )";
        $bukalapak_ppn_diskon = "CAST( ROUND( $bukalapak_ppn * 0.07 ) AS BIGINT )";
        $shopee_cod = "( COALESCE(sbk.shopee_cod, 0) - COALESCE(srbk.shopee_cod, 0) )";
        $magellan_cod = "( COALESCE(sbk.magellan_cod, 0) - COALESCE(srbk.magellan_cod, 0) )";
        $lazada_cod = "( COALESCE(sbk.lazada_cod, 0) - COALESCE(srbk.lazada_cod, 0) ) ";
        $total_biaya_kirim_cod = "CAST( ROUND( $shopee_cod + $magellan_cod + $lazada_cod ) AS BIGINT ) ";
        $total_biaya_kirim_cod_ppn = " CAST( ROUND( $shopee_cod + $magellan_cod + $lazada_cod ) / $ppn::float AS BIGINT )";
        $total_biaya_kirim_cod_ppn_diskon = " CAST( ROUND( ( ( $shopee_cod + $magellan_cod + $lazada_cod ) / $ppn ) * $cashback_marketplace_diskon::float ) AS BIGINT )";
        $total_biaya_kirim_tokopedia = "COALESCE(sbk.tokopedia, 0) + COALESCE(sbk.marketplace_reguler, 0)";
        $tokopedia_ppn = "CAST( ROUND( ($total_biaya_kirim_tokopedia) / $ppn::float ) AS BIGINT )";
        $tokopedia_ppn_diskon = "CAST( ROUND( (($total_biaya_kirim_tokopedia) / $ppn::float) * 0.07 ) AS BIGINT )";
        $query = "
            CREATE OR REPLACE VIEW dpf_cashback_marketplace_cod AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                $bukalapak AS bukalapak,
                $bukalapak AS total_biaya_kirim_bukalapak,
                $bukalapak_ppn AS total_biaya_kirim_bukalapak_dikurangi_ppn,
                $bukalapak_ppn_diskon AS discount_bukalapak_7,
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                COALESCE(sbk.marketplace_reguler, 0) AS tokopedia_reguler,
                $total_biaya_kirim_tokopedia AS total_biaya_kirim_tokopedia,
                $tokopedia_ppn AS total_biaya_kirim_tokopedia_dikurangi_ppn,
                $tokopedia_ppn_diskon AS discount_tokopedia_7,
                ($bukalapak + $total_biaya_kirim_tokopedia) AS total_biaya_kirim_bukalapak_tokopedia,
                COALESCE(sbk.shopee_cod, 0) AS shopee_cod,
                COALESCE(srbk.shopee_cod, 0) AS retur_shopee_cod,
                $shopee_cod AS total_biaya_kirim_shopee_cod,
                COALESCE(sbk.magellan_cod, 0) AS magellan_cod,
                COALESCE(srbk.magellan_cod, 0) AS retur_magellan_cod,
                $magellan_cod AS total_biaya_kirim_magellan_cod,
                COALESCE(sbk.lazada_cod, 0) AS lazada_cod,
                COALESCE(srbk.lazada_cod, 0) AS retur_lazada_cod,
                $lazada_cod AS total_biaya_kirim_lazada_cod,
                $total_biaya_kirim_cod AS total_biaya_kirim_cod,
                $total_biaya_kirim_cod_ppn AS total_biaya_kirim_cod_dikurangi_ppn,
                $total_biaya_kirim_cod_ppn_diskon AS diskon_cod_7,
                CAST(
                    (
                        $bukalapak_ppn_diskon +
                        $total_biaya_kirim_cod_ppn_diskon +
                        $tokopedia_ppn_diskon
                    ) AS BIGINT
                ) AS cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".dpf_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".dpf_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'DPF'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceNonCod($schema){
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;

        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;

        $retur_list = [
            'akulakuob',
            'bukaexpress',
            'bukalapak',
            'bukasend',
            'evermosapi',
            'mengantar',
            'ordivo',
            'tokopedia',
            'clodeohq'
        ];

        $retur_select_list = [];

        foreach($retur_list as $item) {
            $retur_select_list[] = "COALESCE(srbk.$item, 0)";
        }

        $retur_select_sum = implode("+\n", $retur_select_list);
        $total_biaya_kirim_lazada = "(COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0))";
        $total_biaya_kirim_shopee = "(COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0))";
        $total_biaya_kirim_magellan = "(COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0))";
        $total_biaya_kirim_non_cod = "($total_biaya_kirim_lazada + $total_biaya_kirim_shopee + $total_biaya_kirim_magellan)";
        $shopee_cod = "( COALESCE(sbk.shopee_cod, 0) - COALESCE(srbk.shopee_cod, 0) )";
        $magellan_cod = "( COALESCE(sbk.magellan_cod, 0) - COALESCE(srbk.magellan_cod, 0) )";
        $lazada_cod = "( COALESCE(sbk.lazada_cod, 0) - COALESCE(srbk.lazada_cod, 0) ) ";
        $total_biaya_kirim_cod = "CAST( ROUND( $shopee_cod + $magellan_cod + $lazada_cod ) AS BIGINT ) ";
        $bukalapak = "( COALESCE(sbk.bukalapak, 0) + COALESCE(sbk.bukaexpress, 0) + COALESCE(sbk.bukasend, 0) )";
        $tokopedia = "COALESCE(sbk.tokopedia, 0) + COALESCE(sbk.marketplace_reguler, 0)";
        $total_biaya_kirim_marketplace = "CAST(($bukalapak + $tokopedia) AS BIGINT) + CAST($total_biaya_kirim_cod AS BIGINT) + CAST(( $total_biaya_kirim_non_cod - ($retur_select_sum + COALESCE(cds.retur_belum_terpotong, 0))) AS BIGINT)";
        $total_biaya_kirim_non_cod_ppn = "CAST(($total_biaya_kirim_marketplace) / $ppn::float AS BIGINT)";
        $total_biaya_kirim_non_cod_ppn_diskon = "CAST($total_biaya_kirim_non_cod_ppn * $cashback_marketplace_diskon AS BIGINT)";
        $query = "
            CREATE OR REPLACE VIEW dpf_cashback_marketplace_non_cod AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(sbk.lazada, 0) AS lazada,
                COALESCE(srbk.lazada, 0) AS retur_lazada,
                COALESCE(sbk.shopee, 0) AS shopee,
                COALESCE(srbk.shopee, 0) AS retur_shopee,
                --tokotalk 0
                COALESCE(sbk.magellan, 0) AS magellan,
                COALESCE(srbk.magellan, 0) AS retur_magellan,
                $total_biaya_kirim_non_cod AS total_biaya_kirim_non_cod,
                ( $retur_select_sum ) AS total_retur_pilihan,
                COALESCE(cds.retur_belum_terpotong, 0) AS retur_belum_terpotong,
                $total_biaya_kirim_marketplace AS total_biaya_kirim_marketplace,
                CAST(ROUND( $total_biaya_kirim_non_cod_ppn ) AS BIGINT) AS total_biaya_kirim_non_cod_dikurangi_ppn,
                CAST(ROUND( $total_biaya_kirim_non_cod_ppn_diskon ) AS BIGINT) AS discount_total_biaya_kirim_7,
                CAST(ckv.cashback_marketplace AS BIGINT) + CAST(ROUND( $total_biaya_kirim_non_cod_ppn_diskon) AS BIGINT) AS total_cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".dpf_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".dpf_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".dpf_cashback_klien_vip AS ckv ON cp.drop_point_outgoing = ckv.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_setting AS cds ON cp.drop_point_outgoing = cds.nama_cp
            WHERE
                cp.grading_pickup = 'DPF'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackKlienVIP($schema){
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;

        $vip_diskon = intval(CashbackSetting::where('jenis_paket', 'VIP')->first()->diskon) / 100;
        $periode = Periode::where('code', $schema)->first();
        $category = CategoryKlienPengiriman::where('nama_kategori', 'VIP')->first();

        $marketplace_list = [
            'akulakuob',
            'ordivo',
            'evermosapi',
            'mengantar',
            'klien_pengirim_vip'
        ];

        $marketplace = [];
        $total_biaya_kirim = [];

        foreach($marketplace_list as $item){
            $marketplace[] = "COALESCE(sbk.$item, 0) as $item";
            $total_biaya_kirim[] = "COALESCE(sbk.$item, 0)";
        }

        $marketplace_select = implode(", \n", $marketplace);
        // $marketplace = 'sbk.'.$marketplace;

        $total_biaya_kirim = implode("+ ", $total_biaya_kirim);
        // $total_biaya_kirim = 'sbk.'.$total_biaya_kirim;

        $sum_klien_pengiriman_ppn = "(( $total_biaya_kirim ) / $ppn::float )";
        $sum_klien_pengiriman_ppn_diskon = "(( $total_biaya_kirim ) / $ppn::float ) * $vip_diskon";

        $query = "
            CREATE OR REPLACE VIEW dpf_cashback_klien_vip AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                $marketplace_select,
                ( $total_biaya_kirim ) AS total_biaya_kirim_vip,
                CAST(ROUND( $sum_klien_pengiriman_ppn ) AS BIGINT) AS total_biaya_kirim_vip_dikurangi_ppn,
                CAST(ROUND( $sum_klien_pengiriman_ppn_diskon ) AS BIGINT) AS discount_total_biaya_kirim_10,
                CAST(ROUND( $sum_klien_pengiriman_ppn_diskon ) AS BIGINT) AS cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".dpf_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".dpf_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'DPF'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function generateKlienPengirimanVIP($schema){
        $periode = Periode::where('code', $schema)->first();
        $category = CategoryKlienPengiriman::where('nama_kategori', 'VIP')->first();

        $klien_pengiriman = DB::table('periode_klien_pengiriman')
        ->join('global_klien_pengiriman', 'global_klien_pengiriman.id', '=','periode_klien_pengiriman.klien_pengiriman_id')
        ->select('global_klien_pengiriman.klien_pengiriman')
        ->where('periode_klien_pengiriman.category_id', $category->id)
        ->where('periode_klien_pengiriman.periode_id', $periode->id)
        ->orderBy('global_klien_pengiriman.klien_pengiriman', 'ASC')
        ->get()->pluck('klien_pengiriman')->toArray();

        $subquery = [];
        $klien_pengiriman_vip_list = [];

        foreach($klien_pengiriman as $item) {
            $column = ($item != '' ? str_replace(' ','_',$item) : "");
            $as_column = ($item != "" ? str_replace(" ","_",$item) : 'blank');
            $as_column = strtolower(str_replace("-","_",$as_column));
            $subquery[] = "SUM(CASE WHEN dm.klien_pengiriman = '$item' THEN dm.biaya_kirim ELSE 0 END) AS $as_column";
            $klien_pengiriman_vip_list[] = $as_column;
        };

        $subquery = implode(",\n", $subquery);
        $klien_pengiriman_vip_list_select = implode(",\n", $klien_pengiriman_vip_list);
        $klien_pengiriman_vip_list_sum = implode("+\n", $klien_pengiriman_vip_list);


        $query = "
            CREATE OR REPLACE VIEW dpf_rekap_klien_pengiriman_vip AS
            SELECT
            mcp.kode_cp,
            mcp.nama_cp,
            $klien_pengiriman_vip_list_select,
            $klien_pengiriman_vip_list_sum AS grand_total,
            sq.klien_pengiriman_vip
            FROM ( SELECT dm.drop_point_outgoing,
                    $subquery,
                    sum(
                        CASE
                            WHEN dm.sumber_waybill = 'KLIEN PENGIRIM VIP'::text THEN dm.biaya_kirim
                            ELSE 0
                        END) AS klien_pengiriman_vip
                  FROM $schema.data_mart dm
                  WHERE dm.kat = 'DPF'::text
                  GROUP BY dm.drop_point_outgoing) as sq
            join master_collection_point mcp on sq.drop_point_outgoing = mcp.nama_cp
            WHERE mcp.grading_pickup = 'DPF'
            ;
        ";

        return $this->checkAndRunSchema($schema, $query);
    }

    public function rekapDPF($schema){

        $query = "
            CREATE OR REPLACE VIEW dpf_rekap_cashback_dpf AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(cr.total_cashback_reguler, 0) as total_cashback_reguler,
                COALESCE(cmnc.total_cashback_marketplace, 0) as total_cashback_marketplace_non_cod,
                COALESCE(lzrc.total_cashback_luar_zonasi, 0) as total_cashback_mp_luar_zona,
                (COALESCE(cr.total_cashback_reguler, 0) + COALESCE(cmnc.total_cashback_marketplace, 0) + COALESCE(lzrc.total_cashback_luar_zonasi, 0)) as total_cashback
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".dpf_cashback_reguler AS cr ON cp.drop_point_outgoing = cr.nama_cp
            LEFT JOIN
                ".$schema.".dpf_cashback_marketplace_non_cod AS cmnc ON cp.drop_point_outgoing = cmnc.nama_cp
            LEFT JOIN
                ".$schema.".luar_zona_rekap_cashback AS lzrc ON cp.drop_point_outgoing = lzrc.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'DPF'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function rekapDPFDenda($schema){

        $ppn = 1.01;
        $total_cashback_setelah_transit_fee = "(COALESCE(cdrcg1.total_cashback, 0) + COALESCE(cds.penambahan_total, 0)) - COALESCE(cds.transit_fee, 0)";

        $total_denda = "COALESCE(cds.denda_void, 0) + COALESCE(cds.denda_dfod, 0) + COALESCE(cds.denda_pusat, 0) + COALESCE(cds.denda_selisih_berat, 0) + COALESCE(cds.denda_lost_scan_kirim, 0) + COALESCE(cds.denda_auto_claim, 0) + COALESCE(cds.denda_sponsorship, 0) + COALESCE(cds.denda_late_pickup_ecommerce, 0) + COALESCE(cds.potongan_pop, 0) + COALESCE(cds.denda_lainnya, 0)";
        $total_pengurangan_cashback = "($total_denda) + COALESCE(cds.pengurangan_total, 0)";
        $query = "
            CREATE OR REPLACE VIEW dpf_rekap_cashback_denda AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cp.nama_pt,
                COALESCE(cdrcg1.total_cashback, 0) as total_cashback,
                COALESCE(cds.penambahan_total, 0) as penambahan_total,
                ( COALESCE(cdrcg1.total_cashback, 0) + COALESCE(cds.penambahan_total, 0) ) as total_penambahan_cashback,
                COALESCE(cds.transit_fee, 0) as transit_fee,
                ( $total_cashback_setelah_transit_fee ) as total_cashback_setelah_transit_fee,
                COALESCE(cds.denda_void, 0) as denda_void,
                COALESCE(cds.denda_dfod, 0) as denda_dfod,
                COALESCE(cds.denda_pusat, 0) as denda_pusat,
                COALESCE(cds.denda_selisih_berat, 0) as denda_selisih_berat,
                COALESCE(cds.denda_lost_scan_kirim, 0) as denda_lost_scan_kirim,
                COALESCE(cds.denda_auto_claim, 0) as denda_auto_claim,
                COALESCE(cds.denda_sponsorship, 0) as denda_sponsorship,
                COALESCE(cds.denda_late_pickup_ecommerce, 0) as denda_late_pickup_ecommerce,
                COALESCE(cds.potongan_pop, 0) as potongan_pop,
                COALESCE(cds.denda_lainnya, 0) as denda_lainnya,
                ($total_denda) as total_denda,
                (COALESCE(cds.pengurangan_total, 0)) as pengurangan_total,
                ( $total_pengurangan_cashback ) as total_pengurangan_cashback,
                ( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) as total_cashback_setelah_pengurangan,
                ROUND(( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) / $ppn ) as dpp,
                ( cds.setting_pph ) as pph,
                FLOOR((($total_pengurangan_cashback) / $ppn) * (cds.setting_pph / 100)) as amount_pph,
                ROUND(( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) / $ppn ) - (FLOOR((($total_pengurangan_cashback) / $ppn) * (cds.setting_pph / 100))) as amount_setelah_pph,
                cp.nama_bank,
                cp.nomor_rekening

            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".dpf_rekap_cashback_dpf as cdrcg1 on cp.drop_point_outgoing = cdrcg1.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_setting as cds on cp.drop_point_outgoing = cds.nama_cp
            WHERE
                cp.grading_pickup = 'DPF'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function generateMPDPFSumBiayaKirim($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $sumber_waybill_new = $sumber_waybill->map(function ($sumber_waybill) {
            if($sumber_waybill == '') {
                return 'sq.blank';
            }
            return 'sq.'.$sumber_waybill;
        });
        $sumber_waybill_select = implode(",", $sumber_waybill_new->toArray());
        $sumber_waybill_select =  str_replace(" ","_",$sumber_waybill_select);
        $sumber_waybill_select =  str_replace("-","_",$sumber_waybill_select);
        $sumber_waybill_plus = implode("+", $sumber_waybill_new->toArray());
        $sumber_waybill_plus =  str_replace(" ","_",$sumber_waybill_plus);
        $sumber_waybill_plus =  str_replace("-","_",$sumber_waybill_plus);


        $sumber_waybill_sum = $sumber_waybill->map(function ($sumber_waybill) {
            $column = ($sumber_waybill != '' ? str_replace(' ','_',$sumber_waybill) : "");
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            return "SUM(CASE WHEN dm.sumber_waybill = '$sumber_waybill' THEN dm.biaya_kirim ELSE 0 END) AS $as_column";
        });

        $sumber_waybill_sum = implode(",", $sumber_waybill_sum->toArray());

        $dalam_zona = "or (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query .= "
            CREATE OR REPLACE VIEW dpf_mp_sum_biaya_kirim AS
            SELECT
                drop_point_outgoing,
                $sumber_waybill_select,
                ($sumber_waybill_plus) AS grand_total
            FROM (
                SELECT
                    dm.drop_point_outgoing,
                    $sumber_waybill_sum
                FROM
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF' $dalam_zona)
                GROUP BY
                    dm.drop_point_outgoing
            ) AS sq
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function generateMPDPFReturSumBiayaKirim($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $sumber_waybill_new = $sumber_waybill->map(function ($sumber_waybill) {
            if($sumber_waybill == '') {
                return 'sq.blank';
            }
            return 'sq.'.$sumber_waybill;
        });
        $sumber_waybill_select = implode(",", $sumber_waybill_new->toArray());
        $sumber_waybill_select =  str_replace(" ","_",$sumber_waybill_select);
        $sumber_waybill_select =  str_replace("-","_",$sumber_waybill_select);
        $sumber_waybill_plus = implode("+", $sumber_waybill_new->toArray());
        $sumber_waybill_plus =  str_replace(" ","_",$sumber_waybill_plus);
        $sumber_waybill_plus =  str_replace("-","_",$sumber_waybill_plus);


        $sumber_waybill_sum = $sumber_waybill->map(function ($sumber_waybill) {
            $column = ($sumber_waybill != '' ? str_replace(' ','_',$sumber_waybill) : "");
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            return "SUM(CASE WHEN dm.sumber_waybill = '$sumber_waybill' THEN dm.biaya_kirim ELSE 0 END) AS $as_column";
        });

        $sumber_waybill_sum = implode(",", $sumber_waybill_sum->toArray());

        $dalam_zona = "or (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query .= "
            CREATE OR REPLACE VIEW dpf_mp_retur_sum_biaya_kirim AS
            SELECT
                drop_point_outgoing,
                $sumber_waybill_select,
                ($sumber_waybill_plus) AS grand_total
            FROM (
                SELECT
                    dm.drop_point_outgoing,
                    $sumber_waybill_sum
                FROM
                ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF' $dalam_zona)
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing
            ) AS sq
        ";

        $this->checkAndRunSchema($schema, $query);

    }

    public function generateMPDPFCountWaybill($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $sumber_waybill_new = $sumber_waybill->map(function ($sumber_waybill) {
            if($sumber_waybill == '') {
                return 'blank';
            }
            return $sumber_waybill;
        });

        $sumber_waybill_sum = implode("','", $sumber_waybill_new->toArray());
        $sumber_waybill_sum =  str_replace(" ","_",$sumber_waybill_sum);

        $sumber_waybill_count = $sumber_waybill->map(function ($sumber_waybill) {
            $column = ($sumber_waybill != '' ? str_replace(' ','_',$sumber_waybill) : "");
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            return "COUNT(CASE WHEN dm.sumber_waybill = '$sumber_waybill' THEN dm.no_waybill END) AS  $as_column";
        });

        $sumber_waybill_count = implode(",", $sumber_waybill_count->toArray());

        $dalam_zona = "or (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query = "
            CREATE OR REPLACE VIEW dpf_mp_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $sumber_waybill_count,
                    SUM(CASE WHEN dm.sumber_waybill IN ('$sumber_waybill_sum') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF' $dalam_zona)
                GROUP BY
                    dm.drop_point_outgoing
        ";

        $this->checkAndRunSchema($schema, $query);

    }

    public function generateMPDPFReturCountWaybill($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $sumber_waybill_new = $sumber_waybill->map(function ($sumber_waybill) {
            if($sumber_waybill == '') {
                return 'blank';
            }
            return $sumber_waybill;
        });

        $sumber_waybill_sum = implode("','", $sumber_waybill_new->toArray());
        $sumber_waybill_sum =  str_replace(" ","_",$sumber_waybill_sum);

        $sumber_waybill_count = $sumber_waybill->map(function ($sumber_waybill) {
            $column = ($sumber_waybill != '' ? str_replace(' ','_',$sumber_waybill) : "");
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            return "COUNT(CASE WHEN dm.sumber_waybill = '$sumber_waybill' THEN dm.no_waybill END) AS  $as_column";
        });

        $sumber_waybill_count = implode(",", $sumber_waybill_count->toArray());

        $dalam_zona = "or (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query = "
            CREATE OR REPLACE VIEW dpf_mp_retur_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $sumber_waybill_count,
                    SUM(CASE WHEN dm.sumber_waybill IN ('$sumber_waybill_sum') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'DPF' $dalam_zona)
                    AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function checkAndRunSchema($schema, $query){
        if(Schema::hasTable($schema.'.data_mart')) {
            $run = DB::connection('pgsql')->unprepared(
                "
                SET search_path TO $schema, public; \n

                ".$query."
            ");

            return $run;
        };

        return false;
    }
}
