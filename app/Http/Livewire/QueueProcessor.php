<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Redis;

class QueueProcessor extends Component
{
    public $currentJob;
    public $isLoading = false;

    public function processQueue()
    {
        $this->isLoading = true;

        // Start the custom queue worker command
        Artisan::call('custom:queue-worker', [
            'connection' => config('queue.default'),
            '--queue' => 'default',
        ]);

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.queue-processor');
    }
}
