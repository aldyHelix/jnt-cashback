<x-ladmin-auth-layout>
    <x-slot name="title">Setting Delivery</x-slot>
    @can(['ladmin.globalsetting.delivey.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.globalsetting.delivery.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Buat Delivery Setting</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            {{ Modules\Globalsetting\Datatables\DeliveryDatatables::table() }}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
    </x-slot>
    <x-slot name="scripts">
    </x-slot>
</x-ladmin-auth-layout>
