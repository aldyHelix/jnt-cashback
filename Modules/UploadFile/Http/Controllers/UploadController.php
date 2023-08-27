<?php

namespace Modules\UploadFile\Http\Controllers;

use App\Facades\CreateSchema;
use App\Imports\CashbackImport;
use App\Jobs\ProcessCSVData;
use App\Jobs\ProcessCSVDataDelivery;
use App\Jobs\ProcessCSVDataDeliveryOptimized;
use App\Jobs\ProcessCSVDataOptimized;
use App\Models\Cashback;
use App\Models\Periode;
use App\Models\PeriodeDelivery;
use Illuminate\Bus\Batch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\UploadFile\Datatables\UploadFileDatatables;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Modules\UploadFile\Models\UploadFile;
use Throwable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;

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

        if( request()->has('datatables') ) {
            return UploadFileDatatables::renderData();
        }

        return view('uploadfile::index');
    }

    public function uploadFileDelivery(Request $request) {
        try {
        $schema_name = 'delivery_'.strtolower($request->month_period).'_'.$request->year_period;
        $csv    = file($request->file);

        //i need to convert this file to UTF-8 Encoding
        foreach ($csv as $cellIndex => $cell) {
            $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
            $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';

            $csv[$cellIndex] = $cell;
        }

        $chunks = array_chunk($csv,500);
        $count_csv = (count($csv)-1);
        $timeout = intval($count_csv * 0.5 );

        if(!Schema::hasTable($schema_name.'.'.'data_mart')) {
            $schema = CreateSchema::createSchemaDelivery(strtolower($request->month_period), $request->year_period);
        }

        $file = $request->file('file');


        $uploaded_file = UploadFile::create([
            'file_name' => $file->getClientOriginalName(),
            'month_period' => $request->month_period,
            'year_period' => $request->year_period,
            'count_row' => $count_csv,
            'file_size' => $file->getSize(),
            'table_name' => $schema_name.'.'.'data_mart',
            'processed_by' => auth()->user()->id,
            'type_file' => 1, //0 cashback; 1 ttd;
            'processing_status' => 'ON QUEUE',
        ]);

        $queue_name = 'QUEUE DELIVERY : '.$file->getClientOriginalName().';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at;

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
                            'processing_status'=> 'FINISHED'
                        ]);

                        $done = $existing_periode->update([
                            'count_row' => $existing_periode->count_row + $count_csv,
                            'status' => 'FINISHED',
                        ]);

                        ladmin()
                            ->notification()
                                ->setTitle($uploaded_file->file_name.': TTD Done Import!')
                                ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                ->setDescription($uploaded_file->file_name." : Has finished imported")
                            ->send();
                    })
                    ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                        // First batch job failure detected...
                        $uploaded_file->update(['processing_status'=> 'FAILED']);

                        ladmin()
                            ->notification()
                                ->setTitle($uploaded_file->file_name.': TTD Failed to import')
                                ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                ->setDescription($uploaded_file->file_name." : Has failed to import!")
                            ->send();

                        $existing_periode->update([
                            'status' => 'FAILED',
                        ]);
                    })
                    ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                        $status = 'FINISHED '.$batch->progress().'%';
                        if($batch->failedJobs > 0) {
                            $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                            // Set an error toast, with a title
                        }

                        $uploaded_file->update([
                            'processing_status'=> $status,
                        ]);

                        $existing_periode->update([
                            'status' => $status,
                        ]);

                        ladmin()
                            ->notification()
                                ->setTitle($uploaded_file->file_name.': TTD '.$status)
                                ->setLink(route('ladmin.period.detail', $existing_periode->code))
                                ->setDescription($uploaded_file->file_name." : Has ".$status." imported ")
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
                $chunk = preg_replace('/[^(\x20-\x7F)]*/','', $chunk);

                $chunk = str_replace(';;;;;;;;;;;;;;;;;;;;;;;;;;', '', $chunk);
                $chunk = str_replace('\r\n', '', $chunk);
                $chunk = str_replace('\n";', '";', $chunk);


                /**
                 * END CLEANSING
                 */


                $result = array_map('str_getcsv', $chunk);

                if($key == 0){
                    unset($result[0]);
                }

                $batch->add([new ProcessCSVDataDeliveryOptimized($result, $schema_name, $uploaded_file, $timeout, $key, $period_id)]);

                $existing_periode = PeriodeDelivery::where('code', $schema_name)->first();
                $existing_periode->update([
                    'processed_row'=> $existing_periode->processed_row + count($result),
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

    public function uploadFileDEV(Request $request)
    {
        try {
            $schema_name = 'cashback_'.strtolower($request->month_period).'_'.$request->year_period;
            $file_path = $request->file('file')->path();
            $file_size = filesize($file_path); // Use filesize() to get the file size
            $handle = fopen($file_path, 'r');
            // Assuming your CSV file contains a header, read and skip it
            $header = fgetcsv($handle);
            $batchSize = 500; // Set the batch size to your desired value
            $batch = []; // Initialize an empty batch array

            // Create the schema if it doesn't exist
            if (!Schema::hasTable($schema_name.'.'.'data_mart')) {
                $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);
            }

            $uploaded_file = UploadFile::create([
                'file_name' => $request->file('file')->getClientOriginalName(),
                'month_period' => $request->month_period,
                'year_period' => $request->year_period,
                'file_size' => $file_size,
                'table_name' => $schema_name.'.'.'data_mart',
                'processed_by' => auth()->user()->id,
                'type_file' => 0, //0 cashback; 1 ttd;
                'processing_status' => 'ON QUEUE',
            ]);

            $existing_periode = Periode::firstOrCreate(
                ['code' => $schema_name],
                [
                    'month' => $request->month_period,
                    'year' => $request->year_period,
                    'status' => 'ON QUEUE',
                ]
            );

            $period_id = $existing_periode->id;

            while (($csv = fgetcsv($handle)) !== false) {
                // Convert the line to UTF-8 Encoding
                array_walk($csv, function (&$cell) {
                    $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
                    $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';
                });

                $batch[] = $csv[0];

                // Process the batch when it reaches the desired size
                if (count($batch) === $batchSize) {
                    $this->processBatch($batch, $schema_name, $uploaded_file, $existing_periode);
                    $batch = []; // Clear the batch for the next set of rows
                }
            }

            // Process any remaining rows that did not form a complete batch
            if (!empty($batch)) {
                $this->processBatch($batch, $schema_name, $uploaded_file, $existing_periode);
            }

            fclose($handle);
            // $batch->dispatch();

            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
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
                    $status = 'FINISHED '.$batch->progress().'%';
                    if ($batch->failedJobs > 0) {
                        $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                    }

                    $uploaded_file->update([
                        'processing_status' => $status,
                    ]);

                    $existing_periode->update([
                        'status' => $status,
                    ]);
                })
                ->name('QUEUE : '.$uploaded_file->file_name.';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at)->dispatch();

            $existing_periode->update([
                'processed_row' => $existing_periode->processed_row + count($result),
            ]);
        // }
        // ... Perform your cleansing and processing logic on the batch here ...

        // ProcessCSVDataOptimized job can be dispatched here with the processed batch data
    }

    public function uploadFile(Request $request) {
        try {
        $schema_name = 'cashback_'.strtolower($request->month_period).'_'.$request->year_period;
        $csv    = file($request->file);

        //i need to convert this file to UTF-8 Encoding
        foreach ($csv as $cellIndex => $cell) {
            $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
            $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';

            $csv[$cellIndex] = $cell;
            //remove empty rows
            if($cell == ";;;;;;;;;;;;;;;;;;;;;;;;;;\r\n"){
                unset($csv[$cellIndex]);
            }
        }

        $chunks = array_chunk($csv, 500);
        $count_csv = (count($csv)-1);
        $timeout = 1200;

        if(!Schema::hasTable($schema_name.'.'.'data_mart')) {
            $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);
        }

        $file = $request->file('file');

        //as logger
        $uploaded_file = UploadFile::create([
            'file_name' => $file->getClientOriginalName(),
            'month_period' => $request->month_period,
            'year_period' => $request->year_period,
            'count_row' => $count_csv,
            'file_size' => $file->getSize(),
            'table_name' => $schema_name.'.'.'data_mart',
            'is_pivot_processing_done' => 1,
            'processed_by' => auth()->user()->id,
            'type_file' => 0, //0 cashback; 1 ttd;
            'processing_status' => 'ON QUEUE',
        ]);

        $queue_name = 'QUEUE CASHBACK : '.$file->getClientOriginalName().';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at;

        if($uploaded_file) {
            $count_db_dm = DB::table($schema_name.'.data_mart')->selectRaw('COUNT(no_waybill)')->first();
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

            $batch  = Bus::batch([])
            ->then(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                $uploaded_file->update([
                    'processing_status'=> 'FINISHED'
                ]);
                $done = $existing_periode->update([
                    'status' => 'FINISHED',
                ]);

                ladmin()
                ->notification()
                    ->setTitle($uploaded_file->file_name.': CASHBACK Done Import!')
                    ->setLink(route('ladmin.period.detail', $existing_periode->code))
                    ->setDescription($uploaded_file->file_name." : Has finished imported")
                ->send();
            })
            ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                // First batch job failure detected...
                $uploaded_file->update(['processing_status'=> 'FAILED']);
                $existing_periode->update([
                    'status' => 'FAILED',
                ]);

                ladmin()
                    ->notification()
                        ->setTitle($uploaded_file->file_name.': CASHBACK Failed to import')
                        ->setLink(route('ladmin.period.detail', $existing_periode->code))
                        ->setDescription($uploaded_file->file_name." : Has failed to import!")
                    ->send();
            })
            ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                $status = 'FINISHED '.$batch->progress().'%';
                if($batch->failedJobs > 0) {
                   $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                }

                $uploaded_file->update([
                    'processing_status'=> $status,
                    'done_processed_at' => now(),
                ]);

                $existing_periode->update([
                    'status' => $status,
                    'done_processed_at' => now(),
                ]);

                ladmin()
                ->notification()
                    ->setTitle($uploaded_file->file_name.': CASHBACK '.$status)
                    ->setLink(route('ladmin.period.detail', $existing_periode->code))
                    ->setDescription($uploaded_file->file_name." : Has ".$status." imported ")
                ->send();
            })
            ->name($queue_name);

            foreach($chunks as $key => $chunk) {
                $batch->add(new ProcessCSVDataOptimized($chunk, $schema_name, $uploaded_file, $timeout, $key, $period_id));
                // $jobs[] = new ProcessCSVDataOptimized($result, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id);

                $existing_periode = Periode::where('code', $schema_name)->first();
                $existing_periode->update([
                    'processed_row'=> $existing_periode->processed_row + count($chunk),
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
