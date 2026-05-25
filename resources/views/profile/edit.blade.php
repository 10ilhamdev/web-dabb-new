@extends('layouts.app')

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.profile.manage_account') }}</span>
    </div>
@endsection

@php
    function editColLabel(string $col, ?string $dbLabel): string {
        $key = 'dashboard.profile.col_' . $col;
        $trans = __($key);
        if ($trans !== $key) return $trans;
        return $dbLabel ?? \Illuminate\Support\Str::headline($col);
    }

    /**
     * Strip EN→ID translations from a value.
     * Used on old() values so the form never shows a translated EN value.
     */
    function stripEnTranslation(string $val, string $colName = ''): string {
        if (!$val) return $val;

        // Address term replacement: EN → ID
        $isAddress = $colName === 'alamat' || str_contains($colName, 'alamat');
        if ($isAddress) {
            $terms = [
                'Regency of'  => 'Kabupaten',
                'City of'     => 'Kota',
                'Province of' => 'Provinsi',
                'District of' => 'Kecamatan',
                'Village of'  => 'Kelurahan',
                'RT '         => 'RT',
                ' RW '        => ' RW',
                'Jl. '        => 'Jalan ',
                'No. '        => 'No. ',
                'Building '   => 'Gedung ',
                'Floor '      => 'Lantai ',
            ];
            foreach ($terms as $en => $id) {
                $val = str_replace($en, $id, $val);
            }
        }

        // Reverse-lookup: EN value → ID value (always strip EN→ID)
        $authEn = require resource_path('lang/en/auth.php');
        $authId = require resource_path('lang/id/auth.php');
        foreach ($authEn as $key => $enVal) {
            if (!isset($authId[$key])) continue;
            $idVal = $authId[$key];
            if ($idVal === $enVal) continue;
            if ($val === $enVal) return $idVal;
            if (preg_replace('/\s+/', '', $val) === preg_replace('/\s+/', '', $enVal)) return $idVal;
        }

        return $val;
    }

    /**
     * Get the value to display in the edit form.
     * old() values from any previous submit locale are ALWAYS stripped of EN→ID
     * so the form never shows English text in Indonesian locale.
     *
     * Locale ID  → stripped old() → raw DB (both Indonesian)
     * Locale EN  → stripped old() → raw DB (both Indonesian, then displayed as EN)
     */
    function formValue(string $rawDbValue, string $fieldName, string $colName = ''): string {
        $oldVal = old($fieldName);

        if ($oldVal) {
            // Always strip EN→ID from old() — it may come from any previous locale
            return stripEnTranslation($oldVal, $colName);
        }

        // No old() — show raw DB (always Indonesian)
        return $rawDbValue;
    }
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-[22px] font-bold text-[#1E293B]">{{ __('dashboard.profile.user_profile') }}</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full relative">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <!-- Avatar Section -->
                <div class="flex flex-col items-center justify-center pt-10 pb-6 border-b border-gray-100">
                    <div
                        class="relative flex items-center justify-center w-24 h-24 p-[3px] bg-white border @error('photo') border-red-500 @else border-gray-200 @enderror rounded-full shadow-sm shrink-0">
                        <img id="profile-photo-preview" class="w-full h-full rounded-full object-cover block"
                            src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=E5E7EB&color=374151&bold=true&size=128' }}"
                            alt="Avatar">
                    </div>
                    <label for="photo-upload"
                        class="mt-3 text-sm font-medium text-blue-500 hover:text-blue-600 cursor-pointer">
                        {{ __('dashboard.profile.edit_photo') }}
                    </label>
                    <input id="photo-upload" type="file" name="photo" accept="image/*" class="hidden"
                        onchange="previewPhoto(event)">
                    @error('photo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Fields -->
                <div class="p-8 pb-24">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            <div class="font-medium mb-1">{{ __('dashboard.profile.validation_errors_title') }}</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @php
                        // Separate profile columns into groups
                        $textAreaFields = [];
                        $fileFields = [];
                        $selectFields = [];
                        $inputFields = [];

                        $skipFields = ['user_id', 'id', 'created_at', 'updated_at'];

                        foreach ($profileColumns as $col) {
                            if (in_array($col->column_name, $skipFields)) continue;

                            if (in_array($col->column_type, ['text', 'longtext', 'mediumtext'])) {
                                $textAreaFields[] = $col;
                            } elseif ($col->column_type === 'blob') {
                                $fileFields[] = $col;
                            } elseif (in_array($col->column_type, ['enum', 'set'])) {
                                $selectFields[] = $col;
                            } else {
                                $inputFields[] = $col;
                            }
                        }
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                {{ __('dashboard.profile.full_name') }}
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full @error('name') border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                {{ __('dashboard.profile.username') }}
                            </label>
                            <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}"
                                class="w-full @error('username') border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors">
                            @error('username')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                {{ __('dashboard.profile.email') }}
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full @error('email') border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dynamic input fields --}}
                        @foreach($inputFields as $col)
                            @if(!in_array($col->column_name, ['name', 'email', 'photo']))
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                        {{ editColLabel($col->column_name, $col->column_label) }}
                                    </label>

                                    @if(in_array($col->column_type, ['date', 'datetime', 'timestamp']))
                                        <input type="{{ $col->column_type === 'date' ? 'date' : 'datetime-local' }}"
                                            name="{{ $col->column_name }}"
                                            value="{{ old($col->column_name, $user->profile?->{$col->column_name} ?? '') }}"
                                            maxlength="{{ $col->column_length }}"
                                            class="w-full @error($col->column_name) border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors cursor-pointer">
                                    @elseif(in_array($col->column_type, ['int', 'bigint', 'smallint', 'tinyint']))
                                        <input type="number"
                                            name="{{ $col->column_name }}"
                                            value="{{ old($col->column_name, $user->profile?->{$col->column_name} ?? '') }}"
                                            maxlength="{{ $col->column_length }}"
                                            class="w-full @error($col->column_name) border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors">
                                    @else
                                        <input type="text"
                                            name="{{ $col->column_name }}"
                                            value="{{ formValue($user->profile?->{$col->column_name} ?? '', $col->column_name, $col->column_name) }}"
                                            maxlength="{{ $col->column_length }}"
                                            class="w-full @error($col->column_name) border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors">
                                    @endif

                                    @error($col->column_name)
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        @endforeach

                        {{-- Dynamic select fields (enum/set) --}}
                        @foreach($selectFields as $col)
                            <div>
                                <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                    {{ editColLabel($col->column_name, $col->column_label) }}
                                </label>
                                <select name="{{ $col->column_name }}"
                                    class="tom-select-class w-full @error($col->column_name) ts-has-error @enderror bg-gray-50 border border-gray-200 text-gray-800 text-[13px] rounded-lg p-2.5 outline-none focus:border-blue-500 focus:bg-white transition-colors cursor-pointer">
                                    <option value="">{{ __('dashboard.profile.select_placeholder', ['label' => editColLabel($col->column_name, $col->column_label)]) }}</option>
                                    @foreach(($enumOptions[$col->column_name] ?? []) as $option)
                                        <option value="{{ $option }}"
                                            {{ old($col->column_name, $user->profile?->{$col->column_name} ?? '') == $option ? 'selected' : '' }}>
                                            {{ $translatedEnumOptions[$col->column_name][$option] ?? $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error($col->column_name)
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        {{-- Dynamic textarea fields (excluding alamat — rendered separately below) --}}
                        @foreach($textAreaFields as $col)
                            @if($col->column_name !== 'alamat')
                                <div class="md:col-span-2">
                                    <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                        {{ editColLabel($col->column_name, $col->column_label) }}
                                    </label>
                                    <textarea name="{{ $col->column_name }}" rows="3"
                                        class="w-full @error($col->column_name) border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors resize-none">{{ formValue($user->profile?->{$col->column_name} ?? '', $col->column_name, $col->column_name) }}</textarea>
                                    @error($col->column_name)
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        @endforeach

                        {{-- Dynamic file fields --}}
                        @foreach($fileFields as $col)
                            <div>
                                <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                    {{ editColLabel($col->column_name, $col->column_label) }}
                                    <span class="text-gray-300">({{ __('dashboard.profile.optional') }})</span>
                                </label>
                                <input type="file" name="{{ $col->column_name }}"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full @error($col->column_name) border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors cursor-pointer">
                                @if($user->profile?->{$col->column_name})
                                    <a href="{{ Storage::url($user->profile->{$col->column_name}) }}" target="_blank"
                                        class="text-xs text-blue-500 mt-1 block">{{ __('dashboard.profile.view_current_document') }}</a>
                                @endif
                                @error($col->column_name)
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <!-- Alamat (always at end, full width) -->
                        @if($profileColumns->contains('column_name', 'alamat'))
                            <div class="md:col-span-2">
                                <label class="block text-[12px] font-medium text-gray-400 mb-1.5">
                                    {{ editColLabel('alamat', $profileColumns->firstWhere('column_name', 'alamat')->column_label ?? null) }}
                                </label>
                                <textarea name="alamat" rows="3"
                                    class="w-full @error('alamat') border-red-500 focus:border-red-500 @else bg-gray-50 border-gray-200 focus:border-blue-500 focus:bg-white @enderror text-gray-800 text-[13px] rounded-lg p-2.5 outline-none transition-colors resize-none">{{ formValue($user->profile?->alamat ?? '', 'alamat', 'alamat') }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="absolute bottom-6 right-6 flex items-center space-x-3">
                    <a href="{{ route('profile.show') }}"
                        class="inline-flex items-center text-[13px] font-medium text-white bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('dashboard.profile.back') }}
                    </a>
                    <button type="submit"
                        class="bg-[#3B82F6] hover:bg-blue-600 text-white text-[13px] font-medium py-2 px-5 rounded-lg border border-blue-500 shadow-sm flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        {{ __('dashboard.profile.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tom-select-class').forEach((el) => {
                new TomSelect(el, {
                    create: false,
                    maxOptions: null,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    controlInput: '<input>',
                    render: {
                        no_results: function(data, escape) {
                            return '<div class="no-results" style="padding: 8px 12px;">{{ __("dashboard.no_results") }}</div>';
                        }
                    }
                });
            });
        });

        function previewPhoto(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
<style>
    .ts-wrapper.ts-has-error .ts-control {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    .ts-wrapper .ts-control {
        border-radius: 0.5rem !important;
        background-color: #f9fafb !important;
        border-color: #e5e7eb !important;
        padding: 0.625rem 0.75rem !important;
        min-height: unset !important;
        font-size: 13px !important;
        font-family: inherit;
        color: #1f2937 !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
    }
    .ts-wrapper.focus .ts-control, .ts-wrapper.input-active .ts-control {
        background-color: #ffffff !important;
        border-color: #3b82f6 !important;
        box-shadow: none !important;
    }
    .ts-wrapper .ts-dropdown {
        border-radius: 0.5rem !important;
        font-size: 13px !important;
        margin-top: 4px !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
    }
    .ts-wrapper .ts-dropdown .ts-dropdown-content {
        max-height: 200px !important;
        overflow-y: auto !important;
    }
    .ts-wrapper .option {
        padding: 8px 12px !important;
    }
    .ts-wrapper .option:hover, .ts-wrapper .option.active {
        background-color: #f3f4f6 !important;
        color: #1f2937 !important;
    }
    .ts-control.single .ts-input {
        padding-right: 30px;
    }
    .ts-wrapper.single .ts-control:after {
        content: '';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-top-color: #9ca3af;
    }
    .ts-wrapper.single.dropdown-active .ts-control:after {
        border-top-color: transparent;
        border-bottom-color: #9ca3af;
        margin-top: -5px;
    }
</style>
@endpush
