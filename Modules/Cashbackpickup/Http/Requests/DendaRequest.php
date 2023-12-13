<?php

namespace Modules\Cashbackpickup\Http\Requests;

use App\Models\Denda;
use Illuminate\Foundation\Http\FormRequest;

class DendaRequest extends FormRequest
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
            // 'sprinter_pickup' => ['required'],
        ];
    }

    public function createDenda()
    {
        $collection_point = Denda::create([
            'periode_id' => $this->periode_id,
            'grading_type' => $this->grading_type,
            'sprinter_pickup' => $this->sprinter_pickup,
            'transit_fee' => $this->transit_fee,
            'denda_void' => $this->denda_void,
            'denda_dfod' => $this->denda_dfod,
            'denda_pusat' => $this->denda_pusat,
            'denda_selisih_berat' => $this->denda_selisih_berat,
            'denda_lost_scan_kirim' => $this->denda_lost_scan_kirim,
            'denda_auto_claim' => $this->denda_auto_claim,
            'denda_sponsorship' => $this->denda_sponsorship,
            'denda_late_pickup_ecommerce' => $this->denda_late_pickup_ecommerce,
            'potongan_pop' => $this->potongan_pop,
            'denda_lainnya' => $this->denda_lainnya,
        ]);

        // session()->flash('success', 'Denda has been created');

        // return redirect()->route('ladmin.cashbackpickup.index', $this->grading_type);
        return;
    }

    public function updateDenda(Denda $denda)
    {

        $denda->update([
            'periode_id' => $this->periode_id,
            'grading_type' => $this->grading_type,
            'sprinter_pickup' => $this->sprinter_pickup,
            'transit_fee' => $this->transit_fee,
            'denda_void' => $this->denda_void,
            'denda_dfod' => $this->denda_dfod,
            'denda_pusat' => $this->denda_pusat,
            'denda_selisih_berat' => $this->denda_selisih_berat,
            'denda_lost_scan_kirim' => $this->denda_lost_scan_kirim,
            'denda_auto_claim' => $this->denda_auto_claim,
            'denda_sponsorship' => $this->denda_sponsorship,
            'denda_late_pickup_ecommerce' => $this->denda_late_pickup_ecommerce,
            'potongan_pop' => $this->potongan_pop,
            'denda_lainnya' => $this->denda_lainnya,
        ]);

        // session()->flash('success', 'Denda has been updated');

        // return redirect()->back();
        return;
    }
}
