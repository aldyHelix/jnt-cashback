@php
    $back = route('ladmin.globalsetting.delivery.index');
@endphp

@can(['ladmin.globalsetting.delivery.update'])
    <a href="{{ route('ladmin.globalsetting.setting.edit', [$id, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
@endcan

@can(['ladmin.globalsetting.delivery.delete'])
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-danger" data-bs-target="#modal-delete-delivery-{{ $id }}">
    Delete
</a>

<x-ladmin-modal id="modal-delete-delivery-{{ $id }}" class="text-start">
    <x-slot name="title">Delete Delivery Setting</x-slot>
    <x-slot name="body">
        Are you sure you want to delete this delivery setting?
    </x-slot>
    <x-slot name="footer">
        <form action="{{ route('ladmin.globalsetting.delivery.destroy', ['id' => $id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
            </x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button>
        </form>
    </x-slot>
</x-ladmin-modal>
@endcan
