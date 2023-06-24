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

class ProcessCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $header;
    public $data;
    public $month;
    public $year;
    public $raw_before;

    public $tries = 5;

    public $backoff = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header, $month, $year, $raw_before)
    {
        $this->data = $data;
        $this->header = $header;
        $this->month = $month;
        $this->year = $year;
        $this->raw_before = $raw_before;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        foreach ($this->data as $key => $item) {

            unset($item[25]);
            unset($item[26]);

            if (!(count($item) == count($this->header))) {
                $substr = substr_count($this->raw_before[$key],";");
                $substr1 = substr_count($this->raw_before[$key+1],";");
                if ($substr > $substr1) {
                    $next_item = explode(',', $this->data[$key+1][0]);
                    unset($next_item[0]);
                    $item = array_merge($item, $next_item);
                    unset($this->data[$key+1]);
                }

                if ($substr < $substr1) {
                    $item[count($item)-1] = $item[count($item)-1]."".$this->data[$key+1][0];
                    unset($data[$this->key+1][0]);
                    $item = array_merge($item, $this->data[$key+1]);
                    unset($this->data[$key+1]);
                }

                if($key != 0) {
                    $this->fail('Something went wrong.');

                    if (count($this->data[$key-1]) > count($item)){
                        continue;
                    }

                    if (count($item) > count($this->data[$key-1])) {
                        continue;
                    }
                }

                unset($item[25]);
                unset($item[26]);

            }

            if ((count($item) == count($this->header))) {
                $item = array_combine($this->header,$item);

                $item['cod'] = intval($item['cod']);
                $item['biaya_asuransi'] = intval($item['biaya_asuransi']);
                $item['biaya_kirim'] = intval($item['biaya_kirim']);
                $item['biaya_lainnya'] = intval($item['biaya_lainnya']);
                $item['total_biaya'] = intval($item['total_biaya']);
                $item['diskon'] = intval($item['diskon']);
                $item['total_biaya_setelah_diskon'] = intval($item['total_biaya_setelah_diskon']);

                // $item = array_combine($header,$item);

                if($item['waktu_ttd'] == ""){
                    $item['waktu_ttd'] = '01/01/1970 00:00';
                }

                try {
                    $insert = DB::table('cashback_'.$this->month.'_'.$this->year.'.data_mart')->insert($item);
                    //code...
                } catch (QueryException $th) {
                    $this->fail($th);
                }

            } else {
                continue;
                $this->fail('array combine gone wrong, item :'.count($item).'; header :'.count($this->header));
            }

        }

        DB::commit();

        $this->release();
    }

    public function retryUntil()
    {
        return now()->addSeconds(10);
    }
}
