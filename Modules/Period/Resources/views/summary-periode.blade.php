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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="resi-error" data-bs-toggle="tab" data-bs-target="#resi-error-pane" type="button" role="tab" aria-controls="resi-error-pane" aria-selected="false">Resi Error</button>
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
                                            <td style="text-align: left">Total Biaya Kirim</td>
                                            <td style="text-align: left;width: 200px;">: Rp{{ rupiah_format($sum_all_biaya_kirim) }} </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left">Import Last Status</td>
                                            <td style="text-align: left;width: 200px;">: {{ $periode->status }} </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left">Processed By</td>
                                            <td style="text-align: left;width: 200px;">: {{ $periode->processed_by ?? 'SYSTEM' }} </td>
                                        </tr>
                                        {{-- <tr>
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
                                        </tr> --}}
                                        <tr>
                                            <td style="text-align: left">Proccesed at</td>
                                            <td style="text-align: left;width: 200px;">: {{ $periode->start_processed_at ?? 'Not Started' }} </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left">Done Proccesed at</td>
                                            <td style="text-align: left;width: 200px;">: {{ $periode->done_processed_at ?? 'Undone' }} </td>
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
                            <div class="col-4" style="max-height: 100vh">
                                Summary Sumber waybill
                                <div class="table-responsive" style="height: 500px">
                                    <table class="table">
                                        <tbody>
                                            @foreach ($sumber_waybill as $item)
                                            <tr>
                                                <td style="text-align: left;width: 300px;">{{ $item }} </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- <div class="col-4" style="max-height: 100vh">
                                Summary Klien pengiriman
                                <div class="table-responsive" style="height: 500px">
                                    <form action="{{ route('ladmin.period.update-klien', $periode->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Klien</td>
                                                <td>Reguler</td>
                                                <td>DFOD</td>
                                                <td>SUPER</td>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                @foreach ($klien_pengiriman as $key => $item)
                                                <input type="hidden" name="klien[{{$key}}][item]" value="{{ $item->klien_pengiriman }}">
                                                <tr>
                                                    <td style="text-align: left;width: 300px;">{{ $item->klien_pengiriman ?? '(blank)' }} </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="hidden" name="klien[{{$key}}][reguler]" value=0>
                                                            <input class="form-check-input" type="checkbox" name="klien[{{$key}}][reguler]" value=1 id="flexCheckDefaultReguler" {{ $item->is_reguler ? 'checked' : ''}}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="hidden" name="klien[{{$key}}][dfod]" value=0>
                                                            <input class="form-check-input" type="checkbox" name="klien[{{$key}}][dfod]" value=1 id="flexCheckDefaultDfod" {{ $item->is_dfod ? 'checked' : ''}}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="hidden" name="klien[{{$key}}][super]" value=0>
                                                            <input class="form-check-input" type="checkbox" name="klien[{{$key}}][super]" value=1 id="flexCheckDefaultSuper" {{ $item->is_super ? 'checked' : ''}}>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>

                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </form>

                                </div>
                            </div> --}}
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
                                        @if($cp_grade_a->count() > 0)
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
                                        @else
                                        <tr>
                                            <td colspan="4">
                                                No Data
                                            </td>
                                        </tr>
                                        @endif
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
                                        @if($cp_grade_b->count() > 0)
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
                                        @else
                                          <tr>
                                              <td colspan="4">
                                                  No Data
                                              </td>
                                          </tr>
                                        @endif
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
                                        @if($cp_grade_c->count() > 0)
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
                                        @else
                                        <tr>
                                            <td colspan="4">
                                                No Data
                                            </td>
                                        </tr>
                                        @endif
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
                    <div class="tab-pane fade" id="resi-error-pane" role="tabpanel" aria-labelledby="resi-error" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-12 table-responsive">
                                <span>
                                    this data may not inserted, please check the following RESI and re upload for sure. dont forget to write down current total and compare the existing with new uploaded data. thank you.
                                </span>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">RESI</th>
                                            <th scope="col">ERROR</th>
                                            <th scope="col">BEFORE</th>
                                            <th scope="col">AFTER</th>
                                            <th scope="col">DATE AT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($log_resi as $item)
                                        <tr>
                                            <td style="text-align: left">{{$item->resi ?? '-'}}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->before_raw ?? '-'}}</td>
                                            <td>{{ $item->after_raw ?? '-'}}</td>
                                            <td>{{ $item->created_at ?? '-'}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </x-slot>
    </x-ladmin-card>
</x-ladmin-auth-layout>
