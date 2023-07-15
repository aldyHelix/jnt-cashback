<?php

namespace Modules\CashbackPickup\Http\Controllers;

use App\Models\Denda;
use App\Facades\PivotTable;
use App\Models\Periode;
use Illuminate\Http\Request;
use Modules\CashbackPickup\Datatables\Grading1Datatables;
use Modules\CashbackPickup\Datatables\Grading2Datatables;
use Modules\CashbackPickup\Datatables\Grading3Datatables;
use Modules\CashbackPickup\Http\Requests\DendaRequest;
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
        $data['all_summary'] = PivotTable::getPivotAllCountSumCPDP($code);
        $data['reguler_summary'] = PivotTable::getPivotRegulerCountSumCPDP($code);
        $data['dfod_summary'] = PivotTable::getPivotDfodCountSumCPDP($code);
        $data['super_summary'] = PivotTable::getPivotSuperCountSumCPDP($code);
        $data['total'] = [
            'all_summary_total_count' => $data['all_summary']->sum('count'),
            'all_summary_total_sum' => $data['all_summary']->sum('sum'),
            'reguler_summary_total_count' => $data['reguler_summary']->sum('count'),
            'reguler_summary_total_sum' => $data['reguler_summary']->sum('sum'),
            'super_summary_total_count' => $data['super_summary']->sum('count'),
            'super_summary_total_sum' => $data['super_summary']->sum('sum'),
            'dfod_summary_total_count' => $data['dfod_summary']->sum('count'),
            'dfod_summary_total_sum' => $data['dfod_summary']->sum('sum'),
        ];
        return view('cashbackpickup::summary-grading', $data);
    }

    public function saveDenda(Request $request) {
        $denda = $request->data;

        foreach($denda as $item) {
            foreach($item as $key => $data) {
                $item[$key] = intval($data);
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

        session()->flash('success', 'Denda has been saved');

        return redirect()->route('ladmin.cashbackpickup.index', $request->grading_type);

    }

    public function process() {
        return redirect()->back();
    }

    public function lock() {
        return redirect()->back();
    }

}
