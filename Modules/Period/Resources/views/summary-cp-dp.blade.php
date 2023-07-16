<div class="d-flex align-items-start">
    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <button class="nav-link active" id="cp-dp-count-sum-tab" data-bs-toggle="pill" data-bs-target="#cp-dp-count-sum-tab-pane" type="button" role="tab" aria-controls="cp-dp-count-sum-tab-pane" aria-selected="true">Count Sum</button>
        <button class="nav-link" id="cp-dp-mp-count-waybill-tab" data-bs-toggle="pill" data-bs-target="#cp-dp-mp-count-waybill-tab-pane" type="button" role="tab" aria-controls="cp-dp-mp-count-waybill-tab-pane" aria-selected="false">MP Count Waybill</button>
        <button class="nav-link" id="cp-dp-mp-sum-biaya-kirim-tab" data-bs-toggle="pill" data-bs-target="#cp-dp-mp-sum-biaya-kirim-tab-pane" type="button" role="tab" aria-controls="cp-dp-mp-sum-biaya-kirim-tab-pane" aria-selected="false">MP Sum Biaya Kirim</button>
        <button class="nav-link" id="cp-dp-mp-retur-count-waybill-tab" data-bs-toggle="pill" data-bs-target="#cp-dp-mp-retur-count-waybill-tab-pane" type="button" role="tab" aria-controls="cp-dp-mp-retur-count-waybill-tab-pane" aria-selected="false">MP Retur Count Waybill</button>
        <button class="nav-link" id="cp-dp-mp-retur-sum-biaya-kirim-tab" data-bs-toggle="tab" data-bs-target="#cp-dp-mp-retur-sum-biaya-kirim-tab-pane" type="button" role="tab" aria-controls="cp-dp-mp-retur-sum-biaya-kirim-tab-pane" aria-selected="false">MP Retur Sum Biaya Kirim</button>
    </div>
    <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="cp-dp-count-sum-tab-pane" role="tabpanel" aria-labelledby="cp-dp-count-sum-tab" tabindex="0">
            <div class="row">
                <h4>Summary CP DP Count Sum</h4>
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
        <div class="tab-pane fade" id="cp-dp-mp-count-waybill-tab-pane" role="tabpanel" aria-labelledby="cp-dp-mp-count-waybill-tab" tabindex="0">
                    <div class="container text-center table-responsive ">
                        <h4>Summary CP DP MP Count Waybill</h4>
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
                                @foreach ($cp_dp_mp_count_waybill as $item)
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
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('akulakuob'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('bukaexpress'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('bukalapak'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('bukasend'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('evermosapi'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('lazada'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('lazada_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('magellan'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('magellan_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('mengantar'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('ordivo'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('shopee'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('shopee_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('tokopedia'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_count_waybill->sum('grand_total'))}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

        </div>
        <div class="tab-pane fade" id="cp-dp-mp-sum-biaya-kirim-tab-pane" role="tabpanel" aria-labelledby="cp-dp-mp-sum-biaya-kirim-tab" tabindex="0">

                <div class="row">
                    <div class="container text-center table-responsive">
                        <h4>Summary CP DP MP Sum Biaya Kirim</h4>
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
                                @foreach ($cp_dp_mp_sum_biaya_kirim as $item)
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
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('akulakuob'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('bukaexpress'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('bukalapak'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('bukasend'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('evermosapi'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('lazada'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('lazada_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('magellan'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('magellan_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('mengantar'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('ordivo'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('shopee'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('shopee_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('tokopedia'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_sum_biaya_kirim->sum('grand_total'))}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>
        <div class="tab-pane fade" id="cp-dp-mp-retur-count-waybill-tab-pane" role="tabpanel" aria-labelledby="cp-dp-mp-retur-count-waybill-tab" tabindex="0">

                <div class="row">
                    <div class="container text-center table-responsive">
                        <h4>Summary CP DP MP Retur Count Waybill</h4>
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
                                @foreach ($cp_dp_mp_retur_count_waybill as $item)
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
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('akulakuob'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('bukaexpress'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('bukalapak'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('bukasend'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('evermosapi'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('lazada'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('lazada_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('magellan'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('magellan_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('mengantar'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('ordivo'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('shopee'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('shopee_cod'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('tokopedia'))}}</td>
                                    <td>{{decimal_format($cp_dp_mp_retur_count_waybill->sum('grand_total'))}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>
        <div class="tab-pane fade" id="cp-dp-mp-retur-sum-biaya-kirim-tab-pane" role="tabpanel" aria-labelledby="cp-dp-mp-retur-sum-biaya-kirim-tab" tabindex="0">

                <div class="row">
                    <div class="container text-center table-responsive">
                        <h4>Summary CP DP MP Retur Sum Biaya Kirim</h4>
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
                                @foreach ($cp_dp_mp_retur_sum_biaya_kirim as $item)
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
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('akulakuob'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('bukaexpress'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('bukalapak'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('bukasend'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('evermosapi'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('lazada'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('lazada_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('magellan'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('magellan_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('mengantar'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('ordivo'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('shopee'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('shopee_cod'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('tokopedia'))}}</td>
                                    <td>Rp{{rupiah_format($cp_dp_mp_retur_sum_biaya_kirim->sum('grand_total'))}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>
    </div>
</div>

