@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">Manajemen Fitur</a>
@endsection
@section('breadcrumb_active', $feature->name)

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.home.title') }}: {{ $feature->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.home.desc') }}</p>
        </div>
        <a href="{{ url($feature->path) }}" target="_blank"
            class="ml-auto inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#174E93] bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            {{ __('cms.home.view_page') }}
        </a>
    </div>

    {{-- ===== BAHASA INDONESIA ONLY, ENGLISH TAB REMOVED AS PER USER REQUEST ===== --}}
    <div>
        <form action="{{ route('cms.home.update', $feature->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-0">
            @csrf
            @method('PUT')
            <input type="hidden" name="locale" value="id">

            {{-- Hero Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#174E93] flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-1 16h12L17 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.hero.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.hero.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.hero.hero_title') }}</label>
                        <input type="text" name="hero_title" value="{{ $idContent['hero_title'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.hero.hero_cta') }}</label>
                        <input type="text" name="hero_cta" value="{{ $idContent['hero_cta'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Hero Background (gambar atau video) --}}
                    @php
                        $heroBgPath = $idContent['hero']['background_path'] ?? '';
                        $heroBgType = $idContent['hero']['background_type'] ?? '';
                        if ($heroBgPath && !$heroBgType) {
                            $heroExt = strtolower(pathinfo($heroBgPath, PATHINFO_EXTENSION));
                            if (in_array($heroExt, ['mp4', 'webm', 'ogg', 'mov'])) $heroBgType = 'video';
                            elseif (in_array($heroExt, ['jpg','jpeg','png','webp','gif','avif'])) $heroBgType = 'image';
                        }
                    @endphp
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Background Hero (Gambar atau Video)</label>

                        @if($heroBgPath)
                            <div class="mb-3 flex items-start gap-3">
                                <div class="w-48 h-28 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                                    @if($heroBgType === 'video')
                                        <video src="{{ asset('storage/' . $heroBgPath) }}" class="w-full h-full object-cover" muted autoplay loop playsinline></video>
                                    @elseif($heroBgType === 'image')
                                        <img src="{{ asset('storage/' . $heroBgPath) }}" alt="Hero Background" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs text-gray-400">Tipe tidak dikenali</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 mb-2">
                                        <span class="font-semibold">Saat ini:</span>
                                        <span class="uppercase">{{ $heroBgType ?: 'Unknown' }}</span>
                                        · <span class="font-mono break-all">{{ $heroBgPath }}</span>
                                    </p>
                                    <label class="inline-flex items-center gap-2 text-xs text-red-600">
                                        <input type="checkbox" name="hero_background_remove" value="1" class="rounded border-gray-300">
                                        Hapus background (kembali ke video default)
                                    </label>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="hero_background_file"
                            accept="image/jpeg,image/png,image/webp,image/gif,image/avif,video/mp4,video/webm,video/ogg,video/quicktime"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-400 mt-1">
                            Unggah <span class="font-semibold">gambar</span> (JPG/PNG/WebP/GIF/AVIF) atau <span class="font-semibold">video</span> (MP4/WebM/OGG/MOV) untuk menggantikan latar hero.
                            Jika dikosongkan, website akan menggunakan video default (library-books.mp4).
                        </p>

                        {{-- Hidden field agar path lama tidak hilang saat form dikirim tanpa upload baru --}}
                        <input type="hidden" name="hero[background_path]" value="{{ $heroBgPath }}">
                        <input type="hidden" name="hero[background_type]" value="{{ $heroBgType }}">
                    </div>
                </div>
            </div>

            {{-- Feature Strip --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.feature_strip.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.feature_strip.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.left') }}</label>
                        <textarea name="feature_strip[left]" rows="3"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ $idContent['feature_strip']['left'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.middle') }}</label>
                        <input type="text" name="feature_strip[middle]" value="{{ $idContent['feature_strip']['middle'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5 mt-3">{{ __('cms.home.feature_strip.middle_link') }}</label>
                        <input type="text" name="feature_strip[middle_link]" value="{{ $idContent['feature_strip']['middle_link'] ?? '' }}"
                            placeholder="https://example.com"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.right_button') }}</label>
                        <input type="text" name="feature_strip[right_button]" value="{{ $idContent['feature_strip']['right_button'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5 mt-3">{{ __('cms.home.feature_strip.right_button_link') }}</label>
                        <input type="text" name="feature_strip[right_button_link]" value="{{ $idContent['feature_strip']['right_button_link'] ?? '' }}"
                            placeholder="https://example.com"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.right_text') }}</label>
                        <textarea name="feature_strip[right_text]" rows="3"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ $idContent['feature_strip']['right_text'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.info.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.info.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.section') }}</label>
                        <input type="text" name="sections[info_title]" value="{{ $idContent['sections']['info_title'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.image1') }}</label>
                            @if(!empty($idContent['sections']['info_image_1']))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $idContent['sections']['info_image_1']) }}" alt="Info Image 1" class="h-24 rounded-lg border border-gray-200 object-cover">
                                </div>
                            @endif
                            <input type="file" name="info_image_1" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-400 mt-1">{{ __('cms.home.info.image_help') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.paragraph1') }}</label>
                            <textarea name="sections[info_1]" rows="4"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y">{{ $idContent['sections']['info_1'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.image2') }}</label>
                            @if(!empty($idContent['sections']['info_image_2']))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $idContent['sections']['info_image_2']) }}" alt="Info Image 2" class="h-24 rounded-lg border border-gray-200 object-cover">
                                </div>
                            @endif
                            <input type="file" name="info_image_2" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-400 mt-1">{{ __('cms.home.info.image_help') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.info.paragraph2') }}</label>
                            <textarea name="sections[info_2]" rows="4"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y">{{ $idContent['sections']['info_2'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activities Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.activities.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.activities.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.activities.section') }}</label>
                        <input type="text" name="sections[activities]" value="{{ $idContent['sections']['activities'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $actColors = ['#D06767','#3598DB','#89DB51','#000000','#DB420F','#E660D4'];
                            $activityItems = $idContent['activity_items'] ?? [];
                            // Always show 6 fields
                            for ($ai = count($activityItems); $ai < 6; $ai++) {
                                $activityItems[$ai] = '';
                            }
                        @endphp
                        @foreach($activityItems as $i => $item)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                                style="background: {{ $actColors[$i] ?? '#999' }}">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                            <input type="text" name="activity_items[{{ $i }}]" value="{{ $item }}"
                                class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Related Links Section --}}
            @php
                $relatedLinks = $idContent['feature_strip']['related_links'] ?? [];
                if (!is_array($relatedLinks)) {
                    $relatedLinks = [];
                }
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.feature_strip.related_links') }}</h3>
                        <p class="text-xs text-gray-500">Tautan dengan foto yang dapat diklik</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.section_titles.related') }}</label>
                        <input type="text" name="sections[related]" value="{{ $idContent['sections']['related'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>
                <div class="px-6 pb-6" id="related-links-container">
                    @foreach($relatedLinks as $index => $link)
                    <div class="related-link-item flex gap-3 items-start mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.related_link') }}</label>
                            <input type="text" name="feature_strip[related_links][{{ $index }}][link]" value="{{ $link['link'] ?? '' }}"
                                placeholder="https://example.com"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.related_photo') }}</label>
                            @if(!empty($link['photo']))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $link['photo']) }}" alt="Related Link Photo" class="h-16 rounded-lg border border-gray-200 object-cover">
                                </div>
                            @endif
                            <input type="file" name="feature_strip[related_links][{{ $index }}][photo_file]" accept="image/*"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <input type="hidden" name="feature_strip[related_links][{{ $index }}][photo]" value="{{ $link['photo'] ?? '' }}">
                        </div>
                        <button type="button" onclick="this.closest('.related-link-item').remove()"
                            class="mt-6 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 pb-6">
                    <button type="button" onclick="addRelatedLink()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('cms.home.feature_strip.add_related') }}
                    </button>
                </div>
            </div>

            <script>
                let relatedLinkIndex = {{ count($relatedLinks) }};
                function addRelatedLink() {
                    const container = document.getElementById('related-links-container');
                    const item = document.createElement('div');
                    item.className = 'related-link-item flex gap-3 items-start mb-4 p-4 bg-gray-50 rounded-lg';
                    item.innerHTML = `
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.related_link') }}</label>
                            <input type="text" name="feature_strip[related_links][${relatedLinkIndex}][link]" value=""
                                placeholder="https://example.com"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.feature_strip.related_photo') }}</label>
                            <input type="file" name="feature_strip[related_links][${relatedLinkIndex}][photo_file]" accept="image/*"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <input type="hidden" name="feature_strip[related_links][${relatedLinkIndex}][photo]" value="">
                        </div>
                        <button type="button" onclick="this.closest('.related-link-item').remove()"
                            class="mt-6 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    `;
                    container.appendChild(item);
                    relatedLinkIndex++;
                }
            </script>

            {{-- Gallery / Pameran Arsip --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.section_titles.gallery') }}</h3>
                        <p class="text-xs text-gray-500">Judul seksi pameran arsip pada halaman beranda</p>
                    </div>
                </div>
                <div class="p-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.section_titles.gallery') }}</label>
                        <input type="text" name="sections[gallery]" value="{{ $idContent['sections']['gallery'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Konten galeri pameran arsip diambil otomatis dari data pameran virtual.</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.stats.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.stats.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.section_titles.stats') }}</label>
                        <input type="text" name="sections[stats]" value="{{ $idContent['sections']['stats'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.stats.total') }}</label>
                            <input type="text" name="stats[total]" value="{{ $idContent['stats']['total'] ?? '' }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.stats.today') }}</label>
                            <input type="text" name="stats[today]" value="{{ $idContent['stats']['today'] ?? '' }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Gambar Statistik</label>
                        @if(!empty($idContent['stats']['image']))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $idContent['stats']['image']) }}" alt="Stats Image" class="h-24 rounded-lg border border-gray-200 object-cover">
                            </div>
                        @endif
                        <input type="file" name="stats_image" accept="image/jpeg,image/png,image/webp"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-400 mt-1">{{ __('cms.home.info.image_help') }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-600">Angka statistik pengunjung dihitung otomatis berdasarkan jumlah akses halaman oleh pengunjung.</p>
                    </div>
                </div>
            </div>

            {{-- YouTube Videos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.youtube.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.youtube.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.section_titles.youtube') }}</label>
                        <input type="text" name="sections[youtube]" value="{{ $idContent['sections']['youtube'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="youtube-fields">
                        @php
                            $youtubeIds = $idContent['youtube_ids'] ?? [];
                            // Show at least one empty field if no youtube IDs exist
                            if (empty($youtubeIds)) {
                                $youtubeIds = [''];
                            }
                        @endphp
                        @foreach($youtubeIds as $yi => $vid)
                        <div class="youtube-field">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.youtube.video_label', ['number' => $yi + 1]) }}</label>
                            <div class="flex gap-2">
                                <input type="text" name="youtube_ids[]" value="{{ $vid }}"
                                    placeholder="{{ __('cms.home.youtube.placeholder') }}"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @if($vid)
                                <a href="https://youtube.com/watch?v={{ $vid }}" target="_blank"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg transition-colors border border-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        <button type="button" onclick="addYoutubeField()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Video
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{!! __('cms.home.youtube.help') !!}</p>
                </div>

                <script>
                    function addYoutubeField() {
                        const container = document.getElementById('youtube-fields');
                        const fieldCount = container.querySelectorAll('.youtube-field').length;
                        const newField = document.createElement('div');
                        newField.className = 'youtube-field';
                        newField.innerHTML = `
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Video ${fieldCount + 1}</label>
                            <div class="flex gap-2">
                                <input type="text" name="youtube_ids[]" value=""
                                    placeholder="Contoh: F2NhNTiNxoY"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        `;
                        container.appendChild(newField);
                    }
                </script>
            </div>

            {{-- Instagram Feed --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-pink-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.home.instagram.title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('cms.home.instagram.desc') }}</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.section_titles.instagram') }}</label>
                        <input type="text" name="sections[instagram]" value="{{ $idContent['sections']['instagram'] ?? '' }}"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.instagram.username_label') }}</label>
                        <input type="text" name="instagram_username" value="{{ $idContent['instagram_username'] ?? 'arsipnasionalri' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">{{ __('cms.home.instagram.username_help') }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="instagram-fields">
                        @php
                            $igCodes = $idContent['instagram_codes'] ?? [];
                            if (empty($igCodes)) {
                                $igCodes = [''];
                            }
                        @endphp
                        @foreach($igCodes as $ii => $code)
                        <div class="instagram-field">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('cms.home.instagram.post_label', ['number' => $ii + 1]) }}</label>
                            <div class="flex gap-2">
                                <input type="text" name="instagram_codes[]" value="{{ $code }}"
                                    placeholder="{{ __('cms.home.instagram.placeholder') }}"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                @if($code)
                                <a href="https://www.instagram.com/p/{{ strtok($code, '?') }}/" target="_blank"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-pink-50 hover:bg-pink-100 text-pink-500 rounded-lg transition-colors border border-pink-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        <button type="button" onclick="addInstagramField()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('cms.home.instagram.add_post') }}
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">{!! __('cms.home.instagram.help') !!}</p>
                </div>

                <script>
                    function addInstagramField() {
                        const container = document.getElementById('instagram-fields');
                        const fieldCount = container.querySelectorAll('.instagram-field').length;
                        const newField = document.createElement('div');
                        newField.className = 'instagram-field';
                        newField.innerHTML = `
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Post ${fieldCount + 1}</label>
                            <div class="flex gap-2">
                                <input type="text" name="instagram_codes[]" value=""
                                    placeholder="Contoh: DULJ3gDkkDZ"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        `;
                        container.appendChild(newField);
                    }
                </script>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-end gap-3 pb-4">
                <a href="{{ route('cms.features.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors">
                    {{ __('cms.common.back') }}
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                    {{ __('cms.common.save_content') }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
