<div class="row d-flex align-items-center">
    <label for=" sumber_waybill" class="form-label col-lg-3">Sumber Waybill <span class="text-danger">*</span></label>
    <x-ladmin-input id="sumber_waybill" type="text" class="mb-3 col" required name="sumber_waybill"
        value="{{ old('sumber_waybill', $data->sumber_waybill) }}" placeholder="Sumber Waybill" />
</div>

<div class="row d-flex align-items-center">
    <label for="diskon_persen" class="form-label col-lg-3">Diskon (%) <span class="text-danger">*</span></label>
    <x-ladmin-input id="diskon_persen" type="number" min="0" max="100" class="mb-3 col" required name="diskon_persen"
        value="{{ old('diskon_persen', $data->diskon_persen) }}" placeholder="Diskon Persen (%)" />
</div>

<div class="row d-flex align-items-center">
    <label for="fee" class="form-label col-lg-3">Fee <span class="text-danger">*</span></label>
    <x-ladmin-input id="fee" type="text" class="mb-3 col" required name="fee"
        value="{{ old('fee', $data->fee) }}" placeholder="Fee" />
</div>

