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
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> CSV</button>
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> Excel</button>
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> PDF</button>
                    </div>
                  </div>
            </div>
            <div class="row">
                <div class="container text-center">
                    <div class="row">
                      <div class="col">
                        Column
                      </div>
                      <div class="col">
                        Column
                      </div>
                      <div class="col">
                        Column
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container text-center">

        </div>
        </x-slot>
    </x-ladmin-card>
</x-ladmin-auth-layout>
