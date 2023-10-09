<?php

namespace Modules\CashbackPickup\Http\Controllers;

use App\Exports\GradingExport;
use App\Exports\GradingExports;
use App\Facades\CreateSchema;
use App\Facades\GeneratePivot;
use App\Facades\GeneratePivotRekap;
use App\Facades\GenerateSummary;
use App\Facades\GradingProcess;
use App\Models\Denda;
use App\Facades\PivotTable;
use App\Models\Periode;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\CashbackPickup\Datatables\Grading1Datatables;
use Modules\CashbackPickup\Datatables\Grading2Datatables;
use Modules\CashbackPickup\Datatables\Grading3Datatables;
use Modules\CashbackPickup\Http\Requests\DendaRequest;
use Modules\CollectionPoint\Models\CollectionPoint;
use Modules\Period\Models\Period;

class CashbackPickupController extends Controller
{
    //
    public function index($grade) {
        $data['denda'] = new Denda();
        $data['grade'] = $grade;
        switch ($grade) {
            case 1:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.1.index']);

                if( request()->has('datatables') ) {
                    return Grading1Datatables::renderData();
                }

                break;
            case 2:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.2.index']);

                if( request()->has('datatables') ) {
                    return Grading2Datatables::renderData();
                }

                break;

            case 3:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.3.index']);

                if( request()->has('datatables') ) {
                    return Grading3Datatables::renderData();
                }

                break;
            default:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.1.index']);

                if( request()->has('datatables') ) {
                    return Grading1Datatables::renderData();
                }

                break;
        }

        return view('cashbackpickup::index',$data);
    }

    public function viewDetail($code ,$grade) {
        $data['periode'] = Periode::where('code', $code)->first();
        $data['denda'] = Denda::where(['periode_id'=> $data['periode']->id, 'grading_type'=> $grade])->get();
        $data['filename'] = strtoupper($data['periode']->month).'-'.$data['periode']->year.'-GRADING-'.$grade.'.xlsx';
        // $data['cp_grading'] = DB::table($data['periode']->code.'.cp_dp_raw_grading_1')->get();
        // $data['cp_dp_all_count_sum'] = PivotTable::getPivotAllCountSumCPDP($code);
        // $data['cp_dp_reguler_count_sum'] = PivotTable::getPivotRegulerCountSumCPDP($code);
        // $data['cp_dp_dfod_count_sum'] = PivotTable::getPivotDfodCountSumCPDP($code);
        // $data['cp_dp_super_count_sum'] = PivotTable::getPivotSuperCountSumCPDP($code);
        // $data['sum_all_biaya_kirim'] = PivotTable::getSumAllBiayaKirim($code);
        // $data['total'] = [
        //     'cp_dp_all_count_sum_total_count' => $data['cp_dp_all_count_sum']->sum('count'),
        //     'cp_dp_all_count_sum_total_sum' => $data['cp_dp_all_count_sum']->sum('sum'),
        //     'cp_dp_reguler_count_sum_total_count' => $data['cp_dp_reguler_count_sum']->sum('count'),
        //     'cp_dp_reguler_count_sum_total_sum' => $data['cp_dp_reguler_count_sum']->sum('sum'),
        //     'cp_dp_super_count_sum_total_count' => $data['cp_dp_super_count_sum']->sum('count'),
        //     'cp_dp_super_count_sum_total_sum' => $data['cp_dp_super_count_sum']->sum('sum'),
        //     'cp_dp_dfod_count_sum_total_count' => $data['cp_dp_dfod_count_sum']->sum('count'),
        //     'cp_dp_dfod_count_sum_total_sum' => $data['cp_dp_dfod_count_sum']->sum('sum'),
        // ];
        return view('cashbackpickup::summary-grading', $data);
    }

    public function saveDenda(Request $request) {
        $denda = $request->data;


        foreach($denda as $item) {
            foreach($item as $key => $data) {
                $item[$key] = intval(str_replace('.', '', $data));
            }

            $exist = Denda::where(['id' => $item['denda_id']])->first();
            if ($exist) {
                $exist->update([
                    'sprinter_pickup' => $item['sprinter_pickup'],
                    'transit_fee' => $item['transit_fee'],
                    'denda_void' => $item['denda_void'],
                    'denda_dfod' => $item['denda_dfod'],
                    'denda_pusat' => $item['denda_pusat'],
                    'denda_selisih_berat' => $item['denda_selisih_berat'],
                    'denda_lost_scan_kirim' => $item['denda_lost_scan_kirim'],
                    'denda_auto_claim' => $item['denda_auto_claim'],
                    'denda_sponsorship' => $item['denda_sponsorship'],
                    'denda_late_pickup_ecommerce' => $item['denda_late_pickup_ecommerce'],
                    'potongan_pop' => $item['potongan_pop'],
                    'denda_lainnya' => $item['denda_lainnya'],
                ]);
            } else {
                $collection_point = Denda::create([
                    'periode_id' => $request->periode_id,
                    'grading_type' => $request->grading_type,
                    'sprinter_pickup' => $item['sprinter_pickup'],
                    'transit_fee' => $item['transit_fee'],
                    'denda_void' => $item['denda_void'],
                    'denda_dfod' => $item['denda_dfod'],
                    'denda_pusat' => $item['denda_pusat'],
                    'denda_selisih_berat' => $item['denda_selisih_berat'],
                    'denda_lost_scan_kirim' => $item['denda_lost_scan_kirim'],
                    'denda_auto_claim' => $item['denda_auto_claim'],
                    'denda_sponsorship' => $item['denda_sponsorship'],
                    'denda_late_pickup_ecommerce' => $item['denda_late_pickup_ecommerce'],
                    'potongan_pop' => $item['potongan_pop'],
                    'denda_lainnya' => $item['denda_lainnya'],
                ]);
            }
        }

        toastr()->success('Data Denda has been saved successfully!', 'Congrats');

        return redirect()->route('ladmin.cashbackpickup.index', $request->grading_type);

    }

    public function viewDenda($id, $grade){
        //change into page : because its too heavy to load
        $exist = Denda::where(['periode_id' => $id, 'grading_type' => $grade])->first();
        $data['id'] = $id;
        $data['cp'] = CollectionPoint::where('grading_pickup', grading_map($grade))->orderBy('drop_point_outgoing', 'ASC')->get();
        $data['grading'] = $grade;
        $data['denda'] = $exist ?? new Denda(); //find where peride id & grading if null new Denda if not null fill
        return view('cashbackpickup::_form-denda', $data);
    }

    public function process($code, $grade, $id) {

        GeneratePivot::createOrReplacePivot($code, $id);

        GeneratePivot::runMPGenerator($code);

        GeneratePivotRekap::runRekapGenerator($code);

        $periode = Periode::where('code', $code)->first();

        GenerateSummary::runSummaryGenerator($code, $periode);

        /**
         * $code = cashback-code periode
         * $grade = cashback grade
         * $id = periode id
         */
        //update view table



        // CreateSchema::updateView($code, $id);

        GradingProcess::generateGrading($id, $grade);

        //update Category Klien Pengiriman

        //update klien pengiriman
        //CreateSchema::updateViewPivot($periode->code, $string);

        //generate  grading

        //reprocess grading with denda
        switch ($grade) {
            case 1:
                $script = CreateSchema::createViewCPDPRekapDendaGrading1($code);
                // CreateSchema::runScript($code, $script);

                //process dengan rate setting juga disini nanti
                //perlu melakukan query dengan data rate setting dan general setting.

                // update CPDPMPSumBiayaKirim ke sumber_waybill yg telah di distinct
                $periode->processed_grade_1 = 1;
                $periode->processed_grade_1_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            case 2:
                $script = CreateSchema::createViewCPDPCashbackRekapDendaGrading2($code
            );

                // CreateSchema::runScript($code, $script);

                $periode->processed_grade_2 = 1;
                $periode->processed_grade_2_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            case 3:
                $script = CreateSchema::createViewCPDPCashbackRekapDendaGrading3($code);

                // CreateSchema::runScript($code, $script);

                $periode->processed_grade_3 = 1;
                $periode->processed_grade_3_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            default:

                break;
        }
        /**
         * process description
         * first of all
         * artisan queue work at only id if queue not running
         * generate grading Excel & PDF
         * calculation denda each grading
         * joinning by drop_point_outgoing
         * query and table view on processing
         * write on excel
         * un disable pdf download
         * un disable lock data
         * if locked process button will disabled
         * alert on done.
         */

        $periode->save();
        toastr()->success('Data Period has been processed successfully!', 'Congrats');
        return redirect()->back();
    }

    public function lock($code, $grade, $id) {
        $periode = Periode::where('code', $code)->first();

        switch ($grade) {
            case 1:
                $periode->locked_grade_1 = 1;
                $periode->processed_grade_1_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            case 2:
                $periode->locked_grade_2 = 1;
                $periode->processed_grade_2_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            case 3:
                $periode->locked_grade_3 = 1;
                $periode->processed_grade_3_by = auth()->user()->id;
                $periode->updated_at = now();
                break;
            default:

                break;
        }
        /**
         * disabled and lock button on locked
         */
        $periode->save();

        toastr()->success('Data Period has been locked successfully!', 'Congrats');
        return redirect()->back();
    }

    public function downloadExcel($filename){
        // Replace 'path/to/your/excel_file.xlsx' with the actual path to your Excel file.
        $filePath = storage_path('app/public/'.$filename);
        // Check if the file exists and is readable
        if (file_exists($filePath) && is_readable($filePath)) {
            // Set the appropriate headers to initiate the file download
            // header('Content-Type: application/octet-stream');
            // header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            // header('Content-Length: ' . filesize($filePath));

            // Read the file and send its contents to the browser
            return response()->download($filePath);
            exit;
        } else {
            // If the file does not exist or is not readable, display an error message
            die('File not found or not accessible.');
        }
    }

}
