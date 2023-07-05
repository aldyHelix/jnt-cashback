<?php

namespace Modules\Delivery\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;
use Modules\Delivery\Datatables\DeliveryDatatables;

class DeliveryController extends Controller
{
    public function index(){

        ladmin()->allows(['ladmin.delivery.index']);

        $data['denda'] = new Denda();

        if( request()->has('datatables') ) {
            return DeliveryDatatables::renderData();
        }


        return view('delivery::index', $data);
    }

    public function viewDetail($code ) {
        return view('delivery::summary-delivery');
    }

    public function process() {
        return redirect()->back();
    }

    public function lock() {
        return redirect()->back();
    }
}
