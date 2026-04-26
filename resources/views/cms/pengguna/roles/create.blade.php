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
                            class="text-sm border border-gray-200 rounded-lg px-5 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

            {{-- Menu Permissions Section --}}
            <div class="px-6 py-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.roles.permissions_title') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('cms.roles.permissions_desc') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($menuPermissions as $key => $menu)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                                <input type="checkbox" class="menu-permission-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    data-menu-key="{{ $key }}"
                                    onchange="toggleChildren(this, '{{ $key }}')"
                                    checked>
                                <span class="text-sm font-semibold text-gray-800">{{ $menu['label'] }}</span>
                            </label>
                            @if (isset($menu['children']))
                                <div class="ml-5 space-y-1.5 permissions-children" data-parent="{{ $key }}">
                                    @foreach ($menu['children'] as $childKey => $childLabel)
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" name="permissions[{{ $childKey }}]" value="0">
                                            <input type="checkbox" name="permissions[{{ $childKey }}]" value="1"
                                                class="child-permission w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                data-parent="{{ $key }}"
                                                checked>
                                            <span class="text-sm text-gray-700">{{ $childLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="ml-5">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="permissions[{{ $key }}]" value="0">
                                        <input type="checkbox" name="permissions[{{ $key }}]" value="1"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            checked>
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
                    {{ __('cms.pengguna.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <style>
        .bg-gray-50.dragging { opacity: 0.4; }
        input[type="checkbox"]:disabled { opacity: 0.3; cursor: not-allowed; }
    </style>
    <script>
        const columnTypes = @json($columnTypes);
        const unsignedTypes = @json($unsignedTypes);
        const integerTypes = @json($integerTypes);

        const templates = {!! $templatesJson !!};

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
            <div class="flex items-center gap-1">
                <button type="button" onclick="moveUp(this)" class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors ${index === 0 ? 'opacity-30 cursor-not-allowed' : ''}" title="Move Up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <button type="button" class="drag-handle p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors cursor-grab active:cursor-grabbing" draggable="false" title="Drag to reorder">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                </button>
                <button type="button" onclick="moveDown(this)" class="p-1 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors" title="Move Down">
                    <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <button type="button" onclick="removeColumn(this)" class="text-red-500 hover:text-red-700 text-sm p-1 rounded-md transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
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
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_primary') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_unsigned]" value="1" ${colData.unsigned ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_unsigned') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_auto_increment]" value="1" ${colData.auto_increment ? 'checked' : ''}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_auto_increment') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="columns[${index}][is_foreign]" value="1" ${colData.foreign ? 'checked' : ''}
                        onchange="toggleForeign(this, ${index})"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ __('cms.roles.col_foreign') }}</span>
                </label>
            </div>

            <div id="foreign-${index}" class="md:col-span-3 grid grid-cols-1 md:grid-cols-4 gap-3 ${colData.foreign ? '' : 'hidden'}">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_references_table') }}</label>
                    <input type="text" name="columns[${index}][references_table]" value="${colData.references_table || ''}"
                        placeholder="e.g. users"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_references_column') }}</label>
                    <input type="text" name="columns[${index}][references_column]" value="${colData.references_column || ''}"
                        placeholder="e.g. id"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_on_delete') }}</label>
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
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.roles.col_on_update') }}</label>
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
            var typeSelect = div.querySelector('select[name="columns[' + index + '][column_type]"]');
            if (typeSelect) toggleAttributes(typeSelect, index);
            reindexColumns(); // Reindex after add
        }

        function removeColumn(btn) {
            const div = btn.closest('.bg-gray-50');
            div.remove();
            reindexColumns();
        }

        function toggleOptions(select, index) {
            const optionsDiv = document.getElementById('options-' + index);
            if (!optionsDiv) return;
            optionsDiv.classList.toggle('hidden', select.value !== 'enum' && select.value !== 'set');
            toggleAttributes(select, index);
        }

        function toggleAttributes(select, index) {
            const type = select.value;
            const div = select.closest('.bg-gray-50');
            if (!div) return;
            const unsignedCheckbox = div.querySelector('input[name="columns[' + index + '][is_unsigned]"]');
            const autoIncCheckbox = div.querySelector('input[name="columns[' + index + '][is_auto_increment]"]');
            if (unsignedCheckbox) {
                const supported = unsignedTypes.includes(type);
                unsignedCheckbox.disabled = !supported;
                if (!supported) { unsignedCheckbox.checked = false; }
            }
            if (autoIncCheckbox) {
                const supported = integerTypes.includes(type);
                autoIncCheckbox.disabled = !supported;
                if (!supported) { autoIncCheckbox.checked = false; }
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

        // Reorder functions
        function reindexColumns() {
            const container = document.getElementById('columnsContainer');
            if (!container) return;
            const columnDivs = container.querySelectorAll('.bg-gray-50');
            const total = columnDivs.length;

            columnDivs.forEach((div, i) => {
                const headerSpan = div.querySelector('span[class*="text-gray-500"]');
                if (headerSpan) headerSpan.textContent = `Column #${i + 1}`;

                const inputs = div.querySelectorAll('[name^="columns["]');
                inputs.forEach(input => {
                    input.name = input.name.replace(/columns\[\d+\]/, `columns[${i}]`);
                });

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

        function moveUp(btn) {
            const div = btn.closest('.bg-gray-50');
            const prev = div.previousElementSibling;
            if (prev && prev.matches('.bg-gray-50')) {
                div.parentNode.insertBefore(div, prev);
                reindexColumns();
            }
        }

        function moveDown(btn) {
            const div = btn.closest('.bg-gray-50');
            const next = div.nextElementSibling;
            if (next && next.matches('.bg-gray-50')) {
                div.parentNode.insertBefore(next, div);
                reindexColumns();
            }
        }

        // Drag & Drop with mouse events (supports wheel scroll during drag)
        document.addEventListener('DOMContentLoaded', () => {
            reindexColumns();
        });

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
            draggedItem = null;
            isDragging = false;
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.bg-gray-50:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                }
                return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Permission parent-child toggle
        function toggleChildren(checkbox, parentKey) {
            const children = document.querySelectorAll(`.child-permission[data-parent="${parentKey}"]`);
            children.forEach(child => {
                child.checked = checkbox.checked;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // When any child changes, update parent state
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

            // Initialize parent state
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
        });
    </script>
@endpush
