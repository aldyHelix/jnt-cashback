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
                      <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-tab-pane" type="button" role="tab" aria-controls="delivery-tab-pane" aria-selected="false">Summary Delivery Fee</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pivot-tab" data-bs-toggle="tab" data-bs-target="#pivot-tab-pane" type="button" role="tab" aria-controls="pivot-tab-pane" aria-selected="false">Summary Pivot</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pivot-mp-tab" data-bs-toggle="tab" data-bs-target="#pivot-mp-tab-pane" type="button" role="tab" aria-controls="pivot-mp-tab-pane" aria-selected="false">Summary Pivot MP</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pivot-mp-retur-tab" data-bs-toggle="tab" data-bs-target="#pivot-mp-retur-tab-pane" type="button" role="tab" aria-controls="pivot-mp-retur-tab-pane" aria-selected="false">Summary Pivot Retur</button>
                    </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="periode-tab-pane" role="tabpanel" aria-labelledby="periode-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            Periode Code : {{ $periode->code }} <br>
                            Periode Month : {{ $periode->month }}<br>
                            Periode Year : {{ $periode->year }}<br>
                            Row Count : {{ decimal_format($row_total) }}<br>
                            Import Last Status : {{ $periode->status }}<br>
                            Processed By : {{ $periode->processed_by ?? 'SYETEM' }}<br>
                            Is Processed Done : {{ $periode->is_processing_done ? 'TRUE' : 'FALSE' }}<br>
                            Is Pivot Processed Done : {{ $periode->is_pivot_processing_done ? 'TRUE' : 'FALSE' }}<br>
                            Locked : {{ $periode->is_locked ? 'TRUE' : 'FALSE' }}<br>
                            Proccesed at : {{ $periode->start_proccesed_at ?? 'Not Started' }}<br>
                            Done Proccesed at : {{ $periode->done_proccesed_at ?? 'Undone' }}<br>
                            Periode Created at : {{ $periode->created_at->format('d m Y h:i') }}<br>
                            periode Last Updated : {{ $periode->updated_at->format('d m Y h:i') }}<br>
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
                    <div class="tab-pane fade" id="delivery-tab-pane" role="tabpanel" aria-labelledby="delivery-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            summary rate delivery fee
                         </div>
                    </div>
                    <div class="tab-pane fade" id="pivot-mp-tab-pane" role="tabpanel" aria-labelledby="pivot-mp-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="container text-center table-responsive">
                                    <h4>Summary MP Count Waybill</h4>
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Drop point outgoing</th>
                                            <th scope="col">AKULAKUOB</th>
                                            <th scope="col">BUKAEXPRESS</th>
                                            <th scope="col">BUKALAPAK</th>
                                            <th scope="col">BUKASEND</th>
                                            <th scope="col">EVERMOSAPI</th>
                                            <th scope="col">LAZADA</th>
                                            <th scope="col">LAZADA COD</th>
                                            <th scope="col">MAGELLAN</th>
                                            <th scope="col">MAGELLAN COD</th>
                                            <th scope="col">MENGANTAR</th>
                                            <th scope="col">ORDIVO</th>
                                            <th scope="col">SHOPEE</th>
                                            <th scope="col">SHOPEE COD</th>
                                            <th scope="col">TOKOPEDIA</th>
                                            <th scope="col">GRAND TOTAL</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mp_count_waybill as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->akulakuob)}}</td>
                                                    <td>{{decimal_format($item->bukaexpress)}}</td>
                                                    <td>{{decimal_format($item->bukalapak)}}</td>
                                                    <td>{{decimal_format($item->bukasend)}}</td>
                                                    <td>{{decimal_format($item->evermosapi)}}</td>
                                                    <td>{{decimal_format($item->lazada)}}</td>
                                                    <td>{{decimal_format($item->lazada_cod)}}</td>
                                                    <td>{{decimal_format($item->magellan)}}</td>
                                                    <td>{{decimal_format($item->magellan_cod)}}</td>
                                                    <td>{{decimal_format($item->mengantar)}}</td>
                                                    <td>{{decimal_format($item->ordivo)}}</td>
                                                    <td>{{decimal_format($item->shopee)}}</td>
                                                    <td>{{decimal_format($item->shopee_cod)}}</td>
                                                    <td>{{decimal_format($item->tokopedia)}}</td>
                                                    <td>{{decimal_format($item->grand_total)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left" >Total</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('akulakuob'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('bukaexpress'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('bukalapak'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('bukasend'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('evermosapi'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('lazada'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('lazada_cod'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('magellan'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('magellan_cod'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('mengantar'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('ordivo'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('shopee'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('shopee_cod'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('tokopedia'))}}</td>
                                                <td>{{decimal_format($mp_count_waybill->sum('grand_total'))}}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                </div>
                                <div class="container text-center table-responsive">
                                    <h4>Summary MP Sum Biaya Kirim</h4>
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Drop point outgoing</th>
                                            <th scope="col">AKULAKUOB</th>
                                            <th scope="col">BUKAEXPRESS</th>
                                            <th scope="col">BUKALAPAK</th>
                                            <th scope="col">BUKASEND</th>
                                            <th scope="col">EVERMOSAPI</th>
                                            <th scope="col">LAZADA</th>
                                            <th scope="col">LAZADA COD</th>
                                            <th scope="col">MAGELLAN</th>
                                            <th scope="col">MAGELLAN COD</th>
                                            <th scope="col">MENGANTAR</th>
                                            <th scope="col">ORDIVO</th>
                                            <th scope="col">SHOPEE</th>
                                            <th scope="col">SHOPEE COD</th>
                                            <th scope="col">TOKOPEDIA</th>
                                            <th scope="col">GRAND TOTAL</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mp_sum_biaya_kirim as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>Rp{{rupiah_format($item->akulakuob)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukaexpress)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukalapak)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukasend)}}</td>
                                                    <td>Rp{{rupiah_format($item->evermosapi)}}</td>
                                                    <td>Rp{{rupiah_format($item->lazada)}}</td>
                                                    <td>Rp{{rupiah_format($item->lazada_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->magellan)}}</td>
                                                    <td>Rp{{rupiah_format($item->magellan_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->mengantar)}}</td>
                                                    <td>Rp{{rupiah_format($item->ordivo)}}</td>
                                                    <td>Rp{{rupiah_format($item->shopee)}}</td>
                                                    <td>Rp{{rupiah_format($item->shopee_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->tokopedia)}}</td>
                                                    <td>Rp{{rupiah_format($item->grand_total)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left" >Total</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('akulakuob'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('bukaexpress'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('bukalapak'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('bukasend'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('evermosapi'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('lazada'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('lazada_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('magellan'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('magellan_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('mengantar'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('ordivo'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('shopee'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('shopee_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('tokopedia'))}}</td>
                                                <td>Rp{{rupiah_format($mp_sum_biaya_kirim->sum('grand_total'))}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                         </div>
                    </div>
                    <div class="tab-pane fade" id="pivot-mp-retur-tab-pane" role="tabpanel" aria-labelledby="pivot-mp-retur-tab" tabindex="0">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="container text-center table-responsive">
                                    <h4>Summary MP Retur Count Waybill</h4>
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Drop point outgoing</th>
                                            <th scope="col">AKULAKUOB</th>
                                            <th scope="col">BUKAEXPRESS</th>
                                            <th scope="col">BUKALAPAK</th>
                                            <th scope="col">BUKASEND</th>
                                            <th scope="col">EVERMOSAPI</th>
                                            <th scope="col">LAZADA</th>
                                            <th scope="col">LAZADA COD</th>
                                            <th scope="col">MAGELLAN</th>
                                            <th scope="col">MAGELLAN COD</th>
                                            <th scope="col">MENGANTAR</th>
                                            <th scope="col">ORDIVO</th>
                                            <th scope="col">SHOPEE</th>
                                            <th scope="col">SHOPEE COD</th>
                                            <th scope="col">TOKOPEDIA</th>
                                            <th scope="col">GRAND TOTAL</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mp_retur_count_waybill as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->akulakuob)}}</td>
                                                    <td>{{decimal_format($item->bukaexpress)}}</td>
                                                    <td>{{decimal_format($item->bukalapak)}}</td>
                                                    <td>{{decimal_format($item->bukasend)}}</td>
                                                    <td>{{decimal_format($item->evermosapi)}}</td>
                                                    <td>{{decimal_format($item->lazada)}}</td>
                                                    <td>{{decimal_format($item->lazada_cod)}}</td>
                                                    <td>{{decimal_format($item->magellan)}}</td>
                                                    <td>{{decimal_format($item->magellan_cod)}}</td>
                                                    <td>{{decimal_format($item->mengantar)}}</td>
                                                    <td>{{decimal_format($item->ordivo)}}</td>
                                                    <td>{{decimal_format($item->shopee)}}</td>
                                                    <td>{{decimal_format($item->shopee_cod)}}</td>
                                                    <td>{{decimal_format($item->tokopedia)}}</td>
                                                    <td>{{decimal_format($item->grand_total)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left" >Total</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('akulakuob'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('bukaexpress'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('bukalapak'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('bukasend'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('evermosapi'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('lazada'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('lazada_cod'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('magellan'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('magellan_cod'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('mengantar'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('ordivo'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('shopee'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('shopee_cod'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('tokopedia'))}}</td>
                                                <td>{{decimal_format($mp_retur_count_waybill->sum('grand_total'))}}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                </div>
                                <div class="container text-center table-responsive">
                                    <h4>Summary MP Retur Sum Biaya Kirim</h4>
                                    <table class="table">
                                        <thead>
                                          <tr>
                                            <th scope="col">Drop point outgoing</th>
                                            <th scope="col">AKULAKUOB</th>
                                            <th scope="col">BUKAEXPRESS</th>
                                            <th scope="col">BUKALAPAK</th>
                                            <th scope="col">BUKASEND</th>
                                            <th scope="col">EVERMOSAPI</th>
                                            <th scope="col">LAZADA</th>
                                            <th scope="col">LAZADA COD</th>
                                            <th scope="col">MAGELLAN</th>
                                            <th scope="col">MAGELLAN COD</th>
                                            <th scope="col">MENGANTAR</th>
                                            <th scope="col">ORDIVO</th>
                                            <th scope="col">SHOPEE</th>
                                            <th scope="col">SHOPEE COD</th>
                                            <th scope="col">TOKOPEDIA</th>
                                            <th scope="col">GRAND TOTAL</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mp_retur_sum_biaya_kirim as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>Rp{{rupiah_format($item->akulakuob)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukaexpress)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukalapak)}}</td>
                                                    <td>Rp{{rupiah_format($item->bukasend)}}</td>
                                                    <td>Rp{{rupiah_format($item->evermosapi)}}</td>
                                                    <td>Rp{{rupiah_format($item->lazada)}}</td>
                                                    <td>Rp{{rupiah_format($item->lazada_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->magellan)}}</td>
                                                    <td>Rp{{rupiah_format($item->magellan_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->mengantar)}}</td>
                                                    <td>Rp{{rupiah_format($item->ordivo)}}</td>
                                                    <td>Rp{{rupiah_format($item->shopee)}}</td>
                                                    <td>Rp{{rupiah_format($item->shopee_cod)}}</td>
                                                    <td>Rp{{rupiah_format($item->tokopedia)}}</td>
                                                    <td>Rp{{rupiah_format($item->grand_total)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left" >Total</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('akulakuob'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('bukaexpress'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('bukalapak'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('bukasend'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('evermosapi'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('lazada'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('lazada_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('magellan'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('magellan_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('mengantar'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('ordivo'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('shopee'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('shopee_cod'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('tokopedia'))}}</td>
                                                <td>Rp{{rupiah_format($mp_retur_sum_biaya_kirim->sum('grand_total'))}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                         </div>
                    </div>
                    <div class="tab-pane fade" id="pivot-tab-pane" role="tabpanel" aria-labelledby="pivot-tab" tabindex="0">
                        <div class="row">
                            <div class="container text-center">
                                <h4>Summary pivot count and sum</h4>
                                <div class="row">
                                  <div class="col-6 table-responsive">
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
                                            @foreach ($all_summary as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->count)}}</td>
                                                    <td>Rp{{rupiah_format($item->sum)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left">Total</td>
                                                <td>{{ decimal_format($total['all_summary_total_count']) }}</td>
                                                <td>Rp{{ rupiah_format($total['all_summary_total_sum']) }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                  </div>
                                  <div class="col-6 table-responsive">
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
                                            @foreach ($reguler_summary as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->count)}}</td>
                                                    <td>Rp{{rupiah_format($item->sum)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left">Total</td>
                                                <td>{{ decimal_format($total['reguler_summary_total_count']) }}</td>
                                                <td>Rp{{ rupiah_format($total['reguler_summary_total_sum']) }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                  </div>
                                  <div class="col-6 table-responsive">
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
                                            @foreach ($dfod_summary as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->count)}}</td>
                                                    <td>Rp{{rupiah_format($item->sum)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left">Total</td>
                                                <td>{{ decimal_format($total['dfod_summary_total_count']) }}</td>
                                                <td>Rp.{{ rupiah_format($total['dfod_summary_total_sum']) }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                  </div>
                                  <div class="col-6 table-responsive">
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
                                            @foreach ($super_summary as $item)
                                                <tr>
                                                    <td style="text-align: left">{{$item->drop_point_outgoing}}</td>
                                                    <td>{{decimal_format($item->count)}}</td>
                                                    <td>Rp{{rupiah_format($item->sum)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold border">
                                                <td style="text-align: left">Total</td>
                                                <td>{{ decimal_format($total['super_summary_total_count']) }}</td>
                                                <td>Rp{{ rupiah_format($total['super_summary_total_sum']) }}</td>
                                            </tr>
                                        </tbody>
                                      </table>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>
                  </div>
            </div>

        </div>
        <div class="container text-center">

        </div>
        </x-slot>
    </x-ladmin-card>
</x-ladmin-auth-layout>
