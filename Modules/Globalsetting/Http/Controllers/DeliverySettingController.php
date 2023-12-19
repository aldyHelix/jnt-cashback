<?php

namespace Modules\Globalsetting\Http\Controllers;

use Illuminate\Http\Request;
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
}
