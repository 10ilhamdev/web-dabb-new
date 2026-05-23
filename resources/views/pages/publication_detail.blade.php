@extends('layouts.guest')

@section('title', ($locale === 'en' ? $publication->title_en ?? $publication->title : $publication->title) . ' — ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) : time())) }}">
    <style>
        .pub-hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/image/background.png') center 35%/cover no-repeat;
            color: #fff;
            padding: 48px 0;
            min-height: 160px;
            display: flex;
            align-items: center;
        }
        .pub-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .pub-section {
            padding: 48px 0;
            background: #fff;
        }
        
        .pub-container {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2.5rem;
            align-items: start;
        }

        .detail-main {
            grid-column: 1;
            min-width: 0;
        }

        .news-sidebar {
            grid-column: 2;
            position: sticky;
            top: 140px;
            max-height: calc(100vh - 160px);
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .news-sidebar::-webkit-scrollbar {
            display: none;
        }

        .detail-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1a202c;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .meta-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #edf2f7;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .meta-item svg {
            width: 18px;
            height: 18px;
        }

        .detail-content {
            font-size: 1.05rem;
            color: #2d3748;
            line-height: 1.8;
            margin-bottom: 3rem;
        }
        .detail-content img {
            border-radius: 1rem;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Share Section */
        .share-section {
            padding-top: 2rem;
            border-top: 1px solid #edf2f7;
        }
        .share-label {
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
            display: block;
        }
        .share-buttons {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: transform 0.2s;
        }
        .share-btn:hover {
            transform: scale(1.1);
        }
        .btn-copy { background: #718096; }
        .btn-x { background: #000000; }
        .btn-fb { background: #1877F2; }
        .btn-li { background: #0077B5; }
        .btn-wa { background: #25D366; }

        .footer-meta {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #a0aec0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Sidebar */
        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #174E93;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #174E93;
            text-transform: uppercase;
        }
        .popular-news-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #edf2f7;
        }
        .popular-news-img {
            flex: 0 0 80px;
            height: 60px;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .popular-news-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .popular-news-info {
            flex: 1;
        }
        .popular-news-title {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.4;
            color: #2d3748;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .popular-news-date {
            font-size: 0.75rem;
            color: #a0aec0;
        }

        @media (max-width: 1024px) {
            .pub-container { grid-template-columns: 1fr 240px; }
        }
        @media (max-width: 991px) {
            .pub-container { grid-template-columns: 1fr; gap: 2rem; }
            .detail-main { grid-column: 1 / -1; }
            .news-sidebar {
                grid-column: 1 / -1;
                position: static !important;
                max-height: none !important;
                overflow: visible !important;
                min-width: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $title = $locale === 'en' ? $publication->title_en ?? $publication->title : $publication->title;
        $content = $locale === 'en' ? $publication->description_en ?? $publication->description : $publication->description;
        $date = ($publication->published_at ?? $publication->created_at)->translatedFormat('d F Y');
    @endphp

    {{-- Breadcrumb --}}
    <div class="feature-breadcrumb">
        <div class="container">
            @if ($feature->parent)
                <a href="{{ url($feature->parent->path ?? '#') }}">
                    {{ $locale === 'en' ? $feature->parent->name_en ?? $feature->parent->name : $feature->parent->name }}
                </a>
                <span class="sep">/</span>
            @endif
            <a href="{{ url(ltrim($feature->path, '/')) }}">
                {{ $locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name }}
            </a>
            <span class="sep">/</span>
            <span class="current">{{ $title }}</span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="pub-hero">
        <div class="container">
            <h1>{{ strtoupper($locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name) }}</h1>
        </div>
    </div>

    <section class="pub-section">
        <div class="container">
            <div class="pub-container">
                {{-- Main Detail --}}
                <div class="detail-main">
                    <h1 class="detail-title">{{ $title }}</h1>
                    
                    <div class="meta-info">
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <span>Tim DABB</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>{{ $date }}</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <span>{{ $publication->views }} {{ $locale === 'en' ? 'views' : 'kali dilihat' }}</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            <span id="share-count-header">{{ $publication->shares }} {{ $locale === 'en' ? 'shares' : 'kali dibagikan' }}</span>
                        </div>
                    </div>

                    <div class="detail-content richtext-guest-view">
                        {!! $content !!}
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="news-sidebar">

                    <div class="sidebar-block mb-10">
                        <h2 class="sidebar-title">{{ $locale === 'en' ? 'Popular News' : 'Berita Populer' }}</h2>
                        <div class="popular-news-list">
                            @foreach($popularNews as $pn)
                                @php
                                    $pnTitle = $locale === 'en' ? $pn->title_en ?? $pn->title : $pn->title;
                                    $pnImg = $pn->images ? $pn->images[0] : null;
                                @endphp
                                <a href="{{ route('publication.detail', ['path' => ltrim($feature->path, '/'), 'id' => $pn->id]) }}" class="popular-news-item">
                                    @if($pnImg)
                                    <div class="popular-news-img">
                                        <img src="{{ asset('storage/' . $pnImg) }}" alt="{{ $pnTitle }}">
                                    </div>
                                    @endif
                                    <div class="popular-news-info">
                                        <h4 class="popular-news-title">{{ $pnTitle }}</h4>
                                        <div class="flex items-center gap-3">
                                            <span class="popular-news-date">{{ ($pn->published_at ?? $pn->created_at)->translatedFormat('d M Y') }}</span>
                                            <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    <span>{{ $pn->views }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                                    <span>{{ $pn->shares }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="sidebar-block mb-8 bg-[#F8F9FA] p-6 rounded-xl border border-gray-100">
                        <div class="share-section !pt-0 !border-t-0">
                            <span class="share-label text-sm mb-3">{{ $locale === 'en' ? 'Share' : 'Bagikan' }}</span>
                            <div class="share-buttons">
                                <button class="share-btn btn-copy" onclick="copyToClipboard()" title="Copy Link">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                </button>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($title) }}" target="_blank" class="share-btn btn-x" title="Share on X" onclick="trackShare()">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn btn-fb" title="Share on Facebook" onclick="trackShare()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn btn-li" title="Share on LinkedIn" onclick="trackShare()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($title . ' ' . request()->fullUrl()) }}" target="_blank" class="share-btn btn-wa" title="Share on WhatsApp" onclick="trackShare()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.263-1.643a11.822 11.822 0 005.783 1.513h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                            </div>

                            <div class="footer-meta !mt-6 flex flex-col gap-1.5">
                                <div class="text-[13px] text-gray-600 font-medium">{{ $locale === 'en' ? 'Views :' : 'Dilihat :' }} {{ $publication->views }}</div>
                                <div class="text-[13px] text-gray-600 font-medium">{{ $locale === 'en' ? 'Views :' : 'Dilihat :' }} {{ $publication->views }}</div>
                                <div class="text-[13px] text-gray-600 font-medium">{{ $locale === 'en' ? 'Shares :' : 'Dibagikan :' }} <span id="share-count">{{ $publication->shares }}</span></div>
                                <div class="text-[13px] text-gray-600 font-medium">{{ $locale === 'en' ? 'Published on :' : 'Diterbitkan pada :' }} {{ $date }}</div>
                                <div class="text-[13px] text-gray-600 font-medium">{{ $locale === 'en' ? 'Last updated :' : 'Terakhir diperbarui :' }} {{ ($publication->updated_at ?? $publication->created_at)->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function copyToClipboard() {
        var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            Swal.fire({
                icon: 'success',
                title: '{{ $locale === 'en' ? 'Link Copied!' : 'Tautan Disalin!' }}',
                text: '{{ $locale === 'en' ? 'The article link has been copied to your clipboard.' : 'Tautan artikel telah berhasil disalin.' }}',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                timerProgressBar: true
            });
            trackShare();
        }).catch(function(err) {
            // Fallback
            const el = document.createElement('textarea');
            el.value = url;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            trackShare();
        });
    }

    function trackShare() {
        fetch('{{ route('publication.share.increment', $publication->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const counter = document.getElementById('share-count');
                const counterHeader = document.getElementById('share-count-header');
                if (counter) {
                    counter.innerText = data.shares;
                }
                if (counterHeader) {
                    counterHeader.innerText = data.shares + ' {{ $locale === 'en' ? 'shares' : 'kali dibagikan' }}';
                }
            }
        })
        .catch(error => console.error('Error tracking share:', error));
    }
</script>
@endpush
