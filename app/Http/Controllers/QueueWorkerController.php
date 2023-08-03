<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

class QueueWorkerController extends Controller
{
    public function startWorkers()
    {
        $queues = $this->getQueueNames();

        foreach ($queues as $queue) {
            if (!$this->isQueueWorkerRunning($queue)) {
                $this->startWorkerForQueue($queue);
            }
        }

        return response()->json(['message' => 'Queue workers started successfully.']);
    }

    private function getQueueNames()
    {
        $connections = config('queue.connections');
        $queues = [];

        foreach ($connections as $connectionName => $connection) {
            if (!empty($connection['queue'])) {
                $queues[] = $connection['queue'];
            }
        }

        return array_unique($queues);
    }

    private function isQueueWorkerRunning($queue)
    {
        $workers = $this->getQueueWorkers();

        return in_array("queue:work --queue={$queue}", $workers);
    }

    private function getQueueWorkers()
    {
        exec('ps aux | grep "artisan queue:work"', $output);

        return $output;
    }

    private function startAllQueue()
    {
        Artisan::call('queue:work');
    }

    private function startWorkerForQueue($queue)
    {
        Artisan::call('queue:work', [
            '--queue' => $queue,
        ]);
    }
}
