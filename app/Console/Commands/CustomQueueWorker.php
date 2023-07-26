<?php

namespace App\Console\Commands;

use Illuminate\Queue\Console\WorkCommand;

class CustomQueueWorker extends WorkCommand
{
    protected $signature = 'custom:queue-worker {connection? : The name of the queue connection to work} {--queue= : The names of the queues to work}';
    protected $description = 'Process the next job on a queue';

    public function runWorker($connection, $queue)
    {
        $this->beforeLoop();

        while (true) {
            $job = $this->getNextJob($connection, $queue);

            if ($job) {
                $this->output->writeln("<info>Processing job:</info> " . $job->resolveName());
                $this->runJob($job, $connection, $queue);
                $this->output->writeln("<info>Processed job:</info> " . $job->resolveName());
                $this->queueManager->getQueue($connection)->deleteReserved($queue, $job);
            } else {
                $this->sleep();
            }
        }
    }
}
