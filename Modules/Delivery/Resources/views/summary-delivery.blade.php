<x-ladmin-auth-layout>
    <x-slot name="title">Delivery Summary</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <div class="container">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col">
                    <h4>Summary</h4>
                  </div>
                  <div class="col text-end">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary" onclick="downloadExcel('{{ route('ladmin.delivery.download', ['filename' => $filename]) }}')"><i class="fa fa-download"></i>  Download Excel</button>
                    </div>
                  </div>
            </div>
            <div class="row">
                <div class="container text-center">
                    <div class="row">
                      <div class="col">
                        <h3>Delivery Information</h3>
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left">Periode Code</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->code }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Periode Month</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->month }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Periode Year</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->year }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Total Waybill</td>
                                        <td style="text-align: left;width: 200px;">: {{ decimal_format($row_total) }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Import Last Status</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->status }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Processed By</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->processed_by ?? 'SYSTEM' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Is Processed Done</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->is_processing_done ? 'TRUE' : 'FALSE' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Is Pivot Processed Done</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->is_pivot_processing_done ? 'TRUE' : 'FALSE' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Locked</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->is_locked ? 'TRUE' : 'FALSE' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Proccesed at</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->start_proccesed_at ?? 'Not Started' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Done Proccesed at</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->done_proccesed_at ?? 'Undone' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Periode Created at</td>
                                        <td style="text-align: left;width: 200px;">: {{ $periode->created_at->format('d-m-Y h:i') }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left">Periode Last Updated</td>
                                        <td style="text-align: left"> : {{ $periode->updated_at->format('d-m-Y h:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                      </div>
                      <div class="col">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                              <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                  Total AWB
                                </button>
                              </h2>
                              <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Nama TTD</th>
                                            <th scope="col">Count</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($total_awb_by_ttd as $item)
                                            <tr>
                                                <td>{{ $item->drop_point_ttd}}</td>
                                                <td>{{ $item->count}}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                              <td>Total</td>
                                              <td>{{ $total_awb_by_ttd->sum('count') }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                </div>
                              </div>
                            </div>
                            <div class="accordion-item">
                              <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                  Sprinter Counter
                                </button>
                              </h2>
                              <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Sprinter</th>
                                            <th scope="col">Count</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($summary_sprinter as $item)
                                            <tr>
                                                <td>{{ $item->sprinter}}</td>
                                                <td>{{ $item->count}}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                              <td>Total</td>
                                              <td>{{ $summary_sprinter->sum('count') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{-- {!! $direct_fee !!} --}}
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
