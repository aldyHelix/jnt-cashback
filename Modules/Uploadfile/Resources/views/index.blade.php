<x-ladmin-auth-layout>
    <x-slot name="title">Upload File</x-slot>
    {{-- @can(['ladmin.admin.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.admin.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Add New</a>
    </x-slot>
    @endcan --}}
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-6">
                    {{-- @livewire('queue-button') --}}
                    {{-- button reload here --}}
                    {{-- auto reload --}}
                </div>
                <div class="col-6">
                    <div class="container text-end">
                        {{-- <a href="{{ route('ladmin.period.resi-checker') }}" class="btn btn-primary">Resi Checker</a> --}}
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCashback">Upload Data Cashback</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadTTD">Upload Delivery TTD</button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ \Modules\Uploadfile\Datatables\UploadfileDatatables::table() }}
            </div>

            <div class="modal fade" id="uploadCashback" tabindex="-1" aria-labelledby="uploadCashbackLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('ladmin.uploadfile.process.cashback') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="count_row">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Upload Data Cashback</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info" role="alert">
                                    <ul>
                                        <li>Please wait until the process done. don't close the window!</li>
                                        <li>Max File Size 100MB</li>
                                        <li>Max Row processed 500.000 row per file, recommend 100.000 row per file</li>
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">File</label>
                                    <input class="form-control" type="file" id="formFile" name="file" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Month</label>
                                    <select class="form-select" aria-label="Month" name="month_period" required>
                                        <option selected>Select Month</option>
                                        <option value="Jan">January</option>
                                        <option value="Feb">February</option>
                                        <option value="Mar">March</option>
                                        <option value="Apr">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="Augt">August</option>
                                        <option value="Sept">September</option>
                                        <option value="Oct">October</option>
                                        <option value="Nov">November</option>
                                        <option value="Des">December</option>
                                      </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Year</label>
                                    <select class="form-select" aria-label="Year" name="year_period" required>
                                        <option selected>Select Year</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_upload" class="btn btn-primary" onclick="loadingLoader()" disabled>Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="uploadTTD" tabindex="-1" aria-labelledby="uploadTTDLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('ladmin.uploadfile.process.delivery') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="count_row">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Upload Data Delivery TTD</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info" role="alert">
                                    Please wait until the process done. don't close the window!
                                </div>
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">File</label>
                                    <input class="form-control" type="file" id="formFileDelivery" name="file" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Month</label>
                                    <select class="form-select" aria-label="Month" name="month_period" required>
                                        <option selected>Select Month</option>
                                        <option value="Jan">January</option>
                                        <option value="Feb">February</option>
                                        <option value="Mar">March</option>
                                        <option value="Apr">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="Augt">August</option>
                                        <option value="Sept">September</option>
                                        <option value="Oct">October</option>
                                        <option value="Nov">November</option>
                                        <option value="Des">December</option>
                                        </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Year</label>
                                    <select class="form-select" aria-label="Year" name="year_period" required>
                                        <option selected>Select Year</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_upload_delivery" class="btn btn-primary" onclick="loadingLoaderDelivery()" disabled>Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script>
    </x-slot>
    <x-slot name="scripts">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script>
    </x-slot>

</x-ladmin-auth-layout>
