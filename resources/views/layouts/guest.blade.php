<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'web-dabb'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo_anri.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/logo_anri.png') }}">



    <!-- Fonts & CDN Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <!-- CMS RTE Content CSS — loads RTE content styles for guest display -->
    <link rel="stylesheet" href="{{ asset('cms_rte/rte_theme_default.css?v=' . (file_exists(public_path('cms_rte/rte_theme_default.css')) ? filemtime(public_path('cms_rte/rte_theme_default.css')) : time())) }}">
    <!-- Guest-scoped override: removes editor chrome, adapts content styles for guest layout -->
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) : time())) }}">
    <!-- Prism.js Syntax Highlighting CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
    <!-- Kill editor-only visual states on guest pages (inline beats external sheet) -->
    <style>
        td.rte-cell-selected,
        th.rte-cell-selected,
        .rte-cell-selected {
            outline: none !important;
            outline-offset: 0 !important;
            background-color: transparent !important;
            background: transparent !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body class="@yield('body-class', 'font-sans text-gray-900 antialiased flex flex-col min-h-screen')">
    @include('navbar')

    @if (isset($slot) && !$__env->yieldContent('content'))
        {{-- Component mode: used by <x-guest-layout> (confirm-password, verify-email) --}}
        <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-12">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500 mt-8" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg mb-8">
                {{ $slot }}
            </div>
        </div>
    @else
        {{-- Extends mode: used by @extends('layouts.guest') --}}
        @yield('content')
    @endif

    @include('footer')

    <x-chat-widget />

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    @if (session('success') || session('error') || $errors->any() || session('warning') || session('info'))
        @php
            $tType = 'info';
            $tMsg = session('info');

            if (session('success')) {
                $tType = 'success';
                $tMsg = session('success');
            } elseif (session('error') || $errors->any()) {
                $tType = 'error';
                $tMsg = session('error') ?? $errors->first();
            } elseif (session('warning')) {
                $tType = 'warning';
                $tMsg = session('warning');
            }
        @endphp

        {{-- SweetAlert2 Toast --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: '{{ $tType }}',
                    title: `{!! addslashes($tMsg) !!}`
                });
            });
        </script>
    @endif

    <!-- Prism.js Syntax Highlighting JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Prism) {
                try {
                    window.Prism.highlightAll();
                } catch (e) {}
            }
        });
    </script>

    @stack('scripts')

    {{-- Login required modal (shown for protected public pages when guest) --}}
    @if (!empty($requiresLoginModal))
        @include('partials.login_modal')
        <script>
            document.body.style.overflow = 'hidden';
        </script>
    @endif

    {{-- Caption Modal — floats at body level, above everything --}}
    <div id="rte-caption-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:999999;background:rgba(0,0,0,0.75);align-items:center;justify-content:center;padding:20px;box-sizing:border-box;">
        <div style="background:#1e293b;border-radius:16px;max-width:540px;width:100%;padding:52px 36px 40px;position:relative;box-shadow:0 24px 64px rgba(0,0,0,0.6);text-align:center;">
            <button id="rte-caption-modal-close" style="position:absolute;top:14px;right:14px;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.12);border:none;color:#e2e8f0;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;transition:background 0.2s;">&times;</button>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#64748b;margin-bottom:18px;">Keterangan Media</div>
            <div id="rte-caption-modal-text" style="font-size:16px;line-height:1.8;color:#e2e8f0;white-space:pre-wrap;word-break:break-word;"></div>
        </div>
    </div>

    {{-- Media Carousel Runtime Logic --}}
    <script>
        // Caption modal helpers
        var __captionModal = null;
        var __captionModalText = null;
        function __openCaptionModal(text) {
            if (!__captionModal) { __captionModal = document.getElementById('rte-caption-modal'); }
            if (!__captionModalText) { __captionModalText = document.getElementById('rte-caption-modal-text'); }
            if (__captionModal && __captionModalText) {
                __captionModalText.textContent = text;
                __captionModal.style.display = 'flex';
            }
        }
        function __closeCaptionModal() {
            if (!__captionModal) { __captionModal = document.getElementById('rte-caption-modal'); }
            if (__captionModal) { __captionModal.style.display = 'none'; }
        }
        document.getElementById('rte-caption-modal-close').onclick = function() { __closeCaptionModal(); };
        document.getElementById('rte-caption-modal').onclick = function(e) { if (e.target === this) __closeCaptionModal(); };

        window.addEventListener('load', function() {

            // ── 1. Handle OLD format: .rte-carousel-caption div inside slide ──
            document.querySelectorAll('.rte-carousel-caption').forEach(function(cap) {
                var slide = cap.parentElement;
                if (!slide || !slide.classList.contains('rte-carousel-slide')) return;
                // Extract caption text
                var captionText = (cap.textContent || '').trim();
                // Remove old caption from DOM
                cap.parentNode.removeChild(cap);
                // Only add button if not already present
                if (!slide.querySelector('.rte-carousel-caption-btn') && captionText) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'rte-carousel-caption-btn';
                    btn.title = 'Lihat Keterangan';
                    btn.textContent = '?';
                    btn.setAttribute('data-caption', captionText);
                    btn.onclick = function(e) { e.stopPropagation(); __openCaptionModal(captionText); };
                    slide.appendChild(btn);
                }
            });

            // ── 2. Handle NEW format: .rte-carousel-caption-btn already in HTML ──
            document.querySelectorAll('.rte-carousel-caption-btn').forEach(function(btn) {
                // Get caption text from data attribute or sibling popup div
                var captionText = btn.getAttribute('data-caption') || '';
                if (!captionText) {
                    var popup = btn.parentElement && btn.parentElement.querySelector('.rte-carousel-caption-popup');
                    if (popup) {
                        // Extract text from inside popup
                        var textNode = popup.querySelector('div') || popup.lastChild;
                        captionText = textNode ? (textNode.textContent || '').trim() : '';
                    }
                }
                // Remove old in-slide popup element (no longer needed)
                var oldPopup = btn.parentElement && btn.parentElement.querySelector('.rte-carousel-caption-popup');
                if (oldPopup) oldPopup.parentNode.removeChild(oldPopup);
                // Store and rebind
                var text = captionText;
                btn.setAttribute('data-caption', text);
                btn.onclick = null;
                btn.addEventListener('click', function(e) { e.stopPropagation(); __openCaptionModal(text); });
            });

            // ── 3. Carousel slide navigation ──
            var carousels = document.querySelectorAll('.rte-carousel-container');
            carousels.forEach(function(container) {
                var slides = container.querySelectorAll('.rte-carousel-slide');
                var dots = container.querySelectorAll('.rte-carousel-dot');
                var current = 0;
                var total = slides.length;
                var autoplay = container.getAttribute('data-autoplay') === 'true';
                var interval = parseInt(container.getAttribute('data-interval') || '3000', 10);
                var timer = null;

                function showSlide(idx) {
                    if (idx < 0) idx = total - 1;
                    if (idx >= total) idx = 0;
                    slides.forEach(function(s, i) {
                        if (i === idx) s.classList.add('active');
                        else s.classList.remove('active');
                    });
                    dots.forEach(function(d, i) {
                        if (i === idx) d.classList.add('active');
                        else d.classList.remove('active');
                    });
                    current = idx;
                }

                container._next = function() { showSlide(current + 1); resetTimer(); };
                container._prev = function() { showSlide(current - 1); resetTimer(); };
                container._goTo = function(idx) { showSlide(idx); resetTimer(); };

                function startTimer() {
                    if (autoplay && total > 1) {
                        timer = setInterval(function() { showSlide(current + 1); }, interval);
                    }
                }
                function resetTimer() {
                    if (timer) clearInterval(timer);
                    startTimer();
                }

                startTimer();
                
                // Allow interaction with videos
                container.querySelectorAll('video').forEach(function(v) {
                    v.addEventListener('play', function() { if(timer) clearInterval(timer); });
                });
            });

            // Upgrade code blocks to premium layout dynamically
            var pres = document.querySelectorAll('.rte-content pre, .rte-content-body pre, .profile-section-desc pre, .vsshow-section-desc pre, .richtext-guest-view pre');
            pres.forEach(function(pre) {
                if (pre.closest('.rte-code-block-container')) return;
                
                var lang = 'Plain Text';
                var classes = pre.className.split(/\s+/);
                classes.forEach(function(c) {
                    if (c.indexOf('language-') === 0) {
                        lang = c.replace('language-', '').replace('-', ' ');
                    }
                });
                
                var container = document.createElement('div');
                container.className = 'rte-code-block-container';
                container.setAttribute('contenteditable', 'false');
                
                var header = document.createElement('div');
                header.className = 'rte-code-block-header';
                header.setAttribute('contenteditable', 'false');
                
                var langLabel = document.createElement('span');
                langLabel.className = 'rte-code-block-lang';
                langLabel.textContent = lang;
                
                var copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'rte-code-block-copy-btn';
                copyBtn.textContent = 'Copy';
                copyBtn.onclick = function(e) {
                    e.stopPropagation();
                    var codeEl = container.querySelector('code') || pre;
                    if (codeEl) {
                        navigator.clipboard.writeText(codeEl.textContent).then(function() {
                            copyBtn.textContent = 'Copied!';
                            setTimeout(function() { copyBtn.textContent = 'Copy'; }, 2000);
                        });
                    }
                };
                
                header.appendChild(langLabel);
                header.appendChild(copyBtn);
                container.appendChild(header);
                
                var newPre = pre.cloneNode(true);
                newPre.removeAttribute('style');
                newPre.setAttribute('contenteditable', 'true');
                
                var nested = newPre.querySelectorAll('*');
                nested.forEach(function(el) {
                    el.style.backgroundColor = 'transparent';
                    el.style.background = 'transparent';
                });
                
                container.appendChild(newPre);
                if (pre.parentNode) {
                    pre.parentNode.replaceChild(container, pre);
                }
            });

            // Strip any leftover editor selection classes and inline styles from tables dynamically
            document.querySelectorAll('td, th, table').forEach(function(n) {
                if (n.classList.contains('rte-cell-selected')) {
                    n.classList.remove('rte-cell-selected');
                }
                var styleAttr = n.getAttribute('style') || '';
                if (styleAttr) {
                    if (styleAttr.indexOf('377dff') !== -1 || styleAttr.indexOf('55, 125, 255') !== -1 || styleAttr.indexOf('55,125,255') !== -1) {
                        n.style.outline = '';
                        n.style.outlineColor = '';
                        n.style.outlineWidth = '';
                        n.style.outlineStyle = '';
                        n.style.outlineOffset = '';
                        var bg = n.style.backgroundColor;
                        if (bg && (bg.indexOf('55, 125, 255') !== -1 || bg.indexOf('55,125,255') !== -1)) {
                            n.style.backgroundColor = '';
                        }
                    }
                }
            });
            
            // Re-trigger Prism syntax highlighting after container updates
            if (window.Prism) {
                try { window.Prism.highlightAll(); } catch(e) {}
            }
        });
    </script>

    {{-- Auto-expand RTE containers to fit Free Canvas Mode absolute media & prevent horizontal spill --}}
    <script>
        window.addEventListener('load', function() {
            var rteContainers = document.querySelectorAll('.rte-content, .rte-content-body, .profile-section-desc, .vsshow-hero-subtitle, .vsshow-section-desc');

            function getRelativeOffset(el, ancestor, prop) {
                var offset = 0;
                while (el && el !== ancestor && el !== document.body) {
                    offset += el[prop] || 0;
                    el = el.offsetParent;
                }
                return offset;
            }

            rteContainers.forEach(function(container) {
                var maxBottom = 0;
                var cw = container.clientWidth;
                var children = container.querySelectorAll('*');

                for (var i = 0; i < children.length; i++) {
                    var child = children[i];
                    var compStyle = window.getComputedStyle(child);
                    if (compStyle.position === 'absolute' && compStyle.display !== 'none') {
                        var absTop = getRelativeOffset(child, container, 'offsetTop');
                        var absLeft = getRelativeOffset(child, container, 'offsetLeft');
                        var cWidth = child.offsetWidth;
                        var cHeight = child.offsetHeight;

                        // Prevent horizontal spill
                        if (absLeft + cWidth > cw) {
                            var newLeft = Math.max(0, cw - cWidth);
                            child.style.left = newLeft + 'px';
                            // Re-calculate after adjusting left
                            if (child.offsetWidth > cw) {
                                child.style.width = '100%';
                                child.style.left = '0px';
                            }
                        }

                        var bottom = absTop + cHeight;
                        if (bottom > maxBottom) {
                            maxBottom = bottom;
                        }
                    }
                }

                if (maxBottom > 0) {
                    var currentHeight = container.offsetHeight;
                    if (maxBottom > currentHeight) {
                        container.style.minHeight = (maxBottom + 40) + 'px';
                    }
                }
            });
        });
    </script>
</body>

</html>
