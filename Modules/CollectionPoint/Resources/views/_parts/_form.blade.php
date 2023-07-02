<div class="row d-flex align-items-center">
    <label for="kode_cp" class="form-label col-lg-3">Kode Collection Point <span class="text-danger">*</span></label>
    <x-ladmin-input id="kode_cp" type="text" class="mb-3 col" required name="kode_cp"
        value="{{ old('kode_cp', $data->kode_cp) }}" placeholder="Kode Collection Point" />
</div>

<div class="row d-flex align-items-center">
    <label for="name" class="form-label col-lg-3">Nama Collection Point <span class="text-danger">*</span></label>
    <x-ladmin-input id="nama_cp" type="text" class="mb-3 col" required name="nama_cp"
        value="{{ old('nama_cp', $data->nama_cp) }}" placeholder="Nama Collection Point" />
</div>

<div class="row d-flex align-items-center">
    <label for="nama_pt" class="form-label col-lg-3">Nama PT <span class="text-danger">*</span></label>
    <x-ladmin-input id="nama_pt" type="text" class="mb-3 col" required name="nama_pt"
        value="{{ old('nama_pt', $data->nama_pt) }}" placeholder="Nama PT" />
</div>

<div class="row d-flex align-items-center">
    <label for="drop_point_outgoing" class="form-label col-lg-3">Drop Point Outgoing <span class="text-danger">*</span></label>
    <x-ladmin-input id="drop_point_outgoing" type="text" class="mb-3 col" required name="drop_point_outgoing"
        value="{{ old('drop_point_outgoing', $data->drop_point_outgoing) }}" placeholder="Drop Point Outgoing" />
</div>

<div class="row d-flex align-items-center">
    <label for="grading_pickup" class="form-label col-lg-3">Grading Pickup <span class="text-danger">*</span></label>
    <x-ladmin-input id="grading_pickup" type="text" class="mb-3 col" required name="grading_pickup"
        value="{{ old('grading_pickup', $data->grading_pickup) }}" placeholder="Grading Pickup" />
</div>

<div class="row d-flex align-items-center">
    <label for="zona_delivery" class="form-label col-lg-3">Zona Delivery <span class="text-danger">*</span></label>
    <x-ladmin-input id="zona_delivery" type="text" class="mb-3 col" required name="zona_delivery"
        value="{{ old('zona_delivery', $data->zona_delivery) }}" placeholder="Zona Delivery" />
</div>

<div class="row d-flex align-items-center">
    <label for="nomor_rekening" class="form-label col-lg-3">Nomor Rekening <span class="text-danger">*</span></label>
    <x-ladmin-input id="nomor_rekening" type="text" class="mb-3 col" required name="nomor_rekening"
        value="{{ old('nomor_rekening', $data->nomor_rekening) }}" placeholder="Nomor Rekening" />
</div>

<div class="row d-flex align-items-center">
    <label for="nama_rekening" class="form-label col-lg-3">Nama Rekening <span class="text-danger">*</span></label>
    <x-ladmin-input id="nama_rekening" type="text" class="mb-3 col" required name="nama_rekening"
        value="{{ old('nama_rekening', $data->nama_rekening) }}" placeholder="Nama Rekening" />
</div>

<div class="row d-flex align-items-center">
    <label for="nama_bank" class="form-label col-lg-3">Nama Bank <span class="text-danger">*</span></label>
    <x-ladmin-input id="nama_bank" type="text" class="mb-3 col" required name="nama_bank"
        value="{{ old('nama_bank', $data->nama_bank) }}" placeholder="Nama bank" />
</div>
