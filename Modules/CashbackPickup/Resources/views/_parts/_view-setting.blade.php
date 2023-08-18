@php
    $back = route('ladmin.cashbackpickup.index', ['grade' => $grading]);
@endphp

<a href="{{ route('ladmin.period.setting', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View Setting</a>
