<?php

namespace Modules\Ratesetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Ratesetting\Models\Ratesetting;

class RatesettingRequest extends FormRequest
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
            'sumber_waybill' => ['required']
        ];
    }

    public function createRatesetting()
    {
        $rateSetting = Ratesetting::create([
            'grading_type' => $this->grade,
            'sumber_waybill' => $this->sumber_waybill,
            'diskon_persen' => $this->diskon_persen,
            'fee' => $this->fee,
        ]);

        toastr()->success('Data Rate Setting has been created successfully!', 'Congrats');


        return redirect()->route('ladmin.ratesetting.grade-' . strtolower($this->grade) . '.index');
    }

    public function updateRatesetting(Ratesetting $rateSetting)
    {
        $rateSetting->update([
            'sumber_waybill' => $this->sumber_waybill,
            'diskon_persen' => $this->diskon_persen,
            'fee' => $this->fee,
        ]);

        toastr()->success('Data Rate Setting has been updated successfully!', 'Congrats');

        return redirect()->back();
    }
}
