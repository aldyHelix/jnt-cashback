<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class ImportCSVCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-csv:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue cron job executed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Sample
        \Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s'));
        print_r("test");
    }
}
