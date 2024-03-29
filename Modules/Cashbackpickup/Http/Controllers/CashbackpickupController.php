<?php

namespace Modules\Cashbackpickup\Http\Controllers;

use App\Exports\GradingExport;
use App\Exports\GradingExports;
use App\Facades\CreateSchema;
use App\Facades\GenerateDPF;
use App\Facades\GeneratePivot;
use App\Facades\GeneratePivotRekap;
use App\Facades\GenerateRekapLuarZona;
use App\Facades\GenerateSummary;
use App\Facades\GradingProcess;
use App\Models\Denda;
use App\Facades\PivotTable;
use App\Models\Periode;
use App\Models\PeriodeDataJson;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Cashbackpickup\Datatables\Grading1Datatables;
use Modules\Cashbackpickup\Datatables\Grading2Datatables;
use Modules\Cashbackpickup\Datatables\Grading3Datatables;
use Modules\Cashbackpickup\Datatables\DPFDatatables;
use Modules\Cashbackpickup\Http\Requests\DendaRequest;
use Modules\Collectionpoint\Models\Collectionpoint;
use Modules\Period\Models\Period;

class CashbackpickupController extends Controller
{
    //
    public function index($grade)
    {
        $data['denda'] = new Denda();
        $data['grade'] = $grade;
        switch ($grade) {
            case 1:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.1.index']);

                if(request()->has('datatables')) {
                    return Grading1Datatables::renderData();
                }

                break;
            case 2:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.2.index']);

                if(request()->has('datatables')) {
                    return Grading2Datatables::renderData();
                }

                break;

            case 3:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.3.index']);

                if(request()->has('datatables')) {
                    return Grading3Datatables::renderData();
                }

                break;
            default:
                ladmin()->allows(['ladmin.cashbackpickup.index','ladmin.cashbackpickup.grading.1.index']);

                if(request()->has('datatables')) {
                    return Grading1Datatables::renderData();
                }

                break;
        }

        return view('cashbackpickup::index', $data);
    }

    public function viewDetail($code, $grade)
    {
        $data['periode'] = Periode::where('code', $code)->first();
        $summary = PeriodeDataJson::where('periode_id', $data['periode']->id)->first();
        $data['cashback_setting'] = json_decode($summary->cashback_setting);
        $data['denda'] = Denda::where(['periode_id' => $data['periode']->id, 'grading_type' => $grade])->get();
        $data['filename'] = strtoupper($data['periode']->month) . '-' . $data['periode']->year . '-GRADING-' . $grade . '.xlsx';
        $data['grading'] = $grade;
        switch ($grade) {
            case 1:
                $data['locked'] = $data['periode']->locked_grade_1;
                $data['cashback_reguler'] = json_decode($summary->cashback_reguler_a);
                $data['cashback_grading'] = json_decode($summary->cashback_grading_1);
                $data['markeplace'] = json_decode($summary->cashback_marketplace_cod);
                $data['cashback_grading_denda'] = json_decode($summary->cashback_grading_1_denda);
            break;
            case 2:
                $data['locked'] = $data['periode']->locked_grade_2;
                $data['cashback_reguler'] = json_decode($summary->cashback_reguler_b);
                $data['cashback_grading'] = json_decode($summary->cashback_grading_2);
                $data['markeplace'] = json_decode($summary->cashback_marketplace_awb_cod);
                $data['cashback_grading_denda'] = json_decode($summary->cashback_grading_2_denda);
            break;
            case 3:
                $data['locked'] = $data['periode']->locked_grade_3;
                $data['cashback_reguler'] = json_decode($summary->cashback_reguler_a);
                $data['cashback_grading'] = json_decode($summary->cashback_grading_3);
                $data['markeplace'] = json_decode($summary->cashback_marketplace_awb_g3_cod);
                $data['cashback_grading_denda'] = json_decode($summary->cashback_grading_3_denda);
            break;
            default:
                $data['locked'] = $data['periode']->is_locked;
            break;
        }
        return view('cashbackpickup::summary-grading', $data);
    }

    public function saveDenda(Request $request)
    {
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

    public function viewDenda($id, $grade)
    {
        //change into page : because its too heavy to load
        $data['periode'] = Periode::where('id', $id)->first();
        $exist = Denda::where(['periode_id' => $id, 'grading_type' => $grade])->first();
        $data['id'] = $id;
        $data['cp'] = Collectionpoint::where('grading_pickup', grading_map($grade))->orderBy('drop_point_outgoing', 'ASC')->get();
        $data['grading'] = $grade;
        $data['denda'] = $exist ?? new Denda(); //find where peride id & grading if null new Denda if not null fill
        return view('cashbackpickup::_form-denda', $data);
    }

    public function process($code, $grade, $id)
    {

        $periode = Periode::where('code', $code)->first();

        GeneratePivot::createOrReplacePivot($code, $id);

        GeneratePivot::runMPGenerator($code);

        GeneratePivotRekap::runRekapGenerator($code);

        GenerateRekapLuarZona::runZonasiGenerator($code);

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
                $script = CreateSchema::createViewCPDPCashbackRekapDendaGrading2(
                    $code
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

    public function lock($code, $grade, $id)
    {
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

    public function downloadExcel($filename)
    {
        // Replace 'path/to/your/excel_file.xlsx' with the actual path to your Excel file.
        $filePath = storage_path('app/public/' . $filename);
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

    /** =========== DPF Here ==========*/

    public function DPFIndex()
    {
        $data['denda'] = new Denda();
        $data['grade'] = 'DPF';
        ladmin()->allows(['ladmin.cashbackpickup.dpf.index']);

        if(request()->has('datatables')) {
            return DpfDatatables::renderData();
        }

        return view('cashbackpickup::dpf.index', $data);
    }

    public function viewDetailDpf($code, $grade = 'dpf')
    {
        $data['periode'] = Periode::where('code', $code)->first();
        $data['denda'] = Denda::where(['periode_id' => $data['periode']->id, 'grading_type' => $grade])->get();
        $data['filename'] = strtoupper($data['periode']->month) . '-' . $data['periode']->year . '-GRADING-' . $grade . '.xlsx';
        $data['grading'] = $grade;
        return view('cashbackpickup::dpf.summary-grading', $data);
    }

    public function saveDendaDpf(Request $request)
    {
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

    public function viewDendaDpf($id)
    {
        //change into page : because its too heavy to load
        $grade = 'DPF';
        $exist = Denda::where(['periode_id' => $id, 'grading_type' => $grade])->first();
        $data['id'] = $id;
        $data['cp'] = Collectionpoint::where('grading_pickup', grading_map($grade))->orderBy('drop_point_outgoing', 'ASC')->get();
        $data['grading'] = $grade;
        $data['denda'] = $exist ?? new Denda(); //find where peride id & grading if null new Denda if not null fill
        return view('cashbackpickup::dpf._form-denda', $data);
    }

    public function processDpf($code, $id)
    {
        $grade = 'DPF';

        $periode = Periode::where('code', $code)->first();

        GenerateDPF::runRekapGenerator($code, $id);

        GradingProcess::generateGrading($id, 'dpf');


        toastr()->success('Data DPF Period has been processed successfully!', 'Congrats');
        return redirect()->back();
    }

    public function lockDpf($code, $grade, $id)
    {
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

}
