<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#modal-delivery-fee-{{ $id }}">
    Setting Delivery Fee
</a>

<x-ladmin-modal id="modal-delivery-fee-{{ $id }}" class="text-start">
    <x-slot name="title">Setting Delivery Fee</x-slot>
    <form action="{{ route('ladmin.collectionpoint.destroy', $id) }}" method="POST">
        <x-slot name="body">
            <div class="row d-flex align-items-center">
                <label for="sprinter_pickup" class="form-label col-lg-3">Sprinter Pickup <span class="text-danger">*</span></label>
                <x-ladmin-input id="sprinter_pickup" type="text" class="mb-3 col" required name="sprinter_pickup"
                    value="{{ old('sprinter_pickup', '') }}" placeholder="Sprinter Pickup" />
            </div>


        </x-slot>
        <x-slot name="footer">
            @csrf
            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="primary">Simpan</x-ladmin-button>
        </x-slot>
    </form>
</x-ladmin-modal>
