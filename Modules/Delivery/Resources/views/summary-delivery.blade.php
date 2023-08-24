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
