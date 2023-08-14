<?php

namespace Modules\Delivery\Http\Controllers;

use App\Facades\GradingProcess;
use App\Facades\PivotTable;
use App\Models\DendaDelivery;
use App\Models\PeriodeDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('delivery::summary-delivery', $data);
    }

    public function process($code, $id) {
        GradingProcess::generateGradingDelivery($id, $code);
        toastr()->success('Data Delivery has been processed successfully!', 'Congrats');

        return redirect()->back();
    }

    public function lock() {
        return redirect()->back();
    }

    public function saveDenda(Request $request) {
        $denda = $request->data;

        foreach($denda as $item) {
            foreach($item as $key => $data) {
                $item[$key] = intval($data);
            }

            $exist = DendaDelivery::where(['id' => $item['denda_id']])->first();
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
                $collection_point = DendaDelivery::create([
                    'periode_id' => $request->periode_id,
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

        toastr()->success('Data Denda Delivery has been saved successfully!', 'Congrats');

        return redirect()->route('ladmin.delivery.index');

    }
}
