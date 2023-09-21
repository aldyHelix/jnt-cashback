@php
    $back = route('ladmin.category.index');
@endphp

@can(['ladmin.category.update'])
    <a href="{{ route('ladmin.category.edit', [$id, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
@endcan

@can(['ladmin.category.delete'])
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-danger" data-bs-target="#modal-delete-role-{{ $id }}">
    Delete
</a>

<x-ladmin-modal id="modal-delete-role-{{ $id }}" class="text-start">
    <x-slot name="title">Delete Kategori Klien Pengiriman</x-slot>
    <x-slot name="body">
        Are you sure you want to delete this kategori klien pengiriman?
    </x-slot>
    <x-slot name="footer">
        <form action="{{ route('ladmin.category.destroy', $id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
            </x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button>
        </form>
    </x-slot>
</x-ladmin-modal>
@endcan
