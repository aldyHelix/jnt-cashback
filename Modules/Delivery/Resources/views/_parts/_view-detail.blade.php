@php
    $back = route('ladmin.delivery.index');
@endphp

<a href="{{ route('ladmin.delivery.detail', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
