@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
    <style>
        .profile-hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/image/background.png') center 35%/cover no-repeat;
            color: #fff;
            padding: 48px 0;
            min-height: 160px;
            display: flex;
            align-items: center;
        }

        .profile-hero h1 {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .profile-section {
            padding: 36px 0;
            background: transparent !important;
        }

        .profile-section-bg {
            background: #f8f9fa;
        }

        .profile-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .profile-section-subtitle {
            font-size: 1.1rem;
            color: #174E93;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .profile-section-desc {
            color: #475569;
            line-height: 1.75;
            font-size: 1rem;
            width: 100%;
            margin-bottom: 1.5rem;
            padding: 0;
        }

        @media (min-width: 640px) {
            .profile-section-desc {
                padding: 0;
            }
        }

        @media (min-width: 1024px) {
            .profile-section-desc {
                padding: 0;
            }
        }

        .profile-section-desc p,
        .profile-section-desc h1,
        .profile-section-desc h2,
        .profile-section-desc h3,
        .profile-section-desc h4,
        .profile-section-desc h5,
        .profile-section-desc h6,
        .profile-section-desc ul,
        .profile-section-desc ol,
        .profile-section-desc table,
        .profile-section-desc blockquote {
            width: 100%;
        }

        .profile-section-desc table {
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .profile-section-desc table td,
        .profile-section-desc table th {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
        }

        .profile-section-desc img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }

        .page-image {
            width: 100%;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .page-layout-dual {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 768px) {
            .page-layout-dual {
                grid-template-columns: 1fr;
            }
        }

        .page-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #174E93;
            color: white;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 0.9rem;
        }

        .page-link-btn:hover {
            background: #1e40af;
        }

        .struktur-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .struktur-layout {
                grid-template-columns: 1fr auto;
                align-items: start;
            }
        }

        .struktur-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .struktur-logo {
            max-width: 120px;
            height: auto;
            object-fit: contain;
        }

        .struktur-image {
            width: 100%;
            max-width: 500px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .sdm-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .sdm-layout {
                grid-template-columns: 1fr 1fr;
            }
        }

        .chart-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chart-card h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            text-align: center;
        }

        .chart-card canvas {
            max-height: 250px;
        }

        .profile-divider {
            border: 0;
            height: 1px;
            background: #e8ecef;
            margin: 0;
        }

        .section-block {
            margin-bottom: 1.5rem;
        }

        .section-block:last-child {
            margin-bottom: 0;
        }

        .section-block h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .section-block p {
            color: #475569;
            line-height: 1.75;
            margin-bottom: 0.75rem;
        }

        .section-images {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .section-images img {
            max-width: 300px;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            object-fit: cover;
        }

        .page-nav {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .page-nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
        }

        .page-nav-btn:hover {
            border-color: #174E93;
            color: #174E93;
        }

        .page-nav-btn.active {
            background: #174E93;
            border-color: #174E93;
            color: white;
        }

        .page-image-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .page-image-container > div {
            background: transparent;
            box-shadow: none;
            border: none;
        }

        .page-image-container > div > img {
            background: transparent;
            box-shadow: none;
            border: none;
        }
    </style>
@endpush

@section('content')

    {{-- Breadcrumb --}}
    <div class="feature-breadcrumb">
        <div class="container">
            @if ($feature->parent)
                <a href="{{ url($feature->parent->path ?? '#') }}">
                    {{ $locale === 'en' ? $feature->parent->name_en ?? $feature->parent->name : $feature->parent->name }}
                </a>
                <span class="sep">/</span>
            @endif
            <span class="current">
                {{ $locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name }}
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="profile-hero">
        <div class="container">
            @if ($feature->parent)
                <p
                    style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                    {{ $locale === 'en' ? $feature->parent->name_en ?? $feature->parent->name : $feature->parent->name }}
                </p>
            @endif
            <h1>{{ strtoupper($locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name) }}</h1>
        </div>
    </div>

    {{-- Profile Pages --}}
    @if ($currentPage)
        @php
            $page = $currentPage;
            $pageTitle = $locale === 'en' ? $page->title_en ?? $page->title : $page->title;
            $pageDesc =
                $locale === 'en' ? $page->description_en ?? ($page->description ?? '') : $page->description ?? '';
            $chartData = $page->chart_data;
        @endphp

        {{-- ===== DEFAULT & TUGAS FUNGSI ===== --}}
        @if (in_array($page->type, ['default', 'tugas_fungsi']))
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    @if ($page->type === 'tugas_fungsi')
                        {{-- Grid layout: text left, images right --}}
                        <div class="page-layout-dual">
                            {{-- Text column --}}
                            <div>
                                @if ($pageDesc)
                                    <div class="profile-section-desc">{!! $pageDesc !!}</div>
                                @endif
                                <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                                @if ($page->sections && $page->sections->count())
                                    @foreach ($page->sections as $section)
                                        <div class="section-block">
                                            @if ($section->title)
                                                <h3>{{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                                </h3>
                                            @endif
                                            @if ($section->description)
                                                <p>{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                @if ($page->link_text && $page->link_url)
                                    <a href="{{ $page->link_url }}" class="page-link-btn" target="_blank">
                                        {{ $page->link_text }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            {{-- Images column --}}
                            @if ($page->images && count($page->images))
                                <div class="page-image-container">
                                    @foreach ($page->images as $idx => $img)
                                        @php
                                            $posData = $page->image_positions[$idx] ?? null;
                                            $width = 200;
                                            $height = 150;
                                            $offsetX = 0;
                                            $offsetY = 0;
                                            $focalPointX = 50;
                                            $focalPointY = 50;

                                            if (is_array($posData)) {
                                                $width = intval($posData['width'] ?? 200);
                                                $height = intval($posData['height'] ?? 150);
                                                $offsetX = intval($posData['offsetX'] ?? 0);
                                                $offsetY = intval($posData['offsetY'] ?? 0);
                                                if (isset($posData['position'])) {
                                                    $parts = explode(' ', $posData['position']);
                                                    $focalPointX = floatval($parts[0] ?? 50);
                                                    $focalPointY = floatval($parts[1] ?? 50);
                                                }
                                            }
                                        @endphp
                                        <div style="width: {{ $width }}px; height: {{ $height }}px; position: relative;">
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $pageTitle }}"
                                                style="width: 100%; height: 100%; object-fit: contain; object-position: {{ $focalPointX }}% {{ $focalPointY }}%; display: block; border-radius: 0.75rem;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Single column for default --}}
                        @if ($pageDesc)
                            <div class="profile-section-desc">{!! $pageDesc !!}</div>
                        @endif
                        <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                        @if ($page->link_text && $page->link_url)
                            <a href="{{ $page->link_url }}" class="page-link-btn" target="_blank">
                                {{ $page->link_text }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @endif
                        @if ($page->sections && $page->sections->count())
                            @foreach ($page->sections as $section)
                                <div class="section-block" style="margin-top: 1.5rem;">
                                    @if ($section->title)
                                        <h3>{{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                        </h3>
                                    @endif
                                    @if ($section->description)
                                        <p>{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</p>
                                    @endif
                                    @if ($section->images && count($section->images))
                                        <div class="section-images">
                                            @foreach ($section->images as $img)
                                                <img src="{{ asset('storage/' . $img) }}" alt="">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @elseif ($page->images && count($page->images))
                            <div class="section-images" style="margin-top: 1.5rem;">
                                @foreach ($page->images as $img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="">
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </section>
            <hr class="profile-divider">
        @endif

        {{-- ===== STRUKTUR IMAGE ===== --}}
        @if ($page->type === 'struktur_image')
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                    {{-- Grid layout: text left, images right --}}
                    <div class="page-layout-dual">
                        {{-- Text column --}}
                        <div>
                            @if ($pageDesc)
                                <div class="profile-section-desc">{!! $pageDesc !!}</div>
                            @endif
                            @if ($page->sections && $page->sections->count())
                                @foreach ($page->sections as $section)
                                    <div class="section-block">
                                        @if ($section->title)
                                            <h3>{{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                            </h3>
                                        @endif
                                        @if ($section->description)
                                            <p>{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</p>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Images column --}}
                        @if ($page->images && count($page->images))
                            <div class="page-image-container">
                                @foreach ($page->images as $idx => $img)
                                    @php
                                        $posData = $page->image_positions[$idx] ?? null;
                                        $width = 200;
                                        $height = 150;
                                        $focalPointX = 50;
                                        $focalPointY = 50;

                                        if (is_array($posData)) {
                                            $width = intval($posData['width'] ?? 200);
                                            $height = intval($posData['height'] ?? 150);
                                            if (isset($posData['position'])) {
                                                $parts = explode(' ', $posData['position']);
                                                $focalPointX = floatval($parts[0] ?? 50);
                                                $focalPointY = floatval($parts[1] ?? 50);
                                            }
                                        }
                                    @endphp
                                    <div style="width: {{ $width }}px; height: {{ $height }}px; position: relative;">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $pageTitle }}"
                                            style="width: 100%; height: 100%; object-fit: contain; object-position: {{ $focalPointX }}% {{ $focalPointY }}%; display: block; border-radius: 0.75rem;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    {{-- Struktur bottom: logo + section images stacked vertically --}}
                    @if ($page->logo_path || ($page->sections && $page->sections->count()))
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-top: 1.5rem;">
                            @if ($page->logo_path)
                                <img src="{{ asset('storage/' . $page->logo_path) }}" alt="Logo" style="max-width: 120px; height: auto; object-fit: contain;">
                            @endif
                            @if ($page->sections && $page->sections->count())
                                @foreach ($page->sections as $section)
                                    @if ($section->images && count($section->images))
                                        @foreach ($section->images as $img)
                                            <img src="{{ asset('storage/' . $img) }}" alt="Struktur"
                                                style="width: 100%; max-width: 500px; border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); object-fit: contain;">
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </section>
            <hr class="profile-divider">
        @endif

        {{-- ===== SDM CHART ===== --}}
        @if ($page->type === 'sdm_chart')
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    <div style="text-align:center;margin-bottom:2rem">
                        <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                        @if ($page->subtitle)
                            <p class="profile-section-subtitle">
                                {{ $locale === 'en' ? $page->subtitle_en ?? $page->subtitle : $page->subtitle }}
                            </p>
                        @endif
                        @if ($pageDesc)
                            <div class="profile-section-desc" style="max-width:48rem;margin:0 auto;text-align:center">
                                {!! $pageDesc !!}
                            </div>
                        @endif
                    </div>

                    @if ($chartData && is_array($chartData) && count($chartData) > 0)
                        <div class="sdm-layout" style="max-width:64rem;margin:0 auto;" id="sdm-charts-container">
                            @php
                                $chartIndex = 0;
                            @endphp
                            @foreach ($chartData as $chartKey => $chart)
                                @if (isset($chart['labels']) && is_array($chart['labels']) && count($chart['labels']) > 0)
                                    <div class="chart-card" data-chart-key="{{ $chartKey }}"
                                        data-chart-type="{{ $chart['type'] ?? 'bar' }}">
                                        <h4>{{ $chart['title'] ?? ucwords(str_replace('_', ' ', $chart['field'] ?? $chartKey)) }}
                                        </h4>
                                        <div style="height:250px;position:relative">
                                            <canvas id="chart-{{ $page->id }}-{{ $chartIndex }}"></canvas>
                                        </div>
                                    </div>
                                    @php
                                        $chartIndex++;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Bottom Page Navigation --}}
        @if ($totalPages > 1)
            <section class="profile-section" style="padding-top: 0;">
                <div class="container">
                    <div class="page-nav">
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <a href="{{ request()->url() }}?page={{ $i }}"
                                class="page-nav-btn{{ $currentPageIndex + 1 === $i ? ' active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="profile-section">
            <div class="container text-center py-16">
                <p class="text-gray-400">{{ __('cms.profile.public_empty') }}</p>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
    @if ($currentPage && $currentPage->type === 'sdm_chart' && $currentPage->chart_data)
        @php
            $chartData = $currentPage->chart_data;
            $chartColors = [
                '#3B82F6',
                '#06B6D4',
                '#10B981',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6',
                '#EC4899',
                '#14B8A6',
                '#F97316',
                '#6366F1',
            ];
            $chartDataJson = json_encode($chartData);
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = {!! $chartDataJson !!};
                const chartColors = ['#3B82F6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899',
                    '#14B8A6', '#F97316', '#6366F1'
                ];
                const pageId = '{{ $currentPage->id }}';

                let chartIndex = 0;
                Object.keys(chartData).forEach(function(key) {
                    const chart = chartData[key];
                    if (!chart.labels || chart.labels.length === 0) return;

                    const canvasId = 'chart-' + pageId + '-' + chartIndex;
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const isPie = chart.type === 'pie';
                    const colors = chart.colors || chartColors.slice(0, chart.labels.length);

                    if (isPie) {
                        new Chart(canvas.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: chart.labels,
                                datasets: [{
                                    data: chart.data,
                                    backgroundColor: colors,
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                size: 12
                                            },
                                            padding: 12
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'white',
                                        titleColor: '#1f2937',
                                        bodyColor: '#4b5563',
                                        borderColor: '#e5e7eb',
                                        borderWidth: 1
                                    }
                                }
                            }
                        });
                    } else {
                        new Chart(canvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: chart.labels,
                                datasets: [{
                                    label: 'Jumlah',
                                    data: chart.data,
                                    backgroundColor: colors,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'white',
                                        titleColor: '#1f2937',
                                        bodyColor: '#4b5563',
                                        borderColor: '#e5e7eb',
                                        borderWidth: 1
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1,
                                            font: {
                                                size: 11
                                            },
                                            color: '#6b7280'
                                        },
                                        grid: {
                                            color: '#f1f5f9'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            font: {
                                                size: 11
                                            },
                                            color: '#6b7280'
                                        },
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    }
                    chartIndex++;
                });
            });
        </script>
    @endif
@endpush
