<x-ladmin-auth-layout>
    <x-slot name="title">Grading Summary Grading DPF</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <div class="container">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col">
                    <h4>Summary</h4>
                  </div>
                  <div class="col text-end">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        {{-- <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> CSV</button> --}}
                        <button type="button" class="btn btn-primary" onclick="processCashback('{{ route('ladmin.cashbackpickup.dpf.process', ['code' => $periode->code, 'grade' => $grading ,'id' => $periode->id]) }}')"><i class="fa fa-gears"></i>  Process Grade DPF</button> &nbsp;
                        <button type="button" class="btn btn-primary" onclick="downloadExcel('{{ route('ladmin.cashbackpickup.download', ['filename' => $filename]) }}')"><i class="fa fa-download"></i>  Download Excel</button>
                        {{-- <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> PDF</button> --}}
                    </div>
                </div>
            </div>
            <div class="row">
                    @php
                        $data_grading = collect(json_decode($periode->jsonData->dpf_cashback_rekap));
                    @endphp
                        <div class="col table-responsive">
                        <h5>Grading DPF</h5>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama</th>
                                <th scope="col">TOTAL CASHBACK REGULER</th>
                                <th scope="col">TOTAL CASHBACK MARKETPLACE</th>
                                <th scope="col">TOTAL CASHBACK LUAR ZONA</th>
                                <th scope="col">TOTAL CASHBACK</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_grading as $item)
                                    <tr>
                                        <td style="text-align: left">{{$item->kode_cp}}</td>
                                        <td style="text-align: left">{{$item->nama_cp}}</td>
                                        <td>Rp {{ rupiah_format($item->total_cashback_reguler)}}</td>
                                        <td>Rp {{ rupiah_format($item->total_cashback_marketplace) }}</td>
                                        <td>Rp {{ rupiah_format($item->total_cashback_mp_luar_zona) }}</td>
                                        <td>Rp {{ rupiah_format($item->total_cashback) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left" colspan="2">Total</td>
                                    <td>Rp {{ rupiah_format($data_grading->sum('total_cashback_reguler')) }}</td>
                                    <td>Rp {{ rupiah_format($data_grading->sum('total_cashback_marketplace')) }}</td>
                                    <td>Rp {{ rupiah_format($data_grading->sum('total_cashback_mp_luar_zona')) }}</td>
                                    <td>Rp {{ rupiah_format($data_grading->sum('total_cashback')) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        <script>
            function processCashback(route) {
              window.location.href = route;
            }

            function downloadExcel(route) {
                // Make a request to the server-side script to initiate the download
                window.location.href = route;
            }
        </script>
    </x-slot>
</x-ladmin-auth-layout>
