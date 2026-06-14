@extends('layouts.app')

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.profile.manage_account') }}</span>
    </div>
@endsection

@section('content')
    @php
        use App\Http\Controllers\ProfileController;

        // Separate columns: full-width (textarea) vs two-column (other types)
        $fullWidthCols = $profileColumns->filter(fn($c) => in_array($c->column_type, ['text', 'longtext']) && !in_array($c->column_name, ['user_id','id','created_at','updated_at']));
        $twoColCols    = $profileColumns->filter(fn($c) => !in_array($c->column_type, ['text','longtext','blob']) && !in_array($c->column_name, ['user_id','id','created_at','updated_at']));
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-bold text-[#1E293B] mb-6">{{ __('dashboard.profile.user_profile') }}</h1>

        <!-- Top Card: Basic Info -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6 flex items-center">
            <div class="flex items-center justify-center w-16 h-16 p-[3px] bg-white border border-gray-200 rounded-full shadow-sm shrink-0">
                <img class="w-full h-full rounded-full object-cover block"
                    src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E5E7EB&color=374151&bold=true&size=128' }}"
                    alt="Avatar">
            </div>
            <div class="ml-5 flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                        <div class="text-sm font-medium border-l-[3px] border-blue-200 pl-3 mt-1.5 flex items-center space-x-3">
                            @php
                                $roleKey = "dashboard.roles.{$user->role}";
                                if (\Illuminate\Support\Facades\Lang::has($roleKey)) {
                                    $roleNameTranslated = __($roleKey);
                                } else {
                                    $roleModel = \App\Models\Role::where('name', $user->role)->first();
                                    $roleNameTranslated = $roleModel ? $roleModel->label : ucwords(str_replace('_', ' ', $user->role));
                                }
                            @endphp
                            <span class="text-gray-500">{{ $roleNameTranslated }}</span>
                        </div>
                    </div>
                    {{-- Email Verification Badge / Resend Button --}}
                    @if(is_null($user->email_verified_at))
                        <div class="flex flex-col items-end gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-100">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                {{ __('dashboard.profile.status_unverified') }}
                            </span>
                            <form action="{{ route('profile.send-verification') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition-colors shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ __('dashboard.profile.send_verification') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 border border-green-100">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('dashboard.profile.status_verified') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Card: Details Form - Fully Dynamic from role_columns -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 relative">
            <div class="p-6 pb-20">
                <h3 class="text-base font-bold text-gray-800 mb-6">{{ __('dashboard.profile.personal_data') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-y-6 gap-x-8">

                    {{-- Core user fields (always shown first) --}}
                    <div>
                        <div class="text-[11px] font-medium text-gray-400 mb-1">{{ __('dashboard.profile.full_name') }}</div>
                        <div class="text-[13px] font-medium text-gray-800">{{ $user->name }}</div>
                    </div>

                    <div>
                        <div class="text-[11px] font-medium text-gray-400 mb-1">{{ __('dashboard.profile.username') }}</div>
                        <div class="text-[13px] font-medium text-gray-800">{{ $user->username ?? __('dashboard.profile.empty_value') }}</div>
                    </div>

                    <div>
                        <div class="text-[11px] font-medium text-gray-400 mb-1">{{ __('dashboard.profile.email') }}</div>
                        <div class="text-[13px] font-medium text-gray-800">{{ $user->email }}</div>
                    </div>

                    {{-- ALL dynamic profile columns from role_columns --}}
                    @foreach($twoColCols as $col)
                        @php $pdata = $profileData[$col->column_name] ?? ['value' => null, 'type' => $col->column_type]; @endphp
                        <div>
                            <div class="text-[11px] font-medium text-gray-400 mb-1">
                                {{ ProfileController::colLabel($col->column_name, $col->column_label) }}
                            </div>
                            <div class="text-[13px] font-medium text-gray-800">
                                @if($pdata['type'] === 'blob')
                                    <a href="{{ Storage::url($pdata['value']) }}" target="_blank" class="text-blue-500 hover:underline inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        {{ __('dashboard.profile.view_document') }}
                                    </a>
                                @else
                                    {{ $pdata['value'] ?? __('dashboard.profile.empty_value') }}
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Full-width fields (textarea types like alamat) --}}
                @foreach($fullWidthCols as $col)
                    @php $pdata = $profileData[$col->column_name] ?? ['value' => null, 'type' => $col->column_type]; @endphp
                    <div class="mt-6 md:col-span-3">
                        <div class="text-[11px] font-medium text-gray-400 mb-1">
                            {{ ProfileController::colLabel($col->column_name, $col->column_label) }}
                        </div>
                        <div class="text-[13px] font-medium text-gray-800 leading-relaxed">
                            {!! nl2br(e($pdata['value'] ?? __('dashboard.profile.empty_value'))) !!}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Edit Button -->
            <div class="absolute bottom-6 right-6">
                <a href="{{ route('profile.edit') }}"
                    class="bg-[#3B82F6] hover:bg-blue-600 text-white text-[13px] font-medium py-2 px-5 rounded-lg border border-blue-500 shadow-sm flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </svg>
                    {{ __('dashboard.profile.edit') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Status messages --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-4 right-4 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">
                @php
                    $statusMsg = session('status');
                    $authKey = 'auth.' . $statusMsg;
                    $dashKey = 'dashboard.profile.' . str_replace('-', '_', $statusMsg);
                @endphp
                @if (__($authKey) !== $authKey)
                    {{ __($authKey) }}
                @elseif (__($dashKey) !== $dashKey)
                    {{ __($dashKey) }}
                @else
                    {{ __($statusMsg) }}
                @endif
            </span>
            <button @click="show = false" class="ml-2 hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></svg>
                </button>
            </div>
    @endif
@endsection