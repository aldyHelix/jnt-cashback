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
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;

class ProcessCSVDataOptimized implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $schema_name;
    public $data;
    public $uploaded_file;
    // public $raw_before;
    public $period_id;
    public $timeout = 1200;
    public $maxTries = 1;
    // public $key;

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
    public function __construct($data, $schema_name, $uploaded_file, $period_id)
    {
        $this->data = $data;
        $this->schema_name = $schema_name;
        $this->uploaded_file = $uploaded_file;
        // $this->raw_before = $raw_before;
        // $this->timeout = $timeout;
        // $this->key = $key;
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
                DB::beginTransaction();

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

                foreach ($this->data as $item) {
                    $duplicates = DB::table($this->schema_name . '.data_mart')->where('no_waybill', $item[0])->first();

                    if ($duplicates) {
                        // Kalau duplicate akan diupdate data barunya.
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
                        DB::table($this->schema_name . '.data_mart')->insert($item);
                        $inserted++;
                    }
                }

                $periode->update([
                    'inserted_row' => $periode->inserted_row + $inserted,
                ]);

                $uploaded_file->update(['processed_row' => $uploaded_file->processed_row + count($data_insert)]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }, function () {
            // Could not obtain lock...
            return $this->release();
        });
    }
}
