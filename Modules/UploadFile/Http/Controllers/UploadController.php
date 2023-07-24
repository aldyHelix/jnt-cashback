<?php

namespace Modules\UploadFile\Http\Controllers;

use App\Facades\CreateSchema;
use App\Imports\CashbackImport;
use App\Jobs\ProcessCSVData;
use App\Jobs\ProcessCSVDataDelivery;
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

        // DB::connection('pgsql')->unprepared("
        //     CREATE SCHEMA hollywood
        //     CREATE TABLE films (title text, release date, awards text[])
        //     CREATE VIEW winners AS
        //         SELECT title, release FROM films WHERE awards IS NOT NULL;
        // ");
            /**
         * Sometimes we need more than one table on a page.
         * You can also create custom routes for rendering data from datatables.
         * Ladmin uses the index route as a simple example.
         *
         * Look at the \Modules\Ladmin\Datatables\AdminDatatables file in the ajax method
         */
        if( request()->has('datatables') ) {
            return UploadFileDatatables::renderData();
        }

        return view('uploadfile::index');
    }

    public function uploadFileDelivery(Request $request) {
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

        $queue_name = 'QUEUE : '.$file->getClientOriginalName().';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at;

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
                    })
                    ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                        // First batch job failure detected...
                        $uploaded_file->update(['processing_status'=> 'FAILED']);

                        $existing_periode->update([
                            'status' => 'FAILED',
                        ]);
                    })
                    ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                        $status = 'FINISHED '.$batch->progress().'%';
                        if($batch->failedJobs > 0) {
                        $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                        }

                        $uploaded_file->update([
                            'processing_status'=> $status,
                        ]);

                        $existing_periode->update([
                            'status' => $status,
                        ]);
                        // The batch has finished executing...
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
                // $chunk = $this->data;
                $raw_before = $chunk;

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

                $chunk = str_replace('\r\n', '', $chunk);
                $chunk = str_replace('\n";', '";', $chunk);


                /**
                 * END CLEANSING
                 */


                $result = array_map('str_getcsv', $chunk);

                if($key == 0){
                    unset($result[0]);
                }

                $batch->add([new ProcessCSVDataDelivery($result, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id)]);

                $existing_periode = PeriodeDelivery::where('code', $schema_name)->first();
                $existing_periode->update([
                    'processed_row'=> $existing_periode->processed_row + count($result),
                ]);
            }

            $batch->dispatch($uploaded_file);
        }

        return redirect()->back();
    }

    public function uploadFile(Request $request) {
        $schema_name = 'cashback_'.strtolower($request->month_period).'_'.$request->year_period;
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
            $schema = CreateSchema::createSchemaCashback(strtolower($request->month_period), $request->year_period);
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
            'type_file' => 0, //0 cashback; 1 ttd;
            'processing_status' => 'ON QUEUE',
        ]);

        $queue_name = 'QUEUE : '.$file->getClientOriginalName().';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at;

        if($uploaded_file) {
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
             })
             ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $existing_periode, $count_csv) {
                 // First batch job failure detected...
                 $uploaded_file->update(['processing_status'=> 'FAILED']);
                 $existing_periode->update([
                     'status' => 'FAILED',
                 ]);
             })
             ->finally(function (Batch $batch) use ($uploaded_file, $existing_periode, $count_csv) {
                 $status = 'FINISHED '.$batch->progress().'%';
                 if($batch->failedJobs > 0) {
                    $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                 }

                 $uploaded_file->update([
                     'processing_status'=> $status,
                 ]);

                 $existing_periode->update([
                     'status' => $status,
                 ]);
                 // The batch has finished executing...
             })
             ->name($queue_name);
            //  ->allowFailures()


            foreach($chunks as $key => $chunk) {
                // $chunk = $this->data;
                $raw_before = $chunk;

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

                // foreach ($replacements as $replacement) {
                //     $chunk = str_replace($replacement[0], $replacement[1], $chunk);
                // }

                $chunk = str_replace("\xE2\x80\x8B", "", $chunk);
                // Zero-width non-breakabke space
                // See: https://en.wikipedia.org/wiki/Word_joiner
                $chunk = str_replace("\xEF\xBB\xBF", "", $chunk);

                // Zero-width space
                // See: https://en.wikipedia.org/wiki/Zero-width_space
                $chunk = preg_replace('/[^(\x20-\x7F)]*/','', $chunk);

                $chunk = str_replace('\r\n', '', $chunk);
                $chunk = str_replace('\n";', '";', $chunk);


                /**
                 * END CLEANSING
                 */


                $result = array_map('str_getcsv', $chunk);

                if($key == 0){
                    unset($result[0]);
                }

                $batch->add([new ProcessCSVData($result, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id)]);

                $existing_periode = Periode::where('code', $schema_name)->first();
                $existing_periode->update([
                    'processed_row'=> $existing_periode->processed_row + count($result),
                ]);
            }
            $batch->dispatch($uploaded_file);
        }

        return redirect()->back();
    }
}
