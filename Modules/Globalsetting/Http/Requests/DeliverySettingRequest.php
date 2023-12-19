<?php

namespace Modules\Globalsetting\Http\Requests;

use App\Models\DeliveryZone;
use App\Models\Globalsetting;
use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
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
            'collection_point_id' => ['required'],
            'drop_point_outgoing' => ['required'],
            'drop_point_ttd' => ['required'],
            'kpi_target_count' => ['required'],
            'kpi_reduce_not_achievement' => ['required'],
        ];
    }

    public function createDeliverySetting()
    {
        $deliverySetting = DeliveryZone::create([
            'collection_point_id' => $this->collection_point_id,
            'drop_point_outgoing' => $this->drop_point_outgoing,
            'drop_point_ttd' => $this->drop_point_ttd,
            'kpi_target_count' => $this->kpi_target_count,
            'kpi_reduce_not_achievement' => $this->kpi_reduce_not_achievement,
        ]);

        toastr()->success('Data Delivery Setting has been created successfully!', 'Congrats');

        return redirect()->route('ladmin.globalsetting.delivery.index');
    }

    public function updateDeliverySetting(DeliveryZone $deliverySetting)
    {
        $deliverySetting->update([
            'collection_point_id' => $this->collection_point_id,
            'drop_point_outgoing' => $this->drop_point_outgoing,
            'drop_point_ttd' => $this->drop_point_ttd,
            'kpi_target_count' => $this->kpi_target_count,
            'kpi_reduce_not_achievement' => $this->kpi_reduce_not_achievement,
        ]);

        session()->flash('success', 'Delivery Setting has ben updated');
        toastr()->success('Data Delivery Setting has ben updated successfully!', 'Congrats');


        return redirect()->back();
    }
}
