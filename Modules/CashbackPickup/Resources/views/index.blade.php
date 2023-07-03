<x-ladmin-auth-layout>
    <x-slot name="title">Cashback Pickup Grading {{ $grade }}</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
            @switch($grade)
                @case(1)
                    {{ \Modules\CashbackPickup\Datatables\Grading1Datatables::table() }}
                    @break
                @case(2)
                    {{ \Modules\CashbackPickup\Datatables\Grading2Datatables::table() }}
                    @break
                @case(3)
                    {{ \Modules\CashbackPickup\Datatables\Grading3Datatables::table() }}
                    @break
                @default
                    {{ \Modules\CashbackPickup\Datatables\Grading1Datatables::table() }}
                    @break
            @endswitch
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
