@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @foreach($ancestors as $ancestor)
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.show', $ancestor) }}" class="text-gray-400 hover:text-gray-600 transition-colors">{{ $ancestor->name }}</a>
    @endforeach
@endsection
@section('breadcrumb_active', $feature->name)

@section('content')
<div class="space-y-6" x-data="featureDetail()">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ $feature->parent_id ? route('cms.features.show', $feature->parent_id) : route('cms.features.index') }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.features.detail_title', ['name' => $feature->name]) }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                @if($feature->parent)
                    <span class="text-xs text-gray-400">{{ $feature->parent->name }} &raquo;</span>
                @endif
                {{ __('cms.features.type_label') }}:
                @if($feature->type === 'dropdown')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">{{ __('cms.features.type_dropdown') }}</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">{{ __('cms.features.type_link') }}</span>
                @endif
            </p>
        </div>
    </div>

    @if($feature->type === 'dropdown')
    {{-- ===== DROPDOWN TYPE: Show sub-menu list ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.features.sub.list_title', ['name' => $feature->name]) }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.features.sub.list_desc', ['name' => $feature->name]) }}</p>
            </div>
            <button @click="openAddSubModal()"
                class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('cms.features.sub.add_button') }}
            </button>
        </div>

        <div>
            <table id="tableSubFeatures" class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide w-12">No</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('cms.features.sub.col_name') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('cms.features.col_type') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('cms.features.sub.col_path') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">{{ __('cms.features.col_sub_count') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">{{ __('cms.features.sub.col_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($feature->subfeatures as $sub)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $sub->order }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $sub->name }}</td>
                        <td class="px-6 py-4">
                            @if($sub->type === 'dropdown')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ __('cms.features.type_dropdown') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ __('cms.features.type_link') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $sub->path ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $sub->subfeatures_count ?? 0 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Toggle Visibility Button --}}
                                @if($sub->is_active)
                                <button type="button"
                                    @click="openVisibilityModal({{ $sub->id }}, '{{ addslashes($sub->name) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors"
                                    title="{{ __('cms.features.hide') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                @else
                                {{-- Already hidden: show restore button --}}
                                <form action="{{ route('cms.features.toggle-visibility', $sub) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="mode" value="show">
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-gray-400 hover:bg-gray-500 text-white rounded-md transition-colors"
                                        title="{{ __('cms.features.show_label') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if($sub->type === 'dropdown')
                                <!-- Detail Sub Button (dropdown → sub-features list) -->
                                <a href="{{ route('cms.features.show', $sub) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ __('cms.features.detail') }}
                                </a>
                                @else
                                <!-- Detail Sub Button (link → pages management or content editor) -->
                                <a href="{{ $sub->page_type === 'profile' ? route('cms.features.profile.index', $sub) : ($sub->page_type === 'onsite' ? route('cms.features.pages.index', $sub) : route('cms.features.show', $sub)) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ __('cms.features.detail') }}
                                </a>
                                @endif
                                <!-- Edit Sub Button -->
                                <button @click="openEditSubModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', '{{ $sub->type }}', '{{ $sub->path ?? '' }}', {{ $sub->order }}, '{{ $sub->page_type ?? 'none' }}', {{ $sub->is_login_required ? 'true' : 'false' }})"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Delete Sub Button -->
                                <button @click="openDeleteSubModal({{ $sub->id }}, '{{ addslashes($sub->name) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-400 text-sm">{{ __('cms.features.sub.empty') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
 
    @else
    {{-- ===== LINK TYPE: Pages management or content editor ===== --}}
 
    @if($feature->page_type !== 'none')
    <!-- Multi-page management card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.feature_pages.title', ['name' => $feature->name]) }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.feature_pages.desc', ['name' => $feature->name]) }}</p>
            </div>
            <a href="{{ $feature->page_type === 'profile' ? route('cms.features.profile.index', $feature) : route('cms.features.pages.index', $feature) }}"
                class="flex items-center gap-2 bg-[#174E93] hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('cms.feature_pages.add_button') }} ({{ $feature->pages_count ?? 0 }})
            </a>
        </div>
    </div>
    @endif
 
    <!-- Content editor -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">{{ __('cms.features.content.title', ['name' => $feature->name]) }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.features.content.desc', ['name' => $feature->name]) }}</p>
        </div>
        <div class="p-6">
            <form action="{{ route('cms.features.update-content', $feature) }}" method="POST" class="space-y-4" id="contentForm">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.features.content.label') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1" style="min-width: 100%;">
                            {!! old('content', $feature->content) !!}
                        </div>
                    </div>
                    <input type="hidden" name="content" id="content_input" />
                    <p class="text-xs text-gray-400 mt-1.5">{{ __('cms.features.content.help') }}</p>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ $feature->parent_id ? route('cms.features.show', $feature->parent_id) : route('cms.features.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.back') }}
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                        {{ __('cms.common.save_content') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($feature->type === 'dropdown')
    {{-- ===== VISIBILITY MODE MODAL ===== --}}
    <div x-show="visibilityModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="visibilityModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-[9999]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.visibility_modal.title') }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('cms.features.visibility_modal.subtitle', ['name' => '']) }} <strong x-text="visibilityModal.name"></strong></p>
                    </div>
                </div>
                <button @click="visibilityModal.open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            {{-- Options --}}
            <div class="px-6 py-5 space-y-3">
                {{-- Option 1: Menu Only --}}
                <form :action="`/cms/features/${visibilityModal.id}/toggle-visibility`" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="mode" value="menu_only">
                    <button type="submit"
                        class="w-full text-left flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-[#174E93] hover:bg-blue-50 transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center shrink-0 mt-0.5 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('cms.features.visibility_modal.menu_only_title') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{!! __('cms.features.visibility_modal.menu_only_desc') !!}</p>
                        </div>
                    </button>
                </form>
                {{-- Option 2: Total Block --}}
                <form :action="`/cms/features/${visibilityModal.id}/toggle-visibility`" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="mode" value="total">
                    <button type="submit"
                        class="w-full text-left flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-red-400 hover:bg-red-50 transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center shrink-0 mt-0.5 transition-colors">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('cms.features.visibility_modal.total_title') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{!! __('cms.features.visibility_modal.total_desc') !!}</p>
                        </div>
                    </button>
                </form>
            </div>
            {{-- Cancel --}}
            <div class="px-6 pb-5">
                <button type="button" @click="visibilityModal.open = false"
                    class="w-full px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    {{ __('cms.features.visibility_modal.cancel') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ===== ADD SUB MODAL ===== --}}
    <div x-show="addSubModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="addSubModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-[9999]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.sub.add_title') }}</h3>
                <button @click="addSubModal.open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('cms.features.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $feature->id }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.sub.form.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="{{ __('cms.features.sub.form.name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" x-model="addSubModal.type" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="link">{{ __('cms.features.type_link') }}</option>
                        <option value="dropdown">{{ __('cms.features.type_dropdown') }}</option>
                    </select>
                </div>
                <div x-show="addSubModal.type === 'link'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.page_types.label') }}</label>
                    <select name="page_type" x-model="addSubModal.pageType"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="none">{{ __('cms.page_types.none') }}</option>
                        <option value="beranda">{{ __('cms.page_types.beranda') }}</option>
                        <option value="onsite">{{ __('cms.page_types.onsite') }}</option>
                        <option value="real">{{ __('cms.page_types.real') }}</option>
                        <option value="3d">{{ __('cms.page_types.3d') }}</option>
                        <option value="book">{{ __('cms.page_types.book') }}</option>
                        <option value="slideshow">{{ __('cms.page_types.slideshow') }}</option>
                        <option value="profile">{{ __('cms.page_types.profile') }}</option>
                        <option value="publication">{{ __('cms.page_types.publication') }}</option>
                        <option value="layanan_publik">{{ __('cms.page_types.layanan_publik') }}</option>
                        <option value="pengelolaan">{{ __('cms.page_types.pengelolaan') }}</option>
                        <option value="kontak_kami">{{ __('cms.page_types.kontak_kami') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.sub.form.order') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="order" min="0" value="{{ $feature->subfeatures->count() + 1 }}" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div x-show="addSubModal.type === 'link'" class="border-t border-gray-100 pt-4">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" name="is_login_required" id="add_sub_is_login_required"
                                x-model="addSubModal.isLoginRequired"
                                value="1"
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-[#174E93] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('cms.features.form.login_required') }}</span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1 ml-12">{{ __('cms.features.form.login_required_help') }}</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="addSubModal.open = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                        {{ __('cms.features.sub.add_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== EDIT SUB MODAL ===== --}}
    <div x-show="editSubModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="editSubModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-[9999]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.sub.edit_title') }}</h3>
                <button @click="editSubModal.open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form :action="`/cms/features/${editSubModal.id}/sub`" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- Nama Sub Menu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.sub.form.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="editSubModal.name" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- Tipe Menu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" x-model="editSubModal.type" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="link">{{ __('cms.features.type_link') }}</option>
                        <option value="dropdown">{{ __('cms.features.type_dropdown') }}</option>
                    </select>
                </div>

                {{-- Tipe Halaman --}}
                <div x-show="editSubModal.type === 'link'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.page_types.label') }}</label>
                    <select name="page_type" x-model="editSubModal.pageType"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="none">{{ __('cms.page_types.none') }}</option>
                        <option value="beranda">{{ __('cms.page_types.beranda') }}</option>
                        <option value="onsite">{{ __('cms.page_types.onsite') }}</option>
                        <option value="real">{{ __('cms.page_types.real') }}</option>
                        <option value="3d">{{ __('cms.page_types.3d') }}</option>
                        <option value="book">{{ __('cms.page_types.book') }}</option>
                        <option value="slideshow">{{ __('cms.page_types.slideshow') }}</option>
                        <option value="profile">{{ __('cms.page_types.profile') }}</option>
                        <option value="publication">{{ __('cms.page_types.publication') }}</option>
                        <option value="layanan_publik">{{ __('cms.page_types.layanan_publik') }}</option>
                        <option value="pengelolaan">{{ __('cms.page_types.pengelolaan') }}</option>
                        <option value="kontak_kami">{{ __('cms.page_types.kontak_kami') }}</option>
                    </select>
                </div>

                {{-- Urutan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.sub.form.order') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="order" x-model="editSubModal.order" min="0" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div x-show="editSubModal.type === 'link'" class="border-t border-gray-100 pt-4">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" name="is_login_required" id="edit_sub_is_login_required"
                                x-model="editSubModal.isLoginRequired"
                                value="1"
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-[#174E93] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('cms.features.form.login_required') }}</span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1 ml-12">{{ __('cms.features.form.login_required_help') }}</p>
                </div>

                {{-- Pindah ke Menu --}}
                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <svg class="inline w-4 h-4 mr-1 text-blue-500 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        {{ __('cms.features.sub.form.move_title') }}
                    </label>
                    <p class="text-xs text-gray-400 mb-1.5">{!! __('cms.features.sub.form.move_help', ['name' => '<span class="font-medium text-gray-600">' . e($feature->name) . '</span>']) !!}</p>
                    <select name="new_parent_id" x-model="editSubModal.newParentId"
                        class="w-full px-3.5 py-2.5 border border-blue-200 bg-blue-50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">{{ __('cms.features.sub.form.move_keep') }}</option>
                        <option value="top_level">{{ __('cms.features.sub.form.move_top') }}</option>
                        @foreach($dropdownFeatures as $df)
                            <option value="{{ $df->id }}">
                                {{ $df->name }}
                                @if($df->parent_id)
                                    {{ __('cms.features.sub.form.badge_sub') }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="editSubModal.open = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                        {{ __('cms.common.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== DELETE SUB CONFIRMATION MODAL ===== --}}
    <div x-show="deleteSubModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteSubModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.sub.delete.title') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('cms.features.sub.delete.confirm', ['name' => '']) }}
                        <strong x-text="deleteSubModal.name" class="text-gray-700"></strong>
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button @click="deleteSubModal.open = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.cancel') }}
                    </button>
                    <form :action="`/cms/features/${deleteSubModal.id}/sub`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                            {{ __('cms.features.sub.delete.yes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script src="{{ asset('js/cms/features/show.js') }}"></script>
@if($feature->type === 'dropdown')
<script>
$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    $('#tableSubFeatures').DataTable({
        columnDefs: [{ orderable: false, targets: [4] }],
        order: [[0, 'asc']],
        language: {
            info: "{{ __('cms.datatable.info') }}",
            infoEmpty: "{{ __('cms.datatable.info_empty') }}",
            infoFiltered: "{{ __('cms.datatable.info_filtered') }}",
            zeroRecords: "{{ __('cms.datatable.zero_records') }}",
            search: "_INPUT_",
            searchPlaceholder: "{{ __('cms.datatable.search_placeholder') }}",
            paginate: {
                first: "{{ __('cms.datatable.paginate.first') }}",
                last: "{{ __('cms.datatable.paginate.last') }}",
                next: "{{ __('cms.datatable.paginate.next') }}",
                previous: "{{ __('cms.datatable.paginate.previous') }}",
            }
        }
    });
});
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editor1 = new RichTextEditor("#div_editor1", {
            base_url: '/cms_rte',
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean'],
            ],
            editorBodyCssClass: 'rte-content-body',
            file_upload_handler: function(file, callback, errorCallback) {
                var formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("cms.settings.rte.upload") }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Upload gagal.');
                    }
                    return response.json();
                })
                .then(result => {
                    callback(result.url);
                })
                .catch(error => {
                    console.error('Error saat upload:', error);
                    alert('Gagal mengunggah file.');
                    if (errorCallback) errorCallback(error);
                });
            }
        });

        document.getElementById('contentForm').addEventListener('submit', function() {
            var html = editor1.getHTMLCode();
            document.getElementById('content_input').value = html;
        });
    });
</script>
@endif
@endpush
@endsection
