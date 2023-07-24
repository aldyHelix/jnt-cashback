<div class="me-3 ">
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @if($isActive)
        <span>The queue is active.</span>
    @else
        <span>The queue is not active.</span>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('queueStatusUpdated', () => {
                // Call the updateQueueStatus method to get the latest status
                @this.call('updateQueueStatus');
            });

            // Start periodic updates
            document.addEventListener('start-queue-status-updates', function (event) {
                setInterval(function () {
                    Livewire.emit('queueStatusUpdated');
                }, event.detail.interval);
            });
        });
    </script>
    @endpush
</div>
