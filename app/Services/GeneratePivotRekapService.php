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

        $this->generateKlienPengirimanVIP($schema);

        $this->cashbackRegulerA($schema);
        $this->cashbackRegulerB($schema);
        $this->cashbackRegulerC($schema);

        $this->cashbackMarketplaceCod($schema);

        $this->cashbackKlienVIP($schema);

        $this->cashbackMarketplaceNonCod($schema);

        $this->cashbackMarketplaceAWBCod($schema);

        $this->cashbackMarketplaceAWBCodGradeC($schema);

    }

    public function cashbackRegulerA($schema) {
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;
        $cashback_reguler_diskon = CashbackSetting::where('jenis_paket', 'REGULER')->first()->diskon;
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->orderBy('id', 'ASC')->get();
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

        $total_biaya_dengan_ppn = "round(($select_sum) / $ppn::float)::bigint";
        $total_biaya_ppn_diskon = "round($total_biaya_dengan_ppn * 0.25)::bigint";

        $query .= "
        CREATE OR REPLACE VIEW cp_dp_cashback_reguler_a AS
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
                LEFT JOIN $schema.cp_dp_all_count_sum acs ON cp.drop_point_outgoing::text = acs.drop_point_outgoing::text
                $joins
            WHERE cp.grading_pickup::text = 'A'::text;
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackRegulerB($schema) {
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;
        $cashback_reguler_diskon = CashbackSetting::where('jenis_paket', 'REGULER')->first()->diskon;
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->orderBy('id', 'ASC')->get();
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

        $total_biaya_dengan_ppn = "round(($select_sum) / $ppn::float)::bigint";
        $total_biaya_ppn_diskon = "round($total_biaya_dengan_ppn * 0.25)::bigint";

        $query .= "
        CREATE OR REPLACE VIEW cp_dp_cashback_reguler_b AS
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
                LEFT JOIN $schema.cp_dp_all_count_sum acs ON cp.drop_point_outgoing::text = acs.drop_point_outgoing::text
                $joins
            WHERE cp.grading_pickup::text = 'B'::text;
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackRegulerC($schema) {
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = 1.011;
        $cashback_reguler_diskon = CashbackSetting::where('jenis_paket', 'REGULER')->first()->diskon;
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->orderBy('id', 'ASC')->get();
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

        $total_biaya_dengan_ppn = "round(($select_sum) / $ppn::float)::bigint";
        $total_biaya_ppn_diskon = "round($total_biaya_dengan_ppn * 0.25)::bigint";

        $query .= "
        CREATE OR REPLACE VIEW cp_dp_cashback_reguler_c AS
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
                LEFT JOIN $schema.cp_dp_all_count_sum acs ON cp.drop_point_outgoing::text = acs.drop_point_outgoing::text
                $joins
            WHERE cp.grading_pickup::text = 'C'::text;
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
            CREATE OR REPLACE VIEW cp_dp_cashback_marketplace_cod AS
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
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceAWBCod($schema){
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = "1.011";
        $ppn_percent = "1.1 / 100";
        $discount_per_awb = "750";
        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;
        $bukalapak = "( COALESCE(sbk.bukalapak, 0) + COALESCE(sbk.bukaexpress, 0) + COALESCE(sbk.bukasend, 0) )";
        $shopee = "( COALESCE(sbk.shopee, 0) + COALESCE(sbk.shopee_cod, 0) )";
        $lazada = "( COALESCE(sbk.lazada, 0) + COALESCE(sbk.lazada_cod, 0) )";
        $magellan = "( COALESCE(sbk.magellan, 0) + COALESCE(sbk.magellan_cod, 0) )";
        $retur_shopee = "( COALESCE(srbk.shopee, 0) + COALESCE(srbk.shopee_cod, 0) )";
        $retur_lazada = "( COALESCE(srbk.lazada, 0) + COALESCE(srbk.lazada_cod, 0) )";
        $retur_magellan = "( COALESCE(srbk.magellan, 0) + COALESCE(srbk.magellan_cod, 0) )";

        $retur_list = [
            'akulakuob',
            'evermosapi',
            'mengantar',
            'ordivo',
            'tokopedia',
            'lazada',
            'klien_pengirim_vip',
            'clodeohq'
        ];

        $retur_select_list = [];

        foreach($retur_list as $item) {
            $retur_select_list[] = "COALESCE(srbk.$item, 0)";
        }

        $retur_select_sum = implode("+\n", $retur_select_list);

        $total_awb_cod = "( $bukalapak +  $shopee + $lazada + $magellan + COALESCE(sbk.tokopedia, 0) + COALESCE(sbk.klien_pengirim_vip, 0) ) - ($retur_shopee + $retur_magellan + ($retur_select_sum) )";
        $query = "
            CREATE OR REPLACE VIEW cp_dp_cashback_marketplace_awb_cod AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                $bukalapak AS bukalapak,
                $shopee as shopee,
                $lazada AS lazada,
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                $magellan AS magellan,
                COALESCE(sbk.klien_pengirim_vip, 0) AS klien_pengirim_vip,
                $retur_shopee as retur_shopee,
                $retur_magellan as retur_magellan,
                ($retur_select_sum) as retur_pilihan,
                COALESCE(cds.retur_belum_terpotong, 0) AS retur_belum_terpotong,
                $total_awb_cod as total_awb,
                CAST(($discount_per_awb * $total_awb_cod ) AS BIGINT) as discount_per_awb,
                FLOOR(($discount_per_awb * $total_awb_cod ) * ($ppn_percent)) as ppn,
                ROUND(($discount_per_awb * $total_awb_cod) / $ppn) as total_cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_mp_count_waybill AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_count_waybill AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_setting AS cds ON cp.drop_point_outgoing = cds.nama_cp
            WHERE
                cp.grading_pickup = 'B'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function cashbackMarketplaceAWBCodGradeC($schema){
        // $ppn = 1 + (intval(GlobalSetting::where('code', 'ppn')->first()->value) / 100);
        $ppn = "1.011";
        $ppn_percent = "1.1 / 100";
        $discount_per_awb = "350";
        $cashback_marketplace_diskon = intval(CashbackSetting::where('jenis_paket', 'MARKETPLACE')->first()->diskon) / 100;
        $bukalapak = "( COALESCE(sbk.bukalapak, 0) + COALESCE(sbk.bukaexpress, 0) + COALESCE(sbk.bukasend, 0) )";
        $shopee = "( COALESCE(sbk.shopee, 0) + COALESCE(sbk.shopee_cod, 0) )";
        $lazada = "( COALESCE(sbk.lazada, 0) + COALESCE(sbk.lazada_cod, 0) )";
        $magellan = "( COALESCE(sbk.magellan, 0) + COALESCE(sbk.magellan_cod, 0) )";
        $retur_shopee = "( COALESCE(srbk.shopee, 0) + COALESCE(srbk.shopee_cod, 0) )";
        $retur_lazada = "( COALESCE(srbk.lazada, 0) + COALESCE(srbk.lazada_cod, 0) )";
        $retur_magellan = "( COALESCE(srbk.magellan, 0) + COALESCE(srbk.magellan_cod, 0) )";

        $retur_list = [
            'akulakuob',
            'evermosapi',
            'mengantar',
            'ordivo',
            'tokopedia',
            'lazada',
            'klien_pengirim_vip',
            'clodeohq'
        ];

        $retur_select_list = [];

        foreach($retur_list as $item) {
            $retur_select_list[] = "COALESCE(srbk.$item, 0)";
        }

        $retur_select_sum = implode("+\n", $retur_select_list);

        $total_awb_cod = "( $bukalapak +  $shopee + $lazada + $magellan + COALESCE(sbk.tokopedia, 0) + COALESCE(sbk.klien_pengirim_vip, 0) ) - ($retur_shopee + $retur_magellan + ($retur_select_sum) )";
        $query = "
            CREATE OR REPLACE VIEW cp_dp_cashback_marketplace_awb_g3_cod AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                $bukalapak AS bukalapak,
                $shopee as shopee,
                $lazada AS lazada,
                COALESCE(sbk.tokopedia, 0) AS tokopedia,
                $magellan AS magellan,
                COALESCE(sbk.klien_pengirim_vip, 0) AS klien_pengirim_vip,
                $retur_shopee as retur_shopee,
                $retur_magellan as retur_magellan,
                ($retur_select_sum) as retur_pilihan,
                COALESCE(cds.retur_belum_terpotong, 0) AS retur_belum_terpotong,
                $total_awb_cod as total_awb,
                CAST(($discount_per_awb * $total_awb_cod ) AS BIGINT) as discount_per_awb,
                FLOOR(($discount_per_awb * $total_awb_cod ) * ($ppn_percent)) as ppn,
                ROUND(($discount_per_awb * $total_awb_cod) / $ppn) as total_cashback_marketplace
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_mp_count_waybill AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_count_waybill AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_setting AS cds ON cp.drop_point_outgoing = cds.nama_cp
            WHERE
                cp.grading_pickup = 'C'
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
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_cashback_klien_vip AS ckv ON cp.drop_point_outgoing = ckv.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_setting AS cds ON cp.drop_point_outgoing = cds.nama_cp

            WHERE
                cp.grading_pickup = 'A'
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
            CREATE OR REPLACE VIEW cp_dp_cashback_klien_vip AS
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
                ".$schema.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            LEFT JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim AS srbk ON cp.drop_point_outgoing = srbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
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
            CREATE OR REPLACE VIEW cp_dp_rekap_klien_pengiriman_vip AS
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
                  WHERE dm.kat = 'CP'::text OR dm.kat = 'DP'::text
                  GROUP BY dm.drop_point_outgoing) as sq
            join master_collection_point mcp on sq.drop_point_outgoing = mcp.nama_cp
            WHERE mcp.grading_pickup = 'A'
            ;
        ";

        return $this->checkAndRunSchema($schema, $query);
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
