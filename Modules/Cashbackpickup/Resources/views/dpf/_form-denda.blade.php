
<x-ladmin-auth-layout>
    <x-slot name="title">Setting Denda Grading {{ $grading }}</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <form action="{{ route('ladmin.cashbackpickup.dpf.save-denda') }}" method="POST">
            @csrf
                <input type="hidden" name="periode_id" value="{{$id}}">
                <input type="hidden" name="grading_type" value="{{$grading}}">
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
                    $data_denda = App\Models\Denda::where(['grading_type' => $grading, 'sprinter_pickup' => $item->id, 'periode_id' => $id])->first();
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
                        value="{{ old('transit_fee', $denda->transit_fee ) ?? 0 }}" placeholder="Transit Fee" /></td>
                    <td><x-ladmin-input id="denda_void" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_void]"
                        value="{{ old('denda_void', $denda->denda_void)  ?? 0 }}" placeholder="Denda Void" /></td>
                    <td><x-ladmin-input id="denda_dfod" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_dfod]"
                        value="{{ old('denda_dfod', $denda->denda_dfod)  ?? 0 }}" placeholder="Denda DFOD" /></td>
                    <td><x-ladmin-input id="denda_pusat" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_pusat]"
                        value="{{ old('denda_pusat', $denda->denda_pusat)  ?? 0 }}" placeholder="Denda Pusat" /></td>
                    <td><x-ladmin-input id="denda_selisih_berat" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_selisih_berat]"
                        value="{{ old('denda_selisih_berat', $denda->denda_selisih_berat)  ?? 0 }}" placeholder="Denda Selisih Berat" /></td>
                    <td><x-ladmin-input id="denda_lost_scan_kirim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_lost_scan_kirim]"
                        value="{{ old('denda_lost_scan_kirim', $denda->denda_lost_scan_kirim)  ?? 0 }}" placeholder="Denda Lost Scan Kirim" /></td>
                    <td><x-ladmin-input id="denda_auto_claim" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_auto_claim]"
                        value="{{ old('denda_auto_claim', $denda->denda_auto_claim)  ?? 0 }}" placeholder="Denda Auto Claim" /></td>
                    <td><x-ladmin-input id="denda_sponsorship" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_sponsorship]"
                        value="{{ old('denda_sponsorship', $denda->denda_sponsorship)  ?? 0 }}" placeholder="Denda Sponsorship" /></td>
                    <td><x-ladmin-input id="denda_late_pickup_ecommerce" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_late_pickup_ecommerce]"
                        value="{{ old('denda_late_pickup_ecommerce', $denda->denda_late_pickup_ecommerce)  ?? 0 }}" placeholder="Denda Late Pickup Ecommerce" /></td>
                    <td><x-ladmin-input id="potongan_pop" type="text" class="mb-3 col" required name="data[{{ $key }}][potongan_pop]"
                        value="{{ old('potongan_pop', $denda->potongan_pop)  ?? 0 }}" placeholder="Denda Potongan POP" /></td>
                    <td><x-ladmin-input id="denda_lainnya" type="text" class="mb-3 col" required name="data[{{ $key }}][denda_lainnya]"
                        value="{{ old('denda_lainnya', $denda->denda_lainnya)  ?? 0 }}" placeholder="Denda Lainnya" /></td>
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
        </x-ladmin-card>
        <x-slot name="scripts">

        </x-slot>
        <x-slot name="scripts">

        </x-slot>
</x-ladmin-auth-layout>
