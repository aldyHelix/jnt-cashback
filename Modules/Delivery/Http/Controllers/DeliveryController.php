<?php

namespace Modules\Delivery\Http\Controllers;

use App\Facades\CreateSchema;
use App\Facades\GradingProcess;
use App\Facades\PivotTable;
use App\Models\DendaDelivery;
use App\Models\PeriodeDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CollectionPoint\Models\CollectionPoint;
use Modules\Delivery\Datatables\DeliveryDatatables;

class DeliveryController extends Controller
{
    public function index(){

        ladmin()->allows(['ladmin.delivery.index']);

        $data['denda'] = new DendaDelivery();

        if( request()->has('datatables') ) {
            return DeliveryDatatables::renderData();
        }


        return view('delivery::index', $data);
    }

    public function viewDetail($code) {
        $data['periode'] = PeriodeDelivery::where('code', $code)->first();
        $data['summary_sprinter'] = PivotTable::getDeliverySprinter($code);
        $data['row_total'] = DB::table($data['periode']->code.'.data_mart')->count();
        $data['filename'] = strtoupper($data['periode']->month).'-'.$data['periode']->year.'-DELIVERY.xlsx';
        return view('delivery::summary-delivery', $data);
    }

    public function process($code, $id) {
        CreateSchema::DeliveryPivot($code);
        // process count waybill per ttd
        //get ttd list first
        $ttd = DB::table($code.".ttd_list")->get()->pluck('drop_point_ttd')->toArray();
        foreach($ttd as $name){
            CreateSchema::createPivotPerTTD($code, $name);
        }

        GradingProcess::generateGradingDelivery($id, $code);
        toastr()->success('Data Delivery has been processed successfully!', 'Congrats');

        return redirect()->back();
    }

    public function lock() {
        return redirect()->back();
    }

    public function saveDenda(Request $request) {
        $denda = $request->data;
        $periode = PeriodeDelivery::where('id', $request->periode_id)->first();

        foreach($denda as $item) {
            $exist = DendaDelivery::where(['id' => $item['id']])->first();
            $get_total_awb = DB::table($periode->code.".delivery_fee_summary")->where('drop_point', $item['drop_point_outgoing'])->first();

            if ($exist) {
                $exist->update([
                    'denda_lost_scan_kirim' => intval($item['denda_lost_scan_kirim']),
                    'denda_late_pickup_reg' => intval($item['denda_late_pickup_reg']),
                    'denda_auto_claim' => intval($item['denda_auto_claim']),
                    'tarif' => intval($item['tarif']),
                    'admin_bank' => intval( $item['admin_bank']),
                    'dpp' => intval($get_total_awb->total_delivery_setelah_ppn),
                ]);
            } else {
                $collection_point = DendaDelivery::create([
                    'delivery_periode_id' => $request->periode_id,
                    'collection_point_id' => $item['collection_point_id'],
                    'drop_point_outgoing' => $item['drop_point_outgoing'],
                    'denda_late_pickup_reg' => intval($item['denda_late_pickup_reg']),
                    'denda_lost_scan_kirim' => intval($item['denda_lost_scan_kirim']),
                    'denda_auto_claim' => intval($item['denda_auto_claim']),
                    'tarif' => intval($item['tarif']),
                    'admin_bank' => intval($item['admin_bank']),
                    'dpp' => intval($get_total_awb->total_delivery_setelah_ppn),
                ]);
            }
        }

        toastr()->success('Data Denda Delivery has been saved successfully!', 'Congrats');

        return redirect()->route('ladmin.delivery.index');

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
