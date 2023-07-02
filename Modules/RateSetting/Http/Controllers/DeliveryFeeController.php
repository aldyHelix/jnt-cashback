<?php

namespace Modules\RateSetting\Http\Controllers;

use Illuminate\Http\Request;
use Modules\RateSetting\Datatables\DeliveryFeeDatatables;
use Modules\RateSetting\Http\Requests\DeliveryFeeRequest;
use Modules\RateSetting\Models\DeliveryFee;
use Modules\RateSetting\Models\RateSetting;

class DeliveryFeeController extends Controller
{
     //
     public function index() {
        ladmin()->allows(['rate.delivery.index']);

        if( request()->has('datatables') ) {
            return DeliveryFeeDatatables::renderData();
        }

        return view('ratesetting::delivery-index');
    }

    public function create() {
        ladmin()->allows(['rate.delivery.create']);
        $data['data'] = new DeliveryFee();

        return view('ratesetting::create-delivery', $data);
    }

    public function edit($id) {
        ladmin()->allows(['rate.delivery.update']);
        $data['data'] = DeliveryFee::findOrFail($id);

        return view('ratesetting::edit-delivery', $data);
    }

    public function store(DeliveryFeeRequest $request) {
        ladmin()->allows(['rate.delivery.create']);

        return $request->createDeliveryFee();
    }

    public function update(DeliveryFeeRequest $request, $id) {
        ladmin()->allows(['rate.delivery.update']);

        return $request->updateDeliveryFee(
            DeliveryFee::findOrFail($id)
        );
    }

    public function delete($id) {
        ladmin()->allows(['rate.delivery.destroy']);

        $deliveryFee = DeliveryFee::findOrFail($id);

        if($deliveryFee->delete()) {
            session()->flash('success', 'Rate Setting Delivery Fee has been deleted!');
        } else {
            session()->flash('danger', 'Something went wrong while deleting the rate setting delivery fee.');
        }

        return redirect()->back();
    }
}
