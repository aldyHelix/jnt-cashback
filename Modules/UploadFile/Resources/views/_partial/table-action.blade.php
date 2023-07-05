
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#modal-detail-rate-{{ $id }}">
    Detail
</a>

<x-ladmin-modal id="modal-detail-rate-{{ $id }}" class="text-start">
    <x-slot name="title">Upload File detail</x-slot>
    <x-slot name="body">
        Detail Here
    </x-slot>
    <x-slot name="footer">
        {{-- <form action="{{ route('ladmin.ratesetting.grade.destroy', ['grade' => strtolower($grading_type), 'id' => $id]) }}" method="POST">
            @csrf --}}
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</x-ladmin-button>
            {{-- <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button> --}}
        {{-- </form> --}}
    </x-slot>
</x-ladmin-modal>
