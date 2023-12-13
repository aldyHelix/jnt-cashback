<?php

namespace Modules\Globalsetting\Http\Controllers;

use Illuminate\Http\Request;

class RekapSettingController extends Controller
{
    public function index()
    {
        ladmin()->allows(['ladmin.globalsetting.rekap.index']);

        // if( request()->has('datatables') ) {
        //     return SettingDatatables::renderData();
        // }

        return view('globalsetting::rekap.index');
    }
}
