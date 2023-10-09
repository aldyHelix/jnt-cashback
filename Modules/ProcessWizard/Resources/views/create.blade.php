<x-ladmin-auth-layout>
    <x-slot name="title">Process Wizard Cashback</x-slot>

    @livewire('wizard', $data)

    <x-slot name="styles">
        <link href="{{ asset('css/processwizard.css') }}" rel="stylesheet" id="bootstrap-css">
        <link href="{{ asset('css/fileupload.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
        <style>
            /* Webpixels CSS */
            /* Utility and component-centric Design System based on Bootstrap for fast, responsive UI development */
            /* URL: https://github.com/webpixels/css */

            /* @import url(https://unpkg.com/@webpixels/css@1.1.5/dist/index.css); */

            /* Bootstrap Icons */
            @import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.0/font/bootstrap-icons.min.css");

        </style>
    </x-slot>
    <x-slot name="scripts">
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
        <script src="{{ asset('js/processwizard.js') }}"></script>
    </x-slot>
</x-ladmin-auth-layout>
