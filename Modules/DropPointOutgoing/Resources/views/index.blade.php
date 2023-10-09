<x-ladmin-auth-layout>
    <x-slot name="title">Master Drop Point Outgoing</x-slot>
    @can(['ladmin.droppointoutgoing.create'])
    <x-slot name="button">
        <a data-bs-toggle="modal" data-bs-target="#modal-add-drop-point"  class="btn btn-primary">&plus; Tambah Droppoint</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-6">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Drop Point Outgoing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drop_point_outgoing as $i => $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item != '' ? $item : '(blank)'}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    @if($not_sync == [])
                        <span>semua data telah disingkronkan</span>
                    @else
                        <span>data tidak singkron</span>
                        <a data-bs-toggle="modal" data-bs-target="#modal-sync-drop-point-outgoing" class="btn btn-primary"><i class="fas fa-refresh"></i> Singkronasikan</a>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Drop Point Outgoing</th>
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
            </div>
            <x-ladmin-modal id="modal-sync-drop-point-outgoing" class="text-start">
                <x-slot name="title">Singkronasikan Drop point outgoing ke periode</x-slot>
                <x-slot name="body">
                    Apakah anda ingin mensingkronkan daftar Drop point outgoing?
                </x-slot>
                <x-slot name="footer">
                    <form action="{{ route('ladmin.droppointoutgoing.sync') }}" method="POST">
                        @csrf
                        <input type="hidden" name="data_not_sync" value="{{ json_encode($not_sync) }}">
                        <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                        </x-ladmin-button>
                        <x-ladmin-button type="submit" class="text-white" color="danger">Simpan</x-ladmin-button>
                    </form>
                </x-slot>
            </x-ladmin-modal>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">

    </x-slot>
    <x-slot name="scripts">

    </x-slot>
</x-ladmin-auth-layout>
