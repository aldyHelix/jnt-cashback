<x-ladmin-auth-layout>
    <x-slot name="title">Master Kategori Klien Pengiriman</x-slot>
    @can(['ladmin.collectionpoint.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.category.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Tambah Klien Pengiriman</a>
        <a href="{{ route('ladmin.category.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Tambah Kategori Klien Pengiriman</a>
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
                <div class="col-3">
                    <form action="{{ route('ladmin.category.savesetting') }}" method="POST">
                        @csrf
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Klien Pengiriman</th>
                              @foreach ($category as $item)
                                <th scope="col">{{ $item->nama_kategori}}</th>
                              @endforeach
                            </tr>
                          </thead>
                          <tbody>
                              @foreach ($list_klien_pengiriman as $i => $item)
                              @php
                                  $item_category = $item->category->pluck('id')->toArray();
                              @endphp
                              <tr>
                                  <td>{{ $i+1 }}</td>
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
                        <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                    </form>
                </div>
                <x-ladmin-modal id="modal-sync-klien-pengiriman" class="text-start">
                    <x-slot name="title">Singkronasikan klien pengiriman ke periode</x-slot>
                    <x-slot name="body">
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
                <x-ladmin-modal id="modal-create-kategori" class="text-start">
                    <x-slot name="title">Buat kategori</x-slot>
                    <x-slot name="body">
                        form disini
                    </x-slot>
                    <x-slot name="footer">
                        <form action="{{ route('ladmin.category.sync') }}" method="POST">
                            @csrf
                            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                            </x-ladmin-button>
                            <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                        </form>
                    </x-slot>
                </x-ladmin-modal>
            </div>
            {{-- {{ \Modules\Category\Datatables\KlienPengirimanDatatables::table() }} --}}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        {{-- <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script> --}}
    </x-slot>
    <x-slot name="scripts">
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script> --}}
    </x-slot>
</x-ladmin-auth-layout>
