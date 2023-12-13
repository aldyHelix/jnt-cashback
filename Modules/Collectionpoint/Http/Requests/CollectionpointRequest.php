<?php

namespace Modules\Collectionpoint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Collectionpoint\Models\Collectionpoint;

class CollectionpointRequest extends FormRequest
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
            'kode_cp' => ['required'],
            'nama_cp' => ['required', 'max:50']
        ];
    }

    public function createCollectionpoint()
    {
        $collection_point = Collectionpoint::create([
            'kode_cp' => $this->kode_cp,
            'nama_cp' => $this->nama_cp,
            'nama_pt' => $this->nama_pt,
            'drop_point_outgoing' => $this->drop_point_outgoing,
            'grading_pickup' => $this->grading_pickup,
            'zona_delivery' => $this->zona_delivery,
            'nomor_rekening' => $this->nomor_rekening,
            'nama_bank' => $this->nama_bank,
            'nama_rekening' => $this->nama_rekening
        ]);

        toastr()->success('Data Collection point has been created successfully!', 'Congrats');

        return redirect()->route('ladmin.collectionpoint.index');
    }

    public function updateCollectionpoint(Collectionpoint $collectionPoint)
    {

        $collectionPoint->update([
            'kode_cp' => $this->kode_cp,
            'nama_cp' => $this->nama_cp,
            'nama_pt' => $this->nama_pt,
            'drop_point_outgoing' => $this->drop_point_outgoing,
            'grading_pickup' => $this->grading_pickup,
            'zona_delivery' => $this->zona_delivery,
            'nomor_rekening' => $this->nomor_rekening,
            'nama_bank' => $this->nama_bank,
            'nama_rekening' => $this->nama_rekening
        ]);

        session()->flash('success', 'Collection point has been updated');
        toastr()->success('Data Collection point has been updated successfully!', 'Congrats');


        return redirect()->back();
    }
}
