<x-ladmin-auth-layout>
    <x-slot name="title">Setting Zonasi</x-slot>
    {{-- @can(['ladmin.period.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.period.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Add New</a>
    </x-slot>
    @endcan --}}
    <x-ladmin-card>
        <x-slot name="body">
            {{ \Modules\Period\Datatables\PeriodDatatables::table() }}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
    </x-slot>
    <x-slot name="scripts">
    </x-slot>
</x-ladmin-auth-layout>
