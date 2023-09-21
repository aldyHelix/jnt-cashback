

<div class="row d-flex align-items-center">
    <label for="name" class="form-label col-lg-3">Nama Kategori<span class="text-danger">*</span></label>
    <x-ladmin-input id="nama_kategori" type="text" class="mb-3 col" required name="nama_kategori"
        value="{{ old('nama_kategori', $data->nama_kategori) }}" placeholder="Nama Kategori" />
</div>
