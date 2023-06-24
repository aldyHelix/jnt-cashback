<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <link href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon">



    @stack('before-styles')
    @vite('Modules/Ladmin/Resources/sass/ladmin.scss')
    {{ $styles ?? null }}
    @stack('after-styles')

    <style>
        .row {
            --bs-gutter-x: 0rem;
        }
    </style>

</head>

<body class="bg-dark ladmin-auth">

    {{ $slot }}

    @stack('before-scripts')
    @vite(['Modules/Ladmin/Resources/js/ladmin.js'])
    {{ $scripts ?? null }}
    @stack('after-scripts')
</body>

</html>
