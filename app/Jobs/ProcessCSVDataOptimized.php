<?php

namespace App\Jobs;

use App\Models\Periode;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\UploadFile\Models\UploadFile;
use Throwable;
use App\Http\Livewire\QueueProcessor;
use App\Models\LogResi;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;

class ProcessCSVDataOptimized implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $schema_name;
    public $data;
    public $uploaded_file;
    public $raw_before;
    public $period_id;
    public $timeout = 1200;
    public $maxTries = 1;
    public $key;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $schema_name, $uploaded_file, $raw_before, $timeout, $key, $period_id)
    {
        $this->data = $data;
        $this->schema_name = $schema_name;
        $this->uploaded_file = $uploaded_file;
        $this->raw_before = $raw_before;
        $this->timeout = $timeout;
        $this->key = $key;
        $this->period_id = $period_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Redis::throttle('jnt_cashback_horizon')->block(30)->allow(60)->every(40)->then(function () {
            try {
                // DB::beginTransaction();

                $uploaded_file = UploadFile::find($this->uploaded_file->id);
                $periode = Periode::find($this->period_id);

                $uploaded_file->update(['processing_status' => 'ON PROCESSING ' . $this->batch()->progress]);

                $data_insert = [];
                $inserted = 0;
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

                $last_result = count($this->data);
                dump($this->data[$last_result-1]);
                foreach($this->data as $index => $cell) {

                    if($index < $last_result-1) {
                        // $new_string = get_string_between($cell, '"', '"');
                        // $this->data[$index] = str_replace(';', '', $new_string);

                        $substr = substr_count($cell,";"); //15
                        $substr1 = substr_count($this->data[$index + 1],";"); //11

                        if($substr > 25) {
                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => substr($cell, 0, 12),
                                'before_raw' => json_encode($cell),
                                'after_raw' => json_encode($this->data[$index]),
                                'type' => 'error row',
                                'date' => now()
                            ]);
                            continue;
                        }

                        if ($substr < 25 && $substr1 < 25 && $substr1 != 0) {
                            $this->data[$index] = str_replace("\r\n",'', $this->data[$index]);
                            $this->data[$index] = str_replace('"','', $this->data[$index]);
                            $this->data[$index] .= $this->data[$index+1];

                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => substr($cell, 0, 12),
                                'before_raw' => json_encode($cell),
                                'after_raw' => json_encode($this->data[$index]),
                                'type' => 'invalid',
                                'date' => now()
                            ]);

                            unset($this->data[$index+1]);
                        }
                    }
                }

                dd($this->data);
                $chunk = $this->data;

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
                $chunk = str_replace('"', '', $chunk);

                /**
                 * END CLEANSING
                 */
                $result = array_map('str_getcsv', $chunk);

                if($this->key == 0){
                    unset($result[0]);
                }

                foreach ($result as $key2 => $item) {

                    $duplicates = DB::table($this->schema_name . '.data_mart')->where('no_waybill', $item[0])->first();

                    if ($duplicates) {
                        // Kalau duplicate akan diupdate data barunya.
                        //if biaya_kirim baru < $biaya kirim lama
                        if(intval($item[9]) < intval($duplicates->biaya_kirim)) {
                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => $item[0],
                                'before_raw' => json_encode($duplicates),
                                'after_raw' => json_encode($item),
                                'type' => 'duplicate',
                                'date' => $item[1],
                                'created_at' => now()
                            ]);

                            unset($item[25]);
                            unset($item[26]);

                            $new_data = array_combine($header, $item);

                            $new_data['cod'] = intval($new_data['cod']);
                            $new_data['biaya_asuransi'] = intval($new_data['biaya_asuransi']);
                            $new_data['biaya_kirim'] = intval($new_data['biaya_kirim']);
                            $new_data['biaya_lainnya'] = intval($new_data['biaya_lainnya']);
                            $new_data['total_biaya'] = intval($new_data['total_biaya']);
                            $new_data['diskon'] = intval($new_data['diskon']);
                            $new_data['total_biaya_setelah_diskon'] = intval($new_data['total_biaya_setelah_diskon']);

                            if ($new_data['waktu_ttd'] === "") {
                                $new_data['waktu_ttd'] = '01/01/1970 00:00';
                            }

                            dd($new_data);

                            DB::table($this->schema_name . '.data_mart')->where('no_waybill', $item[0])->update($new_data);
                        }

                        unset($result[$key2]);
                        continue;
                    }

                    unset($item[25]);
                    unset($item[26]);

                    if (!(count($item) == count($header))) {
                        // DB::table('log_resi')->insert([
                        //     'periode_id' => $this->period_id,
                        //     'batch_id' => $this->batch()->id,
                        //     'resi' => $item[0],
                        //     'before_raw' => '',
                        //     'after_raw' => json_encode($item),
                        //     'type' => 'invalid',
                        //     'date' => '-'
                        // ]);

                        // $substr = substr_count($this->raw_before[$key2],";"); //15
                        // $substr1 = substr_count($this->raw_before[$key2+1],";"); //11

                        // if ($substr < 26 && $substr1 < 26 && $substr1 != 0) { //based on raw
                        //     if ($substr > $substr1) { // true
                        //         $next_item = explode(',', $result[$key2+1][0]);
                        //         unset($next_item[0]);
                        //         $item = array_merge($item, $next_item);
                        //         unset($result[$key2+1]);
                        //     }

                        //     if ($substr < $substr1) {
                        //         $item[count($item)-1] = $item[count($item)-1]."".$result[$key2+1][0];
                        //         unset($result[$key2+1][0]);
                        //         $item = array_merge($item, $result[$key2+1]);
                        //         unset($result[$key2+1]);
                        //     }
                        // }

                        // if($this->key != 0) {
                        //     if (count($result[$key2-1]) > count($item)){
                        //         continue;
                        //     }

                        //     if (count($item) > count($result[$key2-1])) {
                        //         continue;
                        //     }
                        // }

                        // unset($item[25]);
                        // unset($item[26]);

                        continue;
                    }

                    if (count($item) === count($header)) {
                        $item = array_combine($header, $item);
                        $item['cod'] = intval($item['cod']);
                        $item['biaya_asuransi'] = intval($item['biaya_asuransi']);
                        $item['biaya_kirim'] = intval($item['biaya_kirim']);
                        $item['biaya_lainnya'] = intval($item['biaya_lainnya']);
                        $item['total_biaya'] = intval($item['total_biaya']);
                        $item['diskon'] = intval($item['diskon']);
                        $item['total_biaya_setelah_diskon'] = intval($item['total_biaya_setelah_diskon']);

                        if ($item['waktu_ttd'] === "") {
                            $item['waktu_ttd'] = '01/01/1970 00:00';
                        }

                        $data_insert[] = $item;
                        $dbInsert = DB::table($this->schema_name . '.data_mart')->insert($item);

                        if(!$dbInsert) {
                            // $this->fail($item['no_waybill'].' not inserted');
                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => $item['no_waybill'],
                                'before_raw' => '',
                                'after_raw' => json_encode($item),
                                'type' => 'fail_insert',
                                'date' => $item['tgl_pengiriman'],
                                'created_at' => now()
                            ]);
                        }
                        $inserted++;
                    }
                    //  else {
                    //     DB::table('log_resi')->insert([
                    //         'periode_id' => $this->period_id,
                    //         'batch_id' => $this->batch()->id,
                    //         'resi' => $item[0],
                    //         'before_raw' => '',
                    //         'after_raw' => json_encode($item),
                    //         'type' => 'invalid',
                    //         'date' => $item[1]
                    //     ]);
                    //     Log::info('Row skipped due to missing columns.');
                    // }
                }


                $periode->update([
                    'inserted_row' => $periode->inserted_row + $inserted,
                ]);

                $uploaded_file->update(['processed_row' => $uploaded_file->processed_row + count($data_insert)]);

                // DB::commit();
            } catch (\Exception $e) {
                // DB::rollBack();
                throw $e;
            }
        }, function () {
            // Could not obtain lock...
            return $this->release();
        });
    }
}
