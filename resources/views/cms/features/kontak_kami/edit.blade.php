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
    <a href="{{ route('cms.features.kontak_kami.index', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.kontak_kami.edit_button'))

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="kontakKamiForm({{ json_encode($kontakKami->extra_data ?? null) }}, {{ json_encode($kontakKami->images ?? []) }})">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cms.features.kontak_kami.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.kontak_kami.edit_title') }}</h1>
        </div>
    </div>

    <form action="{{ route('cms.features.kontak_kami.pages.update', [$feature, $kontakKami]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_title') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $kontakKami->title }}" required placeholder="{{ __('cms.kontak_kami.placeholder_title') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Description / Content --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.kontak_kami.label_description') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1">{!! $kontakKami->description !!}</div>
                    </div>
                    <input type="hidden" name="description" id="description_input">
                </div>

                {{-- Media Images --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.kontak_kami.label_gallery') }}</label>

                    {{-- Existing Images --}}
                    <div class="mb-4" x-show="existingImages.length > 0">
                        <label class="block text-xs font-semibold text-gray-500 mb-2">{{ __('cms.layanan_publik.current_photos_title') }}</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <template x-for="(img, index) in existingImages" :key="index">
                                <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shadow-sm">
                                    <img :src="'{{ asset('storage') }}/' + img" class="w-full h-full object-cover">
                                    <input type="hidden" name="existing_images[]" :value="img">
                                    <button type="button" @click="removeExistingImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" title="{{ __('cms.layanan_publik.btn_delete') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Add New Images --}}
                    <label class="block text-xs font-semibold text-gray-500 mb-2">{{ __('cms.layanan_publik.btn_choose_images') }}</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>{{ __('cms.kontak_kami.hint_gallery') }}</span>
                                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFiles">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">{{ __('cms.kontak_kami.hint_gallery_sub') }}</p>
                        </div>
                    </div>
                    {{-- Previews --}}
                    <div id="file-previews" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                </div>

                {{-- EXTRA DATA: Top Cards (Kartu Highlight Utama) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">{{ __('cms.kontak_kami.top_cards_title') }}</h2>
                            <p class="text-xs text-gray-500">{{ __('cms.kontak_kami.top_cards_desc') }}</p>
                        </div>
                        <button type="button" @click="addTopCard()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            {{ __('cms.kontak_kami.btn_add_top_card') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="(item, index) in top_cards" :key="index">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.kontak_kami.top_card_item_prefix') }}' + (index + 1)"></label>
                                    <button type="button" @click="removeTopCard(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors" title="{{ __('cms.kontak_kami.btn_delete_top_card') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_top_card_title') }}</label>
                                    <input type="text" :name="'extra_data[top_cards]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="{{ __('cms.kontak_kami.placeholder_top_card_title') }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_top_card_subtitle') }}</label>
                                    <input type="text" :name="'extra_data[top_cards]['+index+'][subtitle]'" x-model="item.subtitle" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="{{ __('cms.kontak_kami.placeholder_top_card_subtitle') }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_choose_icon') }}</label>
                                    <select :name="'extra_data[top_cards]['+index+'][icon]'" x-model="item.icon" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="map">{{ __('cms.kontak_kami.icon_map') }}</option>
                                        <option value="building">{{ __('cms.kontak_kami.icon_building') }}</option>
                                        <option value="clock">{{ __('cms.kontak_kami.icon_clock') }}</option>
                                        <option value="phone">{{ __('cms.kontak_kami.icon_phone') }}</option>
                                        <option value="message">{{ __('cms.kontak_kami.icon_message') }}</option>
                                        <option value="mail">{{ __('cms.kontak_kami.icon_mail') }}</option>
                                        <option value="globe">{{ __('cms.kontak_kami.icon_globe') }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- EXTRA DATA: Alamat & Kontak Langsung --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.kontak_kami.alamat_section_header') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.kontak_kami.alamat_section_desc') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_section_title_guest') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="extra_data[alamat_section_title]" x-model="alamat_section_title" required placeholder="{{ __('cms.kontak_kami.placeholder_alamat_section_title') }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_alamat') }}</label>
                        <textarea name="extra_data[alamat_lengkap]" x-model="alamat_lengkap" rows="3" placeholder="{{ __('cms.kontak_kami.placeholder_alamat') }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_telepon') }}</label>
                            <input type="text" name="extra_data[telepon]" x-model="telepon" placeholder="{{ __('cms.kontak_kami.placeholder_telepon') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_whatsapp') }}</label>
                            <input type="text" name="extra_data[whatsapp]" x-model="whatsapp" placeholder="{{ __('cms.kontak_kami.placeholder_whatsapp') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_email') }}</label>
                            <input type="text" name="extra_data[email]" x-model="email" placeholder="{{ __('cms.kontak_kami.placeholder_email') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_instagram') }}</label>
                            <input type="text" name="extra_data[instagram]" x-model="instagram" placeholder="{{ __('cms.kontak_kami.placeholder_instagram') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_twitter') }}</label>
                            <input type="text" name="extra_data[twitter]" x-model="twitter" placeholder="{{ __('cms.kontak_kami.placeholder_twitter') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_facebook') }}</label>
                            <input type="text" name="extra_data[facebook]" x-model="facebook" placeholder="{{ __('cms.kontak_kami.placeholder_facebook') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_youtube') }}</label>
                            <input type="text" name="extra_data[youtube]" x-model="youtube" placeholder="{{ __('cms.kontak_kami.placeholder_youtube') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                    </div>
                </div>

                {{-- EXTRA DATA: Jam Operasional --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">{{ __('cms.kontak_kami.jam_section_header') }}</h2>
                            <p class="text-xs text-gray-500">{{ __('cms.kontak_kami.jam_section_desc') }}</p>
                        </div>
                        <button type="button" @click="addJam()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            {{ __('cms.kontak_kami.btn_add_jam') }}
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_section_title_guest') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="extra_data[jam_section_title]" x-model="jam_section_title" required placeholder="{{ __('cms.kontak_kami.placeholder_jam_section_title') }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_jam_desc') }}</label>
                        <input type="text" name="extra_data[jam_operasional_desc]" x-model="jam_operasional_desc"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.kontak_kami.label_jam_list') }}</label>
                        <div class="space-y-3">
                            <template x-for="(item, index) in jam_operasional_list" :key="index">
                                <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                                    <div class="flex-1">
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_jam_hari') }}</label>
                                        <input type="text" :name="'extra_data[jam_operasional_list]['+index+'][hari]'" x-model="item.hari" placeholder="{{ __('cms.kontak_kami.placeholder_jam_hari') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_jam_waktu') }}</label>
                                        <input type="text" :name="'extra_data[jam_operasional_list]['+index+'][jam]'" x-model="item.jam" placeholder="{{ __('cms.kontak_kami.placeholder_jam_waktu') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    </div>
                                    <div class="pt-5">
                                        <button type="button" @click="removeJam(index)" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors" title="{{ __('cms.kontak_kami.btn_delete_jam') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- EXTRA DATA: Cards (Layanan / Saluran Kontak) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">{{ __('cms.kontak_kami.label_cards_title') }}</h2>
                            <p class="text-xs text-gray-500">{{ __('cms.kontak_kami.cards_section_desc') }}</p>
                        </div>
                        <button type="button" @click="addCard()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            {{ __('cms.kontak_kami.btn_add_card') }}
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_section_title_guest') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="extra_data[cards_section_title]" x-model="cards_section_title" required placeholder="{{ __('cms.kontak_kami.placeholder_cards_section_title') }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(item, index) in cards" :key="index">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.kontak_kami.card_item_prefix') }}' + (index + 1)"></label>
                                    <button type="button" @click="removeCard(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors" title="{{ __('cms.kontak_kami.btn_delete_card') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_card_title') }}</label>
                                    <input type="text" :name="'extra_data[cards]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="{{ __('cms.kontak_kami.placeholder_card_title') }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_card_subtitle') }}</label>
                                    <input type="text" :name="'extra_data[cards]['+index+'][subtitle]'" x-model="item.subtitle" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="{{ __('cms.kontak_kami.placeholder_card_subtitle') }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.kontak_kami.label_choose_icon') }}</label>
                                    <select :name="'extra_data[cards]['+index+'][icon]'" x-model="item.icon" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="phone">{{ __('cms.kontak_kami.icon_phone_card') }}</option>
                                        <option value="message">{{ __('cms.kontak_kami.icon_message_card') }}</option>
                                        <option value="mail">{{ __('cms.kontak_kami.icon_mail_card') }}</option>
                                        <option value="map">{{ __('cms.kontak_kami.icon_map_card') }}</option>
                                        <option value="clock">{{ __('cms.kontak_kami.icon_clock_card') }}</option>
                                        <option value="globe">{{ __('cms.kontak_kami.icon_globe_card') }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar Info --}}
            <div class="space-y-6">
                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.kontak_kami.sidebar_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_order') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="order" value="{{ $kontakKami->order }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.kontak_kami.label_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="published_at" value="{{ $kontakKami->published_at ? $kontakKami->published_at->format('Y-m-d') : date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>
                </div>

                {{-- Action --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                    <button type="submit" class="w-full py-3 px-4 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-sm">
                        {{ __('cms.kontak_kami.btn_update') }}
                    </button>
                    <a href="{{ route('cms.features.kontak_kami.index', $feature) }}"
                        class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-center transition-all">
                        {{ __('cms.kontak_kami.btn_cancel') }}
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
<script src="{{ asset('js/cms/features/kontak_kami/edit.js') }}?v={{ time() }}"></script>
@endpush
