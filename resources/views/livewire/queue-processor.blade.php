<div class="me-3 ">
    {{-- The Master doesn't talk, he acts. --}}
    @if ($isLoading)
        <div class="loading-icon"><span>Loading...</span></div>
    @elseif ($currentJob)
        <span>Current Job: {{ $currentJob }}</span>
    @else
        <span>No job is currently being processed.</span>
    @endif
</div>
