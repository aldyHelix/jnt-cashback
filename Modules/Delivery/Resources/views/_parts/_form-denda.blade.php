<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#modal-denda">
    Setting Denda
</a>

<x-ladmin-modal id="modal-denda" class="text-start modal-xl modal-fullscreen">
    <x-slot name="title">Setting Denda Delivery</x-slot>
    <x-slot name="body">
        <form action="{{ route('ladmin.delivery.denda') }}" method="POST">
            @csrf
                <input type="hidden" name="periode_id" value="{{$id}}">
        <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Kode DP</th>
                <th scope="col">Mitra</th>
                <th scope="col">Denda Lost Scan</th>
                <th scope="col">Denda Late Pickup Reg</th>
                <th scope="col">Denda Auto Claim</th>
                <th scope="col">Tarif (%)</th>
                <th scope="col">Admin Bank</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($delivery_zone as $key => $item)
                <tr>
                    <input type="hidden" name="data[{{ $key }}][id]" value="{{$item->id ?? ''}}">
                    <input type="hidden" name="data[{{ $key }}][collection_point_id]" value="{{$item->collection_point_id}}">
                    <input type="hidden" name="data[{{ $key }}][drop_point_outgoing]" value="{{$item->nama_cp}}">
                    <td>
                        <div class="form-check">
                            <input type="hidden" name="cp[{{ $key }}][is_show]" value=0>
                            <input class="form-check-input" type="checkbox" name="cp[{{$key}}][is_show]" value=1 id="dpCheck"
                            {{ $item->is_show ? 'checked' : 0}}>
                        </div>
                    </td>
                    <th scope="row">{{ $item->zona_delivery }}</th>
                    <td>{{ $item->nama_cp}}</td>
                    <td><x-ladmin-input id="denda_lost_scan_kirim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_lost_scan_kirim]"
                        value="{{ old('denda_lost_scan_kirim', $denda->denda_lost_scan_kirim) }}" placeholder="Denda Lost Scan Kirim" /></td>
                    <td><x-ladmin-input id="denda_late_pickup_reg" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_late_pickup_reg]"
                        value="{{ old('denda_late_pickup_reg', $denda->denda_late_pickup_reg) }}" placeholder="Denda Late Pickup Reguler" /></td>
                    <td><x-ladmin-input id="denda_auto_claim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_auto_claim]"
                        value="{{ old('denda_auto_claim', $denda->denda_auto_claim) }}" placeholder="Denda Auto Claim" /></td>
                    <td><x-ladmin-input id="tarif" type="text" class="mb-3 col" required name="data[{{ $key }}][tarif]"
                        value="{{ old('tarif', $denda->tarif) }}" placeholder="Tarif" /></td>
                    <td><x-ladmin-input id="admin_bank" type="text" class="mb-3 col" required name="data[{{ $key }}][admin_bank]"
                        value="{{ old('admin_bank', $denda->admin_bank) }}" placeholder="Admin Bank" /></td>
                </tr>
                @endforeach
            </tbody>
          </table>
          <div>
              <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</x-ladmin-button>
              <x-ladmin-button type="submit" class="text-white" color="primary">Simpan</x-ladmin-button>
          </div>
          </form>
    </x-slot>
</x-ladmin-modal>
