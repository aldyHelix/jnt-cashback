<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Queue;

class QueueStatus extends Component
{
    public $queueName = 'default';
    public $isActive = false;

    public function mount()
    {
        $this->updateQueueStatus();

        // Refresh queue status every 5 seconds (adjust the interval as needed)
        $this->dispatchBrowserEvent('start-queue-status-updates', ['interval' => 5000]);
    }

    public function updateQueueStatus()
    {
        $connection = config('queue.default');
        $queue = Queue::connection($connection)->size($this->queueName);
        $this->isActive = $queue > 0;
    }

    public function render()
    {
        return view('livewire.queue-status');
    }
}
