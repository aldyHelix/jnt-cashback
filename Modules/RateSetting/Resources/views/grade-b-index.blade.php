<x-ladmin-auth-layout>
    <x-slot name="title">Master Data Setting Tarif Grade {{ $grade_type}}</x-slot>
    @can(['ladmin.ratesetting.grade.b.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.ratesetting.grade.create', 'B',ladmin()->back()) }}" class="btn btn-primary">&plus; Add New</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            {{ \Modules\RateSetting\Datatables\RateSettingGradeBDatatables::table() }}

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
