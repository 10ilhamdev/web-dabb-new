@extends('layouts.guest')

@section('title', (app()->getLocale() === 'en' ? 'Verify Email' : 'Verifikasi Email') . ' - ' . config('app.name', 'Laravel'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="login-breadcrumb">
        <div class="container">
            <span class="text-cyan">{{ app()->getLocale() === 'en' ? 'Verify Email' : 'Verifikasi Email' }}</span>
        </div>
    </div>

    <!-- Hero Header -->
    <div class="login-hero">
        <div class="container">
            <h1>{{ strtoupper(app()->getLocale() === 'en' ? 'Verify Email' : 'Verifikasi Email') }}</h1>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow login-main-wrapper">
        <div class="login-card">

            <!-- Left Side: Form -->
            <div class="login-form-side">
                <h2>{{ __('auth.welcome') }}</h2>

                <h3>{{ app()->getLocale() === 'en' ? 'Email Verification' : 'Verifikasi Email' }}</h3>
                <p class="subtitle">{{ app()->getLocale() === 'en' ? 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.' : 'Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerimanya, kami akan dengan senang hati mengirimkan tautan yang baru.' }}</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 border-l-4 border-green-500 rounded-md">
                        {{ app()->getLocale() === 'en' ? 'A new verification link has been sent to the email address you provided during registration.' : 'Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat registrasi.' }}
                    </div>
                @endif

                <div class="mt-8 flex flex-col gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn-submit w-full" style="width: 100%;">
                            {{ app()->getLocale() === 'en' ? 'Resend Verification Email' : 'Kirim Ulang Email Verifikasi' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-900 transition-colors border-none bg-transparent cursor-pointer underline py-2">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Image Banner -->
            <div class="login-banner-side">
                <div class="banner-overlay-logo">
                    <img src="{{ asset('image/logo_anri.png') }}" alt="ANRI Logo">
                    <div class="banner-overlay-text">
                        <div class="title">{!! __('auth.banner_title') !!}</div>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
