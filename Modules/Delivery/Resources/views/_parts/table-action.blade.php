<button type="button" onclick="processDelivery('{{ route('ladmin.delivery.process', ['code' => $code, 'id' => $id]) }}')" class="btn btn-sm btn-outline-primary" {{ $process_available ? '' : 'disabled' }}>Proses</button>
<button type="button" onclick="lockDelivery('{{ route('ladmin.delivery.lock', ['code' => $code, 'id' => $id]) }}')" class="btn btn-sm btn-outline-primary">Kunci</button>

