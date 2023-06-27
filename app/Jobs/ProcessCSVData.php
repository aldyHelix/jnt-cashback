<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\UploadFile\Models\UploadFile;
use Throwable;

class ProcessCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $schema_name;
    public $data;
    public $uploaded_file;
    public $delay;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    public $retryAfter = 120; // Increase the retry delay to 60 seconds

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $schema_name, $uploaded_file)
    {
        $this->data = $data;
        $this->schema_name = $schema_name;
        $this->uploaded_file = $uploaded_file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
                $uploadfile = UploadFile::where('id', $this->uploaded_file)->first();
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

                foreach ($this->data as $key => $chunk) {
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

                    foreach ($result as $key2=>$item) {
                        $duplicates = DB::table($this->schema_name.'.data_mart')->where('no_waybill', $item[0])->first();

                        if($duplicates) {
                            unset($result[$key2]);
                            continue;
                        }

                        unset($item[25]);
                        unset($item[26]);

                            if (!(count($item) == count($header))) {
                                $substr = substr_count($raw_before[$key2],";");
                                $substr1 = substr_count($raw_before[$key2+1],";");
                                if ($substr > $substr1) {
                                    $next_item = explode(',', $result[$key2+1][0]);
                                    unset($next_item[0]);
                                    $item = array_merge($item, $next_item);
                                    unset($result[$key2+1]);
                                }

                                if ($substr < $substr1) {
                                    $item[count($item)-1] = $item[count($item)-1]."".$result[$key2+1][0];
                                    unset($result[$key2+1][0]);
                                    $item = array_merge($item, $result[$key2+1]);
                                    unset($result[$key2+1]);
                                }

                                if($key != 0) {
                                    if (count($result[$key2-1]) > count($item)){
                                        continue;
                                    }

                                    if (count($item) > count($result[$key2-1])) {
                                        continue;
                                    }
                                }

                                unset($item[25]);
                                unset($item[26]);

                            }

                            if ((count($item) == count($header))) {
                                $item = array_combine($header,$item);

                                $item['cod'] = intval($item['cod']);
                                $item['biaya_asuransi'] = intval($item['biaya_asuransi']);
                                $item['biaya_kirim'] = intval($item['biaya_kirim']);
                                $item['biaya_lainnya'] = intval($item['biaya_lainnya']);
                                $item['total_biaya'] = intval($item['total_biaya']);
                                $item['diskon'] = intval($item['diskon']);
                                $item['total_biaya_setelah_diskon'] = intval($item['total_biaya_setelah_diskon']);

                                if($item['waktu_ttd'] == ""){
                                    $item['waktu_ttd'] = '01/01/1970 00:00';
                                }
                            }

                            $insert = DB::table($this->schema_name.'.data_mart')->insert($item);

                    }


                    if(count($this->data) == $key-1) {
                        $uploadfile->update(['processing_status', 'DONE IMPORTING']);
                    }

                    $countData += count($result);
                    $uploadfile->update(['processed_row', $countData]);

                }


            DB::commit();

            $this->release();
        } catch (\Exception $e) {
            Log::error('Error processing CSV data: ' . $e->getMessage());
        }

    }
}
