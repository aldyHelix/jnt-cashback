@php
    $back = route('ladmin.cashbackpickup.index', ['grade' => $grading]);
@endphp

<a href="{{ route('ladmin.cashbackpickup.detail', [$code, $grading, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
