<?php

namespace Modules\RateSetting\Http\Controllers;

use Illuminate\Http\Request;
use Modules\RateSetting\Datatables\RateSettingGradeADatatables;
use Modules\RateSetting\Datatables\RateSettingGradeBDatatables;
use Modules\RateSetting\Datatables\RateSettingGradeCDatatables;
use Modules\RateSetting\Http\Requests\RateSettingRequest;
use Modules\RateSetting\Models\RateSetting;

class RateSettingController extends Controller
{
    //
    public function indexGradeA() {
        ladmin()->allows(['rate.grade.a.index']);

        if( request()->has('datatables') ) {
            return RateSettingGradeADatatables::renderData();
        }

        $data['grade_type'] = 'A';
        return view('ratesetting::grade-a-index', $data);
    }

    public function indexGradeB() {
        ladmin()->allows(['rate.grade.b.index']);

        if( request()->has('datatables') ) {
            return RateSettingGradeBDatatables::renderData();
        }

        $data['grade_type'] = 'B';
        return view('ratesetting::grade-b-index', $data);
    }

    public function indexGradeC() {
        ladmin()->allows(['rate.grade.c.index']);

        if( request()->has('datatables') ) {
            return RateSettingGradeCDatatables::renderData();
        }

        $data['grade_type'] = 'C';
        return view('ratesetting::grade-c-index', $data);
    }

    public function create($grade) {
        ladmin()->allows(['ladmin.grade-'.strtolower($grade).'.create']);
        $data['data'] = new RateSetting();
        $data['grade'] = $grade;

        return view('ratesetting::create', $data);
    }

    public function edit($grade ,$id) {
        ladmin()->allows(['ladmin.grade-'.strtolower($grade).'.update']);
        $data['data'] = RateSetting::findOrFail($id);

        return view('ratesetting::edit', $data);
    }

    public function store(RateSettingRequest $request) {
        ladmin()->allows(['ladmin.grade-'.strtolower($request->grade).'.create']);

        return $request->createRateSetting();
    }

    public function update(RateSettingRequest $request, $id) {
        ladmin()->allows(['ladmin.grade-'.strtolower($request->grade).'.update']);

        return $request->updateRateSetting(
            RateSetting::findOrFail($id)
        );
    }

    public function destroy($grade, $id) {
        ladmin()->allows(['ladmin.grade-'.strtolower($grade).'.delete']);

        $cp = RateSetting::findOrFail($id);

        if ($cp->delete()) {
            session()->flash('success', 'Rate Setting grade '.$grade.' has been deleted!');
        } else {
            session()->flash('danger', 'The Rate Setting cannot be deleted, because it is still used by some users!');
        }

        return redirect()->back();
    }
}
