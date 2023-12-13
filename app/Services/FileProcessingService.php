<?php
namespace App\Services;

use App\Facades\CreateSchema;
use App\Facades\GenerateSchema;
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
use Modules\Uploadfile\Datatables\UploadfileDatatables;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Modules\Uploadfile\Models\Uploadfile;
use Throwable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;

class FileProcessingService {
    public function fileProcessing($uploaded_file, $file_uploaded, $file_original,$schema_name, $periode){
        $csv = file(public_path("storage/".$file_uploaded));

        //i need to convert this file to UTF-8 Encoding
        foreach ($csv as $cellIndex => $cell) {
            $cell = mb_convert_encoding($cell, 'UTF-8', 'UTF-8');
            $cell = mb_check_encoding($cell, 'UTF-8') ? $cell : '';

            $csv[$cellIndex] = $cell;
            //remove empty rows
            if($cell == ";;;;;;;;;;;;;;;;;;;;;;;;;;\r\n"){
                unset($csv[$cellIndex]);
            }

            if($cell == ";;;;;;;;;;;;;;;;;;;;;;;;;;"){
                unset($csv[$cellIndex]);
            }
        }

        $chunks = array_chunk($csv, 500);
        $count_csv = (count($csv)-1);
        $timeout = 1200;

        //generate schema table only generate datamart table

        GenerateSchema::createSchemaCashback($schema_name);

        if($uploaded_file) {
            $queue_name = 'QUEUE CASHBACK : '.$file_original->getClientOriginalName().';SCHEMA : '.$schema_name.';TIME UPLOAD : '.$uploaded_file->created_at;
            $count_db_dm = DB::table($schema_name.'.data_mart')->selectRaw('COUNT(no_waybill)')->first();

            $periode->update([
                'count_row' => $count_db_dm->count + $count_csv
            ]);
            $period_id = $periode->id;

            $batch  = Bus::batch([])
            ->then(function (Batch $batch) use ($uploaded_file, $periode, $count_csv) {
                $uploaded_file->update([
                    'processing_status'=> 'FINISHED'
                ]);
                $done = $periode->update([
                    'status' => 'FINISHED',
                ]);

                ladmin()
                ->notification()
                    ->setTitle($uploaded_file->file_name.': CASHBACK Done Import!')
                    ->setLink(route('ladmin.period.detail', $periode->code))
                    ->setDescription($uploaded_file->file_name." : Has finished imported")
                ->send();
            })
            ->catch(function (Batch $batch, Throwable $e) use ($uploaded_file, $periode, $count_csv) {
                // First batch job failure detected...
                $uploaded_file->update(['processing_status'=> 'FAILED']);
                $periode->update([
                    'status' => 'FAILED',
                ]);

                ladmin()
                    ->notification()
                        ->setTitle($uploaded_file->file_name.': CASHBACK Failed to import')
                        ->setLink(route('ladmin.period.detail', $periode->code))
                        ->setDescription($uploaded_file->file_name." : Has failed to import!")
                    ->send();
            })
            ->finally(function (Batch $batch) use ($uploaded_file, $periode, $count_csv) {
                $status = 'FINISHED '.$batch->progress().'%';
                if($batch->failedJobs > 0) {
                   $status = 'NOT FULLY IMPORTED ('.$batch->progress().'% PROCESSED)';
                }

                $uploaded_file->update([
                    'processing_status'=> $status,
                    'done_processed_at' => now(),
                ]);

                $periode->update([
                    'status' => $status,
                    'done_processed_at' => now(),
                ]);

                ladmin()
                ->notification()
                    ->setTitle($uploaded_file->file_name.': CASHBACK '.$status)
                    ->setLink(route('ladmin.period.detail', $periode->code))
                    ->setDescription($uploaded_file->file_name." : Has ".$status." imported ")
                ->send();
            })
            ->name($queue_name);

            foreach($chunks as $key => $chunk) {
                $batch->add(new ProcessCSVDataOptimized($chunk, $schema_name, $uploaded_file, $timeout, $key, $period_id));
                // $jobs[] = new ProcessCSVDataOptimized($result, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id);

                $periode = Periode::where('code', $schema_name)->first();
                $periode->update([
                    'processed_row'=> $periode->processed_row + count($chunk),
                    'start_processed_at' => now(),
                ]);
            }

            $batch->dispatch();
        }
    }
}
