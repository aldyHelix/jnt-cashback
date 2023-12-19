
<div class="row d-flex align-items-center">
    <label for="collection_point_id" class="form-label col-lg-3">Collection Point <span class="text-danger">*</span></label>
    <x-ladmin-input id="collection_point_id" type="text" class="mb-3 col" required name="collection_point_id"
        value="{{ old('collection_point_id', $data->collection_point_id) }}" placeholder="Collection Point" />
</div>

<div class="row d-flex align-items-center">
    <label for=" name" class="form-label col-lg-3">Drop Point Outgoing <span class="text-danger">*</span></label>
    <x-ladmin-input id="name" type="text" class="mb-3 col" required name="name"
        value="{{ old('name', $data->name) }}" placeholder="Drop Point Outgoing" />
</div>

<div class="row d-flex align-items-center">
    <label for=" value" class="form-label col-lg-3">Drop Point TTD <span class="text-danger">*</span></label>
    <x-ladmin-input id="value" type="text" class="mb-3 col" required name="value"
        value="{{ old('value', $data->value) }}" placeholder="Drop Point TTD" />
</div>

<div class="row d-flex align-items-center">
    <label for=" order" class="form-label col-lg-3">Target KPI<span class="text-danger">*</span></label>
    <x-ladmin-input id="order" type="number" min="1" class="mb-3 col" required name="order"
        value="{{ old('order', intval($data->order)) }}" placeholder="Target KPI" />
</div>

<div class="row d-flex align-items-center">
    <label for=" unit_name" class="form-label col-lg-3">Pengurangan KPI tidak capai target<span class="text-danger">*</span></label>
    <x-ladmin-input id="unit_name" type="text" class="mb-3 col" required name="unit_name"
        value="{{ old('unit_name', $data->unit_name) }}" placeholder="Pengurangan KPI tidak capai target" />
</div>
