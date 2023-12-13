<?php

namespace Modules\Ratesetting\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ratesetting\Datatables\RatesettingGradeADatatables;
use Modules\Ratesetting\Datatables\RatesettingGradeBDatatables;
use Modules\Ratesetting\Datatables\RatesettingGradeCDatatables;
use Modules\Ratesetting\Http\Requests\RatesettingRequest;
use Modules\Ratesetting\Models\Ratesetting;

class RatesettingController extends Controller
{
    //
    public function indexGradeA()
    {
        ladmin()->allows(['ladmin.ratesetting.grade.a.index']);

        if(request()->has('datatables')) {
            return RatesettingGradeADatatables::renderData();
        }

        $data['grade_type'] = 'A';
        return view('ratesetting::grade-a-index', $data);
    }

    public function indexGradeB()
    {
        ladmin()->allows(['ladmin.ratesetting.grade.b.index']);

        if(request()->has('datatables')) {
            return RatesettingGradeBDatatables::renderData();
        }

        $data['grade_type'] = 'B';
        return view('ratesetting::grade-b-index', $data);
    }

    public function indexGradeC()
    {
        ladmin()->allows(['ladmin.ratesetting.grade.b.index']);

        if(request()->has('datatables')) {
            return RatesettingGradeCDatatables::renderData();
        }

        $data['grade_type'] = 'C';
        return view('ratesetting::grade-c-index', $data);
    }

    public function create($grade)
    {
        ladmin()->allows(['ladmin.ratesetting.grade.' . strtolower($grade) . '.create']);
        $data['data'] = new Ratesetting();
        $data['grade'] = $grade;

        return view('ratesetting::create', $data);
    }

    public function edit($grade, $id)
    {
        ladmin()->allows(['ladmin.ratesetting.grade.' . strtolower($grade) . '.update']);
        $data['data'] = Ratesetting::findOrFail($id);

        return view('ratesetting::edit', $data);
    }

    public function store(RatesettingRequest $request)
    {
        ladmin()->allows(['ladmin.ratesetting.grade.' . strtolower($request->grade) . '.create']);

        return $request->createRatesetting();
    }

    public function update(RatesettingRequest $request, $id)
    {
        ladmin()->allows(['ladmin.ratesetting.grade.' . strtolower($request->grade) . '.update']);

        return $request->updateRatesetting(
            Ratesetting::findOrFail($id)
        );
    }

    public function destroy($grade, $id)
    {
        ladmin()->allows(['ladmin.ratesetting.grade.' . strtolower($grade) . '.delete']);

        $cp = Ratesetting::findOrFail($id);

        if ($cp->delete()) {
            session()->flash('success', 'Rate Setting grade ' . $grade . ' has been deleted!');
        } else {
            session()->flash('danger', 'The Rate Setting cannot be deleted, because it is still used by some users!');
        }

        return redirect()->back();
    }
}
