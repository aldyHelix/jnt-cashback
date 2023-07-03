<?php

namespace Modules\CashbackPickup\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;
use Modules\CashbackPickup\Datatables\Grading1Datatables;
use Modules\CashbackPickup\Datatables\Grading2Datatables;
use Modules\CashbackPickup\Datatables\Grading3Datatables;

class CashbackPickupController extends Controller
{
    //
    public function index($grade) {
        $data['denda'] = new Denda();
        $data['grade'] = $grade;
        switch ($grade) {
            case 1:
                ladmin()->allows(['cashback.pickup.index','cashback.pickup.grading1.index']);

                if( request()->has('datatables') ) {
                    return Grading1Datatables::renderData();
                }

                break;
            case 2:
                ladmin()->allows(['cashback.pickup.index','cashback.pickup.grading2.index']);

                if( request()->has('datatables') ) {
                    return Grading2Datatables::renderData();
                }

                break;

            case 3:
                ladmin()->allows(['cashback.pickup.index','cashback.pickup.grading3.index']);

                if( request()->has('datatables') ) {
                    return Grading3Datatables::renderData();
                }

                break;
            default:
                ladmin()->allows(['cashback.pickup.index','cashback.pickup.grading1.index']);

                if( request()->has('datatables') ) {
                    return Grading1Datatables::renderData();
                }

                break;
        }

        return view('cashbackpickup::index',$data);
    }

    public function viewDetail($code ,$grade) {
        return view('cashbackpickup::summary-grading');
    }

    public function process() {
        return redirect()->back();
    }

    public function lock() {
        return redirect()->back();
    }

}
