<x-ladmin-auth-layout>
    <x-slot name="title">Master Kategori Klien Pengiriman</x-slot>
    @can(['ladmin.collectionpoint.create'])
    <x-slot name="button">
        <a data-bs-toggle="modal" data-bs-target="#modal-add-klien-pengiriman"  class="btn btn-primary">&plus; Tambah Klien Pengiriman</a>
        <a data-bs-toggle="modal" data-bs-target="#modal-add-kategori-klien-pengiriman"  class="btn btn-primary">&plus; Tambah Kategori Klien Pengiriman</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-3">
                    @if($not_sync == [])
                        <span>semua data telah disingkronkan</span>
                    @else
                        <span>data tidak singkron</span>
                        <a data-bs-toggle="modal" data-bs-target="#modal-sync-klien-pengiriman" class="btn btn-primary"><i class="fas fa-refresh"></i> Singkronasikan</a>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Klien Pengiriman</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($not_sync as $i => $item)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $item != '' ? $item : '(blank)'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="col-6 ">
                    <form action="{{ route('ladmin.category.savesetting') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Klien Pengiriman</th>
                                @foreach ($category as $item)
                                    <th scope="col">{{ $item->nama_kategori}} <a class="btn btn-icon" data-bs-toggle="modal" data-bs-target="#modal-setting-klien-pengiriman-{{ $item->id }}"><i class="fas fa-edit"></i></a></th>
                                @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list_klien_pengiriman as $i => $item)
                                @php
                                    $item_category = $item->category->pluck('id')->toArray();
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item->klien_pengiriman != '' ? $item->klien_pengiriman : '(blank)'}}</td>
                                    @foreach ($category as $cat)
                                        <input type="hidden" name="klien_pengiriman[{{ $item->id }}][{{ $cat->id }}]" value="0">
                                        <td><input class="form-check-input" type="checkbox" name="klien_pengiriman[{{ $item->id }}][{{ $cat->id }}]" value="1" id="flexCheckDefault" {{ in_array($cat->id, $item_category) ? 'checked' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                    </form>
                </div>
                <div class="col-3">
                    <div class="row">
                    @if($metode_pembayaran_not_sync == [])
                        <span>Metode Pembayaran telah disingkronkan</span>
                        <br>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Metode Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($metode_pembayaran_list as $i => $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item != '' ? $item : '(blank)'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <span>data tidak singkron</span>
                        <a data-bs-toggle="modal" data-bs-target="#modal-sync-metode-pembayaran" class="btn btn-primary"><i class="fas fa-refresh"></i> Singkronasikan</a>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Metode Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($metode_pembayaran_not_sync as $i => $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item != '' ? $item : '(blank)'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    </div>

                    <div class="row">
                        @if($kat_not_sync == [])
                            <span>Kategori Resi telah tersingkronasi</span>
                            <br>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Kategori Pengiriman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kat_list as $i => $item)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $item != '' ? $item : '(blank)'}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <span>data tidak singkron</span>
                            <a data-bs-toggle="modal" data-bs-target="#modal-sync-kat" class="btn btn-primary"><i class="fas fa-refresh"></i> Singkronasikan</a>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Kategori Pengiriman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kat_not_sync as $i => $item)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $item != '' ? $item : '(blank)'}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <x-ladmin-modal id="modal-sync-klien-pengiriman" class="text-start">
                    <x-slot name="title">Singkronasikan klien pengiriman ke periode</x-slot>
                    <x-slot name="body">
                        Apakah anda ingin mensingkronkan daftar klien pengiriman?
                    </x-slot>
                    <x-slot name="footer">
                        <form action="{{ route('ladmin.category.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data_not_sync" value="{{ json_encode($not_sync) }}">
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </form>
                    </x-slot>
                </x-ladmin-modal>
                <x-ladmin-modal id="modal-sync-metode-pembayaran" class="text-start">
                    <x-slot name="title">Singkronasikan klien pengiriman ke periode</x-slot>
                    <x-slot name="body">
                        Apakah anda ingin mensingkronkan daftar metode pembayaran?
                    </x-slot>
                    <x-slot name="footer">
                        <form action="{{ route('ladmin.category.sync.metode-pembayaran') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data_not_sync" value="{{ json_encode($metode_pembayaran_not_sync) }}">
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </form>
                    </x-slot>
                </x-ladmin-modal>
                <x-ladmin-modal id="modal-sync-kat" class="text-start">
                    <x-slot name="title">Singkronasikan klien pengiriman ke periode</x-slot>
                    <x-slot name="body">
                        Apakah anda ingin mensingkronkan daftar kategori resi?
                    </x-slot>
                    <x-slot name="footer">
                        <form action="{{ route('ladmin.category.sync.kategori-resi') }}" method="POST">
                            @csrf
                            <input type="hidden" name="data_not_sync" value="{{ json_encode($kat_not_sync) }}">
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </form>
                    </x-slot>
                </x-ladmin-modal>
                <x-ladmin-modal id="modal-add-kategori-klien-pengiriman" class="text-start">
                        <x-slot name="title">Buat kategori</x-slot>
                        <x-slot name="body">
                            <form action="{{ route('ladmin.category.add.kategori') }}" method="POST">
                                @csrf
                            <div class="row d-flex align-items-center">
                                <label for="name" class="form-label col-lg-5">Nama Kategori<span class="text-danger">*</span></label>
                                <x-ladmin-input id="nama_kategori" type="text" class="mb-3 col" required name="nama_kategori"
                                    value="{{ old('nama_kategori') }}" placeholder="Nama Kategori" />
                            </div>

                            <div class="row d-flex align-items-center">
                                <label for="name" class="form-label col-lg-5">Metode Pembayaran<span class="text-danger">*</span></label>
                                <div>
                                    @foreach ($metode_pembayaran_list as $item)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="metode_pembayaran" name="metode_pembayaran[]" value="{{ $item != "" ? $item : '(blank)'}}">
                                            <label class="form-check-label" for="inlineCheckbox1">{{ $item != "" ? $item : '(blank)'}}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row d-flex align-items-center">
                                <label for="name" class="form-label col-lg-3">Kategori Resi<span class="text-danger">*</span></label>
                                <div>
                                    @foreach ($kat_list as $item)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="metode_pembayaran" name="kat[]" value="{{ $item }}">
                                        <label class="form-check-label" for="inlineCheckbox1">{{ $item }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </form>
                    </x-slot>
                </x-ladmin-modal>
                <x-ladmin-modal id="modal-add-klien-pengiriman" class="text-start">
                    <form action="{{ route('ladmin.category.add.klien-pengiriman') }}" method="POST">
                        @csrf
                        <x-slot name="title">Buat Klien Pengiriman</x-slot>
                        <x-slot name="body">
                            <div class="row d-flex align-items-center">
                                <label for="name" class="form-label col-lg-3">Nama Kategori<span class="text-danger">*</span></label>
                                <x-ladmin-input id="nama_kategori" type="text" class="mb-3 col" required name="nama_kategori"
                                    value="{{ old('nama_kategori') }}" placeholder="Nama Kategori" />
                            </div>
                        </x-slot>
                        <x-slot name="footer">
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </x-slot>
                    </form>
                </x-ladmin-modal>

                @foreach ($category as $cat)
                    <x-ladmin-modal id="modal-setting-klien-pengiriman-{{ $cat->id }}" class="text-start">
                            <x-slot name="title">Setting Kategori Pengiriman {{ $cat->nama_kategori }}</x-slot>
                            <x-slot name="body">
                                <form action="{{ route('ladmin.category.update.kategori') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $cat->id }}">
                                <div class="row d-flex align-items-center">
                                    <div class="row d-flex align-items-center">
                                        <label for="name" class="form-label col-lg-5">Nama Kategori<span class="text-danger">*</span></label>
                                        <x-ladmin-input id="nama_kategori" type="text" class="mb-3 col" required name="nama_kategori"
                                            value="{{ old('nama_kategori') }}" value="{{ $cat->nama_kategori }}" placeholder="Nama Kategori" />
                                    </div>

                                    <div class="row d-flex align-items-center">
                                        <label for="name" class="form-label col-lg-5">Metode Pembayaran<span class="text-danger">*</span></label>
                                        <div>
                                            @foreach ($metode_pembayaran_list as $i)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="metode_pembayaran" name="metode_pembayaran[]" value="{{ $i != "" ? $i : '(blank)'}}" {{ in_array(($i != "" ? $i : '(blank)' ), explode(";" ,$cat->metode_pembayaran)) ? "checked" : "" }}>
                                                    <label class="form-check-label" for="inlineCheckbox1">{{ $i != "" ? $i : '(blank)'}}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="row d-flex align-items-center">
                                        <label for="name" class="form-label col-lg-3">Kategori Resi<span class="text-danger">*</span></label>
                                        <div>
                                            @foreach ($kat_list as $j)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="metode_pembayaran" name="kat[]" value="{{ $j }}" {{ in_array($j, explode(";" ,$cat->kat)) ? "checked" : ""}}>
                                                <label class="form-check-label" for="inlineCheckbox1">{{ $j }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                                </x-ladmin-button>
                                <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                            </x-slot>
                        </form>
                    </x-ladmin-modal>
                @endforeach
            </div>
            {{-- {{ \Modules\Category\Datatables\KlienPengirimanDatatables::table() }} --}}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        {{-- <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script> --}}
    </x-slot>
    <x-slot name="scripts">
        <script>
            // $(function () {
            //     $('[data-toggle="tooltip"]').tooltip()
            // })
        </script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script> --}}
    </x-slot>
</x-ladmin-auth-layout>
