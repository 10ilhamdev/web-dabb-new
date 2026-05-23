<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? __('home.dashboard.title')) - {{ __('home.site_name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/logo_anri.png') }}">

    <!-- Google Fonts & CDN Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdn.datatables.net" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Sidebar & Alert CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alert.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">

    @stack('styles')
</head>

<body class="text-gray-800 antialiased overflow-hidden">
    <!-- Alpine Root Component for Sidebar State -->
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" class="flex h-screen bg-[#F4F6F9]">

        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'sidebar-open' : 'sidebar-closed'"
            class="sidebar text-white transition-all duration-300 ease-in-out flex flex-col h-full z-20 shrink-0">

            <!-- Logo Section -->
            <div class="flex items-center justify-center border-b border-white/10 px-4 py-4">
                <!-- Expanded Logo -->
                <div x-show="sidebarOpen" class="flex flex-col items-center w-full transition-opacity duration-300"
                    x-transition.opacity>
                    <div class="flex items-center justify-start w-full gap-2">
                        <img src="{{ asset('image/logo_anri.png') }}" alt="Logo ANRI"
                            class="h-10 w-auto shrink-0 drop-shadow-md">
                        <div class="flex flex-col items-start leading-tight min-w-0">
                            <div class="font-bold text-[12px] tracking-wide text-white drop-shadow-sm">
                                {{ __('dashboard.logo.line1') }}</div>
                            <div class="font-bold text-[12px] tracking-wide text-white drop-shadow-sm">
                                {{ __('dashboard.logo.line2') }}</div>
                            {{-- <div class="font-normal text-[9px] text-blue-200/80 mt-0.5 whitespace-nowrap">
                                {{ __('dashboard.logo.tagline') }}</div> --}}
                        </div>
                    </div>
                </div>

                <!-- Collapsed Logo -->
                <div x-show="!sidebarOpen" class="flex justify-center w-full transition-opacity duration-300"
                    x-transition.opacity x-cloak>
                    <img src="{{ asset('image/logo_anri.png') }}" alt="Logo ANRI" class="h-9 w-auto shrink-0">
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 mt-8 px-3 space-y-3 overflow-y-auto no-scrollbar">
                <!-- Beranda -->
                <a href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'active text-white' : 'text-[#b8cdef] hover:text-white' }} flex items-center px-3 py-3 rounded-lg group">
                    <svg class="w-6 h-6 shrink-0 opacity-90 group-hover:opacity-100" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-4 text-[14px] font-medium transition-opacity duration-300"
                        x-transition.opacity>{{ __('dashboard.sidebar.home') }}</span>
                </a>

                @php
                    $userRole = auth()->user() ? \App\Models\Role::where('name', auth()->user()->role)->first() : null;
                @endphp

                <!-- Laporan -->
                <div x-data="{ open: {{ request()->routeIs('cms.reports.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="sidebar-link w-full flex items-center px-3 py-3 rounded-lg text-[#b8cdef] hover:text-white group {{ request()->routeIs('cms.reports.*') ? 'active text-white' : '' }}">
                        <svg class="w-6 h-6 shrink-0 opacity-80 group-hover:opacity-100 {{ request()->routeIs('cms.reports.*') ? 'text-white opacity-100' : '' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen"
                            class="ml-4 text-[14px] font-medium transition-opacity duration-300 whitespace-nowrap {{ request()->routeIs('cms.reports.*') ? 'text-white' : '' }}"
                            x-transition.opacity>{{ __('dashboard.sidebar.reports') }}</span>
                        <svg x-show="sidebarOpen"
                            class="cms-chevron w-4 h-4 ml-auto {{ request()->routeIs('cms.reports.*') ? 'text-white' : 'text-[#b8cdef] group-hover:text-white' }}"
                            :style="open ? 'transform: rotate(0deg)' : 'transform: rotate(-90deg)'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown Items -->
                    <div x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="cms-dropdown mt-2 ml-9 mr-1 py-2 px-1 space-y-1">

                        <a href="{{ route('cms.reports.kunjungan') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.reports.kunjungan') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.reports_kunjungan') }}
                        </a>
                        <a href="{{ route('cms.reports.pengunjung') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.reports.pengunjung') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.reports_pengunjung') }}
                        </a>
                        <a href="{{ route('cms.reports.konsultasi') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.reports.konsultasi') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.reports_konsultasi') }}
                        </a>
                        <a href="{{ route('cms.reports.online') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.reports.online') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.reports_online') }}
                        </a>
                    </div>
                </div>

                <!-- CMS -->
                @if (!$userRole || $userRole->hasPermission('cms.features') || $userRole->hasPermission('cms.footer') || $userRole->hasPermission('cms.disclaimer'))
                <div x-data="{ open: {{ request()->routeIs('cms.features.*', 'cms.settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="sidebar-link w-full flex items-center px-3 py-3 rounded-lg text-[#b8cdef] hover:text-white group {{ request()->routeIs('cms.features.*', 'cms.settings.*') ? 'active text-white' : '' }}">
                        <svg class="w-6 h-6 shrink-0 opacity-80 group-hover:opacity-100 {{ request()->routeIs('cms.features.*', 'cms.settings.*') ? 'text-white opacity-100' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen"
                            class="ml-4 text-[14px] font-medium transition-opacity duration-300 whitespace-nowrap {{ request()->routeIs('cms.features.*', 'cms.settings.*') ? 'text-white' : '' }}"
                            x-transition.opacity>{{ __('dashboard.sidebar.cms') }}</span>
                        <svg x-show="sidebarOpen"
                            class="cms-chevron w-4 h-4 ml-auto {{ request()->routeIs('cms.features.*', 'cms.settings.*') ? 'text-white' : 'text-[#b8cdef] group-hover:text-white' }}"
                            :style="open ? 'transform: rotate(0deg)' : 'transform: rotate(-90deg)'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown Items -->
                    <div x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="cms-dropdown mt-2 ml-9 mr-1 py-2 px-1 space-y-1">

                        @if (!$userRole || $userRole->hasPermission('cms.features'))
                        <a href="{{ route('cms.features.index') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.features.*') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.cms_features') }}
                        </a>
                        @endif
                        @if (!$userRole || $userRole->hasPermission('cms.footer'))
                        <a href="{{ route('cms.settings.footer.edit') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.settings.footer.*') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            {{ __('dashboard.sidebar.cms_footer') }}
                        </a>
                        @endif
                        @if (!$userRole || $userRole->hasPermission('cms.disclaimer'))
                        <a href="{{ route('cms.settings.disclaimer.edit') }}"
                            class="flex items-center px-3 py-2 text-[13px] rounded-md {{ request()->routeIs('cms.settings.disclaimer.*') ? 'active-item' : 'text-white/70 hover:text-white font-medium' }}">
                            Disclaimer
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Pengguna -->
                @if (!$userRole || $userRole->hasPermission('pengguna.users') || $userRole->hasPermission('pengguna.roles'))
                <a href="{{ route('cms.pengguna.index') }}"
                    class="sidebar-link {{ request()->routeIs('cms.pengguna.*') ? 'active text-white' : 'text-[#b8cdef] hover:text-white' }} flex items-center px-3 py-3 rounded-lg group">
                    <svg class="w-6 h-6 shrink-0 opacity-80 group-hover:opacity-100" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-4 text-[14px] font-medium transition-opacity duration-300"
                        x-transition.opacity>{{ __('dashboard.sidebar.users') }}</span>
                </a>
                @endif
            </nav>

            <!-- Bottom Links -->
            <div class="px-3 pb-6 pt-4 space-y-2 mt-auto">
                <a href="{{ route('home') }}"
                    class="sidebar-link flex items-center px-3 py-3 rounded-lg text-[#b8cdef] hover:text-white group"
                    title="{{ __('dashboard.sidebar.visit_website') }}">
                    <svg class="w-6 h-6 shrink-0 opacity-80 group-hover:opacity-100" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen"
                        class="ml-3 font-semibold tracking-wide text-[13.5px] whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                        :class="sidebarOpen ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'">{{ __('dashboard.sidebar.visit_website') }}</span>
                </a>

                <button type="button" @click="$dispatch('open-logout-modal')"
                    class="sidebar-link w-full flex items-center px-3 py-3 rounded-lg text-[#b8cdef] hover:text-white group border-none bg-transparent cursor-pointer"
                    title="{{ __('dashboard.sidebar.logout') }}">
                    <svg class="w-6 h-6 shrink-0 opacity-80 group-hover:opacity-100" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    <span x-show="sidebarOpen"
                        class="ml-3 font-semibold tracking-wide text-[13.5px] whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                        :class="sidebarOpen ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'">{{ __('dashboard.sidebar.logout') }}</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-white">

            <!-- Top Header -->
            <header
                class="bg-white border-b border-gray-100 flex items-center justify-between px-3.5 py-3 sm:px-6 sm:py-4 shrink-0 shadow-sm relative z-10">
                <!-- Left: Hamburger & Breadcrumb -->
                <div class="flex items-center space-x-3 sm:space-x-6 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-white bg-[#174E93] hover:bg-blue-800 p-1.5 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <!-- Header slot for optional page title/breadcrumb -->
                    <div class="min-w-0">
                        @hasSection('header')
                            @yield('header')
                        @else
                            <div class="text-[11px] sm:text-[13px] text-gray-500 font-medium flex items-center gap-1 min-w-0">
                                <span class="hidden md:inline-flex items-center gap-1 min-w-0">
                                    @hasSection('breadcrumb_items')
                                        @yield('breadcrumb_items')
                                    @else
                                        @hasSection('breadcrumb_parent')
                                            @hasSection('breadcrumb_parent_url')
                                                <a href="@yield('breadcrumb_parent_url')"
                                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                                    @yield('breadcrumb_parent')
                                                </a>
                                            @else
                                                <span class="text-gray-400">@yield('breadcrumb_parent')</span>
                                            @endif
                                        @else
                                            <a href="{{ route('dashboard') }}"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                                {{ __('dashboard.header.breadcrumb_home') }}
                                            </a>
                                        @endif
                                    @endif
                                    <span class="text-gray-300">/</span>
                                </span>
                                <span class="text-[#0ea5e9] truncate max-w-[100px] xs:max-w-none sm:max-w-none">@yield('breadcrumb_active', __('dashboard.header.breadcrumb_home'))</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Language & Profile -->
                <div class="flex items-center space-x-3 sm:space-x-6 min-w-0">
                    <!-- Language Switcher -->
                    <div class="flex items-center space-x-1.5 text-[12px] sm:text-[13px] font-semibold tracking-wide shrink-0">
                        <a href="{{ route('locale.switch', 'id') }}"
                            class="px-1.5 py-0.5 rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-[#174E93] text-white' : 'text-gray-400 hover:text-gray-800 hover:bg-gray-100' }}">
                            ID
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('locale.switch', 'en') }}"
                            class="px-1.5 py-0.5 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#174E93] text-white' : 'text-gray-400 hover:text-gray-800 hover:bg-gray-100' }}">
                            EN
                        </a>
                    </div>

                    <div x-data="{ profileOpen: false }" class="relative flex items-center pl-3 sm:pl-4 border-l border-gray-200 min-w-0">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                            class="flex items-center focus:outline-none w-full text-left min-w-0">
                            <div
                                class="flex items-center justify-center w-[40px] h-[40px] p-[2px] bg-white border border-gray-200 rounded-full shadow-sm shrink-0">
                                <img class="w-full h-full rounded-full object-cover block"
                                    src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'User') . '&background=E5E7EB&color=374151&bold=true' }}"
                                    alt="Avatar">
                            </div>
                            <div class="ml-2 sm:ml-3 flex flex-col min-w-0" style="line-height: 1.2;">
                                <span
                                    class="text-[13px] font-semibold text-gray-800 truncate max-w-[75px] sm:max-w-none">{{ auth()->user()->name ?? __('dashboard.profile.default_name') }}</span>
                                <span
                                    class="text-[11px] text-gray-500 truncate max-w-[75px] sm:max-w-none">{{ is_null(auth()->user()->password) ? __('dashboard.profile.password_not_set') : __("dashboard.roles." . auth()->user()->role) }}</span>
                            </div>
                            <div
                                class="hidden sm:inline-flex ml-4 p-1 rounded-full border border-gray-200 text-gray-400 hover:text-gray-600 focus:outline-none shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 top-[120%] mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50 py-1"
                            style="display: none;">

                            <!-- Kelola Akun -->
                            <a href="{{ route('profile.show') }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-[#EEF2FF] hover:text-[#4F46E5] transition-colors rounded-t-xl group">
                                <svg class="w-5 h-5 mr-3 text-[#60A5FA] group-hover:text-[#4F46E5]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span
                                    class="font-medium text-[13px]">{{ __('dashboard.profile.manage_account') }}</span>
                            </a>

                            <!-- Ubah Kata Sandi -->
                            <a href="{{ route('profile.password') }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-[#F472B6]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                                <span
                                    class="font-medium text-[13px]">{{ __('dashboard.profile.change_password') }}</span>
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Aktivitas Log -->
                            <a href="{{ route('profile.activity') }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors rounded-b-xl">
                                <svg class="w-5 h-5 mr-3 text-[#C084FC]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span
                                    class="font-medium text-[13px]">{{ __('dashboard.profile.activity_log') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div id="mainContent" class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F4F6FA] p-4 sm:p-6 lg:p-8">
                <main class="max-w-7xl mx-auto w-full">
                    @if (isset($slot) && !$__env->yieldContent('content'))
                        {{-- Component mode: used by <x-app-layout> --}}
                        {{ $slot }}
                    @else
                        {{-- Extends mode: used by @extends('layouts.app') --}}
                        @yield('content')
                    @endif
                </main>

                <!-- Footer within content area -->
                <footer
                    class="max-w-7xl mx-auto w-full mt-10 pt-4 border-t border-gray-200 flex justify-between items-center text-[12px] text-gray-400 font-medium">
                    <div>{{ date('Y') }} &copy; {{ __('dashboard.footer.copyright') }}</div>
                    <img src="{{ asset('image/logo_anri.png') }}" alt="Logo ANRI"
                        class="h-4 opacity-50 grayscale hover:grayscale-0 transition-all">
                </footer>
            </div>
        </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-data="{ show: false }" x-on:open-logout-modal.window="show = true"
        x-on:keydown.escape.window="show = false" x-show="show" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center">

        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="show = false" class="absolute inset-0 bg-black/50"></div>

        <!-- Modal -->
        <div x-show="show" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">

            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('dashboard.sidebar.logout_confirm_title') }}</h3>
            <p class="text-sm text-gray-500 mb-6">{{ __('dashboard.sidebar.logout_confirm_message') }}</p>

            <div class="flex items-center justify-center gap-3">
                <button @click="show = false"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    {{ __('dashboard.sidebar.logout_cancel') }}
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                        {{ __('dashboard.sidebar.logout_confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Set Password Modal for Google Login Users -->
    @if (auth()->check() && is_null(auth()->user()->password))
        <div x-data="{ showSetPassword: true }" x-show="showSetPassword"
            class="fixed inset-0 z-[100] flex items-center justify-center">

            <!-- Backdrop -->
            <div x-show="showSetPassword" class="absolute inset-0 bg-black/60 transition-opacity"></div>

            <!-- Modal Content -->
            <div x-show="showSetPassword" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-8">

                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-5">
                    <svg class="w-8 h-8 text-[#174E93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('dashboard.set_password.title') }}</h3>
                    <p class="text-[13px] text-gray-500">
                        {{ __('dashboard.set_password.description') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('password.set') }}">
                    @csrf

                    <div class="space-y-4">
                        <!-- Role Selector -->
                        <div>
                            <label for="role"
                                class="block text-[13px] font-medium text-gray-700 mb-1">{{ __('dashboard.set_password.select_role') }}
                                <span class="text-red-500">*</span></label>
                            <select id="role" name="role" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#174E93] focus:border-[#174E93] text-[14px]">
                                <option value="" disabled selected>{{ __('dashboard.set_password.select_placeholder') }}</option>
                                <option value="umum">{{ __('dashboard.roles.umum') }}</option>
                                <option value="pelajar_mahasiswa">{{ __('dashboard.roles.pelajar_mahasiswa') }}</option>
                                <option value="instansi_swasta">{{ __('dashboard.roles.instansi_swasta') }}</option>
                            </select>
                            @error('role', 'setPassword')
                                <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Keperluan -->
                        <div>
                            <label for="jenis_keperluan"
                                class="block text-[13px] font-medium text-gray-700 mb-1">{{ __('dashboard.profile.col_jenis_keperluan') }} <span
                                    class="text-red-500">*</span></label>
                            <select id="jenis_keperluan" name="jenis_keperluan" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#174E93] focus:border-[#174E93] text-[14px]">
                                <option value="" disabled selected>{{ __('dashboard.set_password.select_placeholder') }}</option>
                                <option value="Hanya Daftar Akun">{{ __('dashboard.profile.purpose_register_only') }}</option>
                                <option value="Penelitian">{{ __('dashboard.profile.purpose_research') }}</option>
                                <option value="Kunjungan">{{ __('dashboard.profile.purpose_visit') }}</option>
                            </select>
                            @error('jenis_keperluan', 'setPassword')
                                <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Judul Keperluan -->
                        <div>
                            <label for="judul_keperluan"
                                class="block text-[13px] font-medium text-gray-700 mb-1">{{ __('dashboard.profile.col_judul_keperluan') }}
                                <span class="text-red-500">*</span></label>
                            <input id="judul_keperluan" type="text" name="judul_keperluan" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#174E93] focus:border-[#174E93] text-[14px]"
                                placeholder="{{ __('dashboard.set_password.judul_placeholder') }}">
                            @error('judul_keperluan', 'setPassword')
                                <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password"
                                class="block text-[13px] font-medium text-gray-700 mb-1">{{ __('dashboard.set_password.new_password') }}
                                <span class="text-red-500">*</span></label>
                            <input id="password" type="password" name="password" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#174E93] focus:border-[#174E93] text-[14px]"
                                placeholder="{{ __('dashboard.set_password.password_placeholder') }}">
                            @error('password', 'setPassword')
                                <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-[13px] font-medium text-gray-700 mb-1">{{ __('dashboard.set_password.confirm_password') }}
                                <span class="text-red-500">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#174E93] focus:border-[#174E93] text-[14px]"
                                placeholder="{{ __('dashboard.set_password.password_placeholder') }}">
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <button type="submit"
                            class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                            {{ __('dashboard.set_password.submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($errors->setPassword->any())
            <!-- If there are validation errors, ensure modal stays open -->
            <script>
                document.addEventListener('alpine:init', () => {
                    // It will remain open because x-data="{ showSetPassword: true }"
                });
            </script>
        @endif
    @endif

    <svg xmlns="http://www.w3.org/2000/svg" class="hidden">
        <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>

    {{-- SweetAlert2 & html2canvas — always loaded --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>

    @if (session('success') || session('error') || $errors->any() || session('warning') || session('info'))
        @php
            $tType = 'info';
            $tMsg = session('info');

            if (session('success')) {
                $tType = 'success';
                $tMsg = session('success');
            } elseif (session('error') || $errors->any()) {
                $tType = 'error';
                $tMsg = session('error') ?? $errors->first();
            } elseif (session('warning')) {
                $tType = 'warning';
                $tMsg = session('warning');
            }
        @endphp

        {{-- SweetAlert2 Toast --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: '{{ $tType }}',
                    title: `{!! addslashes($tMsg) !!}`
                });
            });
        </script>
    @endif

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>

    <!-- CMS RichTextEditor (MIT licensed, self-built) -->
    <link rel="stylesheet" href="{{ asset('cms_rte/rte_theme_default.css?v=' . (file_exists(public_path('cms_rte/rte_theme_default.css')) ? filemtime(public_path('cms_rte/rte_theme_default.css')) : time())) }}">
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/richtexteditor_content.css')) : time())) }}">
    <script src="{{ asset('cms_rte/rte.js?v=' . (file_exists(public_path('cms_rte/rte.js')) ? filemtime(public_path('cms_rte/rte.js')) : time())) }}" defer></script>
    <script src="{{ asset('cms_rte/all_plugins.js?v=' . (file_exists(public_path('cms_rte/all_plugins.js')) ? filemtime(public_path('cms_rte/all_plugins.js')) : time())) }}" defer></script>

    {{-- Media Carousel Runtime Logic (shared with guest) --}}
    <script>
        // ── Caption Modal Helpers ──
        var __captionModal = null;
        var __captionModalText = null;
        function __openCaptionModal(text) {
            if (!__captionModal) { __captionModal = document.getElementById('rte-caption-modal'); }
            if (!__captionModalText) { __captionModalText = document.getElementById('rte-caption-modal-text'); }
            if (__captionModal && __captionModalText) {
                __captionModalText.textContent = text;
                __captionModal.style.display = 'flex';
            }
        }
        function __closeCaptionModal() {
            if (!__captionModal) { __captionModal = document.getElementById('rte-caption-modal'); }
            if (__captionModal) { __captionModal.style.display = 'none'; }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var closeBtn = document.getElementById('rte-caption-modal-close');
            var overlay = document.getElementById('rte-caption-modal');
            if (closeBtn) closeBtn.onclick = function() { __closeCaptionModal(); };
            if (overlay) overlay.onclick = function(e) { if (e.target === this) __closeCaptionModal(); };
        });

        window.addEventListener('load', function() {

            function upgradeCaptionButtons() {
                // ── 1. Handle OLD format: .rte-carousel-caption div inside slide ──
                document.querySelectorAll('.rte-carousel-caption').forEach(function(cap) {
                    var slide = cap.parentElement;
                    if (!slide || !slide.classList.contains('rte-carousel-slide')) return;
                    var captionText = (cap.textContent || '').trim();
                    cap.parentNode.removeChild(cap);
                    if (!slide.querySelector('.rte-carousel-caption-btn') && captionText) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'rte-carousel-caption-btn';
                        btn.title = 'Lihat Keterangan';
                        btn.textContent = '?';
                        btn.setAttribute('data-caption', captionText);
                        btn.onclick = function(e) { e.stopPropagation(); __openCaptionModal(captionText); };
                        slide.appendChild(btn);
                    }
                });

                // ── 2. Handle NEW format: .rte-carousel-caption-btn already in HTML ──
                document.querySelectorAll('.rte-carousel-caption-btn').forEach(function(btn) {
                    if (btn._captionBound) return; // already wired
                    var captionText = btn.getAttribute('data-caption') || '';
                    if (!captionText) {
                        var popup = btn.parentElement && btn.parentElement.querySelector('.rte-carousel-caption-popup');
                        if (popup) {
                            var textNode = popup.querySelector('div') || popup.lastChild;
                            captionText = textNode ? (textNode.textContent || '').trim() : '';
                        }
                    }
                    // Remove any old inline popup (no longer needed)
                    var oldPopup = btn.parentElement && btn.parentElement.querySelector('.rte-carousel-caption-popup');
                    if (oldPopup) oldPopup.parentNode.removeChild(oldPopup);
                    var text = captionText;
                    btn.setAttribute('data-caption', text);
                    btn.onclick = null;
                    btn.addEventListener('click', function(e) { e.stopPropagation(); __openCaptionModal(text); });
                    btn._captionBound = true;
                });
            }

            function initCarousels() {
                // Upgrade caption buttons first
                upgradeCaptionButtons();

                var carousels = document.querySelectorAll('.rte-carousel-container');
                carousels.forEach(function(container) {
                    if (container._carouselInit) return;
                    var slides = container.querySelectorAll('.rte-carousel-slide');
                    var dots = container.querySelectorAll('.rte-carousel-dot');
                    var current = 0;
                    var total = slides.length;
                    var autoplay = container.getAttribute('data-autoplay') === 'true';
                    var interval = parseInt(container.getAttribute('data-interval') || '3000', 10);
                    var timer = null;

                    function showSlide(idx) {
                        if (idx < 0) idx = total - 1;
                        if (idx >= total) idx = 0;
                        slides.forEach(function(s, i) {
                            if (i === idx) s.classList.add('active');
                            else s.classList.remove('active');
                        });
                        dots.forEach(function(d, i) {
                            if (i === idx) d.classList.add('active');
                            else d.classList.remove('active');
                        });
                        current = idx;
                    }

                    container._next = function() { showSlide(current + 1); resetTimer(); };
                    container._prev = function() { showSlide(current - 1); resetTimer(); };
                    container._goTo = function(idx) { showSlide(idx); resetTimer(); };

                    function startTimer() {
                        if (autoplay && total > 1) {
                            timer = setInterval(function() {
                                // Pause if any media is being edited (resize overlay or popup is visible)
                                if (document.querySelector('.rte-img-overlay') ||
                                    document.querySelector('.rte-img-toolbar')) {
                                    return;
                                }
                                showSlide(current + 1);
                            }, interval);
                        }
                    }
                    function resetTimer() {
                        if (timer) clearInterval(timer);
                        startTimer();
                    }

                    startTimer();
                    container._carouselInit = true;

                    container.querySelectorAll('video').forEach(function(v) {
                        v.addEventListener('play', function() { if(timer) clearInterval(timer); });
                    });
                });
            }

            initCarousels();
            // In CMS, content can be inserted dynamically, so we poll for new carousels.
            setInterval(function() {
                upgradeCaptionButtons();
                initCarousels();
            }, 1500);
        });
    </script>

    @stack('rte-scripts')

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.tailwindcss.min.js"></script>
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        $.extend(true, $.fn.dataTable.defaults, {
            @if (app()->getLocale() === 'id')
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
                },
            @endif
            pageLength: 10,
            stripeClasses: ['odd', 'even'],
            // dom: top controls row, table, bottom controls row
            dom: '<"dt-top-row"<"dataTables_length"l><"dataTables_filter"f>>t<"dt-bottom-row"<"dataTables_info"i><"dataTables_paginate"p>>',
        });

        // Auto-clone <thead> into <tfoot> (AdminLTE-style column titles at bottom)
        // Runs before DataTables init so the footer is registered by DT.
        $(function () {
            $('table').each(function () {
                var $t = $(this);
                // Only apply to tables that will be DataTables (have id starting with "table"
                // or explicit .dataTable class). Skip if a <tfoot> already exists.
                var willBeDT = $t.is('.dataTable') || /^table/i.test($t.attr('id') || '');
                if (!willBeDT) return;
                if ($t.find('tfoot').length) return;
                var $head = $t.find('thead tr').first();
                if (!$head.length) return;
                var $footRow = $('<tr/>');
                $head.children().each(function () {
                    var $cell = $(this);
                    // Keep alignment class + plain text/HTML content of the header
                    var align = '';
                    if ($cell.hasClass('text-center')) align = ' text-center';
                    else if ($cell.hasClass('text-right')) align = ' text-right';
                    $footRow.append('<th class="' + align.trim() + '">' + $cell.html() + '</th>');
                });
                $t.append($('<tfoot/>').append($footRow));
            });
        });
    </script>

    {{-- Caption Modal — floats at body level, above everything --}}
    <div id="rte-caption-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:999999;background:rgba(0,0,0,0.75);align-items:center;justify-content:center;padding:20px;box-sizing:border-box;">
        <div style="background:#1e293b;border-radius:16px;max-width:540px;width:100%;padding:52px 36px 40px;position:relative;box-shadow:0 24px 64px rgba(0,0,0,0.6);text-align:center;">
            <button id="rte-caption-modal-close" style="position:absolute;top:14px;right:14px;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.12);border:none;color:#e2e8f0;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;transition:background 0.2s;">&times;</button>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#64748b;margin-bottom:18px;">Keterangan Media</div>
            <div id="rte-caption-modal-text" style="font-size:16px;line-height:1.8;color:#e2e8f0;white-space:pre-wrap;word-break:break-word;"></div>
        </div>
    </div>

    <!-- Stack for additional scripts -->
    @stack('scripts')
</body>

</html>
