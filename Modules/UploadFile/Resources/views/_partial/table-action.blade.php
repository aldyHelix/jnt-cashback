
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#modal-detail-rate-{{ $file_upload->id }}">
    Detail
</a>

<x-ladmin-modal id="modal-detail-rate-{{ $file_upload->id }}" class="text-start">
    <x-slot name="title">Upload File detail</x-slot>
    <x-slot name="body">
        Detail Related
        <br>
        {{ $file_upload->month_period }} / {{ $file_upload->year_period }}

        <table class="table table-striped-columns">
            <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Filename</th>
                  <th scope="col">Row</th>
                  <th scope="col">Size</th>
                </tr>
              </thead>
              <tbody class="table-group-divider">
                @foreach ($related as $item)
                <tr>
                  <th scope="row">{{ $item->id }}</th>
                  <td>{{ $item->file_name }}</td>
                  <td>{{ decimal_format( $item->count_row) }}</td>
                  <td>{{ file_size_format($item->file_size) }}</td>
                </tr>
                @endforeach
                <tr>
                  <th colspan="2" scope="row">Count</th>
                  <td>{{ decimal_format($related->sum('count_row')) }}</td>
                  <td>{{ file_size_format($related->sum('file_size')) }}</td>
                </tr>
              </tbody>
        </table>
    </x-slot>
    <x-slot name="footer">

            {{-- <form action="{{ route('ladmin.ratesetting.grade.destroy', ['grade' => strtolower($grading_type), 'id' => $id]) }}" method="POST">
            @csrf --}}
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</x-ladmin-button>
            {{-- <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button> --}}
        {{-- </form> --}}
    </x-slot>
</x-ladmin-modal>
