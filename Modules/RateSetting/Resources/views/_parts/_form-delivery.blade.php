<div class="row d-flex align-items-center">
    <label for="zona" class="form-label col-lg-3">Zona <span class="text-danger">*</span></label>
    <x-ladmin-input id="zona" type="text" class="mb-3 col" required name="zona"
        value="{{ old('zona', $data->zona) }}" placeholder="Zona" />
</div>

<div class="row d-flex align-items-center">
    <label for="tarif" class="form-label col-lg-3">Tarif <span class="text-danger">*</span></label>
    <x-ladmin-input id="tarif" type="number" min="0" class="mb-3 col" required name="tarif"
        value="{{ old('tarif', $data->tarif) }}" placeholder="Tarif" />
</div>

