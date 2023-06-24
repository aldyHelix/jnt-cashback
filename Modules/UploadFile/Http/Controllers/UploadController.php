<?php

namespace Modules\UploadFile\Http\Controllers;

use App\Facades\CreateSchema;
use App\Imports\CashbackImport;
use App\Jobs\ProcessCSVData;
use App\Models\Cashback;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\UploadFile\Datatables\UploadFileDatatables;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Modules\UploadFile\Models\UploadFile;

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

        // dump($request->all());

        // menyimpan data file yang diupload ke variabel $file
        $schema_name = 'cashback_'.strtolower($request->month_period).'_'.$request->year_period;
        $csv    = file($request->file);
        $chunks = array_chunk($csv,2000);
        $sum = 0;
        $countData = 0;
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

        foreach ($chunks as $key => $chunk) {
            $sum += count($chunk);
        }

        if(!Schema::hasTable($schema_name.'.'.'data_mart')) {
            $schema = CreateSchema::createSchema(strtolower($request->month_period), $request->year_period);
        }

        $file = $request->file('file');
        $uploaded_file = UploadFile::create([
            'file_name' => $file->getClientOriginalName(),
            'month_period' => $request->month_period,
            'year_period' => $request->year_period,
            'count_row' => $sum,
            'file_size' => $file->getSize(),
            'table_name' => $schema_name.'.'.'data_mart',
            'processed_by' => auth()->user()->id,
            'processing_status' => 'ON PROCESSING'
        ]);


        $batch  = Bus::batch([])
        ->name('importing Cashback CSV '.$schema_name)
        ->then(function (Batch $batch) {
            //All jobs completed successfully...
            $uploaded_file->update('processing_status', 'DONE IMPORTING');
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
            $uploaded_file->update('processing_status', 'ERROR at Batch');
        })->finally(function (Batch $batch) {
            // The batch has finished executing...
            $uploaded_file->update('processing_status', 'ON PROCESSING '.$batch->progress());
        })
        ->dispatch();

        foreach ($chunks as $key => $chunk) {
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

            // $should_replace = [
            //     '?SHOPEE COD',
            //     '?SHOPEE',
            //     '?LAZADA',
            //     '?MAGELLAN COD',
            //     '?TOKOPEDIA',
            //     '?E3',
            //     '?1',
            //     '?AKULAKUOB',
            //     '?APP',
            //     '?APP Sprinter',
            //     '?BITESHIP',
            //     '?BLIBLIAPI',
            //     '?BRTTRIMENTARI',
            //     '?BUKAEXPRESS',
            //     '?BUKALAPAK',
            //     '?BUKASEND',
            //     '?CLODEOHQ',
            //     '?DOCTORSHIP',
            //     '?DONATELLOINDO',
            //     '?EVERMOSAPI',
            //     '?GRAMEDIA',
            //     '?LAZADA COD',
            //     '?MAGELLAN',
            //     '?MAGELLAN COD',
            //     '?MAULAGI',
            //     '?MENGANTAR',
            //     '?ORDIVO',
            //     '?PLUNGO',
            //     '?TRIES',
            //     '?VIP',
            //     '?WEBSITE',
            // ];

            // $replace_with = [
            //     ',SHOPEE COD',
            //     ',SHOPEE',
            //     ',LAZADA',
            //     ',MAGELLAN COD',
            //     ',TOKOPEDIA',
            //     ',E3',
            //     ',1',
            //     ',AKULAKUOB',
            //     ',APP',
            //     ',APP Sprinter',
            //     ',BITESHIP',
            //     ',BLIBLIAPI',
            //     ',BRTTRIMENTARI',
            //     ',BUKAEXPRESS',
            //     ',BUKALAPAK',
            //     ',BUKASEND',
            //     ',CLODEOHQ',
            //     ',DOCTORSHIP',
            //     ',DONATELLOINDO',
            //     ',EVERMOSAPI',
            //     ',GRAMEDIA',
            //     ',LAZADA COD',
            //     ',MAGELLAN',
            //     ',MAGELLAN COD',
            //     ',MAULAGI',
            //     ',MENGANTAR',
            //     ',ORDIVO',
            //     ',PLUNGO',
            //     ',TRIES',
            //     ',VIP',
            //     ',WEBSITE',
            // ];

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
            /**
             * END CLEANSING
             */

            $data = array_map('str_getcsv', $chunk);

            if($key == 0){
                unset($data[0]);
            }

            foreach ($data as $key2=>$item) {
                $duplicates = DB::table($schema_name.'.data_mart')->where('no_waybill', $item[0])->first();

                if($duplicates) {
                    unset($data[$key2]);
                    continue;
                }

                unset($item[25]);
                unset($item[26]);

                    // if (!(count($item) == count($header))) {
                    //     $raw_after[$key2] =
                    //     $substr = substr_count($raw_before[$key2],";");
                    //     $substr1 = substr_count($raw_before[$key2+1],";");
                    //     if ($substr > $substr1) {
                    //         $next_item = explode(',', $data[$key2+1][0]);
                    //         unset($next_item[0]);
                    //         $item = array_merge($item, $next_item);
                    //         unset($data[$key2+1]);
                    //     }

                    //     if ($substr < $substr1) {
                    //         $item[count($item)-1] = $item[count($item)-1]."".$data[$key2+1][0];
                    //         unset($data[$key2+1][0]);
                    //         $item = array_merge($item, $data[$key2+1]);
                    //         unset($data[$key2+1]);
                    //     }

                    //     if($key != 0) {
                    //         if (count($data[$key2-1]) > count($item)){
                    //             continue;
                    //         }

                    //         if (count($item) > count($data[$key2-1])) {
                    //             continue;
                    //         }
                    //     }

                    //     // dd($item);

                    //     unset($item[25]);
                    //     unset($item[26]);

                    // }

                    // if ((count($item) == count($header))) {
                    //     $item = array_combine($header,$item);

                    //     $item['cod'] = intval($item['cod']);
                    //     $item['biaya_asuransi'] = intval($item['biaya_asuransi']);
                    //     $item['biaya_kirim'] = intval($item['biaya_kirim']);
                    //     $item['biaya_lainnya'] = intval($item['biaya_lainnya']);
                    //     $item['total_biaya'] = intval($item['total_biaya']);
                    //     $item['diskon'] = intval($item['diskon']);
                    //     $item['total_biaya_setelah_diskon'] = intval($item['total_biaya_setelah_diskon']);

                    //     // $item = array_combine($header,$item);

                    //     if($item['waktu_ttd'] == ""){
                    //         $item['waktu_ttd'] = '01/01/1970 00:00';
                    //     }
                    // } else {
                    //     dump($raw_before[$key2]);
                    //     dump('CHUNKs at '.$key.': DATA at '.$key2.'IS ERROR');
                    //     dump('array combine gone wrong, item :'.count($item).'; header :'.count($header));
                    // }


                    // $insert = DB::table($schema_name.'.data_mart')->insert($item);
                    // $data[$key2] = $item;
                    // $insert = DB::table('cashback_'.$this->month.'_'.$this->year.'.data_mart')->insert($item);

            }

            // $countData += count($data);

            // if($countData == 0){
            //     continue;
            // }

            // dump('CHUNKs at '.$key.': DATA Count '.$countData.'IS SUCCEED');

            // if($key == 1){
            //     dd('stop');
            //     // dd($data);
            // }
            $batch->add(new ProcessCSVData($data, $header, strtolower($request->month_period), $request->year_period, $raw_before));

        }

        // dd('DONE');

        return redirect()->back();
    }
}
