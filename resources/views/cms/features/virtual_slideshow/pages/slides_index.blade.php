@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @if ($feature->parent)
        @php
            $grandparent = $feature->parent->parent;
        @endphp

        @if ($grandparent && $grandparent->id !== $feature->parent->id)
            <span class="text-gray-300">/</span>
            <a href="{{ url('/cms/features/' . $grandparent->id . '/') }}"
                class="text-gray-400 hover:text-gray-600 transition-colors">{{ $grandparent->name }}</a>
        @endif

        <span class="text-gray-300">/</span>
        <a href="{{ url('/cms/features/' . $feature->parent->id . '/') }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->parent->name }}</a>
    @endif
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.show', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', $page->title)

@section('content')
    <div class="space-y-6" x-data="{ deleteModal: { open: false, id: null, name: '' } }">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('cms.features.slideshow.index', $feature) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
                style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">🎞️ {{ __('cms.virtual_slideshow.manage_slides_title', ['title' => $page->title]) }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
            </div>
        </div>

        {{-- Slides List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.slides_list_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.virtual_slideshow.slides_list_desc') }}</p>
                </div>
                <a href="{{ route('cms.features.slideshow.pages.slides.create', [$feature, $page]) }}"
                    class="flex items-center gap-2 bg-[#174E93] hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('cms.virtual_slideshow.add_slide') }}
                </a>
            </div>

            <div>
                <table id="tableSlideshowSlides" class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">{{ __('cms.virtual_slideshow.col_order') }}</th>
                            <th class="w-28">{{ __('cms.virtual_slideshow.col_thumbnail') }}</th>
                            <th class="w-36">{{ __('cms.virtual_slideshow.col_type') }}</th>
                            <th>{{ __('cms.virtual_slideshow.col_title') }}</th>
                            <th class="w-48">{{ __('cms.virtual_slideshow.col_content') }}</th>
                            <th class="w-32 text-center">{{ __('cms.virtual_slideshow.col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($slides as $slide)
                        <tr>
                            {{-- Order --}}
                            <td class="text-center text-gray-700 font-medium">{{ $slide->order }}</td>

                            {{-- Thumbnail --}}
                            <td>
                            <div
                                class="w-20 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                @if ($slide->slide_type === 'hero')
                                    @if ($slide->images && count($slide->images) > 0)
                                        <img src="{{ asset('storage/' . $slide->images[0]) }}"
                                            class="w-full h-full object-cover" alt="">
                                    @elseif($slide->image_urls && count($slide->image_urls) > 0)
                                        @php
                                            $heroUrl = $slide->image_urls[0];
                                            $heroThumbSrc = $heroUrl;
                                            if (strpos($heroUrl, 'drive.google.com') !== false) {
                                                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $heroUrl, $m)) {
                                                    $heroThumbSrc = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                                }
                                            } elseif (preg_match('/commons\.wikimedia\.org\/wiki\/File:(.+)/', $heroUrl, $m)) {
                                                $heroThumbSrc = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1];
                                            }
                                        @endphp
                                        <img src="{{ $heroThumbSrc }}"
                                            class="w-full h-full object-cover" alt=""
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center" style="display:none;">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-[#174E93] to-blue-400 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z" />
                                            </svg>
                                        </div>
                                    @endif
                                @elseif($slide->images && count($slide->images) > 0)
                                    <img src="{{ asset('storage/' . $slide->images[0]) }}"
                                        class="w-full h-full object-cover" alt="">
                                @elseif($slide->image_urls && count($slide->image_urls) > 0)
                                    @php
                                        $firstUrl = $slide->image_urls[0];
                                        $thumbSrc = $firstUrl;
                                        if (strpos($firstUrl, 'drive.google.com') !== false) {
                                            if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $firstUrl, $m)) {
                                                $thumbSrc = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                            }
                                        } elseif (preg_match('/commons\.wikimedia\.org\/wiki\/File:(.+)/', $firstUrl, $m)) {
                                            $thumbSrc = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1];
                                        }
                                    @endphp
                                    <img src="{{ $thumbSrc }}"
                                        class="w-full h-full object-cover" alt=""
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center" style="display:none;">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @elseif($slide->slide_type === 'video' || $slide->slide_type === 'text_carousel')
                                    @php
                                        $videoThumbSrc = null;
                                        // Check carousel video URLs for YouTube thumbnail
                                        if ($slide->carousel_video_urls && count($slide->carousel_video_urls) > 0) {
                                            $vUrl = $slide->carousel_video_urls[0];
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $vUrl, $ym)) {
                                                $videoThumbSrc = 'https://img.youtube.com/vi/' . $ym[1] . '/1.jpg';
                                            }
                                        }
                                        // Check single video_url
                                        if (!$videoThumbSrc && $slide->video_url) {
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $slide->video_url, $ym)) {
                                                $videoThumbSrc = 'https://img.youtube.com/vi/' . $ym[1] . '/1.jpg';
                                            }
                                        }
                                    @endphp
                                    @if ($videoThumbSrc)
                                        <img src="{{ $videoThumbSrc }}" class="w-full h-full object-cover" alt=""
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center" style="display:none;">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                            </svg>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            </td>

                            {{-- Type --}}
                            <td>
                                @php
                                    $typeLabels = [
                                        'hero' => __('cms.virtual_slideshow.type_hero'),
                                        'text' => __('cms.virtual_slideshow.type_text'),
                                        'carousel' => __('cms.virtual_slideshow.type_carousel'),
                                        'video' => __('cms.virtual_slideshow.type_video'),
                                        'text_carousel' => __('cms.virtual_slideshow.type_text_carousel'),
                                    ];
                                    $typeColors = [
                                        'hero' => 'bg-purple-100 text-purple-700',
                                        'text' => 'bg-gray-100 text-gray-600',
                                        'carousel' => 'bg-blue-100 text-blue-700',
                                        'video' => 'bg-red-100 text-red-700',
                                        'text_carousel' => 'bg-teal-100 text-teal-700',
                                    ];
                                @endphp
                                <div class="flex flex-wrap items-center gap-1">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$slide->slide_type] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $typeLabels[$slide->slide_type] ?? $slide->slide_type }}
                                    </span>
                                    @if ($slide->layout !== 'center')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            {{ ucfirst($slide->layout) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Title + description --}}
                            <td>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ html_entity_decode($slide->title ?: __('cms.virtual_slideshow.untitled')) }}</p>
                                @if ($slide->description)
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ Str::limit(strip_tags(html_entity_decode($slide->description)), 100) }}</p>
                                @endif
                            </td>

                            {{-- Content counts --}}
                            <td>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                    @php
                                        $totalImages = (isset($slide->images) ? count($slide->images) : 0) + (isset($slide->image_urls) ? count($slide->image_urls) : 0);

                                        // Count all video sources
                                        $totalVideos = 0;
                                        // Carousel video URLs
                                        if (isset($slide->carousel_video_urls) && is_array($slide->carousel_video_urls)) {
                                            foreach ($slide->carousel_video_urls as $vUrl) {
                                                if (!empty($vUrl)) $totalVideos++;
                                            }
                                        }
                                        // Carousel uploaded video files (may be JSON string)
                                        if (!empty($slide->carousel_videos)) {
                                            $cv = $slide->carousel_videos;
                                            if (is_string($cv)) {
                                                $cvDecoded = json_decode($cv, true);
                                                if (is_array($cvDecoded)) {
                                                    $totalVideos += count($cvDecoded);
                                                } else {
                                                    $totalVideos++;
                                                }
                                            } elseif (is_array($cv)) {
                                                $totalVideos += count($cv);
                                            }
                                        }
                                        // Single video URL
                                        if (!empty($slide->video_url)) {
                                            $totalVideos++;
                                        }
                                        // Single video file upload (may be JSON array for text_carousel)
                                        if (!empty($slide->video_file)) {
                                            $vf = $slide->video_file;
                                            if (is_string($vf)) {
                                                $decoded = json_decode($vf, true);
                                                if (is_array($decoded)) {
                                                    $totalVideos += count($decoded);
                                                } else {
                                                    $totalVideos++;
                                                }
                                            } elseif (is_array($vf)) {
                                                $totalVideos += count($vf);
                                            } else {
                                                $totalVideos++;
                                            }
                                        }

                                        $totalInfoPopup = 0;
                                        if (isset($slide->info_popup) && is_array($slide->info_popup)) {
                                            // Explicitly handle carousel structure:
                                            // - carousel_video_order: ordering metadata only — skip
                                            // - carousel_videos: actual captions — count each entry
                                            if (isset($slide->info_popup['carousel_videos']) && is_array($slide->info_popup['carousel_videos'])) {
                                                foreach ($slide->info_popup['carousel_videos'] as $caption) {
                                                    if (isset($caption['type']) && $caption['type'] === 'multi'
                                                        && isset($caption['items']) && is_array($caption['items'])) {
                                                        $totalInfoPopup += count($caption['items']);
                                                    } else {
                                                        $totalInfoPopup += 1;
                                                    }
                                                }
                                            }
                                            // Fallback: generic top-level iteration for other slide types
                                            // (e.g. video slides where "0", "1" are direct string captions)
                                            foreach ($slide->info_popup as $key => $entry) {
                                                if (in_array($key, ['carousel_video_order', 'carousel_videos', 'unified_image_order'], true)) {
                                                    continue;
                                                }
                                                if (is_string($entry)) {
                                                    $totalInfoPopup += 1;
                                                } elseif (is_array($entry)) {
                                                    if (isset($entry['type']) && $entry['type'] === 'multi'
                                                        && isset($entry['items']) && is_array($entry['items'])) {
                                                        $totalInfoPopup += count($entry['items']);
                                                    } else {
                                                        $totalInfoPopup += 1;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    @if ($totalImages > 0)
                                        <span>📷 {{ $totalImages }} {{ $totalImages === 1 ? 'gambar' : 'gambar' }}</span>
                                    @endif
                                    @if ($totalVideos > 0)
                                        <span>🎬 {{ $totalVideos }} video</span>
                                    @endif
                                    @if ($totalInfoPopup > 0)
                                        <span>💬 {{ __('cms.virtual_slideshow.info_popup_count', ['count' => $totalInfoPopup]) }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('cms.features.slideshow.pages.slides.edit', [$feature, $page, $slide]) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors"
                                        title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button"
                                        @click="deleteModal = { open: true, id: {{ $slide->id }}, name: '{{ addslashes($slide->title ?? 'slide ' . $slide->order) }}' }"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors"
                                        title="{{ __('cms.virtual_slideshow.delete') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center">
                                        <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-400 text-sm">{{ __('cms.virtual_slideshow.empty_slides') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($slides->isNotEmpty())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <a href="{{ url($feature->path) }}?page={{ $page->order }}" target="_blank"
                        class="inline-flex items-center gap-2 text-sm text-[#174E93] hover:underline font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        {{ __('cms.virtual_slideshow.view_exhibition', ['order' => $page->order]) }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="deleteModal.open" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
            style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false"
                style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.delete_slide_title') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('cms.virtual_slideshow.delete_slide_confirm') }}
                            <strong x-text="deleteModal.name" class="text-gray-700"></strong>?
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full">
                        <button @click="deleteModal.open = false"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            {{ __('cms.virtual_slideshow.cancel') }}
                        </button>
                        <form
                            :action="`{{ url('cms/features/' . $feature->id . '/slideshow/pages/' . $page->id . '/slides') }}/${deleteModal.id}`"
                            method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                {{ __('cms.virtual_slideshow.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tableSlideshowSlides').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [1, 4, 5]
                }],
                order: [
                    [0, 'asc']
                ],
            });
        });
    </script>
@endpush
