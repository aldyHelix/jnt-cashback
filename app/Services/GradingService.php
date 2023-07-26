<?php
namespace App\Services;

use App\Exports\CashbackGrading1Export;
use App\Exports\CashbackGrading2Export;
use App\Exports\CashbackGrading3Export;
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
        $schema = $get_periode->code;

        switch ($grade) {
            case 1:
                $data['cpdp_reguler'] = DB::table($schema.'.cp_dp_cashback_reguler_grading_1')->get()->toArray();
                $data['cpdp_cod'] = DB::table($schema.'.cp_dp_cashback_cod_grading_1')->get()->toArray();
                $data['cpdp_non_cod'] = DB::table($schema.'.cp_dp_cashback_non_cod_grading_1')->get()->toArray();
                $data['cpdp_rekap'] = DB::table($schema.'.cp_dp_rekap_cashback_grading_1')->get()->toArray();
                $data['cpdp_rekap_denda'] = DB::table($schema.'.cp_dp_rekap_denda_cashback_grading_1')->get()->toArray();

                $this->exportFileGrade1($data, $get_periode->month, $get_periode->year);
                break;
            case 2:
                $data['cpdp_reguler'] = DB::table($schema.'.cp_dp_cashback_reguler_grading_2')->get()->toArray();
                $data['cpdp_cod'] = DB::table($schema.'.cp_dp_cashback_awb_grading_2')->get()->toArray();
                $data['cpdp_rekap'] = DB::table($schema.'.cp_dp_rekap_cashback_grading_2')->get()->toArray();
                $data['cpdp_rekap_denda'] = DB::table($schema.'.cp_dp_rekap_denda_cashback_grading_2')->get()->toArray();

                $this->exportFileGrade2($data, $get_periode->month, $get_periode->year);
                break;
            case 3:
                $data['cpdp_reguler'] = DB::table($schema.'.cp_dp_cashback_reguler_grading_3')->get()->toArray();
                $data['cpdp_cod'] = DB::table($schema.'.cp_dp_cashback_awb_grading_3')->get()->toArray();
                $data['cpdp_rekap'] = DB::table($schema.'.cp_dp_rekap_cashback_grading_3')->get()->toArray();
                $data['cpdp_rekap_denda'] = DB::table($schema.'.cp_dp_rekap_denda_cashback_grading_3')->get()->toArray();

                $this->exportFileGrade3($data, $get_periode->month, $get_periode->year);
                break;
            case 'deelivery':

                break;
            default:
                # code...
                break;
        }


        return true;
    }

    public function exportFileGrade1($data ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-1.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading1Export($data, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function exportFileGrade2($periode_code ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-2.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading2Export($periode_code, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }

    public function exportFileGrade3($periode_code ,$month, $year) {
        $file_name = strtoupper($month).'-'.$year.'-GRADING-3.xlsx';

        $storage_exist = storage_path($file_name);

        if (file_exists($storage_exist)) {
            // The file exists in the storage directory.
            // You can perform further actions here.
            unlink($storage_exist); # delete old file before create new one with same name
            Storage::delete($file_name);
        }

        $gradingExport = new CashbackGrading3Export($periode_code, $file_name);

        Excel::store($gradingExport, $file_name, 'public');//this is success

        // Append the sum row after storing the Excel file
    }
}
