<x-ladmin-auth-layout>
    <x-slot name="title">Cashback Pickup Grading DPF</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
            {{ \Modules\CashbackPickup\Datatables\DPFDatatables::table() }}
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
