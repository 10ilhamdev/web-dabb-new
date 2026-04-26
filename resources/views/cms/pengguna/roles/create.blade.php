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
@section('breadcrumb_active', __('cms.roles.create_title'))

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.roles.create_title') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('cms.roles.create_subtitle') }}</p>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('cms.pengguna.roles.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100">
            @csrf

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.roles.create_title') }}</h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_name') }} <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="{{ __('cms.roles.form_name_placeholder') }}"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-xs text-amber-600 mt-1">{{ __('cms.roles.form_name_warning') }}</p>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Label --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.roles.form_label') }} <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label') }}" required
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
                                {{ old('is_system') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ __('cms.roles.type_system') }}</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_system" value="0"
                                {{ old('is_system', '0') == '0' ? 'checked' : '' }}
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
                    <input type="text" name="table_name" value="{{ old('table_name') }}" required
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
                    <input type="text" name="relation_name" value="{{ old('relation_name') }}" required
                        placeholder="{{ __('cms.roles.form_relation_name_placeholder') }}"
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
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('description') }}</textarea>
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
                    <div class="flex gap-2">
                        <select id="templateSelect"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('cms.roles.select_template') }}</option>
                            <option value="admin">Admin / Pegawai</option>
                            <option value="umum">Umum / Pelajar</option>
                            <option value="instansi">Instansi</option>
                            <option value="empty">{{ __('cms.roles.empty_template') }}</option>
                        </select>
                        <button type="button" onclick="addColumn()"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                            + {{ __('cms.roles.add_column') }}
                        </button>
                    </div>
                </div>

                <div id="columnsContainer" class="space-y-3">
                    {{-- Dynamic columns will be added here --}}
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
                    {{ __('cms.pengguna.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const columnTypes = @json($columnTypes);

        const templates = @json(
            $existingRoles->keyBy('name')->map(function ($role) {
                return $role->columns->map(function ($column) {
                        return [
                            'name' => $column->column_name,
                            'type' => $column->column_type,
                            'length' => $column->column_length,
                            'label' => $column->column_label,
                            'nullable' => (bool) $column->is_nullable,
                            'unique' => (bool) $column->is_unique,
                            'options' => is_array($column->options) ? implode(',', $column->options) : '',
                            'primary' => (bool) $column->is_primary,
                            'foreign' => (bool) $column->is_foreign,
                            'references_table' => $column->references_table,
                            'references_column' => $column->references_column,
                            'on_delete' => $column->on_delete,
                            'on_update' => $column->on_update,
                            'unsigned' => (bool) $column->is_unsigned,
                            'auto_increment' => (bool) $column->is_auto_increment,
                        ];
                    })->values();
            }));

        let columnIndex = 0;

        function addColumn(data = null) {
            const container = document.getElementById('columnsContainer');
            const index = columnIndex++;

            const colData = data || {
                name: '',
                type: 'string',
                length: 255,
                label: '',
                nullable: true,
                unique: false,
                options: '',
                primary: false,
                foreign: false,
                references_table: '',
                references_column: '',
                on_delete: '',
                on_update: '',
                unsigned: false,
                auto_increment: false,
            };

            const div = document.createElement('div');
            div.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
            div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('cms.roles.column') }} #${index + 1}</span>
            <button type="button" onclick="removeColumn(this)" class="text-red-500 hover:text-red-700 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_name') }}</label>
                <input type="text" name="columns[${index}][column_name]" value="${colData.name}" required placeholder="e.g. nomor_kartu"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_label') }}</label>
                <input type="text" name="columns[${index}][column_label]" value="${colData.label}" required placeholder="e.g. Nomor Kartu Identitas"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_type') }}</label>
                <select name="columns[${index}][column_type]" onchange="toggleOptions(this, ${index})" required
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    ${Object.entries(columnTypes).map(([key, label]) =>
                        `<option value="${key}" ${colData.type === key ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_column_length') }}</label>
                <input type="number" name="columns[${index}][column_length]" value="${colData.length || ''}" placeholder="e.g. 255"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div id="options-${index}" class="${(colData.type === 'enum' || colData.type === 'set') ? '' : 'hidden'}">
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_options') }} (comma separated)</label>
                <input type="text" name="columns[${index}][options]" value="${colData.options || ''}" placeholder="Option 1,Option 2,Option 3"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_nullable]" value="1" ${colData.nullable ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_nullable') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_unique]" value="1" ${colData.unique ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_unique') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_primary]" value="1" ${colData.primary ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Primary</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_unsigned]" value="1" ${colData.unsigned ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Unsigned</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_auto_increment]" value="1" ${colData.auto_increment ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Auto Increment</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_foreign]" value="1" ${colData.foreign ? 'checked' : ''}
                        onchange="toggleForeign(this, ${index})"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Foreign Key</span>
                </label>
            </div>

            <div id="foreign-${index}" class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 ${colData.foreign ? '' : 'hidden'}">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">References Table</label>
                    <input type="text" name="columns[${index}][references_table]" value="${colData.references_table || ''}"
                        placeholder="e.g. users"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">References Column</label>
                    <input type="text" name="columns[${index}][references_column]" value="${colData.references_column || ''}"
                        placeholder="e.g. id"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">On Delete</label>
                    <select name="columns[${index}][on_delete]"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Default</option>
                        <option value="cascade" ${colData.on_delete === 'cascade' ? 'selected' : ''}>CASCADE</option>
                        <option value="restrict" ${colData.on_delete === 'restrict' ? 'selected' : ''}>RESTRICT</option>
                        <option value="set null" ${colData.on_delete === 'set null' ? 'selected' : ''}>SET NULL</option>
                        <option value="no action" ${colData.on_delete === 'no action' ? 'selected' : ''}>NO ACTION</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">On Update</label>
                    <select name="columns[${index}][on_update]"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Default</option>
                        <option value="cascade" ${colData.on_update === 'cascade' ? 'selected' : ''}>CASCADE</option>
                        <option value="restrict" ${colData.on_update === 'restrict' ? 'selected' : ''}>RESTRICT</option>
                        <option value="set null" ${colData.on_update === 'set null' ? 'selected' : ''}>SET NULL</option>
                        <option value="no action" ${colData.on_update === 'no action' ? 'selected' : ''}>NO ACTION</option>
                    </select>
                </div>
            </div>
        </div>
    `;

            container.appendChild(div);
        }

        function removeColumn(btn) {
            btn.closest('.bg-gray-50').remove();
        }

        function toggleOptions(select, index) {
            const optionsDiv = document.getElementById('options-' + index);
            if (!optionsDiv) return;
            if (select.value === 'enum' || select.value === 'set') {
                optionsDiv.classList.remove('hidden');
            } else {
                optionsDiv.classList.add('hidden');
            }
        }

        function toggleForeign(checkbox, index) {
            const foreignDiv = document.getElementById('foreign-' + index);
            if (!foreignDiv) return;
            foreignDiv.classList.toggle('hidden', !checkbox.checked);
        }

        document.getElementById('templateSelect').addEventListener('change', function() {
            const template = this.value;
            if (!template) return;

            Swal.fire({
                icon: 'question',
                title: '{{ __('cms.roles.select_template') }}',
                text: '{{ __('cms.roles.sync_confirm') }}',
                showCancelButton: true,
                confirmButtonText: '{{ __('cms.roles.sync_columns') }}',
                cancelButtonText: '{{ __('cms.pengguna.cancel') }}',
                confirmButtonColor: '#174E93',
                cancelButtonColor: '#9CA3AF'
            }).then((result) => {
                if (!result.isConfirmed) {
                    this.value = '';
                    return;
                }

                const container = document.getElementById('columnsContainer');
                container.innerHTML = '';
                columnIndex = 0;

                const data = templates[template] || [];
                data.forEach(col => addColumn(col));
            });
        });
    </script>
@endpush
