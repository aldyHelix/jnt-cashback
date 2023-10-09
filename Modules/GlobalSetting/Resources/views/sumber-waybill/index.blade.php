<x-ladmin-auth-layout>
    <x-slot name="title">Setting Sumber Waybill</x-slot>
    {{-- @can(['ladmin.globalsetting.sumberwaybill.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.globalsetting.setting.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Buat General Setting</a>
    </x-slot>
    @endcan --}}
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-3">
                    @if($sumber_waybill_not_sync == [])
                        <span>semua data telah disingkronkan</span>
                    @else
                        <span>data tidak singkron</span>
                        <a data-bs-toggle="modal" data-bs-target="#modal-sync-sumber-waybill" class="btn btn-primary"><i class="fas fa-refresh"></i> Singkronasikan</a>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Sumber Waybill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sumber_waybill_not_sync as $i => $item)
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
            <x-ladmin-modal id="modal-sync-sumber-waybill" class="text-start">
                <x-slot name="title">Singkronasikan sumber waybill</x-slot>
                <x-slot name="body">
                    Apakah anda ingin mensingkronkan daftar sumber waybill?
                </x-slot>
                <x-slot name="footer">
                    <form action="{{ route('ladmin.globalsetting.sumber-waybill.sync') }}" method="POST">
                        @csrf
                        <input type="hidden" name="data_not_sync" value="{{ json_encode($sumber_waybill_not_sync) }}">
                        <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                        </x-ladmin-button>
                        <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                    </form>
                </x-slot>
            </x-ladmin-modal>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="styles">

    </x-slot>
    <x-slot name="scripts">

    </x-slot>
</x-ladmin-auth-layout>
