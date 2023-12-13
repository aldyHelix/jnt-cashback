@php
    $back = route('ladmin.collectionpoint.index');
@endphp

@can(['ladmin.collectionpoint.update'])
    <a href="{{ route('ladmin.collectionpoint.edit', [$id, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
@endcan

@can(['ladmin.collectionpoint.delete'])
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-danger" data-bs-target="#modal-delete-role-{{ $id }}">
    Delete
</a>

<x-ladmin-modal id="modal-delete-role-{{ $id }}" class="text-start">
    <x-slot name="title">Delete Collection Point</x-slot>
    <x-slot name="body">
        Are you sure you want to delete this collection point?
    </x-slot>
    <x-slot name="footer">
        <form action="{{ route('ladmin.collectionpoint.destroy', $id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
            </x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button>
        </form>
    </x-slot>
</x-ladmin-modal>
@endcan
