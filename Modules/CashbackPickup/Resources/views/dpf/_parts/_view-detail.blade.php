@php
    $back = route('ladmin.cashbackpickup.dpf.index', ['grade' => $grading]);
@endphp

<a href="{{ route('ladmin.cashbackpickup.dpf.detail', [$code, $grading, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
