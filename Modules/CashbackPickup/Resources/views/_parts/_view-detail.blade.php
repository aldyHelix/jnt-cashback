@php
    $back = route('ladmin.cashbackpickup.index');
@endphp

<a href="{{ route('ladmin.cashbackpickup.detail', [$code, $grade, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
