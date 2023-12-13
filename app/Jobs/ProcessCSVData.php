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
use Modules\Uploadfile\Models\Uploadfile;
use Throwable;
use App\Http\Livewire\QueueProcessor;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;

class ProcessCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $schema_name;
    public $data;
    public $uploaded_file;
    public $raw_before;
    public $period_id;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    // public $timeout = 60;
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

        $job = $this;

        Redis::throttle('jnt_cashback_horizon')->block(60)->allow(60)->every(40)->then(function () {
            info('Lock obtained...');
                    // try {
            // usleep(100000);
            $uploaded_file = Uploadfile::where('id', $this->uploaded_file->id)->first();
            $periode = Periode::where('id', $this->period_id)->first();

            $uploaded_file->update(['processing_status'=> 'ON PROCESSING '.$this->batch()->progress]);

            $data_insert = [];
            $inserted = 0;
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
            foreach ($this->data as $key2 => $item) {
                $duplicates = DB::table($this->schema_name.'.data_mart')->where('no_waybill', $item[0])->first();

                if($duplicates) {
                    //kalau duplicate akan di update data barunya.
                    unset($this->data[$key2]);
                    continue;
                }

                unset($item[25]);
                unset($item[26]);

                if (!(count($item) == count($header))) {
                    $substr = substr_count($this->raw_before[$key2],";");
                    $substr1 = substr_count($this->raw_before[$key2+1],";");
                    if ($substr > $substr1) {
                        $next_item = explode(',', $this->data[$key2+1][0]);
                        unset($next_item[0]);
                        $item = array_merge($item, $next_item);
                        unset($this->data[$key2+1]);
                    }

                    if ($substr < $substr1) {
                        $item[count($item)-1] = $item[count($item)-1]."".$this->data[$key2+1][0];
                        unset($this->data[$key2+1][0]);
                        $item = array_merge($item, $this->data[$key2+1]);
                        unset($this->data[$key2+1]);
                    }

                    if($this->key != 0) {
                        if (count($this->data[$key2-1]) > count($item)){
                            continue;
                        }

                        if (count($item) > count($this->data[$key2-1])) {
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

                    $data_insert[] = $item;
                    $inserted++;
                }

            }

            $insert = DB::table($this->schema_name.'.data_mart')->insert($data_insert);
            $periode->update([
                'inserted_row' => $periode->inserted_row + $inserted,
            ]);

            $uploaded_file->update(['processed_row' => $uploaded_file->processed_row + count($data_insert)]);

            // $this->release();
            // $this->release(180);
            // Handle job...
        }, function () {
            // Could not obtain lock...

            return $this->release(80);
        });

        // } catch (\Exception $e) {
        //     dump($e);
        // }
    }
}

