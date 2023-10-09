<?
namespace App\Services;

use App\Models\CashbackSetting;
use App\Models\GlobalSetting;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;
use Predis\Command\Redis\KEYS;

class GeneratePivotRekapService {

    public function runRekapGenerator($schema){
        $this->cashbackReguler($schema);

        $this->cashbackMarketplaceCod($schema);

        $this->cashbackMarketplaceNonCod($schema);

        $this->cashbackKlienVIP($schema);
    }

    public function cashbackReguler($schema) {
        $pph = 1 + (intval(GlobalSetting::where('code', 'pph')->first()->value) / 100);
        $cashback_reguler_diskon = CashbackSetting::where('jenis_paket', 'REGULER')->first()->diskon;
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->get();
        $query = "";

        $joins = [];
        $select_column = [];
        $select_sum = [];

        foreach($category as $cat) {
            $joins[] = "LEFT JOIN $schema.cp_dp_".$cat->kode_kategori."_count_sum ON cp.drop_point_outgoing::text = cp_dp_".$cat->kode_kategori."_count_sum.drop_point_outgoing::text";

            $select_column[] = "COALESCE(cp_dp_".$cat->kode_kategori."_count_sum.sum, 0::bigint) AS biaya_kirim_".$cat->kode_kategori;
            $select_sum[] = "COALESCE(cp_dp_".$cat->kode_kategori."_count_sum.sum, 0::bigint)";
        }

        $joins = implode("\n", $joins);
        $select_column = implode(",\n", $select_column);
        $select_sum = implode("+\n", $select_sum);

        $total_biaya_dengan_pph = "round(($select_sum) / $pph::float)::bigint";
        $total_biaya_pph_diskon = "round($total_biaya_dengan_pph * 0.25)::bigint";

        $query .= "
        CREATE OR REPLACE VIEW cp_dp_cashback_reguler AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0::bigint) AS biaya_kirim_all,
                $select_column,
                ($select_sum) AS total_biaya_kirim,
                $total_biaya_dengan_pph AS total_biaya_kirim_dikurangi_ppn,
                $total_biaya_pph_diskon AS amount_discount_25,
                ($total_biaya_pph_diskon) AS total_cashback_reguler
            FROM master_collection_point cp
                LEFT JOIN $schema.cp_dp_all_count_sum acs ON cp.drop_point_outgoing::text = acs.drop_point_outgoing::text
                $joins
            WHERE cp.grading_pickup::text = 'A'::text;
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceCod($schema){
        $ppn = 1 + (intval(GlobalSetting::where('code', 'pph')->first()->value) / 100);
        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;
        $bukalapak = "( COALESCE(sbk.bukalapak, 0) + COALESCE(sbk.bukaexpress, 0) + COALESCE(sbk.bukasend, 0) )";
        $bukalapak_ppn = "CAST( ROUND( ( $bukalapak ) / $ppn::float ) AS BIGINT )";
        $bukalapak_ppn_diskon = "CAST( ROUND( $bukalapak_ppn * 0.05 ) AS BIGINT )";
        $shopee_cod = "( COALESCE(sbk.shopee_cod, 0) - COALESCE(srbk.shopee_cod, 0) )";
        $magellan_cod = "( COALESCE(sbk.magellan_cod, 0) - COALESCE(srbk.magellan_cod, 0) )";
        $lazada_cod = "( COALESCE(sbk.lazada_cod, 0) - COALESCE(srbk.lazada_cod, 0) ) ";
        $total_biaya_kirim_cod = "CAST( ROUND( $shopee_cod + $magellan_cod + $lazada_cod ) AS BIGINT ) ";
        $total_biaya_kirim_cod_ppn = " CAST( ROUND( $shopee_cod + $magellan_cod + $lazada_cod ) / $ppn::float AS BIGINT )";
        $total_biaya_kirim_cod_ppn_diskon = " CAST( ROUND( ( ( $shopee_cod + $magellan_cod + $lazada_cod ) / $ppn ) * $cashback_marketplace_diskon::float ) AS BIGINT )";
        $tokopedia_ppn = "CAST( ROUND( COALESCE(sbk.tokopedia, 0) / $ppn::float ) AS BIGINT )";
        $tokopedia_ppn_diskon = "CAST( ROUND( (COALESCE(sbk.tokopedia, 0) / $ppn::float) * 0.1 ) AS BIGINT )";
        $query = "
            CREATE OR REPLACE VIEW cp_dp_cashback_marketplace_cod AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                $bukalapak AS bukalapak,
                $bukalapak AS total_biaya_kirim_bukalapak,
                $bukalapak_ppn AS total_biaya_kirim_bukalapak_dikurangi_ppn,
                $bukalapak_ppn_diskon AS discount_bukalapak_5,
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
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                COALESCE(sbk.tokopedia, 0) AS total_biaya_kirim_tokopedia,
                $tokopedia_ppn AS total_biaya_kirim_tokopedia_dikurangi_ppn,
                $tokopedia_ppn_diskon AS discount_tokopedia_10,
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
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceNonCod($schema){
        $ppn = 1 + (intval(GlobalSetting::where('code', 'pph')->first()->value) / 100);
        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;

        $retur_list = [
            'akulakuob',
            'bukaexpress',
            'evermosapi',
            'mengantar',
            'ordivo',
            'tokopedia'
        ];

        $retur_select_list = [];

        foreach($retur_list as $item) {
            $retur_select_list[] = "COALESCE(srbk.$item, 0)";
        }

        $retur_select_sum = implode("+\n", $retur_select_list);
        $total_biaya_kirim_lazada = "(COALESCE(sbk.lazada, 0) - COALESCE(srbk.lazada, 0))";
        $total_biaya_kirim_shopee = "(COALESCE(sbk.shopee, 0) - COALESCE(srbk.shopee, 0))";
        $total_biaya_kirim_magellan = "(COALESCE(sbk.magellan, 0) - COALESCE(srbk.magellan, 0))";
        $total_biaya_kirim_non_cod = "($total_biaya_kirim_lazada + $total_biaya_kirim_shopee + $total_biaya_kirim_magellan + ($retur_select_sum) + 0 )";
        $total_biaya_kirim_non_cod_ppn = "(($total_biaya_kirim_lazada + $total_biaya_kirim_shopee + $total_biaya_kirim_magellan + ($retur_select_sum) + 0 ) / $ppn::float )";
        $total_biaya_kirim_non_cod_ppn_diskon = "(($total_biaya_kirim_lazada + $total_biaya_kirim_shopee + $total_biaya_kirim_magellan + ($retur_select_sum) + 0 ) / $ppn::float ) * 0.09 ";

        $query = "
            CREATE OR REPLACE VIEW cp_dp_cashback_marketplace_non_cod AS
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
                ( $retur_select_sum ) AS total_retur_pilihan,
                $total_biaya_kirim_non_cod AS total_biaya_kirim_non_cod,
                CAST(ROUND( $total_biaya_kirim_non_cod_ppn ) AS BIGINT) AS total_biaya_kirim_non_cod_dikurangi_ppn,
                CAST(ROUND( $total_biaya_kirim_non_cod_ppn_diskon ) AS BIGINT) AS discount_total_biaya_kirim_9,
                CAST(cmc.cashback_marketplace AS BIGINT) + CAST(ROUND( $total_biaya_kirim_non_cod_ppn_diskon) AS BIGINT) AS total_cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_cashback_marketplace_cod AS cmc ON cp.drop_point_outgoing = cmc.nama_cp

            WHERE
                cp.grading_pickup = 'A'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackKlienVIP($schema){
        $ppn = 1 + (intval(GlobalSetting::where('code', 'pph')->first()->value) / 100);
        $vip_diskon = intval(CashbackSetting::where('jenis_paket', 'VIP')->first()->diskon) / 100;
        $periode = Periode::where('code', $schema)->first();
        $category = CategoryKlienPengiriman::where('nama_kategori', 'VIP')->first();
        $sumber_waybill_list = DB::getSchemaBuilder()->getColumnListing($schema.'.cp_dp_mp_sum_biaya_kirim');


        $klien_pengiriman = DB::table('periode_klien_pengiriman')
        ->join('global_klien_pengiriman', 'global_klien_pengiriman.id', '=','periode_klien_pengiriman.klien_pengiriman_id')
        ->select('global_klien_pengiriman.klien_pengiriman')
        ->where('periode_klien_pengiriman.category_id', $category->id)
        ->where('periode_klien_pengiriman.periode_id', $periode->id)
        ->orderBy('global_klien_pengiriman.klien_pengiriman', 'ASC')
        ->get()->pluck('klien_pengiriman')->toArray();

        $klien_pengiriman_sumber_waybill = [];

        foreach($klien_pengiriman as $item) {
            if(in_array(strtolower(str_replace("-","_",$item)), $sumber_waybill_list)){
                $klien_pengiriman_sumber_waybill[] = "COALESCE(sbk.".str_replace("-","_",$item).", 0)";
            }
        }

        $sum_klien_pengiriman = implode("+\n", $klien_pengiriman_sumber_waybill);
        $sum_klien_pengiriman_ppn = "($sum_klien_pengiriman / $ppn::float )";
        $sum_klien_pengiriman_ppn_diskon = "($sum_klien_pengiriman / $ppn::float ) * $vip_diskon";

        $query = "
            CREATE OR REPLACE VIEW cp_dp_cashback_klien_vip AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                ($sum_klien_pengiriman) AS total_biaya_kirim_vip,
                CAST(ROUND( $sum_klien_pengiriman_ppn ) AS BIGINT) AS total_biaya_kirim_vip_dikurangi_ppn,
                CAST(ROUND( $sum_klien_pengiriman_ppn_diskon ) AS BIGINT) AS discount_total_biaya_kirim_10
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
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
