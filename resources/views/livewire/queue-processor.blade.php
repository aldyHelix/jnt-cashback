<div class="me-3 ">
     {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @if($isActive)
        <span>Progress: {{ $progress }}% in progress . . . Remaining queue : {{ $totalInBatch }}</span>
    @else
        <span>The queue is not active.</span>
    @endif

    <script>
        document.addEventListener('livewire:load', function () {
            // Define the interval time (in milliseconds)
            const intervalTime = 500; // Refresh every 5 seconds

            // Set up an interval to refresh the Livewire component
            const interval = setInterval(() => {
                Livewire.emit('refreshProgress'); // Emit a custom event to refresh the progress
            }, intervalTime);

            // Stop the interval when the Livewire component is removed
            Livewire.hook('component.remove', () => {
                clearInterval(interval);
            });
        });
    </script>
</div>
