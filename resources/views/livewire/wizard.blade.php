<div>
    @if(!empty($successMessage))
        <div class="alert alert-success">
        {{ $successMessage }}
        </div>
    @endif

    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-circle {{ $currentStep != 1 ? 'btn-default' : 'btn-primary' }}">1</a>
                <p>Buat periode</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-circle {{ $currentStep != 2 ? 'btn-default' : 'btn-primary' }}">2</a>
                <p>Upload file</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-circle {{ $currentStep != 3 ? 'btn-default' : 'btn-primary' }}">3</a>
                <p>Setting global</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-4" type="button" class="btn btn-circle {{ $currentStep != 4 ? 'btn-default' : 'btn-primary' }}">4</a>
                <p>Setting Drop point</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-5" type="button" class="btn btn-circle {{ $currentStep != 5 ? 'btn-default' : 'btn-primary' }}">5</a>
                <p>Setting Grading 1</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-6" type="button" class="btn btn-circle {{ $currentStep != 6 ? 'btn-default' : 'btn-primary' }}">6</a>
                <p>Setting Grading 2</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-7" type="button" class="btn btn-circle {{ $currentStep != 7 ? 'btn-default' : 'btn-primary' }}">7</a>
                <p>Setting Grading 3</p>
            </div>

            <div class="stepwizard-step">
                <a href="#step-8" type="button" class="btn btn-circle {{ $currentStep != 8 ? 'btn-default' : 'btn-primary' }}">8</a>
                <p>Process</p>
            </div>
        </div>
    </div>

    <div class="container">
        <form wire:submit.prevent="process">
            <div class="row setup-content {{ $currentStep != 1 ? 'displayNone' : '' }} justify-content-md-center" id="step-1">
                <div class="card" style="padding: 10px; width: 300px;">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3> Buat Periode</h3>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Month</label>
                                <select class="form-select" aria-label="Month" wire:model="month" wire:loading.attr="disabled" required>
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
                                @error('month') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Year</label>
                                <select class="form-select" aria-label="Year" wire:model="year" wire:loading.attr="disabled" required>
                                    <option selected>Select Year</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                                @error('year') <span class="error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div wire:loading>
                                <a class="btn btn-primary nextBtn pull-right disabled"> <i class="fa fa-spinner"></i></a>
                            </div>

                            <div wire:loading.remove>
                                <a class="btn btn-primary nextBtn pull-right" wire:click="firstStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 2 ? 'displayNone' : '' }}" id="step-2">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12" style="padding: 10px;">
                            <h3> Upload File mentah</h3>

                            <input id="files" wire:model="files" type="file" class="file"  data-show-upload="true" data-show-caption="true" multiple>
                            {{-- will used if using golang micro <services></services>
                                <div id="dropzone">
                                <form class="dropzone needsclick" id="demo-upload" action="/upload">
                                <div class="dz-message needsclick">
                                    Drop files here or click to upload.<BR>
                                </div>
                                </form>
                            </div> --}}
                            @error('files.*') <span class="error">{{ $message }}</span> @enderror

                        </div>
                        <div class="col-md-12">
                            <div wire:loading>
                                <a class="btn btn-primary nextBtn pull-right disabled"> <i class="fa fa-spinner"></i></a>
                            </div>

                            <div wire:loading.remove>
                                <a class="btn btn-primary nextBtn pull-right" wire:click="secondStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></a>
                                <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(1)">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 3 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12" style="padding: 10px;">
                            <h3> Setting Global</h3>
                                <div class="row">
                                    <div class="col-9">
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
                                                        @foreach ($list_klien_pengiriman as $i => $item)
                                                        @php
                                                            $item_category = $item->category->pluck('id')->toArray();
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $i }}</td>
                                                            <td>{{ $item->klien_pengiriman != '' ? $item->klien_pengiriman : '(blank)'}}</td>
                                                            @foreach ($category as $cat)
                                                                <td><input class="form-check-input" type="checkbox" wire:model.defer="global_klien_pengiriman.{{ $item->id }}.{{$cat->id}}" value="{{$cat->id}}" id="flexCheckDefault">
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>
                                </div>
                        </div>

                        <button class="btn btn-success pull-right" wire:click="thirdStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></button>
                        <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(2)">Back</button>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 4 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <h3> Setting Drop Point Outgoing</h3>
                    <div class="row justify-content-md-center">
                        <div class="col-9">
                            <div class="table-responsive" style="max-height: 500px;">
                                <table id="myTable" class="table table-striped table-hover">
                                    <thead class="sticky-top">
                                        <tr>
                                            <td>Nama DP</td>
                                            <td>Pengurangan</td>
                                            <td>Penambahan</td>
                                            <td>Diskon COD</td>
                                        </tr>
                                    </thead>
                                    <tbody style="overflow-y: auto;">
                                        @foreach ($dp as $key => $item)
                                        <input type="hidden" name="dp_setting.{{ $key }}.drop_point_outgoing" wire:model="dp_setting.{{ $key }}.drop_point_outgoing" value="{{ $item->drop_point_outgoing }}">
                                        <tr>
                                            <td style="text-align: left;width: 300px;">{{ $item->drop_point_outgoing }} </td>
                                            <td>
                                                <input id="pengurangan_total" type="number" min="0" class="col" wire:model="dp_setting.{{ $key }}.pengurangan_total" value="0" placeholder="Pengurangan Total" />
                                            </td>
                                            <td>
                                                <input id="penambahan_total" type="number" min="0" class="col" wire:model="dp_setting.{{ $key }}.penambahan_total" value="0" placeholder="Pemambahan  Total" />
                                            </td>
                                            <td>
                                                <input id="diskon_cod" type="number" min="0" class="col" wire:model="dp_setting.{{ $key }}.diskon_cod" value="0" placeholder="Diskon COD" />
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button class="btn btn-success pull-right" wire:click="fourthStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></button>
                            <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(3)">Back</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 5 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3> Setting Denda Grading 1</h3>

                            <div class="table-responsive grading">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" style="max-width:300px">CP</th>
                                    <th scope="col">Transit Fee</th>
                                    <th scope="col">Denda Void</th>
                                    <th scope="col">Denda dfod</th>
                                    <th scope="col">Denda Pusat</th>
                                    <th scope="col">Denda Selisih Berat</th>
                                    <th scope="col">Denda Lost Scan Kirim</th>
                                    <th scope="col">Denda Auto Claim</th>
                                    <th scope="col">Denda Sponsorship</th>
                                    <th scope="col">Denda Late Pickup</th>
                                    <th scope="col">Potongan POP</th>
                                    <th scope="col">Denda Lainnya</th>
                                </tr>
                                </thead>
                                <tbody style="overflow-y: auto;">
                                    @foreach ($cp1 as $key => $item)
                                    <input type="hidden" wire:model="g1.{{ $key }}.id" value={{$item->id}}>
                                    <input type="hidden" wire:model="g1.{{ $key }}.kode_cp" value={{$item->kode_cp}}>
                                    <tr>
                                        <td scope="row">{{ $item->kode_cp }}</td>
                                        <td style="width: 300px;">{{ $item->nama_cp}}</td>
                                        <td><input id="transit_fee" type="text" class="col" required wire:model="g1.{{ $key }}.transit_fee"
                                            value="0" placeholder="Transit Fee" /></td>
                                        <td><input id="denda_void" type="text" class="col" required wire:model="g1.{{ $key }}.denda_void"
                                            value="0" placeholder="Denda Void" /></td>
                                        <td><input id="denda_dfod" type="text" class="col" required wire:model="g1.{{ $key }}.denda_dfod"
                                            value="0" placeholder="Denda DFOD" /></td>
                                        <td><input id="denda_pusat" type="text" class="col" required wire:model="g1.{{ $key }}.denda_pusat"
                                            value="0" placeholder="Denda Pusat" /></td>
                                        <td><input id="denda_selisih_berat" type="text" class="col" required wire:model="g1.{{ $key }}.denda_selisih_berat"
                                            value="0" placeholder="Denda Selisih Berat" /></td>
                                        <td><input id="denda_lost_scan_kirim" type="text" class="col" required wire:model="g1.{{ $key }}.denda_lost_scan_kirim" value="0" placeholder="Denda Lost Scan Kirim" /></td>
                                        <td><input id="denda_auto_claim" type="text" class="col" required wire:model="g1.{{ $key }}.denda_auto_claim"
                                            value="0" placeholder="Denda Auto Claim" /></td>
                                        <td><input id="denda_sponsorship" type="text" class="col" required wire:model="g1.{{ $key }}.denda_sponsorship"
                                            value="0" placeholder="Denda Sponsorship" /></td>
                                        <td><input id="denda_late_pickup_ecommerce" type="text" class="col" required wire:model="g1.{{ $key }}.denda_late_pickup_ecommerce"
                                            value="0" placeholder="Denda Late Pickup Ecommerce" /></td>
                                        <td><input id="potongan_pop" type="text" class="col" required wire:model="g1.{{ $key }}.potongan_pop"
                                            value="0" placeholder="Denda Potongan POP" /></td>
                                        <td><input id="denda_lainnya" type="text" class="col" required wire:model="g1.{{ $key }}.denda_lainnya"
                                            value="0" placeholder="Denda Lainnya" /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <button class="btn btn-success pull-right" wire:click="fifthStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></button>
                        <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(4)">Back</button>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 6 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3> Setting Denda Grading 2</h3>
                            <div class="table-responsive grading" style="max-height: 500px;">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" style="width: 300px;">CP</th>
                                    <th scope="col">Transit Fee</th>
                                    <th scope="col">Denda Void</th>
                                    <th scope="col">Denda dfod</th>
                                    <th scope="col">Denda Pusat</th>
                                    <th scope="col">Denda Selisih Berat</th>
                                    <th scope="col">Denda Lost Scan Kirim</th>
                                    <th scope="col">Denda Auto Claim</th>
                                    <th scope="col">Denda Sponsorship</th>
                                    <th scope="col">Denda Late Pickup</th>
                                    <th scope="col">Potongan POP</th>
                                    <th scope="col">Denda Lainnya</th>
                                </tr>
                                </thead>
                                <tbody style="overflow-y: auto;">
                                    @foreach ($cp2 as $key => $item)
                                    <input type="hidden" wire:model="g2.{{ $key }}.id" value={{$item->id}}>
                                    <input type="hidden" wire:model="g2.{{ $key }}.kode_cp" value={{$item->kode_cp}}>
                                    <tr>
                                        <th scope="row">{{ $item->kode_cp }}</th>
                                        <td style="width: 300px;">{{ $item->nama_cp}}</td>
                                        <td><input id="transit_fee" type="text" class="col" required wire:model="g2.{{ $key }}.transit_fee"
                                            value="0" placeholder="Transit Fee" /></td>
                                        <td><input id="denda_void" type="text" class="col" required wire:model="g2.{{ $key }}.denda_void"
                                            value="0" placeholder="Denda Void" /></td>
                                        <td><input id="denda_dfod" type="text" class="col" required wire:model="g2.{{ $key }}.denda_dfod"
                                            value="0" placeholder="Denda DFOD" /></td>
                                        <td><input id="denda_pusat" type="text" class="col" required wire:model="g2.{{ $key }}.denda_pusat"
                                            value="0" placeholder="Denda Pusat" /></td>
                                        <td><input id="denda_selisih_berat" type="text" class="col" required wire:model="g2.{{ $key }}.denda_selisih_berat"
                                            value="0" placeholder="Denda Selisih Berat" /></td>
                                        <td><input id="denda_lost_scan_kirim" type="text" class="col" required wire:model="g2.{{ $key }}.denda_lost_scan_kirim" value="0" placeholder="Denda Lost Scan Kirim" /></td>
                                        <td><input id="denda_auto_claim" type="text" class="col" required wire:model="g2.{{ $key }}.denda_auto_claim"
                                            value="0" placeholder="Denda Auto Claim" /></td>
                                        <td><input id="denda_sponsorship" type="text" class="col" required wire:model="g2.{{ $key }}.denda_sponsorship"
                                            value="0" placeholder="Denda Sponsorship" /></td>
                                        <td><input id="denda_late_pickup_ecommerce" type="text" class="col" required wire:model="g2.{{ $key }}.denda_late_pickup_ecommerce"
                                            value="0" placeholder="Denda Late Pickup Ecommerce" /></td>
                                        <td><input id="potongan_pop" type="text" class="col" required wire:model="g2.{{ $key }}.potongan_pop"
                                            value="0" placeholder="Denda Potongan POP" /></td>
                                        <td><input id="denda_lainnya" type="text" class="col" required wire:model="g2.{{ $key }}.denda_lainnya"
                                            value="0" placeholder="Denda Lainnya" /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <button class="btn btn-success pull-right" wire:click="sixthStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></button>
                        <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(5)">Back</button>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 7 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <div wire:loading.remove>
                                <h3> Setting Denda Grading 3</h3>
                                <div class="table-responsive grading" style="max-height: 500px;">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" style="width: 300px;">CP</th>
                                        <th scope="col">Transit Fee</th>
                                        <th scope="col">Denda Void</th>
                                        <th scope="col">Denda dfod</th>
                                        <th scope="col">Denda Pusat</th>
                                        <th scope="col">Denda Selisih Berat</th>
                                        <th scope="col">Denda Lost Scan Kirim</th>
                                        <th scope="col">Denda Auto Claim</th>
                                        <th scope="col">Denda Sponsorship</th>
                                        <th scope="col">Denda Late Pickup</th>
                                        <th scope="col">Potongan POP</th>
                                        <th scope="col">Denda Lainnya</th>
                                    </tr>
                                    </thead>
                                    <tbody style="overflow-y: auto;">
                                        @foreach ($cp3 as $key => $item)
                                        <input type="hidden" wire:model="g3.{{ $key }}.id" value={{$item->id}}>
                                        <input type="hidden" wire:model="g3.{{ $key }}.kode_cp" value={{$item->kode_cp}}>
                                        <tr>
                                            <th scope="row">{{ $item->kode_cp }}</th>
                                            <td style="width: 300px;">{{ $item->nama_cp}}</td>
                                            <td><input id="transit_fee" type="text" class="col" required wire:model="g3.{{ $key }}.transit_fee"
                                                value="0" placeholder="Transit Fee" /></td>
                                            <td><input id="denda_void" type="text" class="col" required wire:model="g3.{{ $key }}.denda_void"
                                                value="0" placeholder="Denda Void" /></td>
                                            <td><input id="denda_dfod" type="text" class="col" required wire:model="g3.{{ $key }}.denda_dfod"
                                                value="0" placeholder="Denda DFOD" /></td>
                                            <td><input id="denda_pusat" type="text" class="col" required wire:model="g3.{{ $key }}.denda_pusat"
                                                value="0" placeholder="Denda Pusat" /></td>
                                            <td><input id="denda_selisih_berat" type="text" class="col" required wire:model="g3.{{ $key }}.denda_selisih_berat"
                                                value="0" placeholder="Denda Selisih Berat" /></td>
                                            <td><input id="denda_lost_scan_kirim" type="text" class="col" required wire:model="g3.{{ $key }}.denda_lost_scan_kirim" value="0" placeholder="Denda Lost Scan Kirim" /></td>
                                            <td><input id="denda_auto_claim" type="text" class="col" required wire:model="g3.{{ $key }}.denda_auto_claim"
                                                value="0" placeholder="Denda Auto Claim" /></td>
                                            <td><input id="denda_sponsorship" type="text" class="col" required wire:model="g3.{{ $key }}.denda_sponsorship"
                                                value="0" placeholder="Denda Sponsorship" /></td>
                                            <td><input id="denda_late_pickup_ecommerce" type="text" class="col" required wire:model="g3.{{ $key }}.denda_late_pickup_ecommerce"
                                                value="0" placeholder="Denda Late Pickup Ecommerce" /></td>
                                            <td><input id="potongan_pop" type="text" class="col" required wire:model="g3.{{ $key }}.potongan_pop"
                                                value="0" placeholder="Denda Potongan POP" /></td>
                                            <td><input id="denda_lainnya" type="text" class="col" required wire:model="g3.{{ $key }}.denda_lainnya"
                                                value="0" placeholder="Denda Lainnya" /></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div wire:loading>
                                <a class="btn btn-primary nextBtn pull-right disabled"> <i class="fa fa-spinner"></i></a>
                            </div>

                            <div wire:loading.remove>
                                <a class="btn btn-primary nextBtn pull-right" wire:click="seventhStepSubmit" type="button">Next <i class="fas fa-arrow-right"></i></a>
                                <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(6)">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row setup-content {{ $currentStep != 8 ? 'displayNone' : '' }}" id="step-3">
                <div class="card" style="padding: 10px;">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3> PROCESS PERIODE {{ strtoupper($month) }} {{ $year }}</h3>

                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                                <div>
                                Anda tidak dapat membatalkan proses ini jika sudah dimulai. <br>
                                Lakukan pengecekan Resi yang tidak terinput & error secara berkala setiap periodenya.<br>
                                Lalu proses ulang resi yang tidak terinput menggunakan CSV yang berbeda. <br>
                                anda dapat melakukan pengecekan proses secara detail <a href="/horizon">disini.</a>
                                </div>
                            </div>

                            @include('processwizard::partials._process', [
                                'state' => $state,
                                'drop_point_setting' => $drop_point_setting,
                                'grading_1_setting' => $grading_1_setting,
                                'grading_2_setting' => $grading_2_setting,
                                'grading_3_setting' => $grading_3_setting,
                                'klien_pengiriman_setting' => $klien_pengiriman_setting,
                                'category' => $category
                            ])

                            {{-- status state nanti disini --}}
                        </div>

                        <button class="btn btn-secondary pull-right" wire:click="$emit('refreshComponent')"><i class="fas fa-refresh"></i> Refresh</button>
                        <a data-bs-toggle="modal" data-bs-target="#modal-process-confirm" class="btn btn-primary"><i class="fas fa-gears"></i> Proses</a>
                        <a class="btn btn-secondary pull-right" > Summary </a>
                        <a class="btn btn-success pull-right" > Report </a>
                        <a class="btn btn-primary pull-right" href="{{ route('ladmin.processwizard.index') }}" type="button">Tutup </a>
                        {{-- <button class="btn btn-danger nextBtn pull-right" type="button" wire:click="back(2)">Back</button> --}}
                    </div>
                </div>
            </div>
            <x-ladmin-modal id="modal-process-confirm" class="text-start">
                <x-slot name="title">Proses Data</x-slot>
                <x-slot name="body">
                    Apakah anda ingin memproses data?
                </x-slot>
                <x-slot name="footer">
                    <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan
                    </x-ladmin-button>
                    <x-ladmin-button type="submit" class="text-white" color="danger" wire:click="process" data-bs-dismiss="modal">Proses</x-ladmin-button>
                </x-slot>
            </x-ladmin-modal>
        </form>
    </div>

    <style>
        .table-grading {
            border-collapse: separate;
            border-spacing: 0;
            border-top: 1px solid grey;
        }

        .grading {
            height: 500px;
            overflow-x: auto;
            overflow-y: visible;
        }

    </style>
</div>

