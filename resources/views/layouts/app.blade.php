<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

      <link rel="icon" type="image/png" href="{{ asset('assets/images/Logo_Laperpoll.png') }}">
<link rel="apple-touch-icon" href="{{ asset('assets/images/Logo_Laperpoll.png') }}">

    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/atomic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/media.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">

    {{-- Page-specific CSS --}}
    @stack('styles')

    {{-- Link Specific Page  --}}
    @stack('links')

    <title>@yield('title', 'LaperPoll')</title>
</head>

<body>
    <x-popup-toast />
    @yield('content')

</body>
{{-- Page-specific JS --}}
@stack('scripts')
<script src="{{ asset('js/toast.js') }}"></script>
</html>