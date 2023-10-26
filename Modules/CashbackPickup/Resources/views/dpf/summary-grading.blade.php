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
                        <button type="button" class="btn btn-primary" onclick="processCashback('{{ route('ladmin.cashbackpickup.dpf.process', ['code' => $periode->code, 'grade' => $grading ,'id' => $periode->id]) }}')"><i class="fa fa-replace"></i>  Process Grade DPF</button> &nbsp;
                        <button type="button" class="btn btn-primary" onclick="downloadExcel('{{ route('ladmin.cashbackpickup.download', ['filename' => $filename]) }}')"><i class="fa fa-download"></i>  Download Excel</button>
                        {{-- <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> PDF</button> --}}
                    </div>
                  </div>
            </div>
            <div class="row">

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
