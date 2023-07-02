@php
    $back = route('ladmin.ratesetting.delivery.index');
@endphp

@can(['rate.delivery.update'])
    <a href="{{ route('ladmin.ratesetting.delivery.edit', [ $id, 'back' => $back]) }}" class="btn btn-sm btn-outline-primary">View</a>
@endcan

@can(['rate.delivery.destroy'])
<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-danger" data-bs-target="#modal-delete-rate-{{ $id }}">
    Delete
</a>

<x-ladmin-modal id="modal-delete-rate-{{ $id }}" class="text-start">
    <x-slot name="title">Delete Rate Setting Delivery Fee</x-slot>
    <x-slot name="body">
        Are you sure you want to delete this rate setting?
    </x-slot>
    <x-slot name="footer">
        <form action="{{ route('ladmin.ratesetting.delivery.destroy', $id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No
            </x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="danger">Yes</x-ladmin-button>
        </form>
    </x-slot>
</x-ladmin-modal>
@endcan
