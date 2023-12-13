<?php

namespace Modules\Globalsetting\Http\Controllers;

use App\Models\Globalsetting;
use Illuminate\Http\Request;
use Modules\Globalsetting\Datatables\SettingDatatables;
use Modules\Globalsetting\Http\Requests\GeneralSettingRequest;

class GeneralSettingController extends Controller
{
    public function index()
    {
        ladmin()->allows(['ladmin.globalsetting.setting.index']);

        if(request()->has('datatables')) {
            return SettingDatatables::renderData();
        }

        return view('globalsetting::setting.index');
    }

    public function create()
    {
        ladmin()->allows(['ladmin.globalsetting.setting.create']);
        $data['data'] = new Globalsetting();

        return view('globalsetting::setting.create', $data);
    }

    public function edit($id)
    {
        ladmin()->allows(['ladmin.globalsetting.setting.update']);
        $data['data'] = Globalsetting::findOrFail($id);

        return view('globalsetting::setting.edit', $data);
    }

    public function store(GeneralSettingRequest $request)
    {
        ladmin()->allows(['ladmin.globalsetting.setting.create']);

        return $request->createGeneralSetting();
    }

    public function update(GeneralSettingRequest $request, $id)
    {
        ladmin()->allows(['ladmin.globalsetting.setting.update']);

        return $request->updateGeneralSetting(
            Globalsetting::findOrFail($id)
        );
    }

    public function destroy($id)
    {
        ladmin()->allows(['ladmin.globalsetting.setting.delete']);

        $generalsetting = Globalsetting::findOrFail($id);

        if($generalsetting->delete()) {
            session()->flash('success', 'General Setting has been deleted!');
        } else {
            session()->flash('danger', 'Something went wrong while deleting the rate setting general setting.');
        }

        return redirect()->back();
    }
}
