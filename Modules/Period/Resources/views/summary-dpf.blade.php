<div class="d-flex align-items-start">
    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <button class="nav-link active" id="dpf-count-sum-tab" data-bs-toggle="pill" data-bs-target="#dpf-count-sum-tab-pane" type="button" role="tab" aria-controls="dpf-count-sum-tab-pane" aria-selected="true">Count Sum</button>
        <button class="nav-link" id="dpf-mp-count-waybill-tab" data-bs-toggle="pill" data-bs-target="#dpf-mp-count-waybill-tab-pane" type="button" role="tab" aria-controls="dpf-mp-count-waybill-tab-pane" aria-selected="false">MP Count Waybill</button>
        <button class="nav-link" id="dpf-mp-sum-biaya-kirim-tab" data-bs-toggle="pill" data-bs-target="#dpf-mp-sum-biaya-kirim-tab-pane" type="button" role="tab" aria-controls="dpf-mp-sum-biaya-kirim-tab-pane" aria-selected="false">MP Sum Biaya Kirim</button>
        <button class="nav-link" id="dpf-mp-retur-count-waybill-tab" data-bs-toggle="pill" data-bs-target="#dpf-mp-retur-count-waybill-tab-pane" type="button" role="tab" aria-controls="dpf-mp-retur-count-waybill-tab-pane" aria-selected="false">MP Retur Count Waybill</button>
        <button class="nav-link" id="dpf-mp-retur-sum-biaya-kirim-tab" data-bs-toggle="tab" data-bs-target="#dpf-mp-retur-sum-biaya-kirim-tab-pane" type="button" role="tab" aria-controls="dpf-mp-retur-sum-biaya-kirim-tab-pane" aria-selected="false">MP Retur Sum Biaya Kirim</button>
    </div>
    <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="dpf-count-sum-tab-pane" role="tabpanel" aria-labelledby="dpf-count-sum-tab" tabindex="0">

            <div class="row">
                <div class="container text-center table-responsive">
                    <h4>Summary Delivery Count Sum</h4>
                    <div class="row">
                        <div class="col-6 table-responsive">
                        <h5>All Count SUM DPF</h5>
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
                                        <td>{{decimal_format($item->count)}}</td>
                                        <td>Rp{{rupiah_format($item->sum)}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ decimal_format($total['cp_dp_all_count_sum_total_count']) }}</td>
                                    <td>Rp{{ rupiah_format($total['cp_dp_all_count_sum_total_sum']) }}</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <div class="col-6 table-responsive">
                        <h5>Reguler Count SUM DPF</h5>
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
                                        <td>{{decimal_format($item->count)}}</td>
                                        <td>Rp{{rupiah_format($item->sum)}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ decimal_format($total['cp_dp_reguler_count_sum_total_count']) }}</td>
                                    <td>Rp{{ rupiah_format($total['cp_dp_reguler_count_sum_total_sum']) }}</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <div class="col-6 table-responsive">
                        <h5>Dfod Count SUM DPF</h5>
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
                                        <td>{{decimal_format($item->count)}}</td>
                                        <td>Rp{{rupiah_format($item->sum)}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ decimal_format($total['cp_dp_dfod_count_sum_total_count']) }}</td>
                                    <td>Rp.{{ rupiah_format($total['cp_dp_dfod_count_sum_total_sum']) }}</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <div class="col-6 table-responsive">
                        <h5>Super Count SUM DPF</h5>
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
                                        <td>{{decimal_format($item->count)}}</td>
                                        <td>Rp{{rupiah_format($item->sum)}}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold border">
                                    <td style="text-align: left">Total</td>
                                    <td>{{ decimal_format($total['cp_dp_super_count_sum_total_count']) }}</td>
                                    <td>Rp{{ rupiah_format($total['cp_dp_super_count_sum_total_sum']) }}</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

      </div>
      <div class="tab-pane fade" id="dpf-mp-count-waybill-tab-pane" role="tabpanel" aria-labelledby="dpf-mp-count-waybill-tab" tabindex="0">

            <div class="row">
                <div class="container text-center table-responsive">
                    <h4>Summary Delivery MP Count Waybill</h4>
                </div>
            </div>

      </div>
      <div class="tab-pane fade" id="dpf-mp-sum-biaya-kirim-tab-pane" role="tabpanel" aria-labelledby="dpf-mp-sum-biaya-kirim-tab" tabindex="0">

            <div class="row">
                <div class="container text-center table-responsive">
                    <h4>Summary Delivery MP Sum Biaya Kirim</h4>
                </div>
            </div>

      </div>
      <div class="tab-pane fade" id="dpf-mp-retur-count-waybill-tab-pane" role="tabpanel" aria-labelledby="dpf-mp-retur-count-waybill-tab" tabindex="0">

            <div class="row">
                <div class="container text-center table-responsive">
                    <h4>Summary Delivery MP Retur Count Waybill</h4>
                </div>
            </div>

      </div>
      <div class="tab-pane fade" id="dpf-mp-retur-sum-biaya-kirim-tab-pane" role="tabpanel" aria-labelledby="dpf-mp-retur-sum-biaya-kirim-tab" tabindex="0">

            <div class="row">
                <div class="container text-center table-responsive">
                    <h4>Summary Delivery MP Retur Sum Biaya Kirim</h4>
                </div>
            </div>

      </div>
    </div>
  </div>

