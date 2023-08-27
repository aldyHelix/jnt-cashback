<?php

namespace Modules\RateSetting\Http\Controllers;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Modules\CollectionPoint\Models\CollectionPoint;
use Modules\RateSetting\Models\DeliveryFee as ModelsDeliveryFee;
use Modules\RateSetting\Datatables\DeliveryFeeDatatables;
use Modules\RateSetting\Http\Requests\DeliveryFeeRequest;
use Modules\RateSetting\Models\DeliveryFee;
use Modules\RateSetting\Models\RateSetting;

class DeliveryFeeController extends Controller
{
     //
     public function index() {
        ladmin()->allows(['ladmin.ratesetting.deliferyfee.index']);

        $data['collection_point'] = CollectionPoint::selectRaw('master_collection_point.*, delivery_zone.drop_point_ttd, delivery_zone.kpi_target_count, delivery_zone.kpi_reduce_not_achievement, delivery_zone.is_show')->leftJoin('delivery_zone', function ($join) {
            $join->on('master_collection_point.id', '=', 'delivery_zone.collection_point_id');
        })->orderBy('master_collection_point.drop_point_outgoing', 'ASC')->get();

        $data['zona'] = ModelsDeliveryFee::get()->pluck('zona', 'zona')->toArray();

        if( request()->has('datatables') ) {
            return DeliveryFeeDatatables::renderData();
        }

        return view('ratesetting::delivery-index', $data);
    }

    public function create() {
        ladmin()->allows(['ladmin.ratesetting.deliferyfee.create']);
        $data['data'] = new DeliveryFee();

        return view('ratesetting::create-delivery', $data);
    }

    public function edit($id) {
        ladmin()->allows(['ladmin.ratesetting.deliferyfee.update']);
        $data['data'] = DeliveryFee::findOrFail($id);

        return view('ratesetting::edit-delivery', $data);
    }

    public function store(DeliveryFeeRequest $request) {
        ladmin()->allows(['ladmin.ratesetting.deliferyfee.create']);

        return $request->createDeliveryFee();
    }

    public function update(DeliveryFeeRequest $request, $id) {
        ladmin()->allows(['rate.delivery.update']);

        return $request->updateDeliveryFee(
            DeliveryFee::findOrFail($id)
        );
    }

    public function delete($id) {
        ladmin()->allows(['ladmin.ratesetting.deliferyfee.destroy']);

        $deliveryFee = DeliveryFee::findOrFail($id);

        if($deliveryFee->delete()) {
            session()->flash('success', 'Rate Setting Delivery Fee has been deleted!');
        } else {
            session()->flash('danger', 'Something went wrong while deleting the rate setting delivery fee.');
        }

        return redirect()->back();
    }

    public function settingZona(Request $request){
        foreach($request->cp as $item){
            if($item["is_show"]) {

                $exist = DeliveryZone::where(['collection_point_id' => $item['id'], 'drop_point_outgoing' => $item['drop_point_outgoing']])->first();

                if($exist) {
                    //update
                    $exist->update([
                        'drop_point_ttd' => $item['drop_point_ttd'],
                        'kpi_target_count' => $item['kpi_target_count'],
                        'kpi_reduce_not_achievement' => $item['kpi_reduce_not_achievement'],
                        'is_show' => 1,
                    ]);
                } else {
                    //create
                    DeliveryZone::create([
                        'collection_point_id' => $item['id'],
                        'drop_point_outgoing' => $item['drop_point_outgoing'],
                        'drop_point_ttd' => $item['drop_point_ttd'],
                        'kpi_target_count' => $item['kpi_target_count'],
                        'kpi_reduce_not_achievement' => $item['kpi_reduce_not_achievement'],
                        'is_show' => 1,
                    ]);
                }
            }
        }
        toastr()->success('Delivery fee setting has been succesfully saved!');
        return redirect()->back();
    }
}
