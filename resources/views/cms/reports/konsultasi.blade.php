@extends('layouts.app')

@section('title', __('dashboard.sidebar.reports_konsultasi'))

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-gray-400">{{ __('dashboard.sidebar.reports') }}</span> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.sidebar.reports_konsultasi') }}</span>
    </div>
@endsection

@section('content')
    <div class="mb-8" x-data="{ 
        tf: '{{ $tf }}', 
        showCustom: {{ $tf === 'custom' ? 'true' : 'false' }}, 
        selectedCon: null, 
        formFields: {{ json_encode($formFields) }},
        showModal: false,
        showReplyModal: false,
        selectedConForReply: null,
        replyMessage: '',
        isReplying: false,
        isDeleting: false,
        sendReply() {
            if (!this.replyMessage.trim()) return;
            this.isReplying = true;
            fetch(`/cms/reports/konsultasi/${this.selectedConForReply.id}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                },
                body: JSON.stringify({ message: this.replyMessage })
            })
            .then(res => res.json())
            .then(data => {
                this.isReplying = false;
                if (data.success) {
                    this.showReplyModal = false;
                    Swal.fire({ title: '{{ __('cms.reports.swal_success') }}', text: data.message, icon: 'success', confirmButtonColor: '#174E93' }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ title: '{{ __('cms.reports.swal_fail') }}', text: data.message, icon: 'error', confirmButtonColor: '#174E93' });
                }
            })
            .catch(err => {
                this.isReplying = false;
                Swal.fire({ title: '{{ __('cms.reports.swal_error') }}', text: '{{ __('cms.reports.swal_error_sys') }}', icon: 'error', confirmButtonColor: '#174E93' });
            });
        },
        deleteKonsultasi(id) {
            Swal.fire({
                title: '{{ __('cms.reports.swal_del_title') }}',
                text: '{{ __('cms.reports.swal_del_text') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('cms.reports.swal_del_confirm') }}',
                cancelButtonText: '{{ __('cms.reports.swal_cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isDeleting = true;
                    fetch(`/cms/reports/konsultasi/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isDeleting = false;
                        if (data.success) {
                            Swal.fire({ title: '{{ __('cms.reports.swal_deleted') }}', text: data.message, icon: 'success', confirmButtonColor: '#174E93' }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ title: '{{ __('cms.reports.swal_fail') }}', text: data.message, icon: 'error', confirmButtonColor: '#174E93' });
                        }
                    })
                    .catch(err => {
                        this.isDeleting = false;
                        Swal.fire({ title: '{{ __('cms.reports.swal_error') }}', text: '{{ __('cms.reports.swal_error_sys') }}', icon: 'error', confirmButtonColor: '#174E93' });
                    });
                }
            });
        }
    }">
        <!-- Title & Filter Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-[#1E293B] mb-1">{{ __('dashboard.sidebar.reports_konsultasi') }}</h1>
                <p class="text-gray-500 text-sm">{{ __('cms.reports.konsultasi_subtitle') }}</p>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <!-- Preset Buttons -->
                <div class="inline-flex bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 text-xs font-medium text-gray-600 overflow-x-auto">
                    <a href="{{ route('cms.reports.konsultasi', ['tf' => 'day']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'day' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_today') }}</a>
                    <a href="{{ route('cms.reports.konsultasi', ['tf' => 'week']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'week' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_week') }}</a>
                    <a href="{{ route('cms.reports.konsultasi', ['tf' => 'month']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'month' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_month') }}</a>
                    <a href="{{ route('cms.reports.konsultasi', ['tf' => 'year']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'year' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_year') }}</a>
                    <button @click="showCustom = true; tf = 'custom'" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'custom' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_custom') }}</button>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('cms.reports.konsultasi') }}" x-show="showCustom" x-transition.opacity class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 shrink-0 overflow-x-auto" x-cloak>
                    <input type="hidden" name="tf" value="custom">
                    <div class="flex items-center gap-2 px-2 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_start') }}</span>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-50 text-gray-700 font-medium">
                    </div>
                    <div class="flex items-center gap-2 px-2 border-l border-gray-100 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_end') }}</span>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-50 text-gray-700 font-medium">
                    </div>
                    <button type="submit" class="px-3 py-1 bg-[#174E93] hover:bg-blue-800 text-white rounded-lg font-semibold text-xs transition-colors shadow-sm shrink-0">{{ __('cms.reports.btn_filter') }}</button>
                    <button type="button" @click="showCustom = false" class="px-2 py-1 text-gray-400 hover:text-gray-600 text-xs shrink-0" title="{{ __('cms.reports.btn_cancel') }}">✕</button>
                </form>
            </div>
        </div>

        <!-- Summary Stats Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalKonsultasi) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.total_consultation') }}</div>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-blue-600 bg-blue-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $subtitle }}</span>
                </div>
            </div>
        </div>

        <!-- Data Table: Konsultasi Kearsipan -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.table_konsultasi_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.table_konsultasi_sub') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse dataTable" id="tableKonsultasi">
                    <thead>
                        <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                            <th class="py-3 px-4 rounded-l-xl">{{ __('cms.reports.col_no') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_name_inst') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_contact') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_topic') }}</th>
                            <th class="py-3 px-4 text-center">{{ __('cms.reports.col_submit_date') }}</th>
                            <th class="py-3 px-4 text-center">{{ __('cms.reports.status') }}</th>
                            <th class="py-3 px-4 text-center rounded-r-xl">{{ __('cms.reports.col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                        @forelse($consultations as $index => $con)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-gray-900">{{ $loop->iteration }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 text-[14px]">{{ $con->name }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $con->institution ?: '-' }} ({{ $con->position ?: '-' }})
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div>{{ $con->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $con->phone ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-gray-800 max-w-xs truncate" title="{{ $con->detail }}">{{ $con->detail ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-medium text-gray-800">{{ $con->created_at ? $con->created_at->format('d M Y') : '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $con->created_at ? $con->created_at->format('H:i') : '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs font-bold">
                                    @if($con->is_replied)
                                        <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ __('cms.reports.status_replied') }}</span>
                                    @else
                                        <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ __('cms.reports.status_waiting') }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="selectedCon = {{ json_encode($con) }}; showModal = true" class="p-1.5 bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-600 rounded-lg transition-colors" title="{{ __('cms.reports.btn_detail') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        
                                        @if($hasAllPermission)
                                            @if(!$con->is_replied)
                                                <button @click="selectedConForReply = {{ json_encode($con) }}; replyMessage = ''; showReplyModal = true" class="p-1.5 bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-[#174E93] rounded-lg transition-colors" title="{{ __('cms.reports.btn_reply') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                </button>
                                            @endif
                                        @endif
                                        
                                        @if($con->surat_file)
                                            <a href="{{ asset('storage/' . $con->surat_file) }}" target="_blank" class="p-1.5 bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-600 rounded-lg transition-colors" title="{{ __('cms.reports.btn_download_attachment') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        @endif

                                        @if($hasAllPermission)
                                            <button @click="deleteKonsultasi({{ $con->id }})" class="p-1.5 bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 rounded-lg transition-colors" title="{{ __('cms.reports.btn_delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_konsultasi') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showModal = false">
            <div x-show="showModal" x-transition.opacity.scale.95 class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 overflow-hidden">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('cms.reports.modal_konsultasi_title') }}</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 text-sm" x-if="selectedCon">
                    <template x-if="selectedCon">
                        <div class="space-y-3">
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_name') }}</span>
                                <span class="col-span-2 text-gray-800 font-bold" x-text="selectedCon.name"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_email') }}</span>
                                <span class="col-span-2 text-gray-800" x-text="selectedCon.email"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_phone') }}</span>
                                <span class="col-span-2 text-gray-800" x-text="selectedCon.phone || '-'"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_inst') }}</span>
                                <span class="col-span-2 text-gray-800" x-text="selectedCon.institution || '-'"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_position') }}</span>
                                <span class="col-span-2 text-gray-800" x-text="selectedCon.position || '-'"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_submit_date') }}</span>
                                <span class="col-span-2 text-gray-800 font-medium" x-text="selectedCon.created_at ? selectedCon.created_at.substring(0,10) : '-'"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_topic') }}</span>
                                <span class="col-span-2 text-gray-800 font-semibold" x-text="selectedCon.detail || '-'"></span>
                            </div>

                            @if($hasAllPermission)
                            <template x-if="selectedCon.is_replied">
                                <div class="grid grid-cols-3 gap-2 border-b border-gray-50 pb-2 bg-blue-50/50 -mx-4 px-4 pt-2">
                                    <span class="font-semibold text-blue-700">Balasan Admin</span>
                                    <span class="col-span-2 text-blue-900" x-text="selectedCon.reply_message || '-'"></span>
                                </div>
                            </template>
                            @endif

                            <template x-if="selectedCon.form_data">
                                <div class="mt-4 pt-2 border-t border-gray-100">
                                    <h4 class="font-bold text-gray-700 mb-2">{{ __('cms.reports.modal_form_data') }}</h4>
                                    <div class="bg-gray-50 p-3 rounded-xl space-y-2 text-xs">
                                        <template x-for="field in formFields" :key="field.name">
                                            <template x-if="selectedCon.form_data && selectedCon.form_data[field.name]">
                                                <div class="flex flex-col border-b border-gray-200/60 pb-1.5 last:border-0 last:pb-0">
                                                    <span class="font-bold text-gray-500 capitalize" x-text="field.label"></span>
                                                    <span class="text-gray-800 mt-0.5" x-text="selectedCon.form_data[field.name] || '-'"></span>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">{{ __('cms.reports.btn_close') }}</button>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <div x-show="showReplyModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="if(!isReplying) showReplyModal = false">
            <div x-show="showReplyModal" x-transition.opacity.scale.95 class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 overflow-hidden">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('cms.reports.reply_modal_title') }}</h3>
                    <button @click="showReplyModal = false" :disabled="isReplying" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg focus:outline-none disabled:opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="space-y-4" x-if="selectedConForReply">
                    <template x-if="selectedConForReply">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('cms.reports.reply_modal_to') }}</label>
                                <div class="bg-gray-50 px-3 py-2 rounded-lg text-sm text-gray-600" x-text="selectedConForReply.name + ' (' + selectedConForReply.email + ')'"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('cms.reports.reply_modal_msg') }}</label>
                                <textarea x-model="replyMessage" rows="5" class="w-full form-input rounded-xl text-sm border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('cms.reports.reply_modal_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="showReplyModal = false" :disabled="isReplying" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm disabled:opacity-50">{{ __('cms.reports.swal_cancel') }}</button>
                    <button @click="sendReply()" :disabled="isReplying || !replyMessage.trim()" class="px-5 py-2 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-colors text-sm flex items-center gap-2 disabled:opacity-50">
                        <span x-show="!isReplying">{{ __('cms.reports.btn_send_reply') }}</span>
                        <span x-show="isReplying">{{ __('cms.reports.btn_sending') }}</span>
                        <svg x-show="isReplying" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- DataTables Buttons (export) --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>

    <script>
        window.konsultasiI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnWord: @json(__('cms.pengguna.btn_word')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
            title: @json(__('cms.reports.konsultasi_title')),
        };

        window.LaravelDT = {
            dtInfo: @json(__('cms.datatable.info')),
            dtInfoEmpty: @json(__('cms.datatable.info_empty')),
            dtInfoFiltered: @json(__('cms.datatable.info_filtered')),
            dtZeroRecords: @json(__('cms.datatable.zero_records')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
        };
    </script>
    <script src="{{ asset('js/cms/features/reports/konsultasi.js') }}?v={{ time() }}"></script>
@endpush
