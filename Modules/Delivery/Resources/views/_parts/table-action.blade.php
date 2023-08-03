<button type="button" onclick="processDelivery('{{ route('ladmin.delivery.process', ['code' => $code, 'id' => $id]) }}')" class="btn btn-sm btn-outline-primary" {{ $process_available ? ($is_locked ? 'disabled' : '') : 'disabled' }}>Proses</button>
<button type="button" onclick="lockDelivery('{{ route('ladmin.delivery.lock', ['code' => $code, 'id' => $id]) }}')" class="btn btn-sm btn-outline-primary" {{ $is_locked ? '' : 'disabled' }}>Kunci</button>

