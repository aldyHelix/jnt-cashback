<x-ladmin-auth-layout>
    <x-slot name="title">Periode Data</x-slot>
    @can(['ladmin.period.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.period.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Add New</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            {{ \Modules\Period\Datatables\PeriodDatatables::table() }}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        {{-- <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script> --}}
    </x-slot>
    <x-slot name="scripts">
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script> --}}
    </x-slot>
</x-ladmin-auth-layout>
