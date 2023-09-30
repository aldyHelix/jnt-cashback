<!-- Main -->
<main class="py-6 bg-surface-secondary">
    <div class="container-fluid">
        <!-- Card stats -->
        <div class="row g-6 m-6">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">File Processed</span>
                                <span class="h3 font-bold mb-0">5 dari 10</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                    <i class="fas fa-files"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">file yang sedang di proses</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Baris</span>
                                <span class="h3 font-bold mb-0">215</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">Total baris yang terimport</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Estimasi Pemprosesan</span>
                                <span class="h3 font-bold mb-0">1.400 Jam</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-info text-white text-lg rounded-circle">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">Total Waktu pemprosesan</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Work load</span>
                                <span class="h3 font-bold mb-0">95%</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                    <i class="bi bi-minecart-loaded"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">Persentase Proses Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row g-6 mb-6">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Jumlah Resi Error</span>
                                <span class="h3 font-bold mb-0">95</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                    <i class="bi bi-minecart-loaded"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">Resi Error</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Resi Tidak Terinput</span>
                                <span class="h3 font-bold mb-0">95</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                    <i class="bi bi-minecart-loaded"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-0 text-sm">
                            <span class="text-nowrap text-xs text-muted">Resi tidak terinput</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        @if($state)
        <div class="row g-6 mb-6">
            <div class="col-xl-12 col-sm-12 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        @foreach ($state as $key=>$item)
                            @if($key != 0)
                                <i class="fas fa-{{ intval($item) ? 'square-check' : 'square-minus'}}"></i>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <br>
        @endif
        <div class="row g-6 mb-6">
            <div class="col-xl-12 col-sm-12 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        {{-- @if($klien_pengiriman_setting)
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Klien Pengiriman</h5>
                                <div class="table-responsive" style="max-height: 700px;">
                                    <table id="myTable" class="table table-striped table-hover">
                                        <thead class="sticky-top">
                                            <tr>
                                                <td scope="col">#</td>
                                                <td scope="col">Klien Pengiriman</td>
                                                @foreach ($category as $item)
                                                    <td scope="col">{{ $item->nama_kategori}}</a></td>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody style="overflow-y: auto;">
                                            @foreach ($klien_pengiriman_setting as $i => $item)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $item['klien_pengiriman_id'] }}</td>
                                                @foreach ($category as $cat)
                                                    <td>{{ $cat->id == $item['category_id'] }}</td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @dump($klien_pengiriman_setting)
                            </div>
                        </div>
                        @endif --}}
                        @if($drop_point_setting)
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Setting Drop Point Outgoing</h5>
                                <div class="table-responsive" style="max-height: 700px;">
                                    <table id="myTable" class="table table-striped table-hover">
                                        <thead class="sticky-top">
                                            <tr>
                                                <td scope="col">#</td>
                                                <td scope="col">Drop Point Outgoing</td>
                                                <td>Pengurangan</td>
                                                <td>Penambahan</td>
                                                <td>Diskon COD</td>
                                            </tr>
                                        </thead>
                                        <tbody style="overflow-y: auto;">
                                            @foreach ($drop_point_setting as $i => $item)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $item['drop_point_outgoing'] }}</td>
                                                <td>{{ $item['pengurangan_total'] }}</td>
                                                <td>{{ $item['penambahan_total'] }}</td>
                                                <td>{{ $item['diskon_cod'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- @if($grading_1_setting)
                        <div class="row">
                            <div class="col-sm-12">
                                @dump($grading_1_setting)
                            </div>
                        </div>
                        @endif
                        @if($grading_2_setting)
                        <div class="row">
                            <div class="col-sm-12">
                                @dump($grading_2_setting)
                            </div>
                        </div>
                        @endif
                        @if($grading_3_setting)
                        <div class="row">
                            <div class="col-sm-12">
                                @dump($grading_3_setting)
                            </div>
                        </div>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>

</main>
