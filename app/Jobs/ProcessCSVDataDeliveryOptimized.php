<?php

namespace App\Jobs;

use App\Models\PeriodeDelivery;
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
use Illuminate\Support\Facades\Redis;
use Modules\Uploadfile\Models\Uploadfile;
use Throwable;

class ProcessCSVDataDeliveryOptimized implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $schema_name;
    public $data;
    public $uploaded_file;
    public $period_id;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $timeout = 1200;
    public $maxTries = 1;
    public $key;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $schema_name, $uploaded_file, $timeout, $key, $period_id)
    {
        $this->data = $data;
        $this->schema_name = $schema_name;
        $this->uploaded_file = $uploaded_file;
        $this->timeout = $timeout;
        $this->key = $key;
        $this->period_id = $period_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Redis::throttle('jnt_cashback_horizon')->block(30)->allow(60)->every(40)->then(function () {
            try {
                $uploaded_file = Uploadfile::where('id', $this->uploaded_file->id)->first();
                $uploaded_file->update(['processing_status'=> 'ON PROCESSING']);
                $periode = PeriodeDelivery::where('id', $this->period_id)->first();

                $data_insert = [];
                $inserted = 0;
                $countData = 0;
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
                foreach ($this->data as $key2 => $item) {
                    $duplicates = DB::table($this->schema_name.'.data_mart')->where('no_waybill', $item[3])->first();

                    if($duplicates) {
                        //kalau duplicate akan di update data barunya.
                        unset($this->data[$key2]);
                        continue;
                    }

                    if ((count($item) == count($header))) {
                        $item = array_combine($header,$item);

                        $data_insert[] = $item;
                        $insert = DB::table($this->schema_name.'.data_mart')->insert($item);
                        $inserted++;
                    }
                }

                $periode->update([
                    'inserted_row' => $periode->inserted_row + $inserted,
                ]);

                $uploaded_file->update(['processed_row' => $uploaded_file->processed_row + count($data_insert)]);

            } catch (\Exception $e) {
                throw $e;
            }
        }, function () {
            // Could not obtain lock...
            return $this->release();
        });
    }
}
