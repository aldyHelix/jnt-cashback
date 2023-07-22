<?php
namespace App\Services;

use App\Exports\GradingExport;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GradingService {
    public function generateGrading($id, $grade) {
        // Store on default disk
        $get_periode = Periode::findOrFail($id);
        $schema = $get_periode->code.'.data_mart';

        $get_cp_grading = $this->getDataGrading($get_periode->code, $grade);
        $generate_view = $this->createViewGrading($get_periode->code, $grade);

        $this->exportFile($get_cp_grading, $get_periode->month, $get_periode->year, $grade);

        return true;
    }

    public function exportFile($get_cp_grading ,$month, $year, $grade) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-'.$grade.'.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new GradingExport($get_cp_grading, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function createViewGrading($code, $grade) {
        return $created = DB::connection('pgsql')->unprepared("
            CREATE OR REPLACE VIEW ".$code.".cp_dp_raw_grading_".$grade." AS
            SELECT
                cp.kode_cp,
                cp.nama_cp,
                COALESCE(acs.sum, 0) AS biaya_kirim_all,
                COALESCE(rcs.sum, 0) AS biaya_kirim_reguler,
                COALESCE(dcs.sum, 0) AS biaya_kirim_dfod,
                COALESCE(scs.sum, 0) AS biaya_kirim_super,
                COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0) AS total_biaya_kirim,
                CAST(ROUND(COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0) / 1.011)::BIGINT AS BIGINT) AS total_biaya_kirim_dikurangi_ppn,
                CAST(ROUND((COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0)) * 0.25)::BIGINT AS BIGINT) AS amount_discount_25,
                COALESCE(sbk.akulakuob, 0) AS akulaku,
                COALESCE(sbk.ordivo, 0) AS ordivo,
                COALESCE(sbk.evermosapi, 0) AS evermos,
                COALESCE(sbk.mengantar, 0) AS mengantar,
                COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0) AS total_biaya_kirim_a,
                CAST(ROUND((COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) / 1.011)::BIGINT AS BIGINT) AS total_biaya_kirim_a_dikurangi_ppn,
                CAST(ROUND((COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) * 0.10)::BIGINT AS BIGINT) AS amount_discount_10,
                CAST(ROUND((COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0)) * 0.25 + (COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) * 0.10)::BIGINT AS BIGINT) AS total_cashback_reguler
            FROM
                PUBLIC.master_collection_point AS cp
            LEFT JOIN
                ".$code.".cp_dp_all_count_sum AS acs ON cp.drop_point_outgoing = acs.drop_point_outgoing
            LEFT JOIN
                ".$code.".cp_dp_reguler_count_sum AS rcs ON cp.drop_point_outgoing = rcs.drop_point_outgoing
            LEFT JOIN
                ".$code.".cp_dp_dfod_count_sum AS dcs ON cp.drop_point_outgoing = dcs.drop_point_outgoing
            LEFT JOIN
                ".$code.".cp_dp_super_count_sum AS scs ON cp.drop_point_outgoing = scs.drop_point_outgoing
            LEFT JOIN
                ".$code.".cp_dp_mp_sum_biaya_kirim AS sbk ON cp.drop_point_outgoing = sbk.drop_point_outgoing
            WHERE
                cp.grading_pickup = '".grading_map($grade)."'");
    }

    public function getDataGrading($code, $grade) {

        $get_cp_grading = DB::table('master_collection_point AS cp')
            ->leftJoin($code.'.cp_dp_all_count_sum AS acs', 'cp.drop_point_outgoing', '=', 'acs.drop_point_outgoing')
            ->leftJoin($code.'.cp_dp_reguler_count_sum AS rcs', 'cp.drop_point_outgoing', '=', 'rcs.drop_point_outgoing')
            ->leftJoin($code.'.cp_dp_dfod_count_sum AS dcs', 'cp.drop_point_outgoing', '=', 'dcs.drop_point_outgoing')
            ->leftJoin($code.'.cp_dp_super_count_sum AS scs', 'cp.drop_point_outgoing', '=', 'scs.drop_point_outgoing')
            ->leftJoin($code.'.cp_dp_mp_sum_biaya_kirim AS sbk', 'cp.drop_point_outgoing', '=', 'sbk.drop_point_outgoing')
            ->select(
                'cp.kode_cp',
                'cp.nama_cp',
                DB::raw('COALESCE(acs.sum, 0) as biaya_kirim_all'),
                DB::raw('COALESCE(rcs.sum, 0) as biaya_kirim_reguler'),
                DB::raw('COALESCE(dcs.sum, 0) as biaya_kirim_dfod'),
                DB::raw('COALESCE(scs.sum, 0) as biaya_kirim_super'),
                DB::raw('COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0) as total_biaya_kirim'),
                DB::raw('CAST(ROUND((COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0)) / 1.011, 0) AS BIGINT) as total_biaya_kirim_dikurangi_ppn'),
                DB::raw('CAST(ROUND((COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0)) * 0.25, 0) AS BIGINT) as amount_discount_25'),
                DB::raw('COALESCE(sbk.akulakuob, 0) as akulaku'),
                DB::raw('COALESCE(sbk.ordivo, 0)  as ordivo'),
                DB::raw('COALESCE(sbk.evermosapi, 0)  as evermos'),
                DB::raw('COALESCE(sbk.mengantar, 0)  as mengantar'),
                DB::raw('COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0) as total_biaya_kirim_a'),
                DB::raw('CAST(ROUND((COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) / 1.011, 0) AS BIGINT) as total_biaya_kirim_a_dikurangi_ppn'),
                DB::raw('CAST(ROUND((COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) * 0.10, 0) AS BIGINT) as amount_discount_10'),
                DB::raw('CAST(ROUND((COALESCE(rcs.sum, 0) + COALESCE(dcs.sum, 0) + COALESCE(scs.sum, 0)) * 0.25, 0) AS BIGINT) + CAST(ROUND((COALESCE(sbk.akulakuob, 0) + COALESCE(sbk.ordivo, 0) + COALESCE(sbk.evermosapi, 0) + COALESCE(sbk.mengantar, 0)) * 0.10, 0) AS BIGINT) as total_cashback_reguler')
            )
            ->where('cp.grading_pickup', grading_map($grade))->get();

            $sum = [
                'kode_cp' => 'TOTAL',
                'nama_cp' => '',
                'biaya_kirim_all' => $get_cp_grading->sum('biaya_kirim_all'),
                'biaya_kirim_reguler' => $get_cp_grading->sum('biaya_kirim_reguler'),
                'biaya_kirim_dfod' => $get_cp_grading->sum('biaya_kirim_dfod'),
                'biaya_kirim_super' => $get_cp_grading->sum('biaya_kirim_super'),
                'total_biaya_kirim' => $get_cp_grading->sum('total_biaya_kirim'),
                'total_biaya_kirim_dikurangi_ppn' => $get_cp_grading->sum('total_biaya_kirim_dikurangi_ppn'),
                'amount_discount_25' => $get_cp_grading->sum('amount_discount_25'),
                'akulaku' => $get_cp_grading->sum('akulaku'),
                'ordivo' => $get_cp_grading->sum('ordivo'),
                'evermos' => $get_cp_grading->sum('evermos'),
                'mengantar' => $get_cp_grading->sum('mengantar'),
                'total_biaya_kirim_a' => $get_cp_grading->sum('total_biaya_kirim_a'),
                'total_biaya_kirim_a_dikurangi_ppn' => $get_cp_grading->sum('total_biaya_kirim_a_dikurangi_ppn'),
                'amount_discount_10' => $get_cp_grading->sum('amount_discount_10'),
                'total_cashback_reguler' => $get_cp_grading->sum('total_cashback_reguler')
            ];

            // convert to object
            $sum = (object)$sum;
            // Push the sum row into the new collection
            $get_cp_grading->push($sum);

            return $get_cp_grading;
    }
}
