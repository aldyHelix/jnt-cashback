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
                $uploaded_file = Uploadfile::where('id', $this->uploaded_file->id);
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

                if($this->key == 0){
                    $uploaded_file->update([
                        'processing_status'=> 'STARTED',
                        'start_processed_at' => now(),
                    ]);

                    $periode->update([
                        'start_processed_at' => now(),
                    ]);

                    unset($this->data[0]);
                }

                $last_result = count($this->data);

                foreach($this->data as $index => $cell) {
                    $original = $cell;
                    $invalid = false;

                    if($index < $last_result-1) {
                        // $new_string = get_string_between($cell, '"', '"');
                        // $this->data[$index] = str_replace(';', '', $new_string);

                        if (count($cell) < 8 && count($this->data[$index+1]) < 8 && count($cell) != 0) {
                            $cell = str_replace("\r\n",'', $cell);
                            $cell = str_replace('"','', $cell);
                            $cell .= $this->data[$index+1];

                            $invalid = DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => substr($cell, 0, 12),
                                'before_raw' => $original,
                                'after_raw' => $cell,
                                'type' => 'delivery invalid',
                                'date' => now(),
                                'created_at' => now(),
                            ]);

                            $this->data[$index] = $cell;
                            unset($this->data[$index+1]);
                        }
                    }
                }

                foreach ($this->data as $key2 => $item) {
                    $duplicates = DB::table($this->schema_name.'.data_mart')->where('no_waybill', $item[3])->first();

                    if($duplicates) {
                        DB::table('log_resi')->insert([
                            'periode_id' => $this->period_id,
                            'batch_id' => $this->batch()->id,
                            'resi' => $item[3],
                            'before_raw' => json_encode($duplicates),
                            'after_raw' => json_encode($item),
                            'type' => 'delivery duplicate',
                            'date' => $item[2],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        //kalau duplicate akan di update data barunya.
                        unset($this->data[$key2]);
                        continue;
                    }

                    if ((count($item) == count($header))) {
                        $item = array_combine($header,$item);

                        $data_insert[] = $item;
                        $insert = DB::table($this->schema_name.'.data_mart')->insert($item);

                        if(!$insert) {
                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => $item['no_waybill'],
                                'before_raw' => '',
                                'after_raw' => json_encode($item),
                                'type' => 'delivery fail_insert',
                                'date' => $item['waktu_ttd'],
                                'created_at' => now()
                            ]);
                        }

                        if ($invalid) {
                            $invalid->update(['type' => 'invalid : success inserted']);
                        }
                        $inserted++;
                    } else {
                        DB::table('log_resi')->insert([
                            'periode_id' => $this->period_id,
                            'batch_id' => $this->batch()->id,
                            'resi' => $item[0],
                            'before_raw' => '',
                            'after_raw' => json_encode($item),
                            'type' => 'delivery skiped row',
                            'date' => isset($item[2]) ? $item[2] : now(),
                            'created_at' => now(),
                        ]);
                    }

                    if(isset($item[7])){
                        if($item[7] == "" && !$insert) {
                            DB::table('log_resi')->insert([
                                'periode_id' => $this->period_id,
                                'batch_id' => $this->batch()->id,
                                'resi' => substr($cell, 0, 12),
                                'before_raw' => json_encode($cell),
                                'after_raw' => json_encode($this->data[$index]),
                                'type' => 'delivery error: row not inserted',
                                'date' => now(),
                                'created_at' => now(),
                            ]);

                            unset($result[$key2]);
                            continue;
                        }
                    }
                }

                $periode->update([
                    'inserted_row' => $periode->inserted_row + $inserted,
                ]);

                $this->uploaded_file->update(['processed_row' => $uploaded_file->first()->processed_row + count($data_insert)]);

            } catch (\Exception $e) {
                throw $e;
            }
        }, function () {
            // Could not obtain lock...
            return $this->release();
        });
    }
}
