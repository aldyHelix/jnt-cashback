<?php

namespace Modules\Uploadfile\Http\Controllers;

use App\Facades\CreateSchema;
use App\Facades\GenerateDPF;
use App\Facades\GeneratePivot;
use App\Facades\GeneratePivotRekap;
use App\Facades\GenerateRekapLuarZona;
use App\Facades\GenerateSummary;
use App\Facades\GradingProcess;
use App\Imports\CashbackImport;
use App\Jobs\ProcessCSVData;
use App\Jobs\ProcessCSVDataDelivery;
use App\Jobs\ProcessCSVDataDeliveryOptimized;
use App\Jobs\ProcessCSVDataOptimized;
use App\Models\Cashback;
use App\Models\FileJobs;
use App\Models\Periode;
use App\Models\PeriodeDelivery;
use Illuminate\Bus\Batch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Uploadfile\Datatables\UploadfileDatatables;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Modules\Uploadfile\Models\Uploadfile;
use Throwable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;
use App\Models\PeriodeKlienPengiriman;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\File;
use PhpOffice\PhpSpreadsheet\Reader;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ladmin()->allows(['ladmin.uploadfile.index']);

        if(request()->has('datatables')) {
            return UploadfileDatatables::renderData();
        }

        return view('uploadfile::index');
    }

    public function uploadFileDelivery(Request $request)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|max:102400', //max 100MB
                'month_period' => 'required',
                'year_period' => 'required',
            ]);

            if(!$validated){
                toastr()->error('The file may too big or not CSV format', 'Opps!');

                return redirect()->back();
            }

            $schema_name = 'delivery_' . strtolower($request->month_period) . '_' . $request->year_period;
            $csv    = file($request->file);

            //i need to convert this file to UTF-8 Encoding
            foreach ($csv as $cellIndex => $cell) {
                $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
                $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';

                $csv[$cellIndex] = $cell;
            }

            $chunks = array_chunk($csv, 500);
            $count_csv = (count($csv) - 1);
            $timeout = intval($count_csv * 0.5);

            if(!Schema::hasTable($schema_name . '.' . 'data_mart')) {
                $schema = CreateSchema::createSchemaDelivery(strtolower($request->month_period), $request->year_period);

                CreateSchema::DeliveryPivot($schema_name);
            }

            $file = $request->file('file');


            $uploaded_file = Uploadfile::create([
                'file_name' => $file->getClientOriginalName(),
                'month_period' => $request->month_period,
                'year_period' => $request->year_period,
                'count_row' => $count_csv,
                'file_size' => $file->getSize(),
                'table_name' => $schema_name . '.' . 'data_mart',
                'processed_by' => auth()->user()->id,
                'type_file' => 1, //0 cashback; 1 ttd;
                'processing_status' => 'ON QUEUE',
            ]);

            $queue_name = 'QUEUE DELIVERY : ' . $file->getClientOriginalName() . ';SCHEMA : ' . $schema_name . ';TIME UPLOAD : ' . $uploaded_file->created_at;

            if($uploaded_file) {
                $existing_periode = PeriodeDelivery::where('code', $schema_name)->first();


                if(!$existing_periode) {
                    $existing_periode = PeriodeDelivery::create([
                        'code' => $schema_name,
                        'month' => $request->month_period,
                        'year' => $request->year_period,
                        'count_row' => $count_csv,
                        'status' => 'ON QUEUE',
                    ]);

                    $period_id = $existing_periode->id;
                } else {
                    $existing_periode->update([
                        'count_row' => $existing_periode->count_row + $count_csv
                    ]);
                    $period_id = $existing_periode->id;
                }

                $batch  = Bus::batch([])
                        ->then(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                            $uploaded_file->update([
                                'processing_status' => 'FINISHED'
                            ]);

                            $done = $existing_periode->update([
                                'count_row' => $existing_periode->count_row + $count_csv,
                                'status' => 'FINISHED',
                            ]);

                            ladmin()
                                ->notification()
                                    ->setTitle($uploaded_file->file_name . ': TTD Done Import!')
                                    ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                    ->setDescription($uploaded_file->file_name . " : Has finished imported")
                                ->send();
                        })
                        ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                            // First batch job failure detected...
                            $uploaded_file->update(['processing_status' => 'FAILED']);

                            ladmin()
                                ->notification()
                                    ->setTitle($uploaded_file->file_name . ': TTD Failed to import')
                                    ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                    ->setDescription($uploaded_file->file_name . " : Has failed to import!")
                                ->send();

                            $existing_periode->update([
                                'status' => 'FAILED',
                            ]);
                        })
                        ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                            $status = 'FINISHED ' . $batch->progress() . '%';
                            if($batch->failedJobs > 0) {
                                $status = 'NOT FULLY IMPORTED (' . $batch->progress() . '% PROCESSED)';
                                // Set an error toast, with a title
                            }

                            $uploaded_file->update([
                                'processing_status' => $status,
                            ]);

                            $existing_periode->update([
                                'status' => $status,
                            ]);

                            ladmin()
                                ->notification()
                                    ->setTitle($uploaded_file->file_name . ': TTD ' . $status)
                                    ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                    ->setDescription($uploaded_file->file_name . " : Has " . $status . " imported ")
                                ->send();
                        })->name($queue_name);

                $header = [
                    'drop_point_outgoing',
                    'drop_point_ttd',
                    'waktu_ttd',
                    'no_waybill',
                    'sprinter',
                    'tempat_tujuan',
                    'layanan',
                    'berat',
                ];

                foreach($chunks as $key => $chunk) {
                    /**
                     * cleansing csv
                     */
                    $chunk = str_replace(',', '.', $chunk);
                    $chunk = str_replace(';', ',', $chunk);

                    $chunk = str_replace("\xE2\x80\x8B", "", $chunk);
                    // Zero-width non-breakabke space
                    // See: https://en.wikipedia.org/wiki/Word_joiner
                    $chunk = str_replace("\xEF\xBB\xBF", "", $chunk);

                    // Zero-width space
                    // See: https://en.wikipedia.org/wiki/Zero-width_space
                    $chunk = preg_replace('/[^(\x20-\x7F)]*/', '', $chunk);

                    $chunk = str_replace(';;;;;;;;;;;;;;;;;;;;;;;;;;', '', $chunk);
                    $chunk = str_replace('\r\n', '', $chunk);
                    $chunk = str_replace('\n";', '";', $chunk);


                    /**
                     * END CLEANSING
                     */


                    $result = array_map('str_getcsv', $chunk);

                    if($key == 0) {
                        unset($result[0]);
                    }

                    $batch->add([new ProcessCSVDataDeliveryOptimized($result, $schema_name, $uploaded_file, $timeout, $key, $period_id)]);

                    $existing_periode = PeriodeDelivery::where('code', $schema_name)->first();
                    $existing_periode->update([
                        'processed_row' => $existing_periode->processed_row + count($result),
                    ]);
                }

                $batch->dispatch();
            }

            toastr()->success('Data Raw has been uploaded successfully! please wait the data to be processed!', 'Congrats');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            toastr()->error('The raw data maybe not valid, please check log resi instead!', 'Opps!');
            return redirect()->back();

        }
    }

    // Helper function to process a batch of rows
    private function processBatch(array $batch, string $schema_name, $uploaded_file, $existing_periode)
    {
        $period_id = $existing_periode->id;
        $header = [
            'no_waybill',
            'tgl_pengiriman',
            'drop_point_outgoing',
            'sprinter_pickup',
            'tempat_tujuan',
            'keterangan',
            'berat_yang_ditagih',
            'cod',
            'biaya_asuransi',
            'biaya_kirim',
            'biaya_lainnya',
            'total_biaya',
            'klien_pengiriman',
            'metode_pembayaran',
            'nama_pengirim',
            'sumber_waybill',
            'paket_retur',
            'waktu_ttd',
            'layanan',
            'diskon',
            'total_biaya_setelah_diskon',
            'agen_tujuan',
            'nik',
            'kode_promo',
            'kat'
        ];
        // $raw_before = array_map(function ($row) {
        //     return implode(',', $row);
        // }, $batch);


        $chunk = $batch;

        // foreach ($chunks as $key => $chunk) {
        /**
        * cleansing csv
        */
        $chunk = str_replace(',', '.', $chunk);
        $chunk = str_replace(';', ',', $chunk);

        $chunk = str_replace('?SHOPEE COD', ',SHOPEE COD', $chunk);
        $chunk = str_replace('?SHOPEE', ',SHOPEE', $chunk);
        $chunk = str_replace('?LAZADA', ',LAZADA', $chunk);
        $chunk = str_replace('?MAGELLAN COD', ',MAGELLAN COD', $chunk);
        $chunk = str_replace('?TOKOPEDIA', ',TOKOPEDIA', $chunk);
        $chunk = str_replace('?E3', ',E3', $chunk);
        $chunk = str_replace('?1', ',1', $chunk);
        $chunk = str_replace('?AKULAKUOB', ',AKULAKUOB', $chunk);
        $chunk = str_replace('?APP', ',APP', $chunk);
        $chunk = str_replace('?APP Sprinter', ',APP Sprinter', $chunk);
        $chunk = str_replace('?BITESHIP', ',BITESHIP', $chunk);
        $chunk = str_replace('?BLIBLIAPI', ',BLIBLIAPI', $chunk);
        $chunk = str_replace('?BRTTRIMENTARI', ',BRTTRIMENTARI', $chunk);
        $chunk = str_replace('?BUKAEXPRESS', ',BUKAEXPRESS', $chunk);
        $chunk = str_replace('?BUKALAPAK', ',BUKALAPAK', $chunk);
        $chunk = str_replace('?BUKASEND', ',BUKASEND', $chunk);
        $chunk = str_replace('?CLODEOHQ', ',CLODEOHQ', $chunk);
        $chunk = str_replace('?DOCTORSHIP', ',DOCTORSHIP', $chunk);
        $chunk = str_replace('?DONATELLOINDO', ',DONATELLOINDO', $chunk);
        $chunk = str_replace('?EVERMOSAPI', ',EVERMOSAPI', $chunk);
        $chunk = str_replace('?GRAMEDIA', ',GRAMEDIA', $chunk);
        $chunk = str_replace('?LAZADA COD', ',LAZADA COD', $chunk);
        $chunk = str_replace('?MAGELLAN', ',MAGELLAN', $chunk);
        $chunk = str_replace('?MAGELLAN COD', ',MAGELLAN COD', $chunk);
        $chunk = str_replace('?MAULAGI', ',MAULAGI', $chunk);
        $chunk = str_replace('?MENGANTAR', ',MENGANTAR', $chunk);
        $chunk = str_replace('?ORDIVO', ',ORDIVO', $chunk);
        $chunk = str_replace('?PLUNGO', ',PLUNGO', $chunk);
        $chunk = str_replace('?TRIES', ',TRIES', $chunk);
        $chunk = str_replace('?VIP', ',VIP', $chunk);
        $chunk = str_replace('?WEBSITE', ',WEBSITE', $chunk);

        $chunk = str_replace("\xE2\x80\x8B", "", $chunk);
        $chunk = str_replace("\xEF\xBB\xBF", "", $chunk);
        $chunk = preg_replace('/[^(\x20-\x7F)]*/', '', $chunk);
        $chunk = str_replace('\r\n', '', $chunk);
        $chunk = str_replace('\n";', '";', $chunk);
        /**
         * END CLEANSING
         */
        $result = array_map('str_getcsv', $chunk);

        $batch = Bus::batch(new ProcessCSVDataOptimized($result, $schema_name, $uploaded_file, $period_id))
            ->then(function (Batch $batch) use ($uploaded_file, $existing_periode) {
                $uploaded_file->update([
                    'processing_status' => 'FINISHED',
                ]);
                $done = $existing_periode->update([
                    'status' => 'FINISHED',
                ]);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode) {
                $uploaded_file->update(['processing_status' => 'FAILED']);
                $existing_periode->update([
                    'status' => 'FAILED',
                ]);
            })
            ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode) {
                $status = 'FINISHED ' . $batch->progress() . '%';
                if ($batch->failedJobs > 0) {
                    $status = 'NOT FULLY IMPORTED (' . $batch->progress() . '% PROCESSED)';
                }

                $uploaded_file->update([
                    'processing_status' => $status,
                ]);

                $existing_periode->update([
                    'status' => $status,
                ]);
            })
            ->name('QUEUE : ' . $uploaded_file->file_name . ';SCHEMA : ' . $schema_name . ';TIME UPLOAD : ' . $uploaded_file->created_at)->dispatch();

        $existing_periode->update([
            'processed_row' => $existing_periode->processed_row + count($result),
        ]);
        // }
        // ... Perform your cleansing and processing logic on the batch here ...

        // ProcessCSVDataOptimized job can be dispatched here with the processed batch data
    }

    public function uploadFile(Request $request)
    {
        try {
            //validator
            $validated = $request->validate([
                'file' => 'required|file|max:102400', //max 100MB
                'month_period' => 'required',
                'year_period' => 'required',
            ]);

            if(!$validated){
                toastr()->error('The file may too big or not CSV format', 'Opps!');

                return redirect()->back();
            }

            $file = $request->file('file');

            $schema_name = 'cashback_' . strtolower($request->month_period) . '_' . $request->year_period;

            /**
             * STEP upload
             * get file -> rename format month_year_timestamp
             * format accepted xslx, xls, csv
             * upload to storage
             * save logs into upload.logs
             * insert to db file_jobs
             *
             * trigger endpoint file-jobs/start (no scheduler and using guzzle)
             * response 200 is done
             * response 405 file not found
             * response 500 file not imported
             * response 501 file read column not match
             * response 502 file size overload
             */
            if($request->hasFile('file')){
                // Get filename with the extension
                $filenameWithExt = $request->file('file')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('file')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $file = $request->file('file');
                $name = $file->hashName();
                // Upload Image
                $path = $request->file('file')->storeAs('public/file_upload/'.$schema_name.'',$name);
                //path to read file ./jnt-cashback/public/storage/file_upload/cashback_jan_2022/bH8txro3h1X4RcOpS1OjCLFHW98U7r5GBrQZw7J6.xlsx
                // ../jnt-cashback/storage/app/public/file_upload/cashback_jan_2022/bH8txro3h1X4RcOpS1OjCLFHW98U7r5GBrQZw7J6.xlsx
                $fileJobCreated = FileJobs::create([
                    'path' => $path,
                    'schema_name' => $schema_name,
                    'file_name' => $name,
                    'file_hash' => "../jnt-cashback/storage/app/".$path,
                    'extension' => $file->getClientMimeType(),
                    'disk' => 'local',
                    'collection' => 'cashback',
                    'type_file' => 0,
                    'is_uploaded' => 1,
                    'is_imported' => 0,
                    'is_schema_created' => 0,
                    'size' => $file->getSize(),
                ]);

                // dd($fileJobCreated);
                /**
                 * checkpoint uploadded not read no execution just upload
                 */

                if($fileJobCreated) {
                    //as logger
                    $uploaded_file = Uploadfile::create([
                        'file_name' => $file->getClientOriginalName(),
                        'month_period' => $request->month_period,
                        'year_period' => $request->year_period,
                        'count_row' => 0,
                        'file_size' => $file->getSize(),
                        'table_name' => $schema_name . '.' . 'data_mart',
                        'is_pivot_processing_done' => 1,
                        'processed_by' => auth()->user()->id,
                        'type_file' => 0, //0 cashback; 1 ttd;
                        'processing_status' => 'UPLOADED',
                    ]);

                    if(!Schema::hasTable($schema_name . '.' . 'data_mart')) {
                        $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);

                        $fileJobCreated->update([
                            'is_schema_created' => 1,
                        ]);
                    }

                    $queue_name = 'QUEUE CASHBACK : ' . $file->getClientOriginalName() . ';SCHEMA : ' . $schema_name . ';TIME UPLOAD : ' . $uploaded_file->created_at;
                    $count_db_dm = DB::table($schema_name . '.data_mart')->selectRaw('COUNT(no_waybill)')->first();
                    $existing_periode = Periode::where('code', $schema_name)->first();

                    if(!$existing_periode) {

                        $existing_periode = Periode::create([
                            'code' => $schema_name,
                            'month' => $request->month_period,
                            'year' => $request->year_period,
                            'count_row' => 0,
                            'status' => 'ON QUEUE',
                        ]);

                        $period_id = $existing_periode->id;
                    } else {
                        $existing_periode->update([
                            'count_row' => $count_db_dm->count + 0
                        ]);
                        $period_id = $existing_periode->id;
                    }

                    //import klien pengiriman

                    $get_global_klien_pengiriman = DB::table('category_klien_pengiriman')->get();

                    $periode_klien_pengiriman = $get_global_klien_pengiriman->map(function ($data) use ($period_id) {
                        return [
                            'periode_id' => intval($period_id),
                            'category_id' => $data->category_id,
                            'klien_pengiriman_id' => $data->klien_pengiriman_id
                        ];
                    });

                    //check this current periode before insert (try to not make duplicate) //not support update
                    $get_periode_klien_pengiriman = PeriodeKlienPengiriman::where('periode_id', $period_id)->count();


                    if($get_periode_klien_pengiriman <= 0) {
                        PeriodeKlienPengiriman::insert($periode_klien_pengiriman->toArray());
                    }

                    if(Schema::hasTable($schema_name . '.' . 'data_mart')) {
                        // $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);

                        $code = $schema_name;

                        GeneratePivot::createOrReplacePivot($code, $period_id);

                        GeneratePivot::runMPGenerator($code);

                        GeneratePivotRekap::runRekapGenerator($code);

                        GenerateRekapLuarZona::runZonasiGenerator($code);

                        GenerateSummary::runSummaryGenerator($code, $existing_periode);

                        //process grading
                        // GradingProcess::generateGrading($period_id, $grade);

                        //process dpf
                        GenerateDPF::runRekapGenerator($code, $period_id);

                        GradingProcess::generateGrading($period_id, 'dpf');

                        //generate denda default 0


                    }

                    if($uploaded_file) {
                        //call Guzzle endpoint
                        $serviceUrl = config('services.go.upload_service');
                        $response = Http::timeout(180)->connectTimeout(60)->get($serviceUrl.'/file-job/', [
                            'month' => strtolower($request->month_period),
                            'year' => $request->year_period,
                        ]);

                        /**
                         * Benchmark here
                         *
                         * 100
                         * 10k data 1,7s
                         * 100k data 17s
                         * 650k data 1m34s
                         * 1000k data +- 170s 3min
                         */

                        $response_result = json_decode($response->body());
                        //dd($response_result);

                        //response log

                        /**
                         * success execution
                         */

                        if($response_result->result->StatusCode == 200){
                            toastr()->success('Data Raw has been uploaded successfully! please wait the data to be processed!', 'Congrats');
                            return redirect()->back();
                        }
                    }
                }
            //save success
            }

        } catch (\Throwable $th) {
            throw $th;
            toastr()->error('file not uploaded', 'Opps!');
            return redirect()->back();
        }
    }

    public function uploadFileBAK(Request $request)
    {
        try {
            //validator
            $validated = $request->validate([
                'file' => 'required|file|max:102400', //max 100MB
                'month_period' => 'required',
                'year_period' => 'required',
            ]);

            if(!$validated){
                toastr()->error('The file may too big or not CSV format', 'Opps!');

                return redirect()->back();
            }

            $file = $request->file('file');
            // $csv    = file($request->file);
            //dd($file);

            // $spreadsheet = $reader->load($file);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            $reader->load($file);

            /**
             * Load Test pass
             * 10K -> loaded
             * 100K -> failed
             */
            //$workSheet = $spreadsheet->getActiveSheet();
            dd($reader);

            //using maatwebsite caused to much ram

            // if($file->extension() != 'csv'){
            //     //xlsx / xls
            //     $import = Excel::toArray(new CashbackImport, $request->file);
            //     unset($import[0][0]);
            //     $import = $import[0];
            // } else {
            //     //is csv
            //     $import    = file($request->file);
            //     // $import = Excel::toArray(new CashbackImport)->import($request->file, null, \Maatwebsite\Excel\Excel::CSV);
            // }

            if(count($import[1]) !== 21 ){
                toastr()->error('The file may not match with template, check your file upload', 'Opps!');

                return redirect()->back();
            }

            /**
             * Counter Log checkpoint
             * 10K row -> save
             * 100K row -> too long wait 502 Gateway (toomuch ram maybe)
             */
            // dd($import)

            $schema_name = 'cashback_' . strtolower($request->month_period) . '_' . $request->year_period;
            // $csv    = file($request->file);


            //i need to convert this file to UTF-8 Encoding
            foreach ($import as $cellIndex => $cell) {
                $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
                $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';

                $import[$cellIndex] = $cell;
                //remove empty rows
                if($cell == ";;;;;;;;;;;;;;;;;;;;;;;;;;\r\n") {
                    unset($import[$cellIndex]);
                }

                if($cell == ";;;;;;;;;;;;;;;;;;;;;;;;;;") {
                    unset($import[$cellIndex]);
                }
            }

            $chunks = array_chunk($import, 500);
            $count_csv = (count($import) - 1);
            $timeout = 1200;

            /**
             * checkpoint it here
             * 10k -> chunked 500 -> passed
             */
            dd($chunks[0]);

            //as logger
            $uploaded_file = Uploadfile::create([
                'file_name' => $file->getClientOriginalName(),
                'month_period' => $request->month_period,
                'year_period' => $request->year_period,
                'count_row' => $count_csv,
                'file_size' => $file->getSize(),
                'table_name' => $schema_name . '.' . 'data_mart',
                'is_pivot_processing_done' => 1,
                'processed_by' => auth()->user()->id,
                'type_file' => 0, //0 cashback; 1 ttd;
                'processing_status' => 'ON QUEUE',
            ]);

            if(!Schema::hasTable($schema_name . '.' . 'data_mart')) {
                $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);
            }

            $queue_name = 'QUEUE CASHBACK : ' . $file->getClientOriginalName() . ';SCHEMA : ' . $schema_name . ';TIME UPLOAD : ' . $uploaded_file->created_at;
            $count_db_dm = DB::table($schema_name . '.data_mart')->selectRaw('COUNT(no_waybill)')->first();
            $existing_periode = Periode::where('code', $schema_name)->first();

            if(!$existing_periode) {

                $existing_periode = Periode::create([
                    'code' => $schema_name,
                    'month' => $request->month_period,
                    'year' => $request->year_period,
                    'count_row' => $count_csv,
                    'status' => 'ON QUEUE',
                ]);

                $period_id = $existing_periode->id;
            } else {
                $existing_periode->update([
                    'count_row' => $count_db_dm->count + $count_csv
                ]);
                $period_id = $existing_periode->id;
            }

            //import klien pengiriman

            $get_global_klien_pengiriman = DB::table('category_klien_pengiriman')->get();

            $periode_klien_pengiriman = $get_global_klien_pengiriman->map(function ($data) use ($period_id) {
                return [
                    'periode_id' => intval($period_id),
                    'category_id' => $data->category_id,
                    'klien_pengiriman_id' => $data->klien_pengiriman_id
                ];
            });

            //check this current periode before insert (try to not make duplicate) //not support update
            $get_periode_klien_pengiriman = PeriodeKlienPengiriman::where('periode_id', $period_id)->count();


            if($get_periode_klien_pengiriman <= 0) {
                PeriodeKlienPengiriman::insert($periode_klien_pengiriman->toArray());
            }

            if(Schema::hasTable($schema_name . '.' . 'data_mart')) {
                // $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);

                $code = $schema_name;

                GeneratePivot::createOrReplacePivot($code, $period_id);

                GeneratePivot::runMPGenerator($code);

                GeneratePivotRekap::runRekapGenerator($code);

                GenerateRekapLuarZona::runZonasiGenerator($code);

                GenerateSummary::runSummaryGenerator($code, $existing_periode);

                //process grading
                // GradingProcess::generateGrading($period_id, $grade);

                //process dpf
                GenerateDPF::runRekapGenerator($code, $period_id);

                GradingProcess::generateGrading($period_id, 'dpf');

                //generate denda default 0


            }

            if($uploaded_file) {
                $batch  = Bus::batch([])
                ->then(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                    $uploaded_file->update([
                        'processing_status' => 'FINISHED'
                    ]);
                    $done = $existing_periode->update([
                        'status' => 'FINISHED',
                    ]);

                    ladmin()
                    ->notification()
                        ->setTitle($uploaded_file->file_name . ': CASHBACK Done Import!')
                        ->setLink(route('ladmin.period.detail', $existing_periode->code))
                        ->setDescription($uploaded_file->file_name . " : Has finished imported")
                    ->send();
                })
                ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                    // First batch job failure detected...
                    $uploaded_file->update(['processing_status' => 'FAILED']);
                    $existing_periode->update([
                        'status' => 'FAILED',
                    ]);

                    ladmin()
                        ->notification()
                            ->setTitle($uploaded_file->file_name . ': CASHBACK Failed to import')
                            ->setLink(route('ladmin.period.detail', $existing_periode->code))
                            ->setDescription($uploaded_file->file_name . " : Has failed to import!")
                        ->send();
                })
                ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                    $status = 'FINISHED ' . $batch->progress() . '%';
                    if($batch->failedJobs > 0) {
                        $status = 'NOT FULLY IMPORTED (' . $batch->progress() . '% PROCESSED)';
                    }

                    $uploaded_file->update([
                        'processing_status' => $status,
                        'done_processed_at' => now(),
                    ]);

                    $existing_periode->update([
                        'status' => $status,
                        'done_processed_at' => now(),
                    ]);

                    ladmin()
                    ->notification()
                        ->setTitle($uploaded_file->file_name . ': CASHBACK ' . $status)
                        ->setLink(route('ladmin.period.detail', $existing_periode->code))
                        ->setDescription($uploaded_file->file_name . " : Has " . $status . " imported ")
                    ->send();
                })
                ->name($queue_name);

                foreach($chunks as $key => $chunk) {
                    $batch->add(new ProcessCSVDataOptimized($chunk, $schema_name, $uploaded_file, $timeout, $key, $period_id));
                    // $jobs[] = new ProcessCSVDataOptimized($result, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id);

                    $existing_periode = Periode::where('code', $schema_name)->first();
                    $existing_periode->update([
                        'processed_row' => $existing_periode->processed_row + count($chunk),
                        'start_processed_at' => now(),
                    ]);
                }

                $batch->dispatch();
            }

            toastr()->success('Data Raw has been uploaded successfully! please wait the data to be processed!', 'Congrats');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
            toastr()->error('The raw data maybe not valid, please check log resi instead!', 'Opps!');
            return redirect()->back();
        }
    }
}
