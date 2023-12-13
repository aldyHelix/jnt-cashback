<?php

namespace Modules\Ratesetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Ratesetting\Models\DeliveryFee;

class DeliveryFeeRequest extends FormRequest
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
            'zona' => ['required'],
            'tarif' => ['required']
        ];
    }

    public function createDeliveryFee()
    {
        $deliveryFee = DeliveryFee::create([
            'zona' => $this->zona,
            'tarif' => $this->tarif
        ]);

        toastr()->success('Data Delivery Fee has been created successfully!', 'Congrats');

        return redirect()->route('ladmin.ratesetting.delivery.index');
    }

    public function updateDeliveryFee(DeliveryFee $rateSetting)
    {
        $rateSetting->update([
            'zona' => $this->zona,
            'tarif' => $this->tarif,
        ]);

        session()->flash('success', 'Rate Setting Delivery Fee has ben updated');
        toastr()->success('Data Rate Setting Delivery Fee has ben updated successfully!', 'Congrats');


        return redirect()->back();
    }
}
