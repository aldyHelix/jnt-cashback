<?
namespace App\Services;

use App\Models\GlobalSumberWaybill;
use App\Models\PeriodeKlienPengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;

class GeneratePivotTableService {

    public function runMPGenerator($schema){
        $this->generateMPCountWaybill($schema);

        $this->generateMPSumBiayaKirim($schema);

        $this->generateMPReturCountWaybill($schema);

        $this->generateMPReturSumBiayaKirim($schema);

        $this->generateMPResultSumBiayaKirim($schema);

        $this->generateMPResultCountBiayaKirim($schema);
    }

    public function createOrReplacePivot($schema, $periode_id) {
        //get category
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->get();
        $query = "";
        // $schema = 'cashback_feb_2022'; //for debuging

        $dalam_zona = "or
            (data_mart.drop_point_outgoing = 'CP_BNR' and data_mart.kat = 'DALAM ZONASI') or
            (data_mart.drop_point_outgoing = 'PESONA_DARUSSALAM' and data_mart.kat = 'DALAM ZONASI') or
            (data_mart.drop_point_outgoing = 'PAMOYANAN_BOGOR' and data_mart.kat = 'DALAM ZONASI')";

        $query .= "

            CREATE OR REPLACE VIEW sum_all_biaya_kirim AS
                SELECT SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart;

            CREATE OR REPLACE VIEW cp_dp_all_count_sum AS
                SELECT DISTINCT (data_mart.drop_point_outgoing), COUNT(data_mart.no_waybill), SUM(data_mart.biaya_kirim)
                FROM ".$schema.".data_mart
                WHERE (data_mart.kat = 'CP' OR data_mart.kat = 'DP' $dalam_zona)
                GROUP BY data_mart.drop_point_outgoing;

            CREATE OR REPLACE VIEW cp_dp_setting AS
                select
                    mcp.kode_cp,
                    mcp.nama_cp,
                    coalesce (sdp.retur_klien_pengirim_hq, 0) as retur_klien_pengirim_hq,
                    coalesce (sdp.retur_belum_terpotong,0) as retur_belum_terpotong,
                    coalesce (sdp.pengurangan_total,0) as pengurangan_total ,
                    coalesce (sdp.penambahan_total,0) as penambahan_total,
                    coalesce (sdp.setting_pph,0) as setting_pph,
                    dgp.transit_fee,
                    dgp.denda_void,
                    dgp.denda_dfod,
                    dgp.denda_pusat,
                    dgp.denda_selisih_berat,
                    dgp.denda_lost_scan_kirim,
                    dgp.denda_auto_claim,
                    dgp.denda_sponsorship,
                    dgp.denda_late_pickup_ecommerce,
                    dgp.potongan_pop,
                    dgp.denda_lainnya
                from denda_grading_periode dgp
                left join master_collection_point mcp on mcp.id  = dgp.sprinter_pickup
                left join setting_dp_periode sdp on sdp.drop_point_outgoing  =  mcp.drop_point_outgoing
                where dgp.periode_id = $periode_id;

        ";

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

            $dalam_zona = "or
            (data_mart.drop_point_outgoing = 'CP_BNR' and data_mart.kat = 'DALAM ZONASI') or
            (data_mart.drop_point_outgoing = 'PESONA_DARUSSALAM' and data_mart.kat = 'DALAM ZONASI') or
            (data_mart.drop_point_outgoing = 'PAMOYANAN_BOGOR' and data_mart.kat = 'DALAM ZONASI')";

            $query .= "
                CREATE OR REPLACE VIEW cp_dp_".$cat->kode_kategori."_count_sum AS
                    SELECT DISTINCT data_mart.drop_point_outgoing,
                        count(data_mart.no_waybill) AS count,
                        sum(data_mart.$sum_column) AS sum
                        FROM ".$schema.".data_mart
                    WHERE
                        ($kat  $dalam_zona)
                    AND
                    ($metode_pembayaran)
                    AND
                    (data_mart.klien_pengiriman IN ( $klien_pengiriman ))
                    GROUP BY data_mart.drop_point_outgoing;

            ";
        }

        return $this->checkAndRunSchema($schema, $query);

    }

    public function generateMPSumBiayaKirim($schema){
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

        $dalam_zona = "or
            (dm.drop_point_outgoing = 'CP_BNR' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PESONA_DARUSSALAM' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query .= "
            CREATE OR REPLACE VIEW cp_dp_mp_sum_biaya_kirim AS
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
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP' $dalam_zona)
                GROUP BY
                    dm.drop_point_outgoing
            ) AS sq
        ";

        return $this->checkAndRunSchema($schema, $query);
    }

    public function generateMPReturSumBiayaKirim($schema){
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

        $dalam_zona = "or
            (dm.drop_point_outgoing = 'CP_BNR' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PESONA_DARUSSALAM' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query .= "
            CREATE OR REPLACE VIEW cp_dp_mp_retur_sum_biaya_kirim AS
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
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP' $dalam_zona)
                AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing
            ) AS sq
        ";

        return $this->checkAndRunSchema($schema, $query);

    }

    public function generateMPCountWaybill($schema){
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

        $dalam_zona = "or
            (dm.drop_point_outgoing = 'CP_BNR' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PESONA_DARUSSALAM' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query = "
            CREATE OR REPLACE VIEW cp_dp_mp_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $sumber_waybill_count,
                    SUM(CASE WHEN dm.sumber_waybill IN ('$sumber_waybill_sum') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP' $dalam_zona)
                GROUP BY
                    dm.drop_point_outgoing
        ";

        return $this->checkAndRunSchema($schema, $query);

    }

    public function generateMPReturCountWaybill($schema){
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

        $dalam_zona = "or
            (dm.drop_point_outgoing = 'CP_BNR' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PESONA_DARUSSALAM' and dm.kat = 'DALAM ZONASI') or
            (dm.drop_point_outgoing = 'PAMOYANAN_BOGOR' and dm.kat = 'DALAM ZONASI')";

        $query = "
            CREATE OR REPLACE VIEW cp_dp_mp_retur_count_waybill AS
                SELECT dm.drop_point_outgoing,
                    $sumber_waybill_count,
                    SUM(CASE WHEN dm.sumber_waybill IN ('$sumber_waybill_sum') THEN 1 ELSE 0 END) AS grand_total
                FROM
                    ".$schema.".data_mart dm
                WHERE (dm.kat = 'CP' OR dm.kat = 'DP' $dalam_zona)
                    AND (dm.paket_retur = '1' OR dm.paket_retur = 'Returned' OR (dm.paket_retur ~ '^\\d+$' AND CAST(dm.paket_retur AS INTEGER) = 1))
                GROUP BY
                    dm.drop_point_outgoing
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

    public function generateMPResultSumBiayaKirim($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $grand_total_query = [];

        foreach($sumber_waybill as $sb) {
            $as_column = ($sb != "" ? str_replace(" ","_",$sb) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            $grand_total_query[] = "cdmsbk.$as_column - cdmsrbk.$as_column";
        }

        $sumber_waybill_sum = $sumber_waybill->map(function ($sumber_waybill) use ($grand_total_query){
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);

            return "cdmsbk.$as_column - cdmsrbk.$as_column AS $as_column";
        });

        $sumber_waybill_sum = implode(",\n", $sumber_waybill_sum->toArray());
        $grand_total = implode(" + \n", $grand_total_query);

        $query .= "
            CREATE OR REPLACE VIEW cp_dp_mp_result_sum_biaya_kirim AS
            SELECT
                cdmsbk.drop_point_outgoing,
                $sumber_waybill_sum,
                ($grand_total) as grand_total
            FROM
                ".$schema.".cp_dp_mp_sum_biaya_kirim cdmsbk
            JOIN
                ".$schema.".cp_dp_mp_retur_sum_biaya_kirim cdmsrbk ON cdmsbk.drop_point_outgoing = cdmsrbk.drop_point_outgoing
        ";

        return $this->checkAndRunSchema($schema, $query);
    }

    public function generateMPResultCountBiayaKirim($schema){
        $sumber_waybill = GlobalSumberWaybill::orderBy('sumber_waybill', 'ASC')->get()->pluck('sumber_waybill');
        $query = "";

        $grand_total_query = [];

        foreach($sumber_waybill as $sb) {
            $as_column = ($sb != "" ? str_replace(" ","_",$sb) : 'blank');
            $as_column = str_replace("-","_",$as_column);
            $grand_total_query[] = "cdmcw.$as_column - cdmcrw.$as_column";
        }

        $sumber_waybill_sum = $sumber_waybill->map(function ($sumber_waybill) use ($grand_total_query){
            $as_column = ($sumber_waybill != "" ? str_replace(" ","_",$sumber_waybill) : 'blank');
            $as_column = str_replace("-","_",$as_column);

            return "cdmcw.$as_column - cdmcrw.$as_column AS $as_column";
        });

        $sumber_waybill_sum = implode(",\n", $sumber_waybill_sum->toArray());
        $grand_total = implode(" + \n", $grand_total_query);

        $query .= "
            CREATE OR REPLACE VIEW cp_dp_mp_result_count_biaya_kirim AS
            SELECT
                cdmcw.drop_point_outgoing,
                $sumber_waybill_sum,
                ($grand_total) as grand_total
            FROM
                ".$schema.".cp_dp_mp_count_waybill cdmcw
            JOIN
                ".$schema.".cp_dp_mp_retur_count_waybill cdmcrw ON cdmcw.drop_point_outgoing = cdmcrw.drop_point_outgoing
        ";

        return $this->checkAndRunSchema($schema, $query);
    }
}
