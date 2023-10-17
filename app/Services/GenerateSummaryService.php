<?
namespace App\Services;

use App\Models\Periode;
use App\Models\PeriodeDataJson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;

class GenerateSummaryService {
    public function runSummaryGenerator($schema, Periode $periode){
        $this->rekapGrading1($schema);
        $this->rekapGrading1Denda($schema);

        //tidak perlu summary grading karena data terlalu berat ketika view - to - view kalukulasi.
        //akan dibuatkan kalkulasi via json
        $this->saveToJson($schema, $periode);
    }

    public function saveToJson($schema, Periode $periode){
        $category = CategoryKlienPengiriman::where('cashback_type', 'reguler')->get();
        $data_pivot = [];
        $data_pivot_mp = [];

        foreach($category as $cat) {
            $data_pivot[$cat->kode_kategori] = DB::table($schema.'.cp_dp_'.$cat->kode_kategori.'_count_sum')->get()->toArray();
        }

        //find periode data json
        $data_json = PeriodeDataJson::where('periode_id', $periode->id)->first();

        //save to periode
        $data_pivot_mp['cp_dp_mp_sum_biaya_kirim'] = DB::table($schema.'.cp_dp_mp_sum_biaya_kirim')->get()->toArray();
        $data_pivot_mp['cp_dp_mp_count_waybill'] = DB::table($schema.'.cp_dp_mp_count_waybill')->get()->toArray();
        $data_pivot_mp['cp_dp_mp_retur_sum_biaya_kirim'] = DB::table($schema.'.cp_dp_mp_retur_sum_biaya_kirim')->get()->toArray();
        $data_pivot_mp['cp_dp_mp_retur_count_waybill'] = DB::table($schema.'.cp_dp_mp_retur_count_waybill')->get()->toArray();
        $data_pivot_mp['cp_dp_mp_result_sum_biaya_kirim'] = DB::table($schema.'.cp_dp_mp_result_sum_biaya_kirim')->get()->toArray();
        $data_pivot_mp['cp_dp_mp_result_count_biaya_kirim'] = DB::table($schema.'.cp_dp_mp_result_count_biaya_kirim')->get()->toArray();

        $data_pivot_vip = DB::table($schema.'.cp_dp_rekap_klien_pengiriman_vip')->get()->toArray();

        $data_cashback_reguler = DB::table($schema.'.cp_dp_cashback_reguler');
        $data_cashback_marketplace_cod = DB::table($schema.'.cp_dp_cashback_marketplace_cod');
        $data_cashback_marketplace_non_cod = DB::table($schema.'.cp_dp_cashback_marketplace_non_cod');
        $data_cashback_klien_vip = DB::table($schema.'.cp_dp_cashback_klien_vip');
        $data_cashback_luar_zona = DB::table($schema.'.luar_zona_rekap_cashback');
        $data_cashback_setting = DB::table($schema.'.cp_dp_setting');
        $data_cashback_grading_1_denda = DB::table($schema.'.cp_dp_rekap_cashback_grading_1_denda');

        $get_data_cashback_reguler = $data_cashback_reguler->get()->toArray();
        $get_data_cashback_marketplace_cod = $data_cashback_marketplace_cod->get()->toArray();
        $get_data_cashback_marketplace_non_cod = $data_cashback_marketplace_non_cod->get()->toArray();
        $get_data_cashback_klien_vip = $data_cashback_klien_vip->get()->toArray();
        $get_data_cashback_luar_zona = $data_cashback_luar_zona->get()->toArray();
        $get_data_cashback_setting = $data_cashback_setting->get()->toArray();
        $get_cashback_grading_1_denda = $data_cashback_grading_1_denda->get()->toArray();

        $data_grading_1 = [];

        foreach($get_data_cashback_reguler as $key => $item){
            $data_grading_1[] = [
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
                'pivot_' => json_encode($data_pivot),
                'pivot_mp' => json_encode($data_pivot_mp),
                'pivot_vip' => json_encode($data_pivot_vip),
                'cashback_reguler' => json_encode($data_cashback_reguler->get()->toArray()),
                'cashback_marketplace_cod' => json_encode($data_cashback_marketplace_cod->get()->toArray()),
                'cashback_marketplace_non_cod' => json_encode($data_cashback_marketplace_non_cod->get()->toArray()),
                'cashback_klien_vip' => json_encode($data_cashback_klien_vip->get()->toArray()),
                'cashback_grading_1' => json_encode($data_grading_1),
                'cashback_luar_zona' => json_encode($get_data_cashback_luar_zona),
                'cashback_setting' => json_encode($get_data_cashback_setting),
                'cashback_grading_1_denda'  => json_encode($get_cashback_grading_1_denda),
                'cashback_grading_2' => json_encode([]),
                'cashback_grading_2_denda' => json_encode([]),
                'cashback_grading_3' => json_encode([]),
                'cashback_grading_3_denda' => json_encode([]),
            ]);
        } else {
            PeriodeDataJson::create([
                'periode_id' => $periode->id,
                'pivot_' => json_encode($data_pivot),
                'pivot_mp' => json_encode($data_pivot_mp),
                'pivot_vip' => json_encode($data_pivot_vip),
                'cashback_reguler' => json_encode($data_cashback_reguler->get()->toArray()),
                'cashback_marketplace_cod' => json_encode($data_cashback_marketplace_cod->get()->toArray()),
                'cashback_marketplace_non_cod' => json_encode($data_cashback_marketplace_non_cod->get()->toArray()),
                'cashback_klien_vip' => json_encode($data_cashback_klien_vip->get()->toArray()),
                'cashback_grading_1' => json_encode($data_grading_1),
                'cashback_luar_zona' => json_encode($get_data_cashback_luar_zona),
                'cashback_setting' => json_encode($get_data_cashback_setting),
                'cashback_grading_1_denda'  => json_encode($get_cashback_grading_1_denda),
                'cashback_grading_2' => json_encode([]),
                'cashback_grading_2_denda' => json_encode([]),
                'cashback_grading_3' => json_encode([]),
                'cashback_grading_3_denda' => json_encode([]),
            ]);
        }
    }

    public function rekapGrading1($schema){

        $query = "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_1 AS
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
                ".$schema.".cp_dp_cashback_reguler AS cr ON cp.drop_point_outgoing = cr.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_cashback_marketplace_non_cod AS cmnc ON cp.drop_point_outgoing = cmnc.nama_cp
            LEFT JOIN
                ".$schema.".luar_zona_rekap_cashback AS lzrc ON cp.drop_point_outgoing = lzrc.drop_point_outgoing
            WHERE
                cp.grading_pickup = 'A'
        ";

        $this->checkAndRunSchema($schema, $query);
    }

    public function rekapGrading1Denda($schema){

        $ppn = 1.01;
        $total_cashback_setelah_transit_fee = "(COALESCE(cdrcg1.total_cashback, 0) + COALESCE(cds.penambahan_total, 0)) - COALESCE(cds.transit_fee)";

        $total_denda = "COALESCE(cds.denda_void, 0) + COALESCE(cds.denda_dfod, 0) + COALESCE(cds.denda_pusat, 0) + COALESCE(cds.denda_selisih_berat, 0) + COALESCE(cds.denda_auto_claim, 0) + COALESCE(cds.denda_sponsorship, 0) + COALESCE(cds.denda_late_pickup_ecommerce, 0) + COALESCE(cds.potongan_pop, 0) + COALESCE(cds.denda_lainnya, 0)";
        $total_pengurangan_cashback = "($total_denda) + COALESCE(cds.retur_belum_terpotong, 0) + COALESCE(cds.pengurangan_total, 0)";
        $query = "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_1_denda AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cp.nama_pt,
                COALESCE(cdrcg1.total_cashback, 0) as total_cashback,
                COALESCE(cds.penambahan_total, 0) as penambahan_total,
                ( COALESCE(cdrcg1.total_cashback, 0) + COALESCE(cds.penambahan_total, 0) ) as total_penambahan_cashback,
                COALESCE(cds.transit_fee) as transit_fee,
                ( $total_cashback_setelah_transit_fee ) as total_cashback_setelah_transit_fee,
                COALESCE(cds.denda_void, 0) as denda_void,
                COALESCE(cds.denda_dfod, 0) as denda_dfod,
                COALESCE(cds.denda_pusat, 0) as denda_pusat,
                COALESCE(cds.denda_selisih_berat, 0) as denda_selisih_berat,
                COALESCE(cds.denda_auto_claim, 0) as denda_auto_claim,
                COALESCE(cds.denda_sponsorship, 0) as denda_sponsorship,
                COALESCE(cds.denda_late_pickup_ecommerce, 0) as denda_late_pickup_ecommerce,
                COALESCE(cds.potongan_pop, 0) as potongan_pop,
                COALESCE(cds.denda_lainnya, 0) as denda_lainnya,
                ($total_denda) as total_denda,
                (COALESCE(cds.retur_belum_terpotong, 0)) as retur_belum_terpotong,
                (COALESCE(cds.pengurangan_total, 0)) as pengurangan_total,
                ( $total_pengurangan_cashback ) as total_pengurangan_cashback,
                ( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) as total_cashback_setelah_pengurangan,
                (( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) / $ppn ) as dpp,
                (cds.setting_pph / 100) as pph,
                FLOOR((($total_pengurangan_cashback) / $ppn) * (cds.setting_pph / 100)) as amount_pph,
                (( ($total_cashback_setelah_transit_fee) - ($total_pengurangan_cashback )) / $ppn ) - (FLOOR((($total_pengurangan_cashback) / $ppn) * (cds.setting_pph / 100))) as amount_setelah_pph,
                cp.nama_bank,
                cp.nomor_rekening

            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_rekap_cashback_grading_1 as cdrcg1 on cp.drop_point_outgoing = cdrcg1.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_setting as cds on cp.drop_point_outgoing = cds.nama_cp
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
