<?php

namespace Modules\GlobalSetting\Http\Requests;

use App\Models\GlobalSetting;
use Illuminate\Foundation\Http\FormRequest;

class SumberWaybillSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required'],
            'name' => ['required'],
            'value' => ['required'],
            'order' => ['required'],
            'unit_name' => ['required'],
            'unit_char' => ['required'],
        ];
    }

    public function createGeneralSetting() {
        $generalSetting = GlobalSetting::create([
            'code' => $this->code,
            'name' => $this->name,
            'type' => 'sumber_waybill',
            'category' => 'general',
            'value' => $this->value,
            'order' => $this->order,
            'unit_name' => $this->unit_name,
            'unit_char' => $this->unit_char,
        ]);

        toastr()->success('Data General Setting has been created successfully!', 'Congrats');

        return redirect()->route('ladmin.globalsetting.setting.index');
    }

    public function updateGeneralSetting(GlobalSetting $generalSetting) {
        $generalSetting->update([
            'code' => $this->code,
            'name' => $this->name,
            'type' => 'general',
            'category' => 'general',
            'value' => $this->value,
            'order' => $this->order,
            'unit_name' => $this->unit_name,
            'unit_char' => $this->unit_char,
        ]);

        session()->flash('success', 'General Setting has ben updated');
        toastr()->success('Data General Setting has ben updated successfully!', 'Congrats');


        return redirect()->back();
    }
}
