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
    <a href="{{ route('cms.features.pengelolaan.index', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.pengelolaan.add_button'))

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="pengelolaanForm('penyusutan')">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cms.features.pengelolaan.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.pengelolaan.create_title') }}</h1>
        </div>
    </div>

    <form action="{{ route('cms.features.pengelolaan.pages.store', $feature) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Type & Title --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengelolaan.label_type') }} <span class="text-red-500">*</span></label>
                            <select name="type" x-model="type" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="penyusutan">{{ __('cms.pengelolaan.type_penyusutan') }}</option>
                                <option value="penyimpanan">{{ __('cms.pengelolaan.type_penyimpanan') }}</option>
                                <option value="preservasi">{{ __('cms.pengelolaan.type_preservasi') }}</option>
                                <option value="pengolahan">{{ __('cms.pengelolaan.type_pengolahan') }}</option>
                                <option value="pemanfaatan">{{ __('cms.pengelolaan.type_pemanfaatan') }}</option>
                                <option value="penjangkauan">{{ __('cms.pengelolaan.type_penjangkauan') }}</option>
                                <option value="akuisisi">{{ __('cms.pengelolaan.type_akuisisi') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengelolaan.label_title') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required placeholder="{{ __('cms.pengelolaan.placeholder_title') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Description / Content --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.pengelolaan.label_description') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1"></div>
                    </div>
                    <input type="hidden" name="description" id="description_input">
                </div>

                {{-- Media Images --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengelolaan.label_gallery') }}</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>{{ __('cms.pengelolaan.hint_gallery') }}</span>
                                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="handleFiles">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.hint_gallery_sub') }}</p>
                        </div>
                    </div>
                    {{-- Previews --}}
                    <div id="file-previews" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                </div>

                {{-- Document / Guide / Form File (PDF) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengelolaan.label_pdf') }}</label>
                    <div class="flex items-center gap-2">
                        <div class="relative overflow-hidden shrink-0">
                            <input type="file" name="extra_data_file_upload" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="file_name = $event.target.files[0] ? $event.target.files[0].name : file_name">
                            <button type="button" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition-colors pointer-events-none">
                                {{ __('cms.layanan_publik.btn_choose_file') }}
                            </button>
                        </div>
                        <input type="text" name="extra_data[file_name]" x-model="file_name" placeholder="{{ __('cms.layanan_publik.placeholder_auto_file') }}" class="flex-1 px-3.5 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.pengelolaan.hint_pdf') }}</p>
                </div>

                {{-- PENYIMPANAN Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'penyimpanan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.penyimpanan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.penyimpanan_settings_desc') }}</p>
                    </div>

                    {{-- Prinsip Penyimpanan --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.pengelolaan.label_prinsip_title') }}</label>
                                <input type="text" name="extra_data[prinsip_title]" x-model="prinsip_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.pengelolaan.label_prinsip_desc') }}</label>
                                <textarea name="extra_data[prinsip_desc]" x-model="prinsip_desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_prinsip_list') }}</label>
                                <button type="button" @click="addPrinsip()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    {{ __('cms.pengelolaan.btn_add_poin') }}
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(item, index) in prinsip_list" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                        <div class="flex items-center justify-between gap-2">
                                            <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.pengelolaan.item_prinsip') }} ' + (index + 1)"></label>
                                            <button type="button" @click="removePrinsip(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors" :title="'{{ __('cms.pengelolaan.btn_delete_poin') }}'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_poin_title') }}</label>
                                            <input type="text" :name="'extra_data[prinsip_list]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" :placeholder="'{{ __('cms.pengelolaan.placeholder_prinsip_title') }}'">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_poin_desc') }}</label>
                                            <textarea :name="'extra_data[prinsip_list]['+index+'][desc]'" x-model="item.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" :placeholder="'{{ __('cms.pengelolaan.placeholder_prinsip_desc') }}'"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Sistem Penyimpanan (Cards) --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_sistem_title') }}</h3>
                            <button type="button" @click="addSistem()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_sistem') }}
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.pengelolaan.label_sistem_section_title') }}</label>
                            <input type="text" name="extra_data[sistem_title]" x-model="sistem_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_sistem_title') }}">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(item, index) in sistem_penyimpanan" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.pengelolaan.item_sistem') }} ' + (index + 1)"></label>
                                        <button type="button" @click="removeSistem(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_sistem_name') }}</label>
                                        <input type="text" :name="'extra_data[sistem_penyimpanan]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_choose_icon') }}</label>
                                        <select :name="'extra_data[sistem_penyimpanan]['+index+'][icon]'" x-model="item.icon" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option value="clipboard">{{ __('cms.pengelolaan.icons.sistem_clipboard') }}</option>
                                            <option value="archive">{{ __('cms.pengelolaan.icons.sistem_archive') }}</option>
                                            <option value="book">{{ __('cms.pengelolaan.icons.sistem_book') }}</option>
                                            <option value="calendar">{{ __('cms.pengelolaan.icons.sistem_calendar') }}</option>
                                            <option value="map">{{ __('cms.pengelolaan.icons.sistem_map') }}</option>
                                            <option value="document">{{ __('cms.pengelolaan.icons.sistem_document') }}</option>
                                            <option value="lock">{{ __('cms.pengelolaan.icons.sistem_lock') }}</option>
                                            <option value="database">{{ __('cms.pengelolaan.icons.sistem_database') }}</option>
                                            <option value="tag">{{ __('cms.pengelolaan.icons.sistem_tag') }}</option>
                                            <option value="folder">{{ __('cms.pengelolaan.icons.sistem_folder') }}</option>
                                            <option value="check">{{ __('cms.pengelolaan.icons.sistem_check') }}</option>
                                            <option value="star">{{ __('cms.pengelolaan.icons.sistem_star') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_desc_general') }}</label>
                                        <textarea :name="'extra_data[sistem_penyimpanan]['+index+'][desc]'" x-model="item.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Fasilitas & Ruang --}}
                    <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_fasilitas_title') }}</label>
                                <button type="button" @click="addFasilitas()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    {{ __('cms.pengelolaan.btn_add_fasilitas') }}
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_fasilitas_section_title') }}</label>
                                <input type="text" name="extra_data[fasilitas_title]" x-model="fasilitas_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_fasilitas_title') }}">
                            </div>
                            <div class="mb-4 pt-3 border-t border-gray-100">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_upload_fasilitas') }}</label>
                                <input type="file" name="fasilitas_images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <p class="text-[11px] text-gray-500 mt-1">{{ __('cms.pengelolaan.hint_upload_multiple') }}</p>
                            </div>
                            <div class="space-y-2.5">
                                <template x-for="(item, index) in fasilitas_list" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="'extra_data[fasilitas_list]['+index+'][text]'" x-model="item.text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <button type="button" @click="removeFasilitas(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_ruang_title') }}</label>
                                <button type="button" @click="addRuang()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    {{ __('cms.pengelolaan.btn_add_ruang') }}
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_ruang_section_title') }}</label>
                                <input type="text" name="extra_data[ruang_title]" x-model="ruang_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_ruang_title') }}">
                            </div>
                            <div class="mb-4 pt-3 border-t border-gray-100">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_upload_ruang') }}</label>
                                <input type="file" name="ruang_images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <p class="text-[11px] text-gray-500 mt-1">{{ __('cms.pengelolaan.hint_upload_multiple') }}</p>
                            </div>
                            <div class="space-y-2.5">
                                <template x-for="(item, index) in ruang_list" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="'extra_data[ruang_list]['+index+'][text]'" x-model="item.text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <button type="button" @click="removeRuang(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PRESERVASI Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'preservasi'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.preservasi_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.preservasi_settings_desc') }}</p>
                    </div>

                    {{-- Preservasi List --}}
                    <div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_preservasi_section_title') }}</label>
                            <input type="text" name="extra_data[preservasi_title]" x-model="preservasi_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_preservasi_title') }}">
                        </div>
                        <div class="flex items-center justify-between mb-2 pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_preservasi_list') }}</label>
                            <button type="button" @click="addPreservasi()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_preservasi') }}
                            </button>
                        </div>
                        <div class="space-y-2.5">
                            <template x-for="(item, index) in preservasi_list" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="'extra_data[preservasi_list]['+index+'][text]'" x-model="item.text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <button type="button" @click="removePreservasi(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Restorasi Desc & List --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_restorasi_section_title') }}</label>
                            <input type="text" name="extra_data[restorasi_title]" x-model="restorasi_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_restorasi_title') }}">
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.pengelolaan.label_restorasi_desc') }}</label>
                            <textarea name="extra_data[restorasi_desc]" x-model="restorasi_desc" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_restorasi_list') }}</label>
                                <button type="button" @click="addRestorasi()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    {{ __('cms.pengelolaan.btn_add_restorasi') }}
                                </button>
                            </div>
                            <div class="space-y-2.5">
                                <template x-for="(item, index) in restorasi_list" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="'extra_data[restorasi_list]['+index+'][text]'" x-model="item.text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                        <button type="button" @click="removeRestorasi(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PENGOLAHAN Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'pengolahan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.pengolahan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.pengolahan_settings_desc') }}</p>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_pengolahan_section_title') }}</label>
                            <input type="text" name="extra_data[pengolahan_title]" x-model="pengolahan_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_pengolahan_title') }}">
                        </div>
                        <div class="flex items-center justify-between mb-2 pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_pengolahan_list') }}</label>
                            <button type="button" @click="addPengolahan()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_pengolahan') }}
                            </button>
                        </div>
                        <div class="space-y-2.5">
                            <template x-for="(item, index) in pengolahan_list" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="'extra_data[pengolahan_list]['+index+'][text]'" x-model="item.text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <button type="button" @click="removePengolahan(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- PEMANFAATAN Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'pemanfaatan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.pemanfaatan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.pemanfaatan_settings_desc') }}</p>
                    </div>

                    {{-- Mekanisme Title & Desc --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.pengelolaan.label_mekanisme_title') }}</label>
                            <input type="text" name="extra_data[mekanisme_title]" x-model="mekanisme_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">{{ __('cms.pengelolaan.label_mekanisme_desc') }}</label>
                            <textarea name="extra_data[mekanisme_desc]" x-model="mekanisme_desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                        </div>
                    </div>

                    {{-- Quote --}}
                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-800 mb-1">{{ __('cms.pengelolaan.label_pemanfaatan_quote') }}</label>
                        <textarea name="extra_data[pemanfaatan_quote]" x-model="pemanfaatan_quote" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                    </div>

                    {{-- Akses List (Cards) --}}
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_akses_list') }}</h3>
                            <button type="button" @click="addAkses()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_akses') }}
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_akses_section_title') }}</label>
                            <input type="text" name="extra_data[akses_title]" x-model="akses_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_akses_title') }}">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(item, index) in akses_list" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.pengelolaan.item_akses') }} ' + (index + 1)"></label>
                                        <button type="button" @click="removeAkses(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_akses_name') }}</label>
                                        <input type="text" :name="'extra_data[akses_list]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_choose_icon') }}</label>
                                        <select :name="'extra_data[akses_list]['+index+'][icon]'" x-model="item.icon" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option value="clipboard">{{ __('cms.pengelolaan.icons.akses_clipboard') }}</option>
                                            <option value="archive">{{ __('cms.pengelolaan.icons.akses_archive') }}</option>
                                            <option value="book">{{ __('cms.pengelolaan.icons.akses_book') }}</option>
                                            <option value="calendar">{{ __('cms.pengelolaan.icons.akses_calendar') }}</option>
                                            <option value="map">{{ __('cms.pengelolaan.icons.akses_map') }}</option>
                                            <option value="document">{{ __('cms.pengelolaan.icons.akses_document') }}</option>
                                            <option value="lock">{{ __('cms.pengelolaan.icons.akses_lock') }}</option>
                                            <option value="database">{{ __('cms.pengelolaan.icons.akses_database') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_desc_general') }}</label>
                                        <textarea :name="'extra_data[akses_list]['+index+'][desc]'" x-model="item.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- PENJANGKAUAN Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'penjangkauan'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.penjangkauan_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.penjangkauan_settings_desc') }}</p>
                    </div>

                    {{-- Kegiatan List (Cards) --}}
                    <div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_kegiatan_section_title') }}</label>
                            <input type="text" name="extra_data[kegiatan_title]" x-model="kegiatan_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_kegiatan_title') }}">
                        </div>
                        <div class="flex items-center justify-between mb-4 pt-2 border-t border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_kegiatan_list') }}</h3>
                            <button type="button" @click="addKegiatan()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_kegiatan') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(item, index) in kegiatan_list" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.pengelolaan.item_kegiatan') }} ' + (index + 1)"></label>
                                        <button type="button" @click="removeKegiatan(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_kegiatan_name') }}</label>
                                        <input type="text" :name="'extra_data[kegiatan_list]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_choose_icon') }}</label>
                                        <select :name="'extra_data[kegiatan_list]['+index+'][icon]'" x-model="item.icon" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option value="clipboard">{{ __('cms.pengelolaan.icons.kegiatan_clipboard') }}</option>
                                            <option value="archive">{{ __('cms.pengelolaan.icons.kegiatan_archive') }}</option>
                                            <option value="book">{{ __('cms.pengelolaan.icons.kegiatan_book') }}</option>
                                            <option value="calendar">{{ __('cms.pengelolaan.icons.kegiatan_calendar') }}</option>
                                            <option value="map">{{ __('cms.pengelolaan.icons.kegiatan_map') }}</option>
                                            <option value="document">{{ __('cms.pengelolaan.icons.kegiatan_document') }}</option>
                                            <option value="lock">{{ __('cms.pengelolaan.icons.kegiatan_lock') }}</option>
                                            <option value="database">{{ __('cms.pengelolaan.icons.kegiatan_database') }}</option>
                                            <option value="users">{{ __('cms.pengelolaan.icons.kegiatan_users') }}</option>
                                            <option value="globe">{{ __('cms.pengelolaan.icons.kegiatan_globe') }}</option>
                                            <option value="tag">{{ __('cms.pengelolaan.icons.kegiatan_tag') }}</option>
                                            <option value="folder">{{ __('cms.pengelolaan.icons.kegiatan_folder') }}</option>
                                            <option value="check">{{ __('cms.pengelolaan.icons.kegiatan_check') }}</option>
                                            <option value="star">{{ __('cms.pengelolaan.icons.kegiatan_star') }}</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-gray-200/60">
                                        <div>
                                            <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_button_text') }}</label>
                                            <input type="text" :name="'extra_data[kegiatan_list]['+index+'][button_label]'" x-model="item.button_label" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_button_text') }}">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_button_url') }}</label>
                                            <input type="text" :name="'extra_data[kegiatan_list]['+index+'][button_url]'" x-model="item.button_url" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_button_url') }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_desc_general') }}</label>
                                        <textarea :name="'extra_data[kegiatan_list]['+index+'][desc]'" x-model="item.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- AKUISISI Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" x-cloak x-show="type === 'akuisisi'">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.pengelolaan.akuisisi_settings_title') }}</h2>
                        <p class="text-xs text-gray-500">{{ __('cms.pengelolaan.akuisisi_settings_desc') }}</p>
                    </div>

                    {{-- Tahapan List (Cards) --}}
                    <div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('cms.pengelolaan.label_tahapan_section_title') }}</label>
                            <input type="text" name="extra_data[tahapan_title]" x-model="tahapan_title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="{{ __('cms.pengelolaan.placeholder_tahapan_title') }}">
                        </div>
                        <div class="flex items-center justify-between mb-4 pt-2 border-t border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">{{ __('cms.pengelolaan.label_tahapan_list') }}</h3>
                            <button type="button" @click="addTahapan()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('cms.pengelolaan.btn_add_tahapan') }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(item, index) in tahapan_list" :key="index">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 relative group">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="block text-xs font-bold text-gray-700" x-text="'{{ __('cms.pengelolaan.item_tahapan') }} ' + (index + 1)"></label>
                                        <button type="button" @click="removeTahapan(index)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg border border-red-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_tahapan_name') }}</label>
                                        <input type="text" :name="'extra_data[tahapan_list]['+index+'][title]'" x-model="item.title" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-gray-500 mb-1">{{ __('cms.pengelolaan.label_desc_general') }}</label>
                                        <textarea :name="'extra_data[tahapan_list]['+index+'][desc]'" x-model="item.desc" rows="2" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></textarea>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar Info --}}
            <div class="space-y-6">
                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.pengelolaan.sidebar_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.pengelolaan.label_order') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="order" value="{{ $nextOrder }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                    <button type="submit" class="w-full py-3 px-4 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-sm">
                        {{ __('cms.pengelolaan.btn_save') }}
                    </button>
                    <a href="{{ route('cms.features.pengelolaan.index', $feature) }}"
                        class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-center transition-all">
                        {{ __('cms.pengelolaan.btn_cancel') }}
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
<script src="{{ asset('js/cms/features/pengelolaan/create.js') }}?v={{ time() }}"></script>
@endpush
