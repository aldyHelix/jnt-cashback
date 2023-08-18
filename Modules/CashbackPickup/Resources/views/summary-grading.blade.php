<x-ladmin-auth-layout>
    <x-slot name="title">Grading Summary Grading 1</x-slot>

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
                        <button type="button" class="btn btn-primary" onclick="downloadExcel('{{ route('ladmin.cashbackpickup.download', ['filename' => $filename]) }}')"><i class="fa fa-download"></i>  Download Excel</button>
                        {{-- <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> PDF</button> --}}
                    </div>
                  </div>
            </div>
            <div class="row">
                <div class="col table-responsive">
                    <h5>Grading</h5>
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama</th>
                            <th scope="col">REGULER</th>
                            <th scope="col">DFOD</th>
                            <th scope="col">SUPER</th>
                            <th scope="col">TOTAL CASHBACK REGULER</th>
                            <th scope="col">TOTAL CASHBACK COD</th>
                            <th scope="col">TOTAL CASHBACK NON COD</th>
                            <th scope="col">TOTAL LUARZONA</th>
                            <th scope="col">TOTAL MARKETPLACE</th>
                            <th scope="col">TOTAL CASHBACK</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($cp_grading as $item)
                                <tr>
                                    <td style="text-align: left">{{$item->kode_cp}}</td>
                                    <td style="text-align: left">{{$item->nama_cp}}</td>
                                    <td>Rp {{ rupiah_format($item->biaya_kirim_reguler)}}</td>
                                    <td>Rp {{ rupiah_format($item->biaya_kirim_dfod)}}</td>
                                    <td>Rp {{ rupiah_format($item->biaya_kirim_super)}}</td>
                                    <td>Rp {{ rupiah_format($item->total_cashback_reguler)}}</td>
                                    <td>Rp {{ rupiah_format($item->cashback_marketplace) }}</td>
                                    <td>Rp {{ rupiah_format($item->discount_total_biaya_kirim_9) }}</td>
                                    <td>Rp {{ rupiah_format($item->total_cashback_luar_zona) }}</td>
                                    <td>Rp {{ rupiah_format($item->total_cashback_marketplace) }}</td>
                                    <td>Rp {{ rupiah_format($item->total_cashback) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border">
                                <td style="text-align: left" colspan="2">Total</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('biaya_kirim_reguler')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('biaya_kirim_dfod')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('biaya_kirim_super')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('total_cashback_reguler')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('cashback_marketplace')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('discount_total_biaya_kirim_9')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('total_cashback_luar_zona')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('total_cashback_marketplace')) }}</td>
                                <td>Rp {{ rupiah_format($cp_grading->sum('total_cashback')) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <h5>All Count SUM CP DP</h5>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">CP</th>
                            <th scope="col">Count</th>
                            <th scope="col">Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cp_dp_all_count_sum as $item)
                                <tr>
                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                    <td>{{ decimal_format($item->count) }}</td>
                                    <td>Rp{{ rupiah_format($item->sum) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border">
                                <td style="text-align: left">Total</td>
                                <td>{{ decimal_format($total['cp_dp_all_count_sum_total_count']) }}</td>
                                <td>Rp {{ rupiah_format($total['cp_dp_all_count_sum_total_sum']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <h5>Reguler Count SUM CP DP</h5>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">CP</th>
                            <th scope="col">Count</th>
                            <th scope="col">Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cp_dp_reguler_count_sum as $item)
                                <tr>
                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                    <td>{{ decimal_format($item->count) }}</td>
                                    <td>Rp {{ rupiah_format($item->sum)}}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border">
                                <td style="text-align: left">Total</td>
                                <td>{{ decimal_format($total['cp_dp_reguler_count_sum_total_count']) }}</td>
                                <td>Rp {{ rupiah_format($total['cp_dp_reguler_count_sum_total_sum']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <h5>Dfod Count SUM CP DP</h5>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">CP</th>
                            <th scope="col">Count</th>
                            <th scope="col">Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cp_dp_dfod_count_sum as $item)
                                <tr>
                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                    <td>{{ decimal_format($item->count) }}</td>
                                    <td>Rp {{ rupiah_format($item->sum) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border">
                                <td style="text-align: left">Total</td>
                                <td>{{ decimal_format($total['cp_dp_dfod_count_sum_total_count']) }}</td>
                                <td>Rp {{ rupiah_format($total['cp_dp_dfod_count_sum_total_sum']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <h5>Super Count SUM CP DP</h5>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">CP</th>
                            <th scope="col">Count</th>
                            <th scope="col">Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cp_dp_super_count_sum as $item)
                                <tr>
                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                    <td>{{ decimal_format($item->count) }}</td>
                                    <td>Rp {{ rupiah_format($item->sum)}}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border">
                                <td style="text-align: left">Total</td>
                                <td>{{ decimal_format($total['cp_dp_super_count_sum_total_count']) }}</td>
                                <td>Rp {{ rupiah_format( $total['cp_dp_super_count_sum_total_sum']) }}</td>
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
            function downloadExcel(route) {
                // Make a request to the server-side script to initiate the download
                window.location.href = route;
            }
        </script>
    </x-slot>
</x-ladmin-auth-layout>
