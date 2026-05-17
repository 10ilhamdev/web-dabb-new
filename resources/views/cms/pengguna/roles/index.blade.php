@extends('layouts.app')

@section('breadcrumb_items')
    <a href="{{ route('cms.pengguna.index') }}"
        class="text-gray-500 hover:text-gray-700">{{ __('cms.pengguna.breadcrumb') }}</a>
@endsection
@section('breadcrumb_active', __('cms.roles.title'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        #tableRoles_wrapper .dt-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        #tableRoles_wrapper .dt-top-row .dataTables_length { margin: 0; }

        #tableRoles_wrapper .dt-top-row .dt-top-right {
            display: flex;
            align-items: center;
            gap: .625rem;
            flex-wrap: wrap;
        }

        #tableRoles_wrapper .dt-top-row .dataTables_filter { margin: 0; }

        #tableRoles_wrapper .dataTables_length label,
        #tableRoles_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .8125rem;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }

        #tableRoles_wrapper .dataTables_length select,
        #tableRoles_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem .75rem;
            font-size: .8125rem;
            background: white;
        }

        #tableRoles_wrapper .dataTables_length select {
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .5rem center;
            background-size: 1rem 1rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        #tableRoles_wrapper .dataTables_filter input { min-width: 220px; }

        .table-roles-filter-row {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        @media (min-width: 768px) {
            .table-roles-filter-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .table-roles-filter-row label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: .375rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .table-roles-filter-row select {
            width: 100%;
            padding: .625rem 2.25rem .625rem .875rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .8125rem;
            background-color: white;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .625rem center;
            background-size: 1rem 1rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            transition: all .15s ease;
            color: #374151;
        }

        .table-roles-filter-row option.filter-placeholder {
            color: #9ca3af;
        }

        .table-roles-filter-row select:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        /* Export dropdown button */
        div.dt-buttons {
            display: inline-flex;
            gap: .5rem;
            margin: 0;
        }

        div.dt-buttons > .dt-button {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            padding: .5rem .875rem !important;
            border-radius: .5rem !important;
            font-size: .8125rem !important;
            font-weight: 500 !important;
            box-shadow: none !important;
            margin: 0 !important;
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: .375rem !important;
            white-space: nowrap !important;
        }

        div.dt-buttons > .dt-button:hover {
            background: #f9fafb !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }

        /* Ensure SVG icon stays beside text (not stacked) */
        div.dt-buttons .dt-button svg,
        div.dt-buttons > .dt-button svg {
            display: inline-block !important;
            vertical-align: middle !important;
            width: 1rem !important;
            height: 1rem !important;
            flex-shrink: 0 !important;
        }

        /* Remove dark overlay behind export dropdown */
        div.dt-button-background,
        .dt-button-background {
            display: none !important;
            background: transparent !important;
            opacity: 0 !important;
        }

        /* Collection dropdown menu */
        div.dt-button-collection {
            position: absolute !important;
            z-index: 50 !important;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, .1);
            padding: .375rem;
            min-width: 160px;
            margin-top: 0.25rem !important;
        }

        div.dt-button-collection .dt-button {
            display: block !important;
            width: 100% !important;
            text-align: left !important;
            border: 0 !important;
            border-radius: .375rem !important;
            margin: 0 !important;
            padding: .5rem .75rem !important;
        }

        div.dt-button-collection .dt-button:hover {
            background: #f3f4f6 !important;
        }

        .btn-add-role {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            background: #174E93;
            color: white;
            font-size: .8125rem;
            font-weight: 600;
            padding: .5rem .875rem;
            border-radius: .5rem;
            transition: background-color .15s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-add-role:hover {
            background: #1e40af;
        }

        #tableRoles_wrapper .dt-bottom-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.5rem;
            border-top: 1px solid #f3f4f6;
        }

        #tableRoles_wrapper .dataTables_info {
            font-size: .8125rem;
            color: #6b7280;
        }

        #tableRoles_wrapper .dataTables_paginate .paginate_button {
            padding: .375rem .75rem !important;
            margin: 0 .125rem !important;
            border: 1px solid #e5e7eb !important;
            border-radius: .375rem !important;
            font-size: .8125rem !important;
            background: white !important;
            color: #374151 !important;
        }

        #tableRoles_wrapper .dataTables_paginate .paginate_button.current {
            background: #174E93 !important;
            color: white !important;
            border-color: #174E93 !important;
        }

        #tableRoles_wrapper .dataTables_paginate .paginate_button:hover { background: #f3f4f6 !important; }

        #tableRoles_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #174E93 !important;
            color: white !important;
        }

        /* Child row (column details) styling */
        .role-columns-detail {
            padding: 1rem;
        }

        .role-columns-detail h4 {
            font-size: .875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .75rem;
        }

        .role-columns-detail table {
            width: 100%;
            font-size: .8125rem;
            border-collapse: collapse;
        }

        .role-columns-detail table th {
            text-align: left;
            padding: .5rem .75rem;
            font-size: .6875rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid #e5e7eb;
        }

        .role-columns-detail table td {
            padding: .5rem .75rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .role-columns-detail table tr:last-child td {
            border-bottom: none;
        }

        /* Arrow indicator for expandable row */
        td.details-control {
            cursor: pointer;
            text-align: center;
            width: 40px;
        }

        td.details-control .arrow-icon {
            display: inline-flex;
            transition: transform .2s ease;
        }

        tr.shown td.details-control .arrow-icon {
            transform: rotate(90deg);
        }

        .detail-column-name {
            font-family: ui-monospace, monospace;
            font-size: .75rem;
        }

        .detail-type-badge {
            display: inline-flex;
            align-items: center;
            padding: .125rem .5rem;
            border-radius: .25rem;
            font-size: .6875rem;
            font-weight: 500;
            background-color: #f3f4f6;
            color: #374151;
        }

        .detail-attr-badge {
            display: inline-flex;
            align-items: center;
            padding: .0625rem .375rem;
            border-radius: .1875rem;
            font-size: .625rem;
            font-weight: 500;
            margin-right: .25rem;
            margin-bottom: .125rem;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.roles.title') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('cms.roles.subtitle') }}</p>
            </div>
            <a href="{{ route('cms.pengguna.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                {{ __('cms.pengguna.back') }}
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
                <div>
                    <p class="text-sm text-gray-500">{{ __('cms.roles.stats_total') }}</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 text-[#174E93] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
                <div>
                    <p class="text-sm text-gray-500">{{ __('cms.roles.stats_system') }}</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['system'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
                <div>
                    <p class="text-sm text-gray-500">{{ __('cms.roles.stats_custom') }}</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['custom'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="table-roles-filter-row">
                <div>
                    <label>Filter</label>
                    <select id="filter-type">
                        <option value="" class="filter-placeholder">{{ __('cms.roles.filter_type') }}</option>
                        <option value="system">{{ __('cms.roles.type_system') }}</option>
                        <option value="custom">{{ __('cms.roles.type_custom') }}</option>
                    </select>
                </div>
                <div>
                    <label>Filter</label>
                    <select id="filter-columns">
                        <option value="" class="filter-placeholder">{{ __('cms.roles.filter_columns') }}</option>
                        <option value="0">{{ __('cms.roles.filter_columns_none') }}</option>
                        <option value="1+">{{ __('cms.roles.filter_columns_has') }}</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="tableRoles" class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-10"></th>
                            <th class="w-12">No</th>
                            <th>{{ __('cms.roles.col_name') }}</th>
                            <th>{{ __('cms.roles.col_label') }}</th>
                            <th>{{ __('cms.roles.col_table') }}</th>
                            <th>{{ __('cms.roles.col_columns') }}</th>
                            <th>{{ __('cms.roles.col_type') }}</th>
                            <th>{{ __('cms.roles.col_users') }}</th>
                            <th class="text-center">{{ __('cms.pengguna.col_action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        window.rolesI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            btnAddRole: @json(__('cms.roles.add_button')),
            typeSystem: @json(__('cms.roles.type_system')),
            typeCustom: @json(__('cms.roles.type_custom')),
            columnsCount: @json(__('cms.roles.columns_count')),
            filterColumnsNone: @json(__('cms.roles.filter_columns_none')),
            filterColumnsHas: @json(__('cms.roles.filter_columns_has')),
            urlCreate: @json(route('cms.pengguna.roles.create')),
            csrfToken: @json(csrf_token()),
            urlBaseEdit: @json(route('cms.pengguna.roles.edit', ':id')),
            urlBaseDelete: @json(route('cms.pengguna.roles.destroy', ':id')),
            dtSearchPlaceholder: @json(__('cms.roles.search_placeholder')),
            dtInfo: @json(__('cms.roles.datatable_info')),
            dtInfoEmpty: @json(__('cms.roles.datatable_info_empty')),
            dtInfoFiltered: @json(__('cms.roles.datatable_info_filtered')),
            dtZeroRecords: @json(__('cms.roles.datatable_zero_records')),
            tableStructure: @json(__('cms.roles.table_structure')),
            noColumns: @json(__('cms.roles.no_columns')),
        };
    </script>
    <script src="{{ asset('js/cms/features/pengguna/roles/index.js') }}"></script>
    <script>
        /* Format expandable row columns detail — must be defined before roles/index.js runs */
        function formatRolesColumns(columnsData) {
            if (!columnsData || columnsData.length === 0) {
                return '<div class="role-columns-detail"><p class="text-sm text-gray-400 italic">' + (window.rolesI18n.noColumns || 'No columns') + '</p></div>';
            }
            var html = '<div class="role-columns-detail"><h4>' + (window.rolesI18n.tableStructure || 'Table Structure') + '</h4><div class="overflow-x-auto"><table><thead><tr><th>Column</th><th>Type</th><th>Label</th><th>Attributes</th><th>Foreign Key</th><th>Options</th></tr></thead><tbody>';
            for (var i = 0; i < columnsData.length; i++) {
                var col = columnsData[i];
                var attrs = '';
                if (col.is_primary) attrs += '<span class="detail-attr-badge" style="background:#fef3c7;color:#92400e;">Primary</span>';
                if (col.is_unique) attrs += '<span class="detail-attr-badge" style="background:#dbeafe;color:#1e40af;">Unique</span>';
                if (col.is_nullable) attrs += '<span class="detail-attr-badge" style="background:#d1fae5;color:#065f46;">Nullable</span>';
                if (col.is_unsigned) attrs += '<span class="detail-attr-badge" style="background:#ede9fe;color:#5b21b6;">Unsigned</span>';
                if (col.is_auto_increment) attrs += '<span class="detail-attr-badge" style="background:#fed7aa;color:#9a3412;">Auto Inc</span>';
                if (!attrs) attrs = '<span class="text-gray-400 text-xs">—</span>';
                var foreignHtml = '—';
                if (col.is_foreign) {
                    foreignHtml = '<div class="text-xs"><span class="font-mono text-gray-700">' + (col.references_table || '') + '.' + (col.references_column || '') + '</span>';
                    if (col.on_delete || col.on_update) {
                        foreignHtml += '<div class="text-gray-400">';
                        if (col.on_delete) foreignHtml += '<span class="mr-1">on delete: <span class="font-mono text-gray-600">' + col.on_delete + '</span></span>';
                        if (col.on_update) foreignHtml += '<span>on update: <span class="font-mono text-gray-600">' + col.on_update + '</span></span>';
                        foreignHtml += '</div>';
                    }
                    foreignHtml += '</div>';
                }
                var optionsHtml = '—';
                if (col.options) optionsHtml = '<span class="text-xs font-mono text-gray-600">' + col.options + '</span>';
                var colType = col.column_type + (col.column_length ? '(' + col.column_length + ')' : '');
                html += '<tr><td><span class="detail-column-name">' + col.column_name + '</span></td><td><span class="detail-type-badge">' + colType + '</span></td><td class="text-gray-600">' + (col.column_label || '') + '</td><td><div style="display:flex;flex-wrap:wrap;gap:2px;">' + attrs + '</div></td><td>' + foreignHtml + '</td><td>' + optionsHtml + '</td></tr>';
            }
            html += '</tbody></table></div></div>';
            return html;
        }
        window.formatRolesColumns = formatRolesColumns;
    </script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: @json(session('success')),
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(session('error')),
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        </script>
    @endif
@endpush
