<button type="button" onclick="processCashback('{{ route('ladmin.cashbackpickup.process', ['code' => $code, 'grade' => $grading ,'id' => $id]) }}')" class="btn btn-sm btn-outline-primary" {{ $has_denda ? ($is_locked ? 'disabled' : '') : 'disabled' }}>Proses</button>
<button type="button" onclick="lockCashback('{{ route('ladmin.cashbackpickup.lock', ['code' => $code, 'grade' => $grading ,'id' => $id]) }}')" class="btn btn-sm btn-outline-primary" {{ $is_locked ? '' : 'disabled' }}>Kunci</button>