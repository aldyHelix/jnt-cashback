<?php

namespace Modules\Period\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Period\Datatables\PeriodDatatables;

class PeriodeController extends Controller
{
    public function index(){
        ladmin()->allows(['ladmin.periode.index']);

        if( request()->has('datatables') ) {
            return PeriodDatatables::renderData();
        }

        return view('period::index');
    }
}
