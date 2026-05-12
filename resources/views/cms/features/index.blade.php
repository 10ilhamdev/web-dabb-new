@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
@endsection
@section('breadcrumb_active', __('cms.features.title'))

@section('content')
<div class="space-y-6" x-data="featureManager()">

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.features.title') }}</h1>
    </div>

    <!-- Features Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Card Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.features.card_title') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.features.card_desc') }}</p>
            </div>
            <button @click="openAddModal()"
                class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('cms.features.add_button') }}
            </button>
        </div>

        <!-- Table -->
        <div>
            <table id="tableFeatures" class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide w-12">No</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('cms.features.col_name') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('cms.features.col_type') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">{{ __('cms.features.col_sub_count') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">{{ __('cms.features.col_order') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">{{ __('cms.features.col_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($features as $index => $feature)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $feature->order }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $feature->name }}</td>
                        <td class="px-6 py-4">
                            @if($feature->type === 'dropdown')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ __('cms.features.type_dropdown') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ __('cms.features.type_link') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $feature->subfeatures_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $feature->order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Detail Button -->
                                <a href="{{ url('/cms/features/' . $feature->id . '/') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ __('cms.features.detail') }}
                                </a>
                                <!-- Edit Button -->
                                <button @click="openEditModal({{ $feature->id }}, '{{ addslashes($feature->name) }}', '{{ $feature->type }}', '{{ $feature->path ?? '' }}', {{ $feature->order }}, '{{ $feature->page_type ?? 'none' }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <button @click="openDeleteModal({{ $feature->id }}, '{{ addslashes($feature->name) }}')"
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
                                <p class="text-gray-400 text-sm">{{ __('cms.features.empty') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div x-show="editModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="editModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-[9999]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.edit_title') }}</h3>
                <button @click="editModal.open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Form -->
            <form :action="`/cms/features/${editModal.id}`" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="editModal.name" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" x-model="editModal.type" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="link">{{ __('cms.features.type_link') }}</option>
                        <option value="dropdown">{{ __('cms.features.type_dropdown') }}</option>
                    </select>
                </div>
                <div x-show="editModal.type === 'link'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.page_types.label') }}</label>
                    <select name="page_type" x-model="editModal.pageType"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="none">{{ __('cms.page_types.none') }}</option>
                        <option value="beranda">{{ __('cms.page_types.beranda') }}</option>
                        <option value="onsite">{{ __('cms.page_types.onsite') }}</option>
                        <option value="real">{{ __('cms.page_types.real') }}</option>
                        <option value="3d">{{ __('cms.page_types.3d') }}</option>
                        <option value="book">{{ __('cms.page_types.book') }}</option>
                        <option value="slideshow">{{ __('cms.page_types.slideshow') }}</option>
                        <option value="profile">{{ __('cms.page_types.profile') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.order') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="order" x-model="editModal.order" min="0" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="editModal.open = false"
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

    <!-- ===== ADD MODAL ===== -->
    <div x-show="addModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="addModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-[9999]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.add_title') }}</h3>
                <button @click="addModal.open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Form -->
            <form action="{{ route('cms.features.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="{{ __('cms.features.form.name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" x-model="addModal.type" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="link">{{ __('cms.features.type_link') }}</option>
                        <option value="dropdown">{{ __('cms.features.type_dropdown') }}</option>
                    </select>
                </div>
                <div x-show="addModal.type === 'link'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.page_types.label') }}</label>
                    <select name="page_type" x-model="addModal.pageType"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="none">{{ __('cms.page_types.none') }}</option>
                        <option value="beranda">{{ __('cms.page_types.beranda') }}</option>
                        <option value="onsite">{{ __('cms.page_types.onsite') }}</option>
                        <option value="real">{{ __('cms.page_types.real') }}</option>
                        <option value="3d">{{ __('cms.page_types.3d') }}</option>
                        <option value="book">{{ __('cms.page_types.book') }}</option>
                        <option value="slideshow">{{ __('cms.page_types.slideshow') }}</option>
                        <option value="profile">{{ __('cms.page_types.profile') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.features.form.order') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="order" min="0" value="{{ $features->count() + 1 }}" required
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="addModal.open = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                        {{ __('cms.features.add_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== DELETE CONFIRMATION MODAL ===== -->
    <div x-show="deleteModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <!-- Modal -->
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
                    <h3 class="text-base font-semibold text-gray-800">{{ __('cms.features.delete.title') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('cms.features.delete.confirm', ['name' => '']) }}
                        <strong x-text="deleteModal.name" class="text-gray-700"></strong>
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button @click="deleteModal.open = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.common.cancel') }}
                    </button>
                    <form :action="`/cms/features/${deleteModal.id}`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                            {{ __('cms.features.delete.yes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="{{ asset('js/cms/features/index.js') }}"></script>
<script>
$(document).ready(function() {
    $('#tableFeatures').DataTable({
        columnDefs: [{ orderable: false, targets: [5] }],
        order: [[4, 'asc']],
    });
});
</script>
@endpush
@endsection
