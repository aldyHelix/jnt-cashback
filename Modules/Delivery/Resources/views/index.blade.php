<x-ladmin-auth-layout>
    <x-slot name="title">Delivery</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
            {{ \Modules\Delivery\Datatables\DeliveryDatatables::table() }}
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
