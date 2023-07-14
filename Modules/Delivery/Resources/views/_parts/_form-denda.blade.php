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
                <th scope="col">CP</th>
                <th scope="col">Transit Fee</th>
                <th scope="col">Denda Void</th>
                <th scope="col">Denda dfod</th>
                <th scope="col">Denda Pusat</th>
                <th scope="col">Denda Selisih Berat</th>
                <th scope="col">Denda Lost Scan Kirim</th>
                <th scope="col">Denda Auto Claim</th>
                <th scope="col">Denda Sponsorship</th>
                <th scope="col">Denda Late Pickup</th>
                <th scope="col">Potongan POP</th>
                <th scope="col">Denda Lainnya</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($cp as $key => $item)
                @php
                    $data_denda = App\Models\DendaDelivery::where(['sprinter_pickup' => $item->id, 'periode_id' => $id])->first();
                    if ($data_denda) {
                        $denda = $data_denda;
                    }
                @endphp
                <tr>
                    <input type="hidden" name="data[{{ $key }}][denda_id]" value="{{$denda->id ?? ''}}">
                    <input type="hidden" name="data[{{ $key }}][sprinter_pickup]" value="{{$item->id}}">
                    <th scope="row">{{ $item->kode_cp }}</th>
                    <td>{{ $item->nama_cp}}</td>
                    <td><x-ladmin-input id="transit_fee" type="text" class="mb-3 col" required name="data[{{ $key }}][transit_fee]"
                        value="{{ old('transit_fee', $denda->transit_fee ) }}" placeholder="Transit Fee" /></td>
                    <td><x-ladmin-input id="denda_void" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_void]"
                        value="{{ old('denda_void', $denda->denda_void) }}" placeholder="Denda Void" /></td>
                    <td><x-ladmin-input id="denda_dfod" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_dfod]"
                        value="{{ old('denda_dfod', $denda->denda_dfod) }}" placeholder="Denda DFOD" /></td>
                    <td><x-ladmin-input id="denda_pusat" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_pusat]"
                        value="{{ old('denda_pusat', $denda->denda_pusat) }}" placeholder="Denda Pusat" /></td>
                    <td><x-ladmin-input id="denda_selisih_berat" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_selisih_berat]"
                        value="{{ old('denda_selisih_berat', $denda->denda_selisih_berat) }}" placeholder="Denda Selisih Berat" /></td>
                    <td><x-ladmin-input id="denda_lost_scan_kirim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_lost_scan_kirim]"
                        value="{{ old('denda_lost_scan_kirim', $denda->denda_lost_scan_kirim) }}" placeholder="Denda Lost Scan Kirim" /></td>
                    <td><x-ladmin-input id="denda_auto_claim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_auto_claim]"
                        value="{{ old('denda_auto_claim', $denda->denda_auto_claim) }}" placeholder="Denda Auto Claim" /></td>
                    <td><x-ladmin-input id="denda_sponsorship" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_sponsorship]"
                        value="{{ old('denda_sponsorship', $denda->denda_sponsorship) }}" placeholder="Denda Sponsorship" /></td>
                    <td><x-ladmin-input id="denda_late_pickup_ecommerce" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_late_pickup_ecommerce]"
                        value="{{ old('denda_late_pickup_ecommerce', $denda->denda_late_pickup_ecommerce) }}" placeholder="Denda Late Pickup Ecommerce" /></td>
                    <td><x-ladmin-input id="potongan_pop" type="text" class="mb-3 col" required name="data[{{ $key }}][potongan_pop]"
                        value="{{ old('potongan_pop', $denda->potongan_pop) }}" placeholder="Denda Potongan POP" /></td>
                    <td><x-ladmin-input id="denda_lainnya" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_lainnya]"
                        value="{{ old('denda_lainnya', $denda->denda_lainnya) }}" placeholder="Denda Lainnya" /></td>
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
