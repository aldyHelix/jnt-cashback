<x-ladmin-auth-layout>
    <x-slot name="title">Cashback Pickup Grading {{ $grade }}</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
            @switch($grade)
                @case(1)
                    {{ \Modules\Cashbackpickup\Datatables\Grading1Datatables::table() }}
                    @break
                @case(2)
                    {{ \Modules\Cashbackpickup\Datatables\Grading2Datatables::table() }}
                    @break
                @case(3)
                    {{ \Modules\Cashbackpickup\Datatables\Grading3Datatables::table() }}
                    @break
                @default
                    {{ \Modules\Cashbackpickup\Datatables\Grading1Datatables::table() }}
                    @break
            @endswitch
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        {{-- <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script> --}}
    </x-slot>
    <x-slot name="scripts">
        <script>
            function processCashback(route) {
              window.location.href = route;
            }

            function lockCashback(route) {
              window.location.href = route;
            }
          </script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script> --}}
    </x-slot>
</x-ladmin-auth-layout>
