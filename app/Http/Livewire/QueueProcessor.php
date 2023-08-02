<?php
namespace App\Http\Livewire;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Livewire\Component;
use Illuminate\Support\Facades\Redis;

class QueueProcessor extends Component
{
    public $progress = 0;
    public $totalInBatch = 0; // New property to store the total items in the current batch
    public $queueName = 'default';
    public $isActive = false;

    protected $listeners = ['updateProgress' , 'refreshProgress'];

    public function updateProgress($progress)
    {
        // Update the progress value received from the event
        $batchId = session('batchId');
        if($batchId){
            $batch = Bus::findBatch($batchId);
        }
        $this->progress = $batch->progress ?? 0;
        $connection = config('queue.default');
        $queue = Queue::connection($connection)->size($this->queueName);
        $this->isActive = $queue > 0;
        $this->totalInBatch = $queue;

        // This will automatically update the Livewire component on the client side.
        // The updated progress value will be reflected in the Livewire component's view.
    }

    public function refreshProgress()
    {
        // You can perform any necessary actions here before emitting the updateProgress event again.
        // For example, you can check the current progress of the job and update the $progress property accordingly.

        // For demonstration purposes, let's just emit the 'updateProgress' event with the current progress value.
        $this->emit('updateProgress', $this->progress);
    }

    public function render()
    {
        return view('livewire.queue-processor');
    }
}
