<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'web-dabb'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/logo_anri.png') }}">

    <!-- CMS RTE Content CSS — loads RTE content styles for guest display -->
    <link rel="stylesheet" href="{{ asset('cms_rte/rte_theme_default.css') }}">
    <!-- Guest-scoped override: removes editor chrome, adapts content styles for guest layout -->
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="@yield('body-class', 'font-sans text-gray-900 antialiased flex flex-col min-h-screen')">
    @include('navbar')

    @if (isset($slot) && !$__env->yieldContent('content'))
        {{-- Component mode: used by <x-guest-layout> (confirm-password, verify-email) --}}
        <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-12">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500 mt-8" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg mb-8">
                {{ $slot }}
            </div>
        </div>
    @else
        {{-- Extends mode: used by @extends('layouts.guest') --}}
        @yield('content')
    @endif

    @include('footer')

    <x-chat-widget />

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @stack('scripts')

    {{-- Login required modal (shown for protected public pages when guest) --}}
    @if (!empty($requiresLoginModal))
        @include('partials.login_modal')
        <script>
            document.body.style.overflow = 'hidden';
        </script>
    @endif
</body>

</html>
