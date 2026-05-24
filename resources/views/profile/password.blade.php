@extends('layouts.app')

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.profile.change_password') }}</span>
    </div>
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-bold text-[#1E293B] mb-6">{{ __('dashboard.password.title') }}</h1>

        <!-- Requirements Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
            <h3 class="text-[13px] font-bold text-gray-800 mb-2">{{ __('dashboard.password.requirements_title') }}</h3>
            <p class="text-[13px] text-gray-500 mb-4">{{ __('dashboard.password.description') }}</p>

            <ul class="space-y-2">
                <li class="flex items-center text-[13px] text-gray-600">
                    <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('dashboard.password.requirements_1') }}
                </li>
                <li class="flex items-center text-[13px] text-gray-600">
                    <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('dashboard.password.requirements_2') }}
                </li>
                <li class="flex items-center text-[13px] text-gray-400">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('dashboard.password.requirements_3') }}
                </li>
                <li class="flex items-center text-[13px] text-gray-400">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ __('dashboard.password.requirements_4') }}
                </li>
            </ul>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-[#007BFF] px-6 py-4">
                <h3 class="text-[14px] font-semibold text-white">{{ __('dashboard.password.title') }}</h3>
            </div>

            <!-- Form Body -->
            <div class="p-6">
                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <!-- Old Password -->
                    <div>
                        <label for="update_password_current_password"
                            class="block text-[13px] font-bold text-gray-800 mb-2">{{ __('dashboard.password.current_password') }}</label>
                        <input id="update_password_current_password" name="current_password" type="password"
                            class="mt-1 block w-full border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 text-[13px] py-2.5 px-4 placeholder-gray-300"
                            placeholder="{{ __('dashboard.password.current_password') }}"
                            autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-[12px] text-red-500" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="update_password_password"
                            class="block text-[13px] font-bold text-gray-800 mb-2">{{ __('dashboard.password.new_password') }}</label>
                        <input id="update_password_password" name="password" type="password"
                            class="mt-1 block w-full border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 text-[13px] py-2.5 px-4 placeholder-gray-300"
                            placeholder="{{ __('dashboard.password.new_password') }}" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-[12px] text-red-500" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="update_password_password_confirmation"
                            class="block text-[13px] font-bold text-gray-800 mb-2">{{ __('dashboard.password.confirm_password') }}</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 text-[13px] py-2.5 px-4 placeholder-gray-300"
                            placeholder="{{ __('dashboard.password.confirm_password') }}" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-[12px] text-red-500" />
                    </div>

            </div>

            <!-- Form Footer -->
            <div class="bg-[#F2F4F8] px-6 py-4 border-t border-gray-100 flex items-center gap-4">
                <button type="submit"
                    class="bg-[#007BFF] hover:bg-blue-600 text-white text-[13px] font-medium py-2 px-6 rounded-md shadow-sm transition-colors">
                    {{ __('dashboard.profile.save') }}
                </button>

                @if (session('status') === 'password-updated' || session('success') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-green-600 font-medium">{{ __('dashboard.password.saved') }}</p>
                @endif
            </div>
            </form>
        </div>
    </div>
@endsection
