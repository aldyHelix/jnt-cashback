<?php

namespace Modules\Globalsetting\Http\Controllers;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Modules\Collectionpoint\Models\Collectionpoint;
use Modules\Globalsetting\Datatables\DeliveryDatatables;

class DeliverySettingController extends Controller
{
    public function index()
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.index']);

        if( request()->has('datatables') ) {
            return DeliveryDatatables::renderData();
        }

        return view('globalsetting::delivery.index');
    }

    public function create()
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.create']);
        $data['data'] = new DeliveryZone();
        $data['cp'] = Collectionpoint::get();

        return view('globalsetting::delivery.create', $data);
    }

    public function edit($id)
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.update']);
        $data['data'] = DeliveryZone::findOrFail($id);
        $data['cp'] = Collectionpoint::get();

        return view('globalsetting::delivery.edit', $data);
    }

    public function store(DeliverySettingRequest $request)
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.create']);

        return $request->createDeliverySetting();
    }

    public function update(DeliverySettingRequest $request, $id)
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.update']);

        return $request->updateDeliverySetting(
            DeliveryZone::findOrFail($id)
        );
    }

    public function destroy($id)
    {
        ladmin()->allows(['ladmin.globalsetting.delivery.delete']);

        $deliverysetting = DeliveryZone::findOrFail($id);

        if($deliverysetting->delete()) {
            session()->flash('success', 'Delivery Setting has been deleted!');
        } else {
            session()->flash('danger', 'Something went wrong while deleting the delivery setting.');
        }

        return redirect()->back();
    }
}
