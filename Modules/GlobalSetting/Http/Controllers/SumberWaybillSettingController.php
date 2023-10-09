<?php

namespace Modules\GlobalSetting\Http\Controllers;

use App\Models\GlobalSumberWaybill;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SumberWaybillSettingController extends Controller
{
    public function index(){
        ladmin()->allows(['ladmin.globalsetting.sumber-waybill.index']);

        $periode = Periode::get();

        $sumber_waybill_cashback = [];

        foreach($periode as $item) {
            //get all distict klien pengiriman
            $sumber_waybill = DB::table($item->code.'.data_mart')->selectRaw("DISTINCT(sumber_waybill)")->get()->pluck('sumber_waybill')->toArray();
            $sumber_waybill_cashback = array_merge($sumber_waybill_cashback, $sumber_waybill);
        }

        $sumber_waybill_cashback = array_unique($sumber_waybill_cashback);
        $data['list_sumber_waybill'] = GlobalSumberWaybill::orderBy('sumber_waybill')->get()->pluck('sumber_waybill');
        $data['sumber_waybill_not_sync'] = array_diff($sumber_waybill_cashback, $data['list_sumber_waybill']->toArray());
        return view('globalsetting::sumber-waybill.index', $data);
    }

    public function syncSumberWaybill(Request $request){
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);
        $data = array_map(function($data) {
            $check = GlobalSumberWaybill::where('sumber_waybill', $data)->first();
            if($check == 0) {
                return array(
                    'sumber_waybill' => $data,
                );
            }

        }, $data);

        $insert = GlobalSumberWaybill::insert($data);
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan sumberwaybill');
    }
}
