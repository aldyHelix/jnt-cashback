
<div class="row d-flex align-items-center">
    <label for=" code" class="form-label col-lg-3">Kode <span class="text-danger">*</span></label>
    <x-ladmin-input id="code" type="text" class="mb-3 col" required name="code"
        value="{{ old('code', $data->code) }}" placeholder="Kode Setting" />
</div>

<div class="row d-flex align-items-center">
    <label for=" name" class="form-label col-lg-3">Nama Setting <span class="text-danger">*</span></label>
    <x-ladmin-input id="name" type="text" class="mb-3 col" required name="name"
        value="{{ old('name', $data->name) }}" placeholder="Nama Setting" />
</div>

<div class="row d-flex align-items-center">
    <label for=" value" class="form-label col-lg-3">Nilai Setting<span class="text-danger">*</span></label>
    <x-ladmin-input id="value" type="text" class="mb-3 col" required name="value"
        value="{{ old('value', $data->value) }}" placeholder="Nilai Setting" />
</div>

<div class="row d-flex align-items-center">
    <label for=" order" class="form-label col-lg-3">Urutan<span class="text-danger">*</span></label>
    <x-ladmin-input id="order" type="number" min="1" class="mb-3 col" required name="order"
        value="{{ old('order', intval($data->order)) }}" placeholder="Urutan Setting" />
</div>

<div class="row d-flex align-items-center">
    <label for=" unit_name" class="form-label col-lg-3">Nama Ukur<span class="text-danger">*</span></label>
    <x-ladmin-input id="unit_name" type="text" class="mb-3 col" required name="unit_name"
        value="{{ old('unit_name', $data->unit_name) }}" placeholder="Nama Ukuran Unit" />
</div>

<div class="row d-flex align-items-center">
    <label for=" unit_char" class="form-label col-lg-3">Kode Karakter Ukur<span class="text-danger">*</span></label>
    <x-ladmin-input id="unit_char" type="text" class="mb-3 col" required name="unit_char"
        value="{{ old('unit_char', $data->unit_char) }}" placeholder="Kode Karakter Ukuran Unit" />
</div>
