
<x-ladmin-auth-layout>
    <x-slot name="title">Process Wizard</x-slot>
    @can(['ladmin.processwizard.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.processwizard.create', ladmin()->back()) }}" class="btn btn-primary">&plus; New Periode Process</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            // datatables
            // tampilkan halaman proses
            // tombol ke halaman summary
            // tombol ke halaman report
        </x-slot>
    </x-ladmin-card>
    <x-slot name="styles">

    </x-slot>
    <x-slot name="scripts">

    </x-slot>
</x-ladmin-auth-layout>
