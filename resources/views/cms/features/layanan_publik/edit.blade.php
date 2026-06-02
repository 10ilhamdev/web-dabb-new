@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @if ($feature->parent)
        <span class="text-gray-300">/</span>
        <a href="{{ route('cms.features.show', $feature->parent) }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->parent->name }}</a>
    @endif
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.layanan_publik.index', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.layanan_publik.edit_title'))

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="layananPublikForm('{{ $layananPublik->type }}', {{ json_encode($layananPublik->extra_data ?? null) }})">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cms.features.layanan_publik.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.layanan_publik.edit_title') }}</h1>
        </div>
    </div>

    <form action="{{ route('cms.features.layanan_publik.pages.update', [$feature, $layananPublik]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Type & Title --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.layanan_publik.label_type') }} <span class="text-red-500">*</span></label>
                            <select name="type" x-model="type" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="kunjungan" {{ $layananPublik->type === 'kunjungan' ? 'selected' : '' }}>{{ __('cms.layanan_publik.type_kunjungan') }}</option>
                                <option value="laraska" {{ $layananPublik->type === 'laraska' ? 'selected' : '' }}>{{ __('cms.layanan_publik.type_laraska') }}</option>
                                <option value="statis" {{ $layananPublik->type === 'statis' ? 'selected' : '' }}>{{ __('cms.layanan_publik.type_statis') }}</option>
                                <option value="konsultasi" {{ $layananPublik->type === 'konsultasi' ? 'selected' : '' }}>{{ __('cms.layanan_publik.type_konsultasi') }}</option>
                                <option value="perpustakaan" {{ $layananPublik->type === 'perpustakaan' ? 'selected' : '' }}>{{ __('cms.layanan_publik.type_perpustakaan') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.layanan_publik.label_title') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ $layananPublik->title }}" required placeholder="{{ __('cms.layanan_publik.placeholder_title') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Description / Content --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.layanan_publik.label_description') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1">{!! $layananPublik->description !!}</div>
                    </div>
                    <input type="hidden" name="description" id="description_input">
                </div>

                {{-- Kunjungan Settings (Only visible when type === 'kunjungan') --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'kunjungan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.layanan_publik.kunjungan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.kunjungan_settings_desc') }}</p>
                    </div>

                    {{-- 1. Jadwal Kunjungan & 2. Pengajuan Kunjungan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section1_title') }}</label>
                                <input type="hidden" name="extra_data[show_jadwal]" :value="show_jadwal">
                                <button type="button" @click="toggleShow('show_jadwal')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors" :class="show_jadwal == 1 ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" :title="show_jadwal == 1 ? '{{ __('cms.layanan_publik.btn_hide_guest') }}' : '{{ __('cms.layanan_publik.btn_show_guest') }}'">
                                    <template x-if="show_jadwal == 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="show_jadwal != 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </template>
                                    <span x-text="show_jadwal == 1 ? '{{ __('cms.layanan_publik.status_show') }}' : '{{ __('cms.layanan_publik.status_hide') }}'"></span>
                                </button>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.section1_label_title') }}</label>
                                <input type="text" name="extra_data[title_jadwal]" x-model="title_jadwal" placeholder="{{ __('cms.layanan_publik.section1_placeholder_title') }}" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.section1_label_desc') }}</label>
                                <textarea name="extra_data[jadwal_kunjungan]" x-model="jadwal_kunjungan" rows="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section2_title') }}</label>
                                <input type="hidden" name="extra_data[show_pengajuan]" :value="show_pengajuan">
                                <button type="button" @click="toggleShow('show_pengajuan')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors" :class="show_pengajuan == 1 ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" :title="show_pengajuan == 1 ? '{{ __('cms.layanan_publik.btn_hide_guest') }}' : '{{ __('cms.layanan_publik.btn_show_guest') }}'">
                                    <template x-if="show_pengajuan == 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="show_pengajuan != 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </template>
                                    <span x-text="show_pengajuan == 1 ? '{{ __('cms.layanan_publik.status_show') }}' : '{{ __('cms.layanan_publik.status_hide') }}'"></span>
                                </button>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.section2_label_title') }}</label>
                                <input type="text" name="extra_data[title_pengajuan]" x-model="title_pengajuan" placeholder="{{ __('cms.layanan_publik.section2_placeholder_title') }}" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.section2_label_desc') }}</label>
                                <textarea name="extra_data[pengajuan_kunjungan]" x-model="pengajuan_kunjungan" rows="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Kalender: Pengaturan Hari Libur & Slot Tutup --}}
                    <div class="pt-6 border-t border-gray-100 space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-4">
                            <div class="flex items-center gap-3">
                                <label class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section3_title') }}</label>
                                <input type="hidden" name="extra_data[show_kalender]" :value="show_kalender">
                                <button type="button" @click="toggleShow('show_kalender')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors" :class="show_kalender == 1 ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" :title="show_kalender == 1 ? '{{ __('cms.layanan_publik.btn_hide_guest') }}' : '{{ __('cms.layanan_publik.btn_show_guest') }}'">
                                    <template x-if="show_kalender == 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="show_kalender != 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </template>
                                    <span x-text="show_kalender == 1 ? '{{ __('cms.layanan_publik.status_show') }}' : '{{ __('cms.layanan_publik.status_hide') }}'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Hari Libur --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section3a_title') }}</h3>
                                    <p class="text-xs text-gray-500 mb-2.5">{{ __('cms.layanan_publik.section3a_desc') }}</p>
                                    {{-- Info Box Hari Libur Nasional Otomatis --}}
                                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-1 flex items-start gap-2.5 text-xs text-blue-700">
                                        <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div>
                                            <span class="font-semibold">{{ __('cms.layanan_publik.info_auto_title') }}</span> {{ __('cms.layanan_publik.info_auto_desc') }}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="addLibur()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 self-start mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    {{ __('cms.layanan_publik.btn_add_holiday') }}
                                </button>
                            </div>
                            <template x-for="(item, index) in libur_dates" :key="index">
                                <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="w-1/3">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_holiday_date') }}</label>
                                        <input type="date" :name="`extra_data[libur_dates][${index}][date]`" x-model="item.date" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_holiday_reason') }}</label>
                                        <input type="text" :name="`extra_data[libur_dates][${index}][reason]`" x-model="item.reason" placeholder="{{ __('cms.layanan_publik.placeholder_holiday_reason') }}" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div class="w-1/6 flex items-end justify-end">
                                        <button type="button" @click="removeLibur(index)" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-sm font-medium transition-colors">{{ __('cms.layanan_publik.btn_delete') }}</button>
                                    </div>
                                </div>
                            </template>
                            <p x-show="libur_dates.length === 0" class="text-xs text-gray-400 italic py-2">{{ __('cms.layanan_publik.empty_holidays') }}</p>
                        </div>

                        {{-- 3b. Kuota Harian Maksimal --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="mb-3">
                                <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section3b_title') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.section3b_desc') }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg md:w-1/2">
                                <label class="block text-xs text-gray-700 font-medium mb-1">{{ __('cms.layanan_publik.label_daily_quota') }}</label>
                                <input type="number" name="extra_data[kuota_harian]" x-model="kuota_harian" min="1" @input="tutup_slots.forEach((_, i) => validateMaxQuota(i))" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>

                        {{-- 3c. Slot Tutup Khusus / Kuota Khusus --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section3c_title') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.section3c_desc') }}</p>
                                </div>
                                <button type="button" @click="addTutupSlot()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    {{ __('cms.layanan_publik.btn_add_close_slot') }}
                                </button>
                            </div>
                            <template x-for="(item, index) in tutup_slots" :key="index">
                                <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="w-1/4">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_date') }}</label>
                                        <input type="date" :name="`extra_data[tutup_slots][${index}][date]`" x-model="item.date" @change="tutup_slots.forEach((_, i) => validateMaxQuota(i))" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div class="w-1/4">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_slot_time') }}</label>
                                        <select :name="`extra_data[tutup_slots][${index}][slot]`" x-model="item.slot" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                            <option value="pagi">{{ __('cms.layanan_publik.slot_pagi') }}</option>
                                            <option value="siang">{{ __('cms.layanan_publik.slot_siang') }}</option>
                                        </select>
                                    </div>
                                    <div class="w-1/6">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_max_slot') }}</label>
                                        <input type="number" :name="`extra_data[tutup_slots][${index}][max_quota]`" x-model="item.max_quota" min="0" :max="getMaxQuota(index)" @input="validateMaxQuota(index)" placeholder="{{ __('cms.layanan_publik.placeholder_close_slot') }}" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                        <span class="text-[10px] text-gray-500 block mt-0.5" x-text="`{{ __('cms.layanan_publik.label_max_hint', ['max' => '']) }}${getMaxQuota(index)}`"></span>
                                    </div>
                                    <div class="w-1/4">
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_close_reason') }}</label>
                                        <input type="text" :name="`extra_data[tutup_slots][${index}][reason]`" x-model="item.reason" placeholder="{{ __('cms.layanan_publik.placeholder_close_reason') }}" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div class="w-1/12 flex items-end justify-end">
                                        <button type="button" @click="removeTutupSlot(index)" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-sm font-medium transition-colors">{{ __('cms.layanan_publik.btn_delete') }}</button>
                                    </div>
                                </div>
                            </template>
                            <p x-show="tutup_slots.length === 0" class="text-xs text-gray-400 italic py-2">{{ __('cms.layanan_publik.empty_close_slots') }}</p>
                        </div>
                    </div>

                    {{-- 4. Daftar Form Kunjungan --}}
                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.section4_title') }}</h3>
                                <input type="hidden" name="extra_data[show_form]" :value="show_form">
                                <button type="button" @click="toggleShow('show_form')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors" :class="show_form == 1 ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" :title="show_form == 1 ? '{{ __('cms.layanan_publik.btn_hide_guest') }}' : '{{ __('cms.layanan_publik.btn_show_guest') }}'">
                                    <template x-if="show_form == 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="show_form != 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </template>
                                    <span x-text="show_form == 1 ? '{{ __('cms.layanan_publik.status_show') }}' : '{{ __('cms.layanan_publik.status_hide') }}'"></span>
                                </button>
                            </div>
                            <button type="button" @click="addFormField()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                {{ __('cms.layanan_publik.btn_add_form_field') }}
                            </button>
                        </div>

                        <template x-for="(item, index) in form_fields" :key="index">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl mb-4 relative group">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_id') }}</label>
                                        <input type="text" :name="`extra_data[form_fields][${index}][id]`" x-model="item.id" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_label') }}</label>
                                        <input type="text" :name="`extra_data[form_fields][${index}][label]`" x-model="item.label" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_type') }}</label>
                                        <select :name="`extra_data[form_fields][${index}][type]`" x-model="item.type" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                            <option value="text">{{ __('cms.layanan_publik.type_text') }}</option>
                                            <option value="email">{{ __('cms.layanan_publik.type_email') }}</option>
                                            <option value="number">{{ __('cms.layanan_publik.type_number') }}</option>
                                            <option value="date">{{ __('cms.layanan_publik.type_date') }}</option>
                                            <option value="select">{{ __('cms.layanan_publik.type_select') }}</option>
                                            <option value="file">{{ __('cms.layanan_publik.type_file') }}</option>
                                            <option value="textarea">{{ __('cms.layanan_publik.type_textarea') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-between pt-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :name="`extra_data[form_fields][${index}][required]`" value="1" x-model="item.required" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                            <span class="ml-2 text-sm text-gray-700 font-medium">{{ __('cms.layanan_publik.label_required') }}</span>
                                        </label>
                                        <button type="button" @click="removeFormField(index)" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-sm font-medium transition-colors">{{ __('cms.layanan_publik.btn_delete') }}</button>
                                    </div>
                                </div>
                                <div class="mt-3" x-show="item.type === 'select' || item.type === 'file'">
                                    <label class="block text-xs text-gray-500 mb-1" x-text="item.type === 'select' ? '{{ __('cms.layanan_publik.label_options_select') }}' : '{{ __('cms.layanan_publik.label_options_file') }}'"></label>
                                    <input type="text" :name="`extra_data[form_fields][${index}][options]`" x-model="item.options" :placeholder="item.type === 'select' ? '{{ __('cms.layanan_publik.placeholder_options_select') }}' : '{{ __('cms.layanan_publik.placeholder_options_file') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- LARASKA Settings (Only visible when type === 'laraska') --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'laraska'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.layanan_publik.laraska_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.laraska_settings_desc') }}</p>
                    </div>

                    {{-- 1. Waktu Pelayanan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.layanan_publik.label_laraska_hours') }}</label>
                        <textarea name="extra_data[laraska_hours]" x-model="laraska_hours" rows="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                    </div>

                    {{-- 2. Maklumat Pelayanan --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.maklumat_box_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_maklumat_title') }}</label>
                                <input type="text" name="extra_data[maklumat_title]" x-model="maklumat_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_maklumat_date') }}</label>
                                <input type="text" name="extra_data[maklumat_date]" x-model="maklumat_date" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_maklumat_director') }}</label>
                                <input type="text" name="extra_data[maklumat_director]" x-model="maklumat_director" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_maklumat_content') }}</label>
                                <textarea name="extra_data[maklumat_content]" x-model="maklumat_content" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Mekanisme Layanan --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.label_laraska_mech_title') }}</h3>

                        @if(isset($layananPublik->extra_data['file']))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-800">{{ __('cms.layanan_publik.current_doc') }}</p>
                                    <a href="{{ asset('storage/' . $layananPublik->extra_data['file']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('cms.layanan_publik.view_pdf') }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="extra_data[file]" value="{{ $layananPublik->extra_data['file'] }}">
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1" :title="'{{ __('cms.layanan_publik.btn_delete_doc') }}'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('cms.layanan_publik.btn_delete') }}
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_laraska_mech_title') }}</label>
                                <input type="text" name="extra_data[laraska_mech_title]" x-model="laraska_mech_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_pdf') }}</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative overflow-hidden shrink-0">
                                        <input type="file" name="extra_data_file_upload" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="file_name = $event.target.files[0] ? $event.target.files[0].name : file_name">
                                        <button type="button" class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                            {{ __('cms.layanan_publik.btn_choose_file') }}
                                        </button>
                                    </div>
                                    <input type="text" name="extra_data[file_name]" x-model="file_name" :placeholder="'{{ __('cms.layanan_publik.placeholder_auto_file') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(step, index) in laraska_steps" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl relative space-y-2 group">
                                        <button type="button" @click="removeLaraskaStep(index)" class="absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-semibold" :title="'{{ __('cms.layanan_publik.btn_delete_step') }}'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            {{ __('cms.layanan_publik.btn_delete') }}
                                        </button>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_title_direct') }}' + ' ' + (index + 1)"></label>
                                            <input type="text" :name="'extra_data[laraska_steps]['+index+'][title]'" x-model="step.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_desc') }}' + ' ' + (index + 1)"></label>
                                            <textarea :name="'extra_data[laraska_steps]['+index+'][desc]'" x-model="step.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addLaraskaStep()" class="w-full py-2.5 border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl text-xs font-semibold text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('cms.layanan_publik.btn_add_laraska_step') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STATIS Settings (Only visible when type === 'statis') --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'statis'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.layanan_publik.statis_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.statis_settings_desc') }}</p>
                    </div>

                    {{-- 1. Waktu Pelayanan & Pemesanan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.layanan_publik.label_statis_hours') }}</label>
                            <textarea name="extra_data[statis_hours]" x-model="statis_hours" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.layanan_publik.label_statis_order_hours') }}</label>
                            <textarea name="extra_data[statis_order_hours]" x-model="statis_order_hours" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                        </div>
                    </div>

                    {{-- 2. Tahapan Penelusuran Arsip (Lingkaran Alur) --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.statis_stages_title') }}</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <template x-for="(stage, index) in statis_stages" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl relative space-y-2 group">
                                        <button type="button" @click="removeStatisStage(index)" class="absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-semibold" :title="'{{ __('cms.layanan_publik.btn_delete_stage') }}'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            {{ __('cms.layanan_publik.btn_delete') }}
                                        </button>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1" x-text="'{{ __('cms.layanan_publik.label_stage_name') }}' + ' ' + (index + 1)"></label>
                                            <input type="text" :name="'extra_data[statis_stages]['+index+'][title]'" x-model="stage.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addStatisStage()" class="w-full py-2.5 border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl text-xs font-semibold text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('cms.layanan_publik.btn_add_statis_stage') }}
                            </button>
                        </div>
                    </div>

                    {{-- 3. Mekanisme Layanan Langsung --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        @if(isset($layananPublik->extra_data['statis_direct_pdf']))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-800">{{ __('cms.layanan_publik.current_doc') }} (Langsung)</p>
                                    <a href="{{ asset('storage/' . $layananPublik->extra_data['statis_direct_pdf']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('cms.layanan_publik.view_pdf') }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="extra_data[statis_direct_pdf]" value="{{ $layananPublik->extra_data['statis_direct_pdf'] }}">
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1" :title="'{{ __('cms.layanan_publik.btn_delete_doc') }}'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('cms.layanan_publik.btn_delete') }}
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.layanan_publik.label_statis_mech1_title') }}</label>
                                <input type="text" name="extra_data[statis_mech1_title]" x-model="statis_mech1_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_statis_direct_pdf') }}</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative overflow-hidden shrink-0">
                                        <input type="file" name="extra_data_statis_direct_pdf" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="statis_direct_pdf_name = $event.target.files[0] ? $event.target.files[0].name : statis_direct_pdf_name">
                                        <button type="button" class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                            {{ __('cms.layanan_publik.btn_choose_file') }}
                                        </button>
                                    </div>
                                    <input type="text" name="extra_data[statis_direct_pdf_name]" x-model="statis_direct_pdf_name" :placeholder="'{{ __('cms.layanan_publik.placeholder_auto_file') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 pt-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(step, index) in statis_mech1_steps" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl relative space-y-2 group">
                                        <button type="button" @click="removeStatisMech1Step(index)" class="absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-semibold" :title="'{{ __('cms.layanan_publik.btn_delete_step') }}'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            {{ __('cms.layanan_publik.btn_delete') }}
                                        </button>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_title_direct') }}' + ' ' + (index + 1) + ' (Langsung)'"></label>
                                            <input type="text" :name="'extra_data[statis_mech1_steps]['+index+'][title]'" x-model="step.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_desc') }}' + ' ' + (index + 1)"></label>
                                            <textarea :name="'extra_data[statis_mech1_steps]['+index+'][desc]'" x-model="step.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addStatisMech1Step()" class="w-full py-2.5 border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl text-xs font-semibold text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('cms.layanan_publik.btn_add_statis_mech1_step') }}
                            </button>
                        </div>
                    </div>

                    {{-- 4. Mekanisme Layanan Tidak Langsung --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        @if(isset($layananPublik->extra_data['statis_indirect_pdf']))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-800">{{ __('cms.layanan_publik.current_doc') }} (Tidak Langsung)</p>
                                    <a href="{{ asset('storage/' . $layananPublik->extra_data['statis_indirect_pdf']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('cms.layanan_publik.view_pdf') }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="extra_data[statis_indirect_pdf]" value="{{ $layananPublik->extra_data['statis_indirect_pdf'] }}">
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1" :title="'{{ __('cms.layanan_publik.btn_delete_doc') }}'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('cms.layanan_publik.btn_delete') }}
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.layanan_publik.label_statis_mech2_title') }}</label>
                                <input type="text" name="extra_data[statis_mech2_title]" x-model="statis_mech2_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_statis_indirect_pdf') }}</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative overflow-hidden shrink-0">
                                        <input type="file" name="extra_data_statis_indirect_pdf" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="statis_indirect_pdf_name = $event.target.files[0] ? $event.target.files[0].name : statis_indirect_pdf_name">
                                        <button type="button" class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                            {{ __('cms.layanan_publik.btn_choose_file') }}
                                        </button>
                                    </div>
                                    <input type="text" name="extra_data[statis_indirect_pdf_name]" x-model="statis_indirect_pdf_name" :placeholder="'{{ __('cms.layanan_publik.placeholder_auto_file') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 pt-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(step, index) in statis_mech2_steps" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl relative space-y-2 group">
                                        <button type="button" @click="removeStatisMech2Step(index)" class="absolute top-3 right-3 bg-red-100 hover:bg-red-200 text-red-600 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-semibold" :title="'{{ __('cms.layanan_publik.btn_delete_step') }}'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            {{ __('cms.layanan_publik.btn_delete') }}
                                        </button>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_title_indirect') }}' + ' ' + (index + 1) + ' (Tidak Langsung)'"></label>
                                            <input type="text" :name="'extra_data[statis_mech2_steps]['+index+'][title]'" x-model="step.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1" x-text="'{{ __('cms.layanan_publik.label_statis_step_desc') }}' + ' ' + (index + 1)"></label>
                                            <textarea :name="'extra_data[statis_mech2_steps]['+index+'][desc]'" x-model="step.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addStatisMech2Step()" class="w-full py-2.5 border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-xl text-xs font-semibold text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                {{ __('cms.layanan_publik.btn_add_statis_mech2_step') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- KONSULTASI Settings (Only visible when type === 'konsultasi') --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'konsultasi'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.layanan_publik.konsultasi_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.konsultasi_settings_desc') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.layanan_publik.label_consultation_desc') }}</label>
                        <textarea name="extra_data[consultation_desc]" x-model="consultation_desc" rows="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.consultation_form_general_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_consultation_form_title') }}</label>
                                <input type="text" name="extra_data[consultation_form_title]" x-model="consultation_form_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_consultation_form_send') }}</label>
                                <input type="text" name="extra_data[consultation_form_send]" x-model="consultation_form_send" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_consultation_success') }}</label>
                                <input type="text" name="extra_data[consultation_success]" x-model="consultation_success" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>
                    </div>

                    {{-- Pengaturan Daftar Kolom Formulir Konsultasi --}}
                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.consultation_form_fields_title') }}</h3>
                                <input type="hidden" name="extra_data[show_consultation_form]" :value="show_consultation_form">
                                <button type="button" @click="toggleShow('show_consultation_form')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors" :class="show_consultation_form == 1 ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" :title="show_consultation_form == 1 ? '{{ __('cms.layanan_publik.btn_hide_guest') }}' : '{{ __('cms.layanan_publik.btn_show_guest') }}'">
                                    <template x-if="show_consultation_form == 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="show_consultation_form != 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    </template>
                                    <span x-text="show_consultation_form == 1 ? '{{ __('cms.layanan_publik.status_show') }}' : '{{ __('cms.layanan_publik.status_hide') }}'"></span>
                                </button>
                            </div>
                            <button type="button" @click="addConsultationFormField()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                {{ __('cms.layanan_publik.btn_add_form_field') }}
                            </button>
                        </div>

                        <template x-for="(item, index) in consultation_form_fields" :key="index">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl mb-4 relative group">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_id') }}</label>
                                        <input type="text" :name="`extra_data[consultation_form_fields][${index}][id]`" x-model="item.id" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_label') }}</label>
                                        <input type="text" :name="`extra_data[consultation_form_fields][${index}][label]`" x-model="item.label" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_field_type') }}</label>
                                        <select :name="`extra_data[consultation_form_fields][${index}][type]`" x-model="item.type" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                            <option value="text">{{ __('cms.layanan_publik.type_text') }}</option>
                                            <option value="email">{{ __('cms.layanan_publik.type_email') }}</option>
                                            <option value="number">{{ __('cms.layanan_publik.type_number') }}</option>
                                            <option value="date">{{ __('cms.layanan_publik.type_date') }}</option>
                                            <option value="select">{{ __('cms.layanan_publik.type_select') }}</option>
                                            <option value="file">{{ __('cms.layanan_publik.type_file') }}</option>
                                            <option value="textarea">{{ __('cms.layanan_publik.type_textarea') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center justify-between pt-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :name="`extra_data[consultation_form_fields][${index}][required]`" value="1" x-model="item.required" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                            <span class="ml-2 text-sm text-gray-700 font-medium">{{ __('cms.layanan_publik.label_required') }}</span>
                                        </label>
                                        <button type="button" @click="removeConsultationFormField(index)" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-sm font-medium transition-colors">{{ __('cms.layanan_publik.btn_delete') }}</button>
                                    </div>
                                </div>
                                <div class="mt-3" x-show="item.type === 'select' || item.type === 'file'">
                                    <label class="block text-xs text-gray-500 mb-1" x-text="item.type === 'select' ? '{{ __('cms.layanan_publik.label_options_select') }}' : '{{ __('cms.layanan_publik.label_options_file') }}'"></label>
                                    <input type="text" :name="`extra_data[consultation_form_fields][${index}][options]`" x-model="item.options" :placeholder="item.type === 'select' ? '{{ __('cms.layanan_publik.placeholder_options_select') }}' : '{{ __('cms.layanan_publik.placeholder_options_file') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                </div>
                                <div class="mt-3" x-show="item.type === 'text' || item.type === 'textarea' || item.type === 'email' || item.type === 'number'">
                                    <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_placeholder') }}</label>
                                    <input type="text" :name="`extra_data[consultation_form_fields][${index}][placeholder]`" x-model="item.placeholder" :placeholder="'{{ __('cms.layanan_publik.placeholder_placeholder') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- PERPUSTAKAAN Settings (Only visible when type === 'perpustakaan') --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'perpustakaan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.layanan_publik.perpustakaan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.layanan_publik.perpustakaan_settings_desc') }}</p>
                    </div>

                    {{-- 1. Tujuan --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_objs_title') }}</h3>
                            <button type="button" @click="addLibObj()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.layanan_publik.btn_add_lib_obj') }}
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(obj, index) in lib_objs" :key="index">
                                <div class="flex items-start gap-2">
                                    <textarea :name="'extra_data[lib_objs]['+index+'][text]'" x-model="obj.text" rows="2" :placeholder="'{{ __('cms.layanan_publik.placeholder_lib_obj') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    <button type="button" @click="removeLibObj(index)" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 2. Tombol Kunjungan Website --}}
                    <div class="pt-6 border-t border-gray-100 space-y-3">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_visit_btn_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_visit_btn') }}</label>
                                <input type="text" name="extra_data[lib_visit_btn]" x-model="lib_visit_btn" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_redirect_url') }}</label>
                                <input type="text" name="extra_data[lib_redirect_url]" x-model="lib_redirect_url" placeholder="https://..." class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <span class="text-[10px] text-gray-500 block mt-0.5">{{ __('cms.layanan_publik.hint_lib_redirect_url') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Fasilitas / Layanan (Cards) --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_cards_title') }}</h3>
                            <button type="button" @click="addLibCard()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.layanan_publik.btn_add_lib_card') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(card, index) in lib_cards" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.layanan_publik.label_lib_card') }}' + ' ' + (index + 1)"></label>
                                        <button type="button" @click="removeLibCard(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_card_title') }}</label>
                                        <input type="text" :name="'extra_data[lib_cards]['+index+'][title]'" x-model="card.title" :placeholder="'{{ __('cms.layanan_publik.placeholder_title_general') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_card_desc') }}</label>
                                        <textarea :name="'extra_data[lib_cards]['+index+'][desc]'" x-model="card.desc" rows="2" :placeholder="'{{ __('cms.layanan_publik.placeholder_desc_general') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 4. Waktu Pelayanan & Tata Tertib --}}
                    <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.layanan_publik.label_lib_hours') }}</label>
                            <textarea name="extra_data[lib_hours]" x-model="lib_hours" rows="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_rules_title') }}</label>
                                <button type="button" @click="addLibRule()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    {{ __('cms.layanan_publik.btn_add_lib_rule') }}
                                </button>
                            </div>
                            <div class="space-y-2.5">
                                <template x-for="(rule, index) in lib_rules" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="'extra_data[lib_rules]['+index+'][text]'" x-model="rule.text" :placeholder="'{{ __('cms.layanan_publik.placeholder_lib_rule') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <button type="button" @click="removeLibRule(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Foto Perpustakaan --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_photos_title') }}</h3>

                        {{-- Daftar Foto Saat Ini --}}
                        @php
                            $currentLibPhotos = $layananPublik->extra_data['lib_photos'] ?? [];
                            if (!is_array($currentLibPhotos)) { $currentLibPhotos = []; }
                            if (isset($layananPublik->extra_data['lib_photo1']) && !in_array($layananPublik->extra_data['lib_photo1'], $currentLibPhotos)) {
                                $currentLibPhotos[] = $layananPublik->extra_data['lib_photo1'];
                            }
                            if (isset($layananPublik->extra_data['lib_photo2']) && !in_array($layananPublik->extra_data['lib_photo2'], $currentLibPhotos)) {
                                $currentLibPhotos[] = $layananPublik->extra_data['lib_photo2'];
                            }
                        @endphp

                        @if(!empty($currentLibPhotos))
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-gray-700">{{ __('cms.layanan_publik.current_photos_title') }}</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($currentLibPhotos as $photoPath)
                                <div class="p-2 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between gap-2 shadow-sm">
                                    <div class="flex items-center gap-2 overflow-hidden min-w-0">
                                        <img src="{{ asset('storage/' . $photoPath) }}" class="w-10 h-10 object-cover rounded border border-blue-200 shrink-0">
                                        <div class="min-w-0">
                                            <a href="{{ asset('storage/' . $photoPath) }}" target="_blank" class="text-xs text-blue-600 hover:underline block truncate" title="{{ basename($photoPath) }}">{{ basename($photoPath) }}</a>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <input type="hidden" name="extra_data[existing_lib_photos][]" value="{{ $photoPath }}">
                                        <button type="button" onclick="this.closest('.p-2').remove()" class="p-1 bg-red-100 hover:bg-red-200 text-red-600 rounded text-xs font-semibold transition-colors" :title="'{{ __('cms.layanan_publik.btn_delete_photo') }}'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Tambah Foto Baru --}}
                        <div class="pt-2">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_photos_edit') }}</label>
                            <div class="flex items-center gap-2">
                                <div class="relative overflow-hidden shrink-0">
                                    <input type="file" name="extra_data_lib_photos[]" multiple accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="lib_photos_names = Array.from($event.target.files).map(f => f.name).join(', ')">
                                    <button type="button" class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                        {{ __('cms.layanan_publik.btn_choose_images') }}
                                    </button>
                                </div>
                                <input type="text" name="extra_data[lib_photos_names]" x-model="lib_photos_names" :placeholder="'{{ __('cms.layanan_publik.placeholder_lib_photos_names') }}'" readonly class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-600 truncate">
                            </div>
                            <span class="text-[10px] text-gray-500 block mt-1">{{ __('cms.layanan_publik.hint_lib_photos_edit') }}</span>
                        </div>
                    </div>

                    {{-- 6. Prosedur Infographic --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-bold text-gray-800">{{ __('cms.layanan_publik.lib_procs_title') }}</label>
                            <button type="button" @click="addLibProc()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.layanan_publik.btn_add_lib_proc') }}
                            </button>
                        </div>
                        <div>
                            <input type="text" name="extra_data[lib_proc_title]" x-model="lib_proc_title" :placeholder="'{{ __('cms.layanan_publik.placeholder_lib_proc_title') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white mb-4">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(proc, index) in lib_procs" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.layanan_publik.label_lib_proc') }}' + ' ' + (index + 1)"></label>
                                        <button type="button" @click="removeLibProc(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_proc_title') }}</label>
                                        <input type="text" :name="'extra_data[lib_procs]['+index+'][title]'" x-model="proc.title" :placeholder="'{{ __('cms.layanan_publik.placeholder_title_general') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_proc_desc') }}</label>
                                        <textarea :name="'extra_data[lib_procs]['+index+'][desc]'" x-model="proc.desc" rows="2" :placeholder="'{{ __('cms.layanan_publik.placeholder_desc_general') }}'" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 7. File Panduan PDF Perpustakaan --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <label class="block text-xs text-gray-500 mb-1">{{ __('cms.layanan_publik.label_lib_pdf') }}</label>
                        @if(isset($layananPublik->extra_data['lib_pdf']))
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-blue-800">{{ __('cms.layanan_publik.current_doc') }} (Perpustakaan)</p>
                                    <a href="{{ asset('storage/' . $layananPublik->extra_data['lib_pdf']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('cms.layanan_publik.view_pdf') }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="extra_data[lib_pdf]" value="{{ $layananPublik->extra_data['lib_pdf'] }}">
                                <button type="button" onclick="this.closest('.mb-4').remove()" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1" :title="'{{ __('cms.layanan_publik.btn_delete_doc') }}'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    {{ __('cms.layanan_publik.btn_delete') }}
                                </button>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <div class="relative overflow-hidden shrink-0">
                                <input type="file" name="extra_data_lib_pdf" accept="application/pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="lib_pdf_name = $event.target.files[0] ? $event.target.files[0].name : lib_pdf_name">
                                <button type="button" class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                    {{ __('cms.layanan_publik.btn_choose_file') }}
                                </button>
                            </div>
                            <input type="text" name="extra_data[lib_pdf_name]" x-model="lib_pdf_name" :placeholder="'{{ __('cms.layanan_publik.placeholder_lib_photos_names') }}'" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-50 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar Info --}}
            <div class="space-y-6">
                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.layanan_publik.sidebar_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.layanan_publik.label_order') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="order" value="{{ $layananPublik->order }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.layanan_publik.label_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="published_at" value="{{ $layananPublik->published_at ? $layananPublik->published_at->format('Y-m-d') : date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="mt-2 flex flex-col gap-1.5">
                                <label class="inline-flex items-center cursor-pointer text-xs text-gray-600 font-medium">
                                    <input type="checkbox" name="extra_data[auto_today_date]" value="1" x-model="auto_today_date" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 mr-1.5">
                                    {{ __('cms.layanan_publik.label_auto_today') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                    <button type="submit" class="w-full py-3 px-4 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-sm">
                        {{ __('cms.layanan_publik.btn_update') }}
                    </button>
                    <a href="{{ route('cms.features.layanan_publik.index', $feature) }}"
                        class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-center transition-all">
                        {{ __('cms.layanan_publik.btn_cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.rteUploadUrl = '{{ route('cms.settings.rte.upload') }}';
</script>
<script src="{{ asset('js/cms/features/layanan_publik/create.js') }}"></script>
@endpush
