@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="mx-2 text-gray-300">/</span>
    <a href="{{ route('cms.pengguna.index') }}"
        class="text-gray-500 hover:text-gray-700">{{ __('cms.pengguna.breadcrumb') }}</a>
    <span class="mx-2 text-gray-300">/</span>
    <a href="{{ route('cms.pengguna.roles.index') }}"
        class="text-gray-500 hover:text-gray-700">{{ __('cms.roles.title') }}</a>
@endsection
@section('breadcrumb_active', __('cms.roles.edit_title'))

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.roles.edit_title') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $role->label }}</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('cms.pengguna.roles.update', $role) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100">
            @csrf
            @method('PUT')

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.roles.edit_title') }}</h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_name') }} <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                        placeholder="{{ __('cms.roles.form_name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-xs text-amber-600 mt-1">{{ __('cms.roles.form_name_warning') }}</p>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Label --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_label') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label', $role->label) }}" required
                        placeholder="{{ __('cms.roles.form_label_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('label')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type (is_system) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_type') }} <span
                            class="text-red-500">*</span></label>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_system" value="1"
                                {{ old('is_system', $role->is_system ? '1' : '0') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ __('cms.roles.type_system') }}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_system" value="0"
                                {{ old('is_system', $role->is_system ? '1' : '0') == '0' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ __('cms.roles.type_custom') }}</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ __('cms.roles.form_type_help') }}</p>
                    @error('is_system')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Table Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_table_name') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="table_name" value="{{ old('table_name', $role->table_name) }}" required
                        placeholder="{{ __('cms.roles.form_table_name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-400 mt-1">{{ __('cms.roles.form_table_name_help') }}</p>
                    @error('table_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Relation Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_relation_name') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="relation_name" value="{{ old('relation_name', $role->relation_name) }}"
                        required placeholder="{{ __('cms.roles.form_relation_name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-400 mt-1">{{ __('cms.roles.form_relation_name_help') }}</p>
                    @error('relation_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_description') }}</label>
                    <textarea name="description" rows="3" placeholder="{{ __('cms.roles.form_description_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Columns Section --}}
            <div class="px-6 py-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.roles.columns_title') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('cms.roles.columns_desc') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="syncColumnsFromDb()"
                            class="px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200">
                            {{ __('cms.roles.sync_columns') }}
                        </button>
                        <button type="button" onclick="addColumn()"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                            + {{ __('cms.roles.add_column') }}
                        </button>
                    </div>
                </div>

                <div id="columnsContainer" class="space-y-3">
                    @foreach ($role->columns as $index => $col)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 reorder-column">
                            <input type="hidden" name="columns[{{ $index }}][id]" value="{{ $col->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Column
                                    #{{ $index + 1 }}</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="moveUp(this)"
                                        class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors @if ($index === 0) opacity-30 cursor-not-allowed @endif"
                                        title="Geser Atas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="drag-handle p-1 text-gray-500 hover:text-[#174E93] hover:bg-[#174E93]/10 rounded-md transition-colors cursor-grab drag cursor-grabbing"
                                        title="Drag untuk urutkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="moveDown(this)"
                                        class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors"
                                        title="Geser Bawah">
                                        <svg class="w-4 h-4 transform -rotate-180" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="removeColumn(this)"
                                        class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_name') }}</label>
                                    <input type="text" name="columns[{{ $index }}][column_name]"
                                        value="{{ $col->column_name }}" required
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_label') }}</label>
                                    <input type="text" name="columns[{{ $index }}][column_label]"
                                        value="{{ $col->column_label }}" required
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_type') }}</label>
                                    <select name="columns[{{ $index }}][column_type]"
                                        onchange="toggleOptions(this, {{ $index }})" data-init="{{ $index }}" required
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                        @foreach ($columnTypes as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $col->column_type == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_length') }}</label>
                                    <input type="number" name="columns[{{ $index }}][column_length]"
                                        value="{{ $col->column_length }}"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div id="options-{{ $index }}"
                                    class="{{ in_array($col->column_type, ['enum', 'set']) ? '' : 'hidden' }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-medium text-gray-600">{{ __('cms.roles.col_options') }} (comma separated)</label>
                                        <button type="button" onclick="openEnumEditor({{ $index }})"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 rounded px-1.5 py-0.5 border border-blue-200 hover:bg-blue-50 transition-colors">
                                            {{ __('cms.roles.enum_editor_btn') }}
                                        </button>
                                    </div>
                                    <input type="text" id="options-input-{{ $index }}" name="columns[{{ $index }}][options]"
                                        value="{{ $col->options ? implode(',', $col->options) : '' }}"
                                        placeholder="IV,IB,VIP"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_nullable]"
                                            value="1" {{ $col->is_nullable ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_nullable') }}</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_unique]"
                                            value="1" {{ $col->is_unique ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_unique') }}</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_primary]"
                                            value="1" {{ $col->is_primary ? 'checked' : '' }}
                                            onchange="toggleAttributes(this, {{ $index }})"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_primary') }}</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_unsigned]"
                                            value="1" {{ $col->is_unsigned ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_unsigned') }}</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_auto_increment]"
                                            value="1" {{ $col->is_auto_increment ? 'checked' : '' }}
                                            onchange="toggleAttributes(this, {{ $index }})"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_auto_increment') }}</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="columns[{{ $index }}][is_foreign]"
                                            value="1" {{ $col->is_foreign ? 'checked' : '' }}
                                            onchange="toggleForeign(this, {{ $index }})"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.col_foreign') }}</span>
                                    </label>
                                </div>

                                <div id="foreign-{{ $index }}"
                                    class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 {{ $col->is_foreign ? '' : 'hidden' }}">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_references_table') }}</label>
                                        <select name="columns[{{ $index }}][references_table]"
                                            onchange="loadForeignColumns(this, {{ $index }})"
                                            class="tom-fk w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <option value="">— Select Table —</option>
                                            @foreach($dbTables as $t)
                                                <option value="{{ $t['name'] }}" {{ $col->references_table == $t['name'] ? 'selected' : '' }}>{{ $t['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_references_column') }}</label>
                                        <select name="columns[{{ $index }}][references_column]"
                                            data-saved="{{ $col->references_column }}"
                                            class="tom-fk-col w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <option value="">— Select Column —</option>
                                            @if($col->references_table && isset($dbColumnsByTable[$col->references_table]))
                                                @foreach($dbColumnsByTable[$col->references_table] as $c)
                                                    <option value="{{ $c['name'] }}" {{ $col->references_column == $c['name'] ? 'selected' : '' }}>{{ $c['name'] }} <span class="text-gray-400">({{ $c['type'] }})</span></option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_on_delete') }}</label>
                                        <select name="columns[{{ $index }}][on_delete]"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <option value="">Default</option>
                                            <option value="cascade" {{ $col->on_delete === 'cascade' ? 'selected' : '' }}>
                                                CASCADE</option>
                                            <option value="restrict"
                                                {{ $col->on_delete === 'restrict' ? 'selected' : '' }}>RESTRICT</option>
                                            <option value="set null"
                                                {{ $col->on_delete === 'set null' ? 'selected' : '' }}>SET NULL</option>
                                            <option value="no action"
                                                {{ $col->on_delete === 'no action' ? 'selected' : '' }}>NO ACTION</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_on_update') }}</label>
                                        <select name="columns[{{ $index }}][on_update]"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                            <option value="">Default</option>
                                            <option value="cascade" {{ $col->on_update === 'cascade' ? 'selected' : '' }}>
                                                CASCADE</option>
                                            <option value="restrict"
                                                {{ $col->on_update === 'restrict' ? 'selected' : '' }}>RESTRICT</option>
                                            <option value="set null"
                                                {{ $col->on_update === 'set null' ? 'selected' : '' }}>SET NULL</option>
                                            <option value="no action"
                                                {{ $col->on_update === 'no action' ? 'selected' : '' }}>NO ACTION</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Menu Permissions Section --}}
            <div class="px-6 py-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.roles.permissions_title') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('cms.roles.permissions_desc') }}</p>
                    </div>
                </div>

                @php
                    $rolePerms = $role->permissions->pluck('can_access', 'menu_key')->toArray();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($menuPermissions as $key => $menu)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                                <input type="checkbox" class="menu-permission-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    data-menu-key="{{ $key }}"
                                    onchange="toggleChildren(this, '{{ $key }}')"
                                    @if(!isset($menu['children'])) checked @endif>
                                <span class="text-sm font-semibold text-gray-800">{{ $menu['label'] }}</span>
                            </label>
                            @if (isset($menu['children']))
                                <div class="ml-5 space-y-1.5 permissions-children" data-parent="{{ $key }}">
                                    @foreach ($menu['children'] as $childKey => $childLabel)
                                        @php $checked = $rolePerms[$childKey] ?? true; @endphp
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" name="permissions[{{ $childKey }}]" value="0">
                                            <input type="checkbox" name="permissions[{{ $childKey }}]" value="1"
                                                class="child-permission w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                data-parent="{{ $key }}"
                                                {{ $checked ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $childLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                @php $checked = $rolePerms[$key] ?? true; @endphp
                                <div class="ml-5">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="permissions[{{ $key }}]" value="0">
                                        <input type="checkbox" name="permissions[{{ $key }}]" value="1"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            {{ $checked ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ __('cms.roles.permissions_access') }}</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50 rounded-b-xl">
                <a href="{{ route('cms.pengguna.roles.index') }}"
                    class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    {{ __('cms.pengguna.cancel') }}
                </a>
                <button type="submit"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                    {{ __('cms.pengguna.update') }}
                </button>
            </div>
        </form>

        {{-- ENUM/SET Editor Modal (phpMyAdmin style) --}}
        <div id="enumEditorModal" tabindex="-1" class="fixed inset-0 z-[100] hidden" aria-modal="true" role="dialog">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEnumEditor()"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg pointer-events-auto flex flex-col max-h-[85vh]">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">{{ __('cms.roles.enum_editor_title') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('cms.roles.enum_editor_subtitle') }}</p>
                        </div>
                        <button type="button" onclick="closeEnumEditor()"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto p-5">
                        <p class="text-xs text-gray-500 mb-3">{{ __('cms.roles.enum_editor_help') }}</p>
                        <div id="enumValuesList" class="space-y-2">
                            {{-- Dynamic rows will be inserted here by JS --}}
                        </div>
                        <button type="button" onclick="enumAddValue()"
                            class="mt-3 w-full flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('cms.roles.enum_editor_add') }}
                        </button>
                    </div>
                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-100 flex-shrink-0 bg-gray-50/50 rounded-b-xl">
                        <button type="button" onclick="closeEnumEditor()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400">
                            {{ __('cms.pengguna.cancel') }}
                        </button>
                        <button type="button" onclick="saveEnumEditor()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{ __('cms.pengguna.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .bg-gray-50.dragging { opacity: 0.4; }
        input[type="checkbox"]:disabled { opacity: 0.3; cursor: not-allowed; }
        #enumEditorModal { transition: opacity 0.2s ease; }
        #enumEditorModal.hidden { opacity: 0; pointer-events: none; }
        .enum-value-row { display: flex; align-items: center; gap: 6px; }
        .enum-value-row .move-btns { display: flex; flex-direction: column; gap: 1px; flex-shrink: 0; }
        .enum-value-row .move-btns button { padding: 1px 4px; }
    </style>
    <script>
        const columnTypes = @json($columnTypes);
        const unsignedTypes = @json($unsignedTypes);
        const integerTypes = @json($integerTypes);
        const i18n = {
            colColumnName: '{{ __('cms.roles.col_column_name') }}',
            colColumnLabel: '{{ __('cms.roles.col_column_label') }}',
            colColumnType: '{{ __('cms.roles.col_column_type') }}',
            colColumnLength: '{{ __('cms.roles.col_column_length') }}',
            colOptions: '{{ __('cms.roles.col_options') }}',
            colNullable: '{{ __('cms.roles.col_nullable') }}',
            colUnique: '{{ __('cms.roles.col_unique') }}',
            colPrimary: '{{ __('cms.roles.col_primary') }}',
            colUnsigned: '{{ __('cms.roles.col_unsigned') }}',
            colAutoIncrement: '{{ __('cms.roles.col_auto_increment') }}',
            colForeign: '{{ __('cms.roles.col_foreign') }}',
            colReferencesTable: '{{ __('cms.roles.col_references_table') }}',
            colReferencesColumn: '{{ __('cms.roles.col_references_column') }}',
            colOnDelete: '{{ __('cms.roles.col_on_delete') }}',
            colOnUpdate: '{{ __('cms.roles.col_on_update') }}',
            enumEditorBtn: '{{ __('cms.roles.enum_editor_btn') }}',
        };
        let columnIndex = {{ $role->columns->count() }};

        function addColumn() {
            const container = document.getElementById('columnsContainer');
            const index = columnIndex++;

            const div = document.createElement('div');
            div.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200 reorder-column';
            div.draggable = true;
            div.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Column #${index + 1}</span>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="moveUp(this)"
                            class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors opacity-30 cursor-not-allowed"
                            title="Geser Atas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button type="button" class="drag-handle p-1 text-gray-500 hover:text-[#174E93] hover:bg-[#174E93]/10 rounded-md transition-colors cursor-grab drag cursor-grabbing"
                            title="Drag untuk urutkan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8h16M4 16h16"></path>
                            </svg>
                        </button>
                        <button type="button" onclick="moveDown(this)"
                            class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors"
                            title="Geser Bawah">
                            <svg class="w-4 h-4 transform -rotate-180" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button type="button" onclick="removeColumn(this)"
                            class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                            title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colColumnName}</label>
                        <input type="text" name="columns[${index}][column_name]" required placeholder="e.g. nomor_kartu"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colColumnLabel}</label>
                        <input type="text" name="columns[${index}][column_label]" required placeholder="e.g. Nomor Kartu Identitas"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colColumnType}</label>
                        <select name="columns[${index}][column_type]" onchange="toggleOptions(this, ${index})" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            ${Object.entries(columnTypes).map(([key, label]) =>
                                `<option value="${key}">${label}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colColumnLength}</label>
                        <input type="number" name="columns[${index}][column_length]" value=""
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div id="options-${index}" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-gray-600">${i18n.colOptions}</label>
                            <button type="button" onclick="openEnumEditor(${index})"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium focus:outline-none focus:ring-1 focus:ring-blue-500 rounded px-1.5 py-0.5 border border-blue-200 hover:bg-blue-50 transition-colors">
                                ${i18n.enumEditorBtn}
                            </button>
                        </div>
                        <input type="text" id="options-input-${index}" name="columns[${index}][options]" placeholder="IV,IB,VIP"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_nullable]" value="1" checked
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colNullable}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_unique]" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colUnique}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_primary]" value="1"
                                onchange="toggleAttributes(this, ${index})"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colPrimary}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_unsigned]" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colUnsigned}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_auto_increment]" value="1"
                                onchange="toggleAttributes(this, ${index})"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colAutoIncrement}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="columns[${index}][is_foreign]" value="1"
                                onchange="toggleForeign(this, ${index})"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">${i18n.colForeign}</span>
                        </label>
                    </div>

                    <div id="foreign-${index}" class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 hidden">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colReferencesTable}</label>
                            <input type="text" name="columns[${index}][references_table]" placeholder="e.g. users"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colReferencesColumn}</label>
                            <input type="text" name="columns[${index}][references_column]" placeholder="e.g. id"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colOnDelete}</label>
                            <select name="columns[${index}][on_delete]"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                <option value="">Default</option>
                                <option value="cascade">CASCADE</option>
                                <option value="restrict">RESTRICT</option>
                                <option value="set null">SET NULL</option>
                                <option value="no action">NO ACTION</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">${i18n.colOnUpdate}</label>
                            <select name="columns[${index}][on_update]"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                <option value="">Default</option>
                                <option value="cascade">CASCADE</option>
                                <option value="restrict">RESTRICT</option>
                                <option value="set null">SET NULL</option>
                                <option value="no action">NO ACTION</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(div);
            var typeSelect = div.querySelector('select[name="columns[' + index + '][column_type]"]');
            if (typeSelect) toggleAttributes(typeSelect, index);
            reindexColumns(); // Reindex & setup drag
        }

        function removeColumn(btn) {
            btn.closest('.bg-gray-50').remove();
            reindexColumns();
        }

        function toggleOptions(select, index) {
            const optionsDiv = document.getElementById('options-' + index);
            if (!optionsDiv) return;
            optionsDiv.classList.toggle('hidden', select.value !== 'enum' && select.value !== 'set');
            toggleAttributes(select, index);
        }

        /**
         * Toggle all attribute checkboxes and field visibility based on MySQL rules.
         * MySQL Reference: https://dev.mysql.com/doc/refman/8.0/en/create-index.html
         *
         * PRIMARY KEY  : integer types only; implies NOT NULL
         * AUTO_INC    : integer types only; implies NOT NULL and PR
         * NOT NULL    : NOT allowed if PK or AUTO_INCREMENT is checked
         * UNIQUE      : all scalar types EXCEPT BLOB/TEXT (any variant)
         * UNSIGNED    : integer + decimal + float + double + boolean
         * FOREIGN KEY : integer + char + varchar only; implies NOT NULL
         * LENGTH      : varchar + char + decimal only
         */
        function toggleAttributes(el, index) {
            // Derive index from the element's name attribute — always correct, even after reorder.
            // index param is accepted for backward compat but is ignored if a name attr is found.
            const colDiv = el.closest('.bg-gray-50');
            if (!colDiv) return;

            const nameAttr = colDiv.querySelector('[name^="columns["]')?.name;
            const resolvedIndex = nameAttr ? (nameAttr.match(/columns\[(\d+)\]/) || [])[1] : index;
            if (resolvedIndex === undefined) return;

            // Get current column type
            const typeSelect = colDiv.querySelector('select[name^="columns["][name$="[column_type]"]');
            const type = typeSelect?.value || '';
            if (!type) return;

            const isInteger      = integerTypes.includes(type);
            const isDecimal      = type === 'decimal';
            const isUnsignedable = unsignedTypes.includes(type);
            const isUnindexable  = ['text','longtext','mediumtext','tinytext','blob','longblob','mediumblob'].includes(type);
            const canHaveLength  = ['varchar','char'].includes(type) || isDecimal;

            // --- Column Length visibility ---
            const lengthRow = colDiv.querySelector(`input[name="columns[${resolvedIndex}][column_length]"]`)?.closest('div');
            if (lengthRow) {
                lengthRow.style.display = canHaveLength ? '' : 'none';
                if (!canHaveLength) {
                    const input = lengthRow.querySelector('input');
                    if (input) input.value = '';
                }
            }

            // --- All attribute checkboxes ---
            const checkboxesContainer = colDiv.querySelector('.md\\:col-span-3.grid');
            if (!checkboxesContainer) return;

            const cb = {};
            ['is_nullable','is_unique','is_primary','is_unsigned','is_auto_increment','is_foreign'].forEach(function(name) {
                cb[name] = checkboxesContainer.querySelector(`input[name="columns[${resolvedIndex}][${name}]"]`);
            });

            // Determine which control triggered this call
            const isFromPrimaryCB = !!(el.name && el.name.includes('is_primary'));
            const isFromAutoIncCB = !!(el.name && el.name.includes('is_auto_increment'));

            // Resolve intended state after this interaction
            const intendedPK   = isFromPrimaryCB ? el.checked : !!cb.is_primary?.checked;
            const intendedAuto  = isFromAutoIncCB ? el.checked : !!cb.is_auto_increment?.checked;
            const forcedNotNull = intendedPK || intendedAuto;

            // PRIMARY KEY: integer types only
            if (cb.is_primary) {
                cb.is_primary.disabled = !isInteger;
                if (!isInteger) cb.is_primary.checked = false;
            }

            // AUTO_INCREMENT: integer types only
            if (cb.is_auto_increment) {
                cb.is_auto_increment.disabled = !isInteger;
                if (!isInteger) cb.is_auto_increment.checked = false;
            }

            // UNIQUE: all scalar types except BLOB/TEXT
            if (cb.is_unique) {
                cb.is_unique.disabled = isUnindexable;
                if (isUnindexable) cb.is_unique.checked = false;
            }

            // UNSIGNED: integer + decimal + float + double + boolean
            if (cb.is_unsigned) {
                cb.is_unsigned.disabled = !isUnsignedable;
                if (!isUnsignedable) cb.is_unsigned.checked = false;
            }

            // FOREIGN KEY: integer + varchar + char
            if (cb.is_foreign) {
                cb.is_foreign.disabled = !isInteger && type !== 'varchar' && type !== 'char';
                if (cb.is_foreign.disabled) {
                    cb.is_foreign.checked = false;
                    const foreignDiv = document.getElementById('foreign-' + resolvedIndex);
                    if (foreignDiv) foreignDiv.classList.add('hidden');
                }
            }

            // NOT NULL: disabled when PK or AUTO_INCREMENT is active
            if (cb.is_nullable) {
                cb.is_nullable.disabled = forcedNotNull;
                if (forcedNotNull) cb.is_nullable.checked = false;
            }

            // Cascade: AUTO_INC → PK
            if (isFromAutoIncCB && el.checked && cb.is_primary) {
                cb.is_primary.checked = true;
                cb.is_primary.disabled = !isInteger;
            }
        }

        function toggleForeign(checkbox, index) {
            const foreignDiv = document.getElementById('foreign-' + index);
            if (!foreignDiv) return;
            const shouldShow = checkbox.checked;
            foreignDiv.classList.toggle('hidden', !shouldShow);
            // When panel opens and a table is already selected, reload its columns
            if (shouldShow) {
                const tableSelect = foreignDiv.querySelector('select[name="columns[' + index + '][references_table]"]');
                if (tableSelect && tableSelect.value) {
                    loadForeignColumns(tableSelect, index);
                }
            }
        }

        /**
         * Cascade: when a FK table is selected, load its columns into the column dropdown.
         */
        function loadForeignColumns(tableSelect, index) {
            const table = tableSelect.value;
            const colSelect = document.querySelector(`select[name="columns[${index}][references_column]"]`);
            if (!colSelect) return;

            colSelect.innerHTML = '<option value="">Loading…</option>';
            if (!table) {
                colSelect.innerHTML = '<option value="">— Select Column —</option>';
                return;
            }

            fetch(`{{ route('cms.pengguna.roles.tables.columns', ['table' => '__TABLE__']) }}`.replace('__TABLE__', encodeURIComponent(table)), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(cols => {
                const savedValue = colSelect.dataset.saved || '';
                colSelect.innerHTML = '<option value="">— Select Column —</option>';
                cols.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.textContent = c.name + ' (' + c.type + ')';
                    if (c.name === savedValue) opt.selected = true;
                    colSelect.appendChild(opt);
                });
                // If saved value not in list, select it anyway so it doesn't disappear
                if (savedValue && !cols.find(c => c.name === savedValue)) {
                    const placeholder = document.createElement('option');
                    placeholder.value = savedValue;
                    placeholder.textContent = savedValue + ' (unknown type)';
                    placeholder.selected = true;
                    colSelect.appendChild(placeholder);
                }
            })
            .catch(() => {
                colSelect.innerHTML = '<option value="">Error loading columns</option>';
            });
        }

        function syncColumnsFromDb() {
            Swal.fire({
                icon: 'question',
                title: '{{ __('cms.roles.sync_columns') }}',
                text: '{{ __('cms.roles.sync_confirm') }}',
                showCancelButton: true,
                confirmButtonText: '{{ __('cms.roles.sync_columns') }}',
                cancelButtonText: '{{ __('cms.pengguna.cancel') }}',
                confirmButtonColor: '#174E93',
                cancelButtonColor: '#9CA3AF'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const btn = document.querySelector('button[onclick="syncColumnsFromDb()"]');
                const originalText = btn.innerText;
                btn.innerText = 'Syncing...';
                btn.disabled = true;

                fetch('{{ route('cms.pengguna.roles.sync', $role) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message || '{{ __('cms.roles.columns_synced') }}',
                                confirmButtonColor: '#174E93'
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Sync failed.',
                                confirmButtonColor: '#174E93'
                            });
                            btn.innerText = originalText;
                            btn.disabled = false;
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.message || 'Sync failed.',
                            confirmButtonColor: '#174E93'
                        });
                        btn.innerText = originalText;
                        btn.disabled = false;
                    });
            });
        }

        function reindexColumns() {
            const container = document.getElementById('columnsContainer');
            const columnDivs = container.querySelectorAll('.bg-gray-50');
            const total = columnDivs.length;
            columnDivs.forEach((div, i) => {
                const headerSpan = div.querySelector('span[class*="text-gray-500"]');
                if (headerSpan) headerSpan.textContent = `Column #${i + 1}`;
                const inputs = div.querySelectorAll('[name^="columns["]');
                inputs.forEach(input => input.name = input.name.replace(/columns\[\d+\]/, `columns[${i}]`));

                const btnGroup = div.querySelector('.flex.items-center.gap-1');
                if (btnGroup) {
                    const btns = btnGroup.querySelectorAll('button');
                    if (btns.length >= 4) {
                        btns[0].classList.toggle('opacity-30', i === 0);
                        btns[0].classList.toggle('cursor-not-allowed', i === 0);
                        btns[2].classList.toggle('opacity-30', i === total - 1);
                        btns[2].classList.toggle('cursor-not-allowed', i === total - 1);
                    }
                }
            });
            columnIndex = total;
        }

        let draggedItem = null;
        let ghostEl = null;
        let ghostOffsetX = 0;
        let ghostOffsetY = 0;
        let isDragging = false;
        let dragStartY = 0;
        const scrollEl = document.getElementById('mainContent');

        const container = document.getElementById('columnsContainer');

        container.addEventListener('mousedown', e => {
            const handle = e.target.closest('.drag-handle');
            if (!handle) return;
            e.preventDefault();
            draggedItem = handle.closest('.bg-gray-50');
            if (!draggedItem) return;
            isDragging = false;
            dragStartY = e.clientY;
            document.body.style.cursor = 'grabbing';
            document.body.style.userSelect = 'none';
        });

        document.addEventListener('mousemove', e => {
            if (!draggedItem) return;

            if (e.clientY < 150) {
                scrollEl.scrollBy(0, -20);
            } else if (e.clientY > window.innerHeight - 150) {
                scrollEl.scrollBy(0, 20);
            }

            if (!isDragging) {
                if (Math.abs(e.clientY - dragStartY) < 5) return;
                isDragging = true;
                draggedItem.classList.add('dragging');

                const rect = draggedItem.getBoundingClientRect();
                ghostOffsetX = e.clientX - rect.left;
                ghostOffsetY = e.clientY - rect.top;
                ghostEl = draggedItem.cloneNode(true);
                ghostEl.style.position = 'fixed';
                ghostEl.style.pointerEvents = 'none';
                ghostEl.style.opacity = '0.85';
                ghostEl.style.zIndex = '9999';
                ghostEl.style.width = rect.width + 'px';
                ghostEl.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
                ghostEl.style.borderRadius = '8px';
                ghostEl.style.transform = 'scale(1.02)';
                ghostEl.style.left = (e.clientX - ghostOffsetX) + 'px';
                ghostEl.style.top = (e.clientY - ghostOffsetY) + 'px';
                document.body.appendChild(ghostEl);
            }

            if (ghostEl) {
                ghostEl.style.left = (e.clientX - ghostOffsetX) + 'px';
                ghostEl.style.top = (e.clientY - ghostOffsetY) + 'px';
            }

            const afterElement = getDragAfterElement(container, e.clientY);
            if (afterElement == null) {
                container.appendChild(draggedItem);
            } else {
                container.insertBefore(draggedItem, afterElement);
            }
        });

        document.addEventListener('mouseup', e => {
            if (!draggedItem) return;
            draggedItem.classList.remove('dragging');
            if (ghostEl) { ghostEl.remove(); ghostEl = null; }
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            reindexColumns();
            // Refresh toggle state for ALL columns after reorder — critical!
            document.querySelectorAll('#columnsContainer select[name$="[column_type]"]').forEach(function(sel) {
                toggleAttributes(sel, 0);
            });
            draggedItem = null;
            isDragging = false;
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.bg-gray-50:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) return {
                    offset,
                    element: child
                };
                return closest;
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }

        function moveUp(btn) {
            const div = btn.closest('.bg-gray-50');
            const prev = div.previousElementSibling;
            if (prev && prev.matches('.bg-gray-50')) {
                div.parentNode.insertBefore(div, prev);
                reindexColumns();
                // Refresh toggle state for all columns after reorder
                document.querySelectorAll('#columnsContainer select[name$="[column_type]"]').forEach(function(sel) {
                    toggleAttributes(sel, 0);
                });
            }
        }

        function moveDown(btn) {
            const div = btn.closest('.bg-gray-50');
            const next = div.nextElementSibling;
            if (next && next.matches('.bg-gray-50')) {
                div.parentNode.insertBefore(next, div);
                reindexColumns();
                // Refresh toggle state for all columns after reorder
                document.querySelectorAll('#columnsContainer select[name$="[column_type]"]').forEach(function(sel) {
                    toggleAttributes(sel, 0);
                });
            }
        }

        // Init
        reindexColumns();

        // Init column_length visibility for all columns
        document.querySelectorAll('#columnsContainer select[name$="[column_type]"]').forEach(function(sel) {
            toggleAttributes(sel, 0);
        });

        // Permission parent-child toggle
        function toggleChildren(checkbox, parentKey) {
            const children = document.querySelectorAll(`.child-permission[data-parent="${parentKey}"]`);
            children.forEach(child => {
                child.checked = checkbox.checked;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.child-permission').forEach(child => {
                child.addEventListener('change', function() {
                    const parentKey = this.dataset.parent;
                    const parentCheckbox = document.querySelector(`.menu-permission-checkbox[data-menu-key="${parentKey}"]`);
                    const siblings = document.querySelectorAll(`.child-permission[data-parent="${parentKey}"]`);
                    const allChecked = Array.from(siblings).every(s => s.checked);
                    const anyChecked = Array.from(siblings).some(s => s.checked);
                    if (parentCheckbox) {
                        parentCheckbox.checked = allChecked;
                        parentCheckbox.indeterminate = anyChecked && !allChecked;
                    }
                });
            });

            document.querySelectorAll('.menu-permission-checkbox').forEach(checkbox => {
                const parentKey = checkbox.dataset.menuKey;
                const children = document.querySelectorAll(`.child-permission[data-parent="${parentKey}"]`);
                if (children.length > 0) {
                    const allChecked = Array.from(children).every(c => c.checked);
                    const anyChecked = Array.from(children).some(c => c.checked);
                    checkbox.checked = allChecked;
                    checkbox.indeterminate = anyChecked && !allChecked;
                }
            });

            // Init column_length visibility for all pre-filled columns on page load
            document.querySelectorAll('#columnsContainer select[name$="[column_type]"]').forEach(function(sel) {
                const match = sel.name.match(/columns\[(\d+)\]/);
                if (match) toggleAttributes(sel, parseInt(match[1]));
            });
        });

        // =====================================================
        // ENUM/SET Editor Modal (phpMyAdmin style)
        // =====================================================
        let _enumEditorIndex = null;

        function openEnumEditor(index) {
            _enumEditorIndex = index;
            const modal = document.getElementById('enumEditorModal');
            const input = document.getElementById('options-input-' + index);
            const values = input && input.value
                ? input.value.split(',').map(v => v.trim()).filter(v => v !== '')
                : [];

            const list = document.getElementById('enumValuesList');
            list.innerHTML = '';

            if (values.length === 0) {
                // Add one empty row as default
                values.push('');
            }

            values.forEach((val, i) => {
                addEnumValueRow(list, val, i);
            });

            // Remove the "add" button temporarily to avoid duplication
            // (it's already in the modal HTML, not here)

            modal.classList.remove('hidden');
            modal.focus();

            // Focus first input
            const firstInput = list.querySelector('input');
            if (firstInput) firstInput.focus();
        }

        function addEnumValueRow(container, value, index) {
            const row = document.createElement('div');
            row.className = 'enum-value-row';
            row.dataset.index = index;
            row.innerHTML = `
                <span class="text-xs text-gray-400 font-mono w-6 text-center flex-shrink-0 select-none">${index + 1}.</span>
                <input type="text"
                    value="${_escapeHtml(value)}"
                    placeholder="{{ __('cms.roles.enum_editor_value_placeholder') }}"
                    class="flex-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();enumAddValue();}">
                <div class="move-btns">
                    <button type="button" onclick="enumMoveValue(this, -1)" title="{{ __('cms.roles.enum_editor_move_up') }}"
                        class="text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors focus:outline-none focus:ring-1 focus:ring-blue-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                    <button type="button" onclick="enumMoveValue(this, 1)" title="{{ __('cms.roles.enum_editor_move_down') }}"
                        class="text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors focus:outline-none focus:ring-1 focus:ring-blue-400">
                        <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                </div>
                <button type="button" onclick="enumRemoveValue(this)" title="{{ __('cms.roles.enum_editor_remove') }}"
                    class="text-gray-400 hover:text-red-600 hover:bg-red-50 rounded p-1 transition-colors flex-shrink-0 focus:outline-none focus:ring-1 focus:ring-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(row);
        }

        function enumAddValue() {
            const list = document.getElementById('enumValuesList');
            addEnumValueRow(list, '', list.children.length);
            // Focus the newly added input
            const inputs = list.querySelectorAll('input');
            inputs[inputs.length - 1].focus();
        }

        function enumRemoveValue(btn) {
            const row = btn.closest('.enum-value-row');
            const list = row.parentElement;
            if (list.children.length <= 1) return; // Keep at least one row
            row.remove();
            reindexEnumRows();
        }

        function enumMoveValue(btn, direction) {
            const row = btn.closest('.enum-value-row');
            const list = row.parentElement;

            if (direction === -1) {
                // Move UP: swap with previous sibling
                const prev = row.previousElementSibling;
                if (!prev) return;
                list.insertBefore(row, prev);
            } else {
                // Move DOWN: swap with next sibling
                const next = row.nextElementSibling;
                if (!next) return;
                list.insertBefore(next, row);
            }
            reindexEnumRows();
        }

        function reindexEnumRows() {
            const list = document.getElementById('enumValuesList');
            [...list.children].forEach((row, i) => {
                row.dataset.index = i;
                const span = row.querySelector('span');
                if (span) span.textContent = (i + 1) + '.';
            });
        }

        function saveEnumEditor() {
            if (_enumEditorIndex === null) return;
            const list = document.getElementById('enumValuesList');
            const inputs = list.querySelectorAll('input');
            const values = [...inputs]
                .map(inp => inp.value.trim())
                .filter(v => v !== '');

            const inputField = document.getElementById('options-input-' + _enumEditorIndex);
            if (inputField) inputField.value = values.join(',');

            closeEnumEditor();
        }

        function closeEnumEditor() {
            const modal = document.getElementById('enumEditorModal');
            modal.classList.add('hidden');
            _enumEditorIndex = null;
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeEnumEditor();
        });

        function _escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
@endpush
