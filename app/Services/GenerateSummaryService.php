<?
namespace App\Services;

use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\CategoryKlienPengiriman;

class GenerateSummaryService {
    public function runSummaryGenerator($schema, Periode $periode){
        $this->rekapGrading1($schema);

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

        $get_data_cashback_reguler = $data_cashback_reguler->get()->toArray();
        $get_data_cashback_marketplace_cod = $data_cashback_marketplace_cod->get()->toArray();
        $get_data_cashback_marketplace_non_cod = $data_cashback_marketplace_non_cod->get()->toArray();
        $get_data_cashback_klien_vip = $data_cashback_klien_vip->get()->toArray();
        $get_data_cashback_mp_luar_zona = 0;

        $data_grading_1 = [];

        foreach($get_data_cashback_reguler as $key => $item){
            $data_grading_1[] = [
                'kode_cp' => $item->kode_cp,
                'nama_cp' => $item->nama_cp,
                'total_cashback_reguler' => $item->total_cashback_reguler,
                'total_cashback_marketplace_cod' => $get_data_cashback_marketplace_cod[$key]->cashback_marketplace,
                'total_cashback_marketplace_non_cod' => $get_data_cashback_marketplace_non_cod[$key]->total_cashback_marketplace,
                'total_cashback_klien_vip' => $get_data_cashback_klien_vip[$key]->cashback_marketplace ?? 0,
                'total_cashback_mp_luar_zona' => $get_data_cashback_mp_luar_zona,
                'total_cashback' => $item->total_cashback_reguler + $get_data_cashback_marketplace_non_cod[$key]->total_cashback_marketplace + $get_data_cashback_mp_luar_zona
            ];
        }

        $updated = $periode->update([
            'data_pivot' => json_encode($data_pivot),
            'data_pivot_mp' => json_encode($data_pivot_mp),
            'data_pivot_vip' => json_encode($data_pivot_vip),
            'data_cashback_reguler' => json_encode($data_cashback_reguler->get()->toArray()),
            'data_cashback_marketplace_cod' => json_encode($data_cashback_marketplace_cod->get()->toArray()),
            'data_cashback_marketplace_non_cod' => json_encode($data_cashback_marketplace_non_cod->get()->toArray()),
            'data_cashback_klien_vip' => json_encode($data_cashback_klien_vip->get()->toArray()),
            'data_cashback_grading_1' => json_encode($data_grading_1)
        ]);
    }

    public function rekapGrading1($schema){

        $query = "
            CREATE OR REPLACE VIEW cp_dp_rekap_cashback_grading_1 AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                cr.total_cashback_reguler as total_cashback_reguler,
                cmnc.total_cashback_marketplace as total_cashback_marketplace_non_cod,
                (cr.total_cashback_reguler + cmnc.total_cashback_marketplace) as total_cashback
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$schema.".cp_dp_cashback_reguler AS cr ON cp.drop_point_outgoing = cr.nama_cp
            LEFT JOIN
                ".$schema.".cp_dp_cashback_marketplace_non_cod AS cmnc ON cp.drop_point_outgoing = cmnc.nama_cp
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
