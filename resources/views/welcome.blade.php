@extends('layouts.guest')

@php
    // Push Blade $content variable into global scope so the home() helper can access it
    if (isset($content) && is_array($content)) {
        $GLOBALS['_beranda_content'] = $content;
    }

    if (!function_exists('home')) {
    function home($key, $default = null) {
        $content = $GLOBALS['_beranda_content'] ?? null;

        if (isset($content) && is_array($content)) {
            // Support dot notation (e.g. 'feature_strip.left')
            $value = $content;
            foreach (explode('.', $key) as $segment) {
                if (is_array($value) && array_key_exists($segment, $value)) {
                    $value = $value[$segment];
                } else {
                    return $default;
                }
            }
            if ($value !== null) {
                return $value;
            }
            return $default;
        }

        // When no content passed (feature ID 1), directly load home.php
        $locale = app()->getLocale();
        $homePath = resource_path("lang/{$locale}/home.php");
        if (file_exists($homePath)) {
            $homeContent = include $homePath;
            $value = $homeContent;
            foreach (explode('.', $key) as $segment) {
                if (is_array($value) && array_key_exists($segment, $value)) {
                    $value = $value[$segment];
                } else {
                    return $default;
                }
            }
            if ($value !== null) {
                return $value;
            }
        }

        return $default;
    }
    } // end function_exists
@endphp

@section('title', home('site_name'))

@section('body-class')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush

@section('content')
    <header class="hero">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('video/library-books.mp4') }}" type="video/mp4">
        </video>
        <div class="container hero-grid">
            <div>
                <h1>{{ home('hero_title') }}</h1>
                <a class="cta" href="#info-section">{{ home('hero_cta') }}</a>
            </div>
            <img src="{{ asset('image/logo_anri.png') }}" alt="ANRI" class="hero-logo">
        </div>
    </header>

    <div class="feature-strip">
        <div class="left">
            <div class="text">{{ home('feature_strip.left') }}</div>
            <a class="btn" href="{{ home('feature_strip.middle_link', '#') }}">{{ home('feature_strip.middle') }}</a>
        </div>
        <div class="right">
            <a class="btn" href="{{ home('feature_strip.right_button_link', '#') }}">{{ home('feature_strip.right_button') }}</a>
            <div class="text">{{ home('feature_strip.right_text') }}</div>
        </div>
    </div>

    @if(home('sections.info_title') || home('sections.info_image_1') || home('sections.info_1') || home('sections.info_image_2') || home('sections.info_2'))
    <section id="info-section">
        <div class="container">
            <h2 class="section-title">{{ home('sections.info_title') }}</h2>
            <div class="separator"></div>
            @if(home('sections.info_image_1') || home('sections.info_1') || home('sections.info_image_2') || home('sections.info_2'))
            <div class="info-grid">
                @if(home('sections.info_image_1'))
                <img class="info-photo" src="{{ asset('storage/' . home('sections.info_image_1')) }}" alt="Kantor DABB">
                @endif
                @if(home('sections.info_1'))
                <p>{{ home('sections.info_1') }}</p>
                @endif
                @if(home('sections.info_image_2'))
                <img class="info-photo" src="{{ asset('storage/' . home('sections.info_image_2')) }}" alt="Pegawai DABB">
                @endif
                @if(home('sections.info_2'))
                <p>{{ home('sections.info_2') }}</p>
                @endif
            </div>
            @endif
        </div>
    </section>
    @endif

    @php
        $activityItems = home('activity_items', []);
    @endphp
    @if(home('sections.activities') || (!empty($activityItems) && is_array($activityItems)))
    <section class="activities">
        <div class="container">
            <h2 class="section-title">{{ home('sections.activities') }}</h2>
            <div class="separator"></div>
            @if(!empty($activityItems) && is_array($activityItems))
            @php
                $colors = ['#D06767', '#3598DB', '#89DB51', '#000000', '#DB420F', '#E660D4'];
            @endphp
            <div class="activity-list">
                @foreach ($activityItems as $index => $item)
                    <div class="activity-card">
                        <div class="activity-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="activity-text" style="background: {{ $colors[$index] ?? '#3598DB' }}">{{ $item }}</div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Links Related Section (Tautan Terkait) - displays CMS data --}}
    @php
        $relatedLinks = home('feature_strip.related_links', []);
    @endphp
    @if(home('sections.related') || !empty($relatedLinks))
    <section class="links-related">
        <div class="container">
            <h2 class="section-title">{{ home('sections.related') }}</h2>
            <div class="separator"></div>
            @if(!empty($relatedLinks))
            @php
                $validLinks = array_values(array_filter($relatedLinks, fn($l) => !empty($l['photo'])));
            @endphp
            @if(count($validLinks) > 0)
            <div class="related-links-marquee" id="related-links-marquee">
                <div class="related-links-track" id="related-links-track">
                    {{-- Slide 1: gambar ditengah, selebar viewport --}}
                    <div class="related-links-slide">
                        @foreach($validLinks as $link)
                        <a href="{{ $link['link'] ?? '#' }}" class="related-link-card" target="{{ !empty($link['link']) ? '_blank' : '_self' }}">
                            <div class="related-link-image">
                                <img src="{{ asset('storage/' . $link['photo']) }}" alt="{{ $link['title'] ?? 'Related Link' }}">
                            </div>
                        </a>
                        @endforeach
                    </div>
                    {{-- Slide 2: duplikat tersembunyi untuk seamless loop --}}
                    @if(count($validLinks) > 1)
                    <div class="related-links-slide" aria-hidden="true">
                        @foreach($validLinks as $link)
                        <a href="{{ $link['link'] ?? '#' }}" class="related-link-card" target="{{ !empty($link['link']) ? '_blank' : '_self' }}" tabindex="-1">
                            <div class="related-link-image">
                                <img src="{{ asset('storage/' . $link['photo']) }}" alt="">
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @if(count($validLinks) > 1)
            <script>
                (function() {
                    var marquee = document.getElementById('related-links-marquee');
                    var track = document.getElementById('related-links-track');
                    if (!marquee || !track) return;

                    var firstSlide = track.querySelector('.related-links-slide');
                    if (!firstSlide) return;

                    function measure() {
                        var cards = firstSlide.querySelectorAll('.related-link-card');
                        var gap = 32;
                        var contentW = 0;
                        cards.forEach(function(card) { contentW += card.offsetWidth; });
                        contentW += gap * Math.max(cards.length - 1, 0);

                        if (contentW <= 0) return false;

                        track.style.setProperty('--slide-width', contentW + 'px');
                        marquee.style.maxWidth = contentW + 'px';
                        marquee.style.margin = '24px auto 0';
                        track.style.animationDuration = Math.max(contentW / 50, 6) + 's';
                        track.classList.add('scrolling');
                        return true;
                    }

                    function start() {
                        // Wait until every image in the track reports a real size.
                        var imgs = track.querySelectorAll('img');
                        var pending = 0;
                        imgs.forEach(function(img) {
                            if (!img.complete || !img.naturalWidth) {
                                pending++;
                                var done = function() {
                                    img.removeEventListener('load', done);
                                    img.removeEventListener('error', done);
                                    if (--pending === 0) measure();
                                };
                                img.addEventListener('load', done);
                                img.addEventListener('error', done);
                            }
                        });
                        if (pending === 0) measure();
                    }

                    // Recompute on resize so widths stay correct when the viewport changes.
                    var resizeTimer;
                    window.addEventListener('resize', function() {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(measure, 150);
                    });

                    marquee.addEventListener('mouseenter', function() {
                        track.style.animationPlayState = 'paused';
                    });
                    marquee.addEventListener('mouseleave', function() {
                        track.style.animationPlayState = 'running';
                    });

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', start);
                    } else {
                        start();
                    }
                })();
            </script>
            @endif
            @endif
            @endif
        </div>
    </section>
    @endif

    <section class="gallery">
        <div class="container">
            <h2 class="section-title">{{ home('sections.gallery') }}</h2>
            <div class="separator"></div>
            @php
                $locale = app()->getLocale();
                $previews = collect();
                $fallbackImg = asset('image/pameran1.png');

                $pickTitle = function ($model, $fields = ['name', 'title']) use ($locale) {
                    foreach ($fields as $f) {
                        $en = $f . '_en';
                        if ($locale === 'en' && !empty($model->{$en})) return $model->{$en};
                        if (!empty($model->{$f})) return $model->{$f};
                    }
                    return null;
                };

                $storageImg = function ($path) use ($fallbackImg) {
                    return $path ? asset('storage/' . ltrim($path, '/')) : $fallbackImg;
                };

                // 1. Pameran 360° (real)
                foreach (\App\Models\VirtualRoom::with('feature')->orderBy('id', 'desc')->limit(8)->get() as $room) {
                    if (!$room->feature || !$room->feature->path) continue;
                    $previews->push([
                        'name'  => $pickTitle($room) ?? 'Pameran 360°',
                        'image' => $storageImg($room->thumbnail_path ?: $room->image_360_path),
                        'link'  => url($room->feature->path),
                        'type'  => '360° Virtual',
                    ]);
                }

                // 2. Pameran Arsip 3D
                foreach (\App\Models\Virtual3dRoom::with('feature')->orderBy('id', 'desc')->limit(8)->get() as $room) {
                    if (!$room->feature || !$room->feature->path) continue;
                    $previews->push([
                        'name'  => $pickTitle($room) ?? 'Pameran 3D',
                        'image' => $storageImg($room->thumbnail_path),
                        'link'  => url($room->feature->path),
                        'type'  => '3D Virtual',
                    ]);
                }

                // 3. Pameran Arsip Buku Virtual
                foreach (\App\Models\Book::with('feature')->orderBy('id', 'desc')->limit(8)->get() as $book) {
                    if (!$book->feature || !$book->feature->path) continue;
                    $previews->push([
                        'name'  => $pickTitle($book, ['title', 'name']) ?? 'Buku Virtual',
                        'image' => $storageImg($book->thumbnail ?: $book->cover_image),
                        'link'  => url($book->feature->path),
                        'type'  => 'Buku Virtual',
                    ]);
                }

                // 4. Pameran Arsip Slideshow Virtual — sumber gambar mengikuti logika
                //    halaman landing slideshow: thumbnail_path → slide pertama (images/image_urls).
                $slideshowPages = \App\Models\VirtualSlideshowPage::with(['feature', 'slideshowSlides'])
                    ->orderBy('id', 'desc')
                    ->limit(8)
                    ->get();

                foreach ($slideshowPages as $slidePage) {
                    if (!$slidePage->feature || !$slidePage->feature->path) continue;

                    $thumbUrl = null;
                    if (!empty($slidePage->thumbnail_path)) {
                        $thumbUrl = asset('storage/' . ltrim($slidePage->thumbnail_path, '/'));
                    } else {
                        $firstSlide = $slidePage->slideshowSlides->sortBy('order')->first();
                        if ($firstSlide) {
                            $imgs = $firstSlide->images ?? [];
                            $urls = $firstSlide->image_urls ?? [];
                            if (!empty($imgs)) {
                                $thumbUrl = asset('storage/' . ltrim($imgs[0], '/'));
                            } elseif (!empty($urls)) {
                                $firstUrl = $urls[0];
                                // Normalisasi Google Drive URL agar bisa di-embed sebagai gambar.
                                if (strpos($firstUrl, 'drive.google.com') !== false) {
                                    foreach (['/\/file\/d\/([a-zA-Z0-9_-]+)/', '/id=([a-zA-Z0-9_-]+)/', '/\/open\?id=([a-zA-Z0-9_-]+)/'] as $pat) {
                                        if (preg_match($pat, $firstUrl, $m)) {
                                            $thumbUrl = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                            break;
                                        }
                                    }
                                    if (!$thumbUrl) $thumbUrl = $firstUrl;
                                } elseif (preg_match('/commons\.wikimedia\.org\/wiki\/File:(.+)/', $firstUrl, $m)) {
                                    $thumbUrl = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1];
                                } else {
                                    $thumbUrl = $firstUrl;
                                }
                            }
                        }
                    }

                    $previews->push([
                        'name'  => $pickTitle($slidePage, ['title', 'name']) ?? 'Slideshow Virtual',
                        'image' => $thumbUrl ?: $fallbackImg,
                        'link'  => url($slidePage->feature->path) . '?page=' . ($slidePage->order ?? 1),
                        'type'  => 'Slideshow Virtual',
                    ]);
                }

                // Dedupe: hanya tampilkan satu kartu per kombinasi type+name.
                // Nama yang sama pada tipe pameran yang sama dianggap duplikat
                // walaupun berasal dari feature berbeda — agar tidak muncul kembar di gallery.
                $previews = $previews
                    ->unique(fn ($p) => strtolower(trim(($p['type'] ?? '') . '|' . ($p['name'] ?? ''))))
                    ->values();

                // Fallback kosong jika blm ada data sama sekali
                if ($previews->isEmpty()) {
                    $previews->push(['name' => 'Pameran 1', 'image' => asset('image/pameran1.png'), 'link' => '#', 'type' => 'Pameran']);
                    $previews->push(['name' => 'Pameran 2', 'image' => asset('image/desain_dokumentasi.png'), 'link' => '#', 'type' => 'Pameran']);
                    $previews->push(['name' => 'Pameran 3', 'image' => asset('image/pameran1.png'), 'link' => '#', 'type' => 'Pameran']);
                }
            @endphp

            <div class="gallery-wrapper">
                <button type="button" class="gallery-nav prev" id="btn-gallery-prev" aria-label="Previous">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <div class="gallery-slider" id="gallery-carousel">
                    @foreach($previews as $preview)
                    <div class="gallery-item-card">
                        <a href="{{ $preview['link'] }}">
                            <img src="{{ $preview['image'] }}" alt="{{ $preview['name'] }}" loading="lazy">
                            <div class="gallery-item-overlay">
                                <div><span class="badge">{{ $preview['type'] }}</span></div>
                                <h3>{{ $preview['name'] }}</h3>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="gallery-nav next" id="btn-gallery-next" aria-label="Next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <style>
                .gallery-wrapper {
                    position: relative;
                    padding: 0 50px;
                    max-width: 1100px;
                    margin: 0 auto;
                }
                .gallery-slider {
                    display: flex;
                    overflow-x: auto;
                    scroll-behavior: smooth;
                    scroll-snap-type: x mandatory;
                    gap: 20px;
                    padding: 10px 0 20px 0;
                    scrollbar-width: none;
                }
                .gallery-slider::-webkit-scrollbar {
                    display: none;
                }
                .gallery-item-card {
                    flex: 0 0 calc(33.333% - 14px);
                    scroll-snap-align: start;
                    border-radius: 12px;
                    overflow: hidden;
                    position: relative;
                    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
                    background: #fff;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .gallery-item-card:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
                }
                .gallery-item-card a {
                    display: block;
                    height: 100%;
                    text-decoration: none;
                }
                .gallery-item-card img {
                    width: 100%;
                    height: 240px;
                    object-fit: cover;
                    display: block;
                    transition: transform 0.5s ease;
                }
                .gallery-item-card:hover img {
                    transform: scale(1.05);
                }
                .gallery-item-overlay {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(to top, rgba(0,25,70,0.9) 0%, rgba(0,25,70,0) 100%);
                    padding: 40px 16px 16px;
                    color: #fff;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                }
                .gallery-item-overlay .badge {
                    background: var(--blue-light);
                    color: #000;
                    font-size: 11px;
                    font-weight: 700;
                    padding: 4px 8px;
                    border-radius: 4px;
                    display: inline-block;
                    margin-bottom: 8px;
                }
                .gallery-item-overlay h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 700;
                    line-height: 1.3;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
                    color: #fff;
                }
                .gallery-nav {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    z-index: 10;
                    color: var(--blue);
                    transition: all 0.2s ease;
                }
                .gallery-nav:hover {
                    background: var(--blue);
                    color: #fff;
                    border-color: var(--blue);
                }
                .gallery-nav.prev {
                    left: -2px;
                }
                .gallery-nav.next {
                    right: -2px;
                }
                @media (max-width: 768px) {
                    .gallery-wrapper { padding: 0; }
                    .gallery-nav { display: none; }
                    .gallery-item-card { flex: 0 0 calc(85% - 10px); }
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const slider = document.getElementById('gallery-carousel');
                    const btnPrev = document.getElementById('btn-gallery-prev');
                    const btnNext = document.getElementById('btn-gallery-next');

                    if (slider && btnPrev && btnNext) {
                        btnPrev.addEventListener('click', () => {
                            const itemWidth = slider.querySelector('.gallery-item-card').offsetWidth + 20;
                            slider.scrollBy({ left: -itemWidth, behavior: 'smooth' });
                        });
                        btnNext.addEventListener('click', () => {
                            const itemWidth = slider.querySelector('.gallery-item-card').offsetWidth + 20;
                            slider.scrollBy({ left: itemWidth, behavior: 'smooth' });
                        });
                    }
                });
            </script>
        </div>
    </section>

    @if(home('sections.stats') || home('stats.total') || home('stats.today'))
    <section class="stats-section">
        <div class="container">
            @if(home('sections.stats'))
            <h2 class="section-title">{{ home('sections.stats') }}</h2>
            <div class="separator"></div>
            @endif
            <div class="stats-grid">
                @if(home('stats.image'))
                <img src="{{ asset('storage/' . home('stats.image')) }}" alt="Statistik">
                @else
                <img src="{{ asset('image/statistik_pengunjung.png') }}" alt="Statistik">
                @endif
                <div class="counter">
                    <div class="number">{{ \App\Models\PageView::count() }}</div>
                    <div>{{ home('stats.total') }}</div>
                </div>
                <div class="counter">
                    <div class="number">{{ \App\Models\PageView::where('viewed_date', now()->toDateString())->count() }}</div>
                    <div>{{ home('stats.today') }}</div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @php
        $youtubeIds = home('youtube_ids', []);
    @endphp
    @if(home('sections.youtube') || (!empty($youtubeIds) && is_array($youtubeIds)))
    <section class="related">
        <div class="container">
            <h2 class="section-title">{{ home('sections.youtube') }}</h2>
            <div class="separator"></div>
            @if(!empty($youtubeIds) && is_array($youtubeIds))
            <div class="youtube-wrap">
                <div class="youtube-carousel-container">
                    <div id="youtube-carousel" class="youtube-carousel">
                        @foreach($youtubeIds as $index => $videoId)
                        <div class="youtube-item">
                            <div class="youtube-thumb">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="Video {{ $index + 1 }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                                <button class="youtube-play"><span></span></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="youtube-nav">
                    <button id="youtube-prev" aria-label="Sebelumnya">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" fill="#F3F3F3" />
                            <path d="M18 22L12 16L18 10" stroke="#545456" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <span>
                        @for($i = 0; $i < count($youtubeIds); $i++)
                        <span class="youtube-dot"></span>
                        @endfor
                    </span>
                    <button id="youtube-next" aria-label="Berikutnya">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" fill="#F3F3F3" />
                            <path d="M14 10L20 16L14 22" stroke="#545456" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    @php
        $igCodes = home('instagram_codes', []);
        $igUsername = home('instagram_username', '');
        $igPosts = [];
        if (!empty($igCodes) && is_array($igCodes)) {
            foreach($igCodes as $code) {
                if (!empty($code)) {
                    $shortCode = strtok($code, '?');
                    $igPosts[] = [
                        'code' => $shortCode,
                        'url' => 'https://www.instagram.com/p/' . $shortCode . '/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==',
                    ];
                }
            }
        }
    @endphp
    @if(home('sections.instagram') || !empty($igUsername) || !empty($igPosts))
    <section>
        <div class="container">
            @if(home('sections.instagram'))
            <h2 class="section-title">{{ home('sections.instagram') }}</h2>
            <div class="separator"></div>
            @endif
            @if(!empty($igUsername))
            <div class="instagram-grid">
                <div class="left">
                    <a class="ig-profile-preview"
                        href="https://www.instagram.com/{{ $igUsername }}/?utm_source=ig_web_button_share_sheet"
                        target="_blank" rel="noopener noreferrer">
                        <div class="ig-profile-head">
                            <img src="{{ asset('image/logo_anri.png') }}" alt="{{ $igUsername }}" class="ig-avatar">
                            <div>
                                <div class="ig-username">{{ $igUsername }}</div>
                                <div class="ig-name">Arsip Nasional RI</div>
                                <div class="ig-stats">4,106 posts · 119K followers · 91 following</div>
                            </div>
                        </div>
                    </a>
                    <a href="https://www.instagram.com/{{ $igUsername }}/?utm_source=ig_web_button_share_sheet"
                        class="follow-btn" target="_blank" rel="noopener noreferrer">Follow Kami</a>
                </div>
                @if(!empty($igPosts))
                <div class="right">
                    @foreach ($igPosts as $index => $post)
                        <div class="ig-post" aria-label="Instagram konten {{ $index + 1 }}">
                            <iframe src="https://www.instagram.com/p/{{ $post['code'] }}/embed/"
                                title="Instagram konten {{ $index + 1 }}" loading="lazy"
                                allowtransparency="true">
                            </iframe>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
    </section>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/welcome.js') }}" defer></script>
@endpush
