<x-ladmin-auth-layout>
    <x-slot name="title">Home</x-slot>


    <div class="row justify-content-center">
        <div class="col-lg-3 text-center">
            <x-ladmin-card class="mb-3">
                <x-slot name="body">
                    <div class="mb-3">
                        <img src="{{ $user->gravatar }}" alt="Avatar" width="150"
                            class="mb-3 img-thumbnail rounded-circle">
                        <h5>{{ $user->name }}</h5>
                        <p>
                            <small class="text-muted">{{ $user->roles->pluck('name')->join(', ') }}</small>
                        </p>
                    </div>

                    <a href="{{ route('ladmin.profile.edit', ladmin()->back()) }}"
                        class="btn btn-outline-primary btn-sm w-50">Edit
                        Profile</a>

                    <a class="btn btn-outline-primary btn-sm" href="" data-bs-toggle="modal"
                        data-bs-target="#modal-logout">
                        <i class="fas fa-power-off"></i>
                    </a>
                </x-slot>
            </x-ladmin-card>

            <x-ladmin-card class="mb-3">
                <x-slot name="body">
                    <h5 class="card-title">Process Online</h5>

                    <div class="d-flex align-items-center">
                        <div class="mx-3">
                            <i class="fa-solid fa-cogs fa-3x text-primary"></i>
                        </div>
                        <div class="mx-3 flex-grow-1">
                            @livewire('queue-status')
                        </div>
                    </div>

                </x-slot>
            </x-ladmin-card>

            <x-ladmin-card class="mb-3">
                <x-slot name="body">
                    <h5 class="card-title">Total Time Processing</h5>

                    <div class="d-flex align-items-center">
                        <div class="mx-3">
                            <i class="fa-solid fa-clock fa-3x text-primary"></i>
                        </div>
                        <div data-role="ajax"
                            data-route="{{ route('ladmin.index', ['ajax' => 'load_avg_processing']) }}">
                        </div>
                    </div>

                </x-slot>
            </x-ladmin-card>

            <x-ladmin-card class="mb-3">
                <x-slot name="body">
                    <h5 class="card-title">Last Uploaded File</h5>

                    <div class="d-flex align-items-center">
                        <div class="mx-3">
                            <i class="fa-solid fa-upload fa-3x text-primary"></i>
                        </div>
                        <div data-role="ajax"
                            data-route="{{ route('ladmin.index', ['ajax' => 'latest_upload_file']) }}">
                        </div>
                    </div>

                </x-slot>
            </x-ladmin-card>
        </div>
        <div class="col-lg-9">
            <x-ladmin-card class="mb-3">
                <x-slot name="body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="caard-title fw-bold">Welcome to {{ config('app.name') }}</h5>
                        <div class="text-muted">{{ now()->format('D m d, Y') }}</div>
                    </div>
                    <p>{!! $inspire !!}</p>
                </x-slot>
            </x-ladmin-card>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">Admin Online</h5>

                            <div class="d-flex align-items-center">
                                <div class="mx-3">
                                    <i class="fa-solid fa-earth-asia fa-3x text-primary"></i>
                                </div>
                                <div class="mx-3 flex-grow-1">
                                    <h1 data-role="ajax"
                                        data-route="{{ route('ladmin.index', ['ajax' => 'load_total_online']) }}">
                                    </h1>
                                </div>
                            </div>

                        </x-slot>
                    </x-ladmin-card>
                </div>
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">Admin Online</h5>
                            <div data-role="ajax"
                                data-route="{{ route('ladmin.index', ['ajax' => 'load_percenteage_online']) }}">
                            </div>
                        </x-slot>
                    </x-ladmin-card>
                </div>
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">Admin Total</h5>
                            <h1 data-role="ajax"
                                data-route="{{ route('ladmin.index', ['ajax' => 'load_total_admin']) }}"></h1>
                        </x-slot>
                    </x-ladmin-card>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">Periode Created</h5>

                            <div class="d-flex align-items-center">
                                <div class="mx-3">
                                    <i class="fa-solid fa-line-chart fa-3x text-primary"></i>
                                </div>
                                <div class="mx-3 flex-grow-1">
                                    <h1 data-role="ajax"
                                        data-route="{{ route('ladmin.index', ['ajax' => 'load_total_period']) }}">
                                    </h1>
                                </div>
                            </div>

                        </x-slot>
                    </x-ladmin-card>
                </div>
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">Collection Point </h5>
                            <div class="d-flex align-items-center">
                                <div class="mx-3">
                                    <i class="fa-solid fa-map-pin fa-3x text-primary"></i>
                                </div>
                                <div class="mx-3 flex-grow-1">
                                    <h1 data-role="ajax"
                                        data-route="{{ route('ladmin.index', ['ajax' => 'load_total_collection_point']) }}">
                                    </h1>
                                </div>
                            </div>
                        </x-slot>
                    </x-ladmin-card>
                </div>
                <div class="col-lg-4">
                    <x-ladmin-card class="mb-3">
                        <x-slot name="body">
                            <h5 class="card-title">File Uploaded</h5>
                            <div class="d-flex align-items-center">
                                <div class="mx-3">
                                    <i class="fa-solid fa-file-upload fa-3x text-primary"></i>
                                </div>
                                <div class="mx-3 flex-grow-1">
                                <h1 data-role="ajax"
                                    data-route="{{ route('ladmin.index', ['ajax' => 'load_total_file_upload']) }}"></h1>
                                </div>
                            </div>
                        </x-slot>
                    </x-ladmin-card>
                </div>
            </div>

            <div class="mb-3">
                <x-ladmin-card class="mb-3">
                    <x-slot name="body">
                        <h5 class="card-title">Your Coworkers</h5>

                        <div class="table-responsive" data-role="ajax"
                            data-route="{{ route('ladmin.index', ['ajax' => 'load_table_coworkers']) }}">

                        </div>
                    </x-slot>
                </x-ladmin-card>
            </div>

            <div class="mb-3">
                <x-ladmin-card class="mb-3">
                    <x-slot name="body">
                        <h5 class="card-title">Period chart</h5>

                        <div>
                            <div id="myChart"></div>
                          </div>
                    </x-slot>
                </x-ladmin-card>
            </div>
        </div>
    </div>



    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <script>
        const ctx = document.getElementById('myChart');
        var monthNames = @json(array_keys($period));
        var monthValues = @json(array_values($period));
        var options = {
            chart: {
                type: 'line'
            },
            series: [{
                name: 'Total biaya kirim',
                data: monthValues
            }],
            xaxis: {
                categories: monthNames
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return formatCurrency(value);
                    }
                }
            },
            tooltip: {
                            style: {
                                fontSize: '12px'
                            },
                            y: {
                                formatter: function (val) {
                                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumSignificantDigits: 12 }).format(val) ;
                                }
                            }
                        },
            };

            var chart = new ApexCharts(document.querySelector("#myChart"), options);

            chart.render();



        function formatCurrency(value) {
                if (value >= 1000000000) {
                    return (value / 1000000000).toFixed(1) + ' m';
                } else if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + ' jt';
                } else if (value >= 1000) {
                    return (value / 1000).toFixed(1) + ' rb';
                } else {
                    return value;
                }
            }
        </script>

    </x-slot>

</x-ladmin-auth-layout>
