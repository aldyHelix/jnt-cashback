<div class="container text-start">
    {{-- <span>Queue Status: {{ $isActive ? 'Active' : 'Not Active' }}</span> --}}
    <button class="btn btn-primary" ="updateQueueStatus" @if($isActive) disabled @endif>Start Process</button>
</div>
