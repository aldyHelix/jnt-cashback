<?php

namespace Modules\UploadFile\Http\Controllers;

use App\Facades\CreateSchema;
use App\Imports\CashbackImport;
use App\Jobs\ProcessCSVData;
use App\Models\Cashback;
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
        ladmin()->allows(['ladmin.uploadfile.welcome']);

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

        if(!Schema::hasTable($schema_name.'.'.'data_mart')) {
            $schema = CreateSchema::createSchema(strtolower($request->month_period), $request->year_period);
        }

        $file = $request->file('file');
        $uploaded_file = UploadFile::create([
            'file_name' => $file->getClientOriginalName(),
            'month_period' => $request->month_period,
            'year_period' => $request->year_period,
            'count_row' => count($csv)-1,
            'file_size' => $file->getSize(),
            'table_name' => $schema_name.'.'.'data_mart',
            'processed_by' => auth()->user()->id,
            'processing_status' => 'ON PROCESSING'
        ]);

        /**
         * END DEBUG */

        $batch  = Bus::batch([
            new ProcessCSVData($chunks, $schema_name, $uploaded_file->id),
        ])
        // ->then(function (Batch $batch) {
        //     // All jobs completed successfully...
        //})
        ->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
            dd($e);
        })
        ->finally(function (Batch $batch) {

            $update = UploadFile::where('id', $uploaded_file->id)->first();
            $update->update(['processing_status' => 'DONE']);
            // The batch has finished executing...
        })
        ->name('importing Cashback CSV '.$schema_name)
        ->dispatch();



        return redirect()->back();
    }
}
