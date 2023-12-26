@php
    $back = route('ladmin.period.index');
@endphp

<a href="{{ route('ladmin.period.setting', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View Setting</a>
<a href="{{ route('ladmin.period.detail', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View Summary</a>
<a href="{{ route('ladmin.period.lock', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary {{ $row->is_locked ? 'disabled' : ''}}">Kunci</a>
<a href="{{ route('ladmin.period.unlock', [$code, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary {{ $row->is_locked ? '' : 'disabled'}}">Buka Kunci</a>
