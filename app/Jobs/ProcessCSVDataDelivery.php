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
use Modules\UploadFile\Models\UploadFile;
use Throwable;

class ProcessCSVDataDelivery implements ShouldQueue
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
    public $backoff = 1;
    public $tries = 1;
    public $key;
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
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->key)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $uploaded_file = UploadFile::where('id', $this->uploaded_file->id)->first();
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
                $duplicates = DB::table($this->schema_name.'.data_mart')->where('no_waybill', $item[0])->first();

                if($duplicates) {
                    //kalau duplicate akan di update data barunya.
                    unset($this->data[$key2]);
                    continue;
                }

                if ((count($item) == count($header))) {
                    $item = array_combine($header,$item);

                    $data_insert[] = $item;
                    $inserted++;
                }
            }

            $insert = DB::table($this->schema_name.'.data_mart')->insert($data_insert);

            $periode->update([
                'inserted_row' => $periode->inserted_row + $inserted,
            ]);

            $uploaded_file->update(['processed_row' => $uploaded_file->processed_row + $countData]);

            $this->release();
        } catch (\Exception $e) {
            dump($e);
        }
    }
}
