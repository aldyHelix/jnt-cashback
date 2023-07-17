@php
    $back = route('ladmin.period.index');
@endphp

<a href="{{ route('ladmin.period.detail', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View Summary</a>
