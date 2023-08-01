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
                <div class="container text-center">
                    <div class="row">
                      <div class="col">
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
                                        <td>{{$item->count}}</td>
                                        <td>{{$item->sum}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ $total['cp_dp_all_count_sum_total_count'] }}</td>
                                    <td>{{ $total['cp_dp_all_count_sum_total_sum'] }}</td>
                                </tr>
                            </tbody>
                          </table>
                      </div>
                      <div class="col">
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
                                        <td>{{$item->count}}</td>
                                        <td>{{$item->sum}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ $total['cp_dp_reguler_count_sum_total_count'] }}</td>
                                    <td>{{ $total['cp_dp_reguler_count_sum_total_sum'] }}</td>
                                </tr>
                            </tbody>
                          </table>
                      </div>
                      <div class="col">
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
                                        <td>{{$item->count}}</td>
                                        <td>{{$item->sum}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ $total['cp_dp_dfod_count_sum_total_count'] }}</td>
                                    <td>{{ $total['cp_dp_dfod_count_sum_total_sum'] }}</td>
                                </tr>
                            </tbody>
                          </table>
                      </div>
                      <div class="col">
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
                                        <td>{{$item->count}}</td>
                                        <td>{{$item->sum}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ $total['cp_dp_super_count_sum_total_count'] }}</td>
                                    <td>{{ $total['cp_dp_super_count_sum_total_sum'] }}</td>
                                </tr>
                            </tbody>
                          </table>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container text-center">

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
