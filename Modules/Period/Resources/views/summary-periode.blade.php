<x-ladmin-auth-layout>
    <x-slot name="title">Period Summary</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <div class="container">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col">
                    <h4>Summary</h4>
                  </div>
                  {{-- <div class="col text-end">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> CSV</button>
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> Excel</button>
                        <button type="button" class="btn btn-primary"><i class="fa fa-download"></i> PDF</button>
                    </div>
                  </div> --}}
            </div>
             <div class="row" style="margin-bottom: 10px;">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="periode-tab" data-bs-toggle="tab" data-bs-target="#periode-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Periode Information</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="rate-grading-tab" data-bs-toggle="tab" data-bs-target="#rate-grading-tab-pane" type="button" role="tab" aria-controls="rate-grading-tab-pane" aria-selected="false">Summary Grading</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cp-dp-tab" data-bs-toggle="tab" data-bs-target="#cp-dp-tab-pane" type="button" role="tab" aria-controls="cp-dp-tab-pane" aria-selected="false">Summary CP DP</button>
                      </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-tab-pane" type="button" role="tab" aria-controls="delivery-tab-pane" aria-selected="false">Summary Delivery Fee</button>
                    </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="periode-tab-pane" role="tabpanel" aria-labelledby="periode-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-4 table-responsive">
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
                                            <td style="text-align: left">Row Count</td>
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
                    <div class="tab-pane fade" id="rate-grading-tab-pane" role="tabpanel" aria-labelledby="rate-grading-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <h5>Summary Grading</h5>
                            <div class="row">
                                <div class="col-4 table-responsive">
                                  <h5>Grade A</h5>
                                  <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">Kode</th>
                                          <th scope="col">Nama</th>
                                          <th scope="col">Count</th>
                                          <th scope="col">Sum</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          @foreach ($cp_grade_a as $item)
                                              <tr>
                                                  <td style="text-align: left">{{$item->kode_cp}}</td>
                                                  <td style="text-align: left">{{$item->nama_cp}}</td>
                                                  <td>{{decimal_format($item->count)}}</td>
                                                  <td>Rp{{rupiah_format($item->sum)}}</td>
                                              </tr>
                                          @endforeach
                                          <tr class="font-weight-bold border">
                                              <td style="text-align: left" colspan="2">Total</td>
                                              <td>{{ decimal_format($total['grade_a_summary_total_count']) }}</td>
                                              <td>Rp{{ rupiah_format($total['grade_a_summary_total_sum']) }}</td>
                                          </tr>
                                      </tbody>
                                    </table>
                                </div>
                                <div class="col-4 table-responsive">
                                  <h5>Grade B</h5>
                                  <table class="table">
                                      <thead>
                                        <tr>
                                            <th scope="col">Kode</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Count</th>
                                            <th scope="col">Sum</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach ($cp_grade_b as $item)
                                            <tr>
                                                <td style="text-align: left">{{$item->kode_cp}}</td>
                                                <td style="text-align: left">{{$item->nama_cp}}</td>
                                                <td>{{decimal_format($item->count)}}</td>
                                                <td>Rp{{rupiah_format($item->sum)}}</td>
                                            </tr>
                                        @endforeach
                                          <tr class="font-weight-bold border">
                                              <td style="text-align: left" colspan="2">Total</td>
                                              <td>{{ decimal_format($total['grade_b_summary_total_count']) }}</td>
                                              <td>Rp{{ rupiah_format($total['grade_b_summary_total_sum']) }}</td>
                                          </tr>
                                      </tbody>
                                    </table>
                                </div>
                                <div class="col-4 table-responsive">
                                  <h5>Grade C</h5>
                                  <table class="table">
                                      <thead>
                                        <tr>
                                            <th scope="col">Kode</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Count</th>
                                            <th scope="col">Sum</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach ($cp_grade_c as $item)
                                            <tr>
                                                <td style="text-align: left">{{$item->kode_cp}}</td>
                                                <td style="text-align: left">{{$item->nama_cp}}</td>
                                                <td>{{decimal_format($item->count)}}</td>
                                                <td>Rp{{rupiah_format($item->sum)}}</td>
                                            </tr>
                                        @endforeach
                                          <tr class="font-weight-bold border">
                                              <td style="text-align: left" colspan="2">Total</td>
                                              <td>{{ decimal_format($total['grade_c_summary_total_count']) }}</td>
                                              <td>Rp{{ rupiah_format($total['grade_c_summary_total_sum'] )}}</td>
                                          </tr>
                                      </tbody>
                                    </table>
                                </div>
                              </div>
                         </div>
                    </div>
                    <div class="tab-pane fade" id="cp-dp-tab-pane" role="tabpanel" aria-labelledby="cp-dp-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <h5>Summary CP DP</h5>
                            @include('period::summary-cp-dp')
                        </div>
                    </div>
                    <div class="tab-pane fade" id="delivery-tab-pane" role="tabpanel" aria-labelledby="delivery-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <h5>Summary DPF</h5>
                            @include('period::summary-dpf')
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </x-slot>
    </x-ladmin-card>
</x-ladmin-auth-layout>