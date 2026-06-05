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

    {{-- Table of Contents Runtime — isolated so unrelated errors above can't stop it from binding --}}
    <script>
        window.addEventListener('load', function() {
            // Table of Contents guest-side smooth scroll navigation
            function assignGuestAnchorIds() {
                var contentRoots = document.querySelectorAll('.rte-content, .rte-content-body, .profile-section-desc, .vsshow-section-desc, .richtext-guest-view');
                contentRoots.forEach(function(root) {
                    function slugify(text) {
                        var s = (text || '').toString().toLowerCase();
                        s = s.replace(/[^a-z0-9\s\-_]/g, '');
                        s = s.replace(/[\s_]+/g, '-');
                        s = s.replace(/-+/g, '-').replace(/^-+|-+$/g, '');
                        return s || 'section';
                    }
                    var nodes = root.querySelectorAll('h1, h2, h3, h4, h5, h6, p, blockquote, li, div, td, th');
                    var usedIds = {};
                    // Seed with every anchor already present in the saved HTML so that
                    // re-running this never renames an existing anchor (which would break
                    // the TOC links that point at those exact ids).
                    for (var s = 0; s < nodes.length; s++) {
                        var existing = nodes[s].getAttribute('data-rte-anchor');
                        if (existing) usedIds[existing] = true;
                    }
                    for (var i = 0; i < nodes.length; i++) {
                        var n = nodes[i];
                        if (n.closest && n.closest('.rte-toc-block')) continue;
                        if (n.querySelector('h1, h2, h3, h4, h5, h6, p, blockquote, li')) continue;

                        // Never touch a node that already carries an anchor — keep it stable.
                        if (n.getAttribute('data-rte-anchor')) {
                            if (!n.id) n.setAttribute('id', n.getAttribute('data-rte-anchor'));
                            continue;
                        }

                        var text = (n.textContent || '').trim();
                        if (!text) continue;
                        var baseId = slugify(text);

                        var finalId = baseId;
                        var counter = 1;
                        while (usedIds[finalId]) {
                            finalId = baseId + '-' + (i + 1) + '-' + counter;
                            counter++;
                        }
                        usedIds[finalId] = true;

                        n.setAttribute('data-rte-anchor', finalId);
                        n.setAttribute('id', finalId);
                    }
                });
            }
            
            // Assign anchors initially
            assignGuestAnchorIds();

            var lastGuestTocScrollTime = 0;
            var handleGuestTocClick = function(e) {
                var el = e.target;
                if (el && el.nodeType === 3) el = el.parentNode;
                var a = el ? (el.closest('.rte-toc-item a') || el.closest('a.rte-toc-item') || el.closest('.rte-toc-block a')) : null;
                if (a) {
                    e.preventDefault();
                    e.stopPropagation();
                    var now = Date.now();
                    if (now - lastGuestTocScrollTime < 100) return;
                    lastGuestTocScrollTime = now;
                    assignGuestAnchorIds(); // Ensure missing IDs are populated before lookup
                    var targetId = a.getAttribute('data-rte-target') || a.getAttribute('href');
                    if (targetId) {
                        var cleanId = targetId;
                        var hashIndex = cleanId.indexOf('#');
                        if (hashIndex !== -1) {
                            cleanId = cleanId.substring(hashIndex + 1);
                        } else if (cleanId.indexOf('http') === 0) {
                            cleanId = ''; // Not a hash link
                        } else {
                            cleanId = cleanId.replace(/^#/, '');
                        }
                        if (!cleanId) return;
                        var contentRoot = document;
                        var heading = null;
                        try {
                            heading = document.getElementById(cleanId);
                        } catch (e) {}
                        if (!heading) {
                            try {
                                heading = contentRoot.querySelector('[id="' + cleanId.replace(/"/g, '\\"') + '"]');
                            } catch (err) {}
                        }
                        if (!heading) {
                            try {
                                heading = contentRoot.querySelector('[data-rte-anchor="' + cleanId.replace(/"/g, '\\"') + '"]');
                            } catch (err) {}
                        }
                        if (!heading) {
                            var cleanLower = cleanId.toLowerCase().replace(/[^a-z0-9]/g, '');
                            var allNodes = contentRoot.querySelectorAll('*');
                            for (var ni = 0; ni < allNodes.length; ni++) {
                                var elNode = allNodes[ni];
                                var nodeAnchor = elNode.getAttribute('data-rte-anchor');
                                var nodeId = elNode.id;
                                if ((nodeAnchor && nodeAnchor.toLowerCase().replace(/[^a-z0-9]/g, '') === cleanLower) || 
                                    (nodeId && nodeId.toLowerCase().replace(/[^a-z0-9]/g, '') === cleanLower)) {
                                    heading = elNode;
                                    break;
                                }
                            }
                        }
                        if (!heading) {
                            var cleanLower = cleanId.toLowerCase().replace(/[^a-z0-9]/g, '');
                            var rootTarget = cleanLower.replace(/\d+$/, '');
                            var allCandidates = contentRoot.querySelectorAll('[data-rte-anchor]');
                            for (var ci = 0; ci < allCandidates.length; ci++) {
                                var candId = allCandidates[ci].getAttribute('data-rte-anchor').toLowerCase().replace(/[^a-z0-9]/g, '');
                                var candRoot = candId.replace(/\d+$/, '');
                                if (candRoot === rootTarget || candId === rootTarget || candId.indexOf(rootTarget) === 0 || rootTarget.indexOf(candRoot) === 0) {
                                    heading = allCandidates[ci];
                                    break;
                                }
                            }
                        }
                        if (!heading) {
                            var linkText = (a.textContent || '').trim();
                            if (linkText) {
                                var normalizedText = linkText.toLowerCase().replace(/[^a-z0-9]/g, '');
                                
                                // 1. Exact match on standard headings
                                var headings = contentRoot.querySelectorAll('h1, h2, h3, h4, h5, h6');
                                for (var i = 0; i < headings.length; i++) {
                                    if (headings[i].closest && headings[i].closest('.rte-toc-block')) continue;
                                    var txt = (headings[i].textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                    if (txt === normalizedText) {
                                        heading = headings[i];
                                        break;
                                    }
                                }
                                // 2. Exact match on standard text blocks
                                if (!heading) {
                                    var textBlocks = contentRoot.querySelectorAll('p, blockquote, li, td, th, div');
                                    for (var i = 0; i < textBlocks.length; i++) {
                                        var elNode = textBlocks[i];
                                        if (elNode.closest && elNode.closest('.rte-toc-block')) continue;
                                        if (elNode.tagName === 'DIV' && elNode.querySelector('p, h1, h2, h3, h4, h5, h6, li, blockquote')) continue; // Ensure we don't match parent divs
                                        var txt = (elNode.textContent || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                                        if (txt === normalizedText) {
                                            heading = elNode;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        if (!heading) {
                            var tocItem = a.closest('.rte-toc-item');
                            if (tocItem) {
                                var tocIndexAttr = tocItem.getAttribute('data-rte-toc-index');
                                var tocIndex = tocIndexAttr ? parseInt(tocIndexAttr, 10) - 1 : -1;
                                if (tocIndex === -1) {
                                    var siblings = Array.prototype.slice.call(tocItem.parentNode.children);
                                    tocIndex = siblings.indexOf(tocItem);
                                }
                                if (tocIndex >= 0) {
                                    var candidates = Array.prototype.slice.call(contentRoot.querySelectorAll('h1, h2, h3, h4, h5, h6, p, blockquote, li, div, td, th'));
                                    candidates = candidates.filter(function(el2) {
                                        if (el2.closest && el2.closest('.rte-toc-block')) return false;
                                        if (el2.querySelector('h1, h2, h3, h4, h5, h6, p, blockquote, li')) return false;
                                        var text = (el2.textContent || '').trim();
                                        if (!text || text.length < 2) return false;
                                        return true;
                                    });
                                    if (tocIndex < candidates.length) {
                                        heading = candidates[tocIndex];
                                    }
                                }
                            }
                        }
                        if (heading && cleanId) {
                            if (!heading.id) heading.id = cleanId;
                            if (!heading.getAttribute('data-rte-anchor')) heading.setAttribute('data-rte-anchor', cleanId);
                        }
                        if (heading) {
                            document.querySelectorAll('.rte-toc-highlight-target').forEach(function(el) {
                                el.classList.remove('rte-toc-highlight-target');
                            });
                            
                            heading.style.scrollMarginTop = '130px';
                            heading.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                            
                            heading.classList.add('rte-toc-highlight-target');
                            heading.querySelectorAll('*').forEach(function(child) {
                                child.classList.add('rte-toc-highlight-target');
                            });
                            
                            heading.setAttribute('tabindex', '-1');
                            heading.focus();

                            // Directly highlight the exact target heading element via Selection API
                            // Run at multiple intervals to ensure it persists during smooth scrolling & focus shifts.
                            function applyHighlightSelection() {
                                try {
                                    var doc = heading.ownerDocument || document;
                                    var win = doc.defaultView || window;
                                    var range = doc.createRange();
                                    var textNode = null;
                                    var walk = doc.createTreeWalker(heading, NodeFilter.SHOW_TEXT, null, false);
                                    var next = walk.nextNode();
                                    while (next) {
                                        if (next.textContent.trim()) {
                                            textNode = next;
                                            break;
                                        }
                                        next = walk.nextNode();
                                    }
                                    if (textNode) {
                                        var parent = textNode.parentNode;
                                        if (parent && (parent.tagName === 'A' || parent.closest('a'))) {
                                            range.selectNodeContents(parent.closest('a') || parent);
                                        } else {
                                            range.setStart(textNode, 0);
                                            range.setEnd(textNode, textNode.textContent.length);
                                        }
                                    } else {
                                        range.selectNodeContents(heading);
                                    }
                                    var sel = win.getSelection();
                                    sel.removeAllRanges();
                                    sel.addRange(range);
                                } catch (e) { }
                            }
                            applyHighlightSelection();
                            setTimeout(applyHighlightSelection, 50);
                            setTimeout(applyHighlightSelection, 200);
                            setTimeout(applyHighlightSelection, 500);
                            setTimeout(applyHighlightSelection, 800);

                            setTimeout(function() {
                                heading.classList.remove('rte-toc-highlight-target');
                                heading.querySelectorAll('*').forEach(function(child) {
                                    child.classList.remove('rte-toc-highlight-target');
                                });
                                heading.removeAttribute('tabindex');
                                // Clear selection highlight after animation finishes
                                try {
                                    window.getSelection().removeAllRanges();
                                } catch (e) { }
                            }, 2500);
                        }
                    }
                }
            };
            document.addEventListener('click', handleGuestTocClick);
            document.addEventListener('mousedown', handleGuestTocClick);
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
    
    {{-- Search Block Runtime — makes .rte-search-block interactive on guest pages --}}
    <script>
        window.addEventListener('load', function () {
            // ── Initialize all search blocks on the page ──
            var searchBlocks = document.querySelectorAll('.rte-search-block');
            searchBlocks.forEach(function (block) {
                initSearchBlock(block);
            });

            function initSearchBlock(block) {
                var input = block.querySelector('.rte-search-block-input');
                var submitBtn = block.querySelector('.rte-search-block-submit');
                var prevBtn = block.querySelector('.rte-search-block-prev');
                var nextBtn = block.querySelector('.rte-search-block-next');
                var clearBtn = block.querySelector('.rte-search-block-clear');
                var status = block.querySelector('.rte-search-block-status');

                if (!input || !submitBtn) return;

                var currentMatches = [];
                var currentIndex = -1;

                // ── Determine the search scope: the closest content container ──
                function getSearchRoot() {
                    var root = block.closest('.rte-content, .rte-content-body, .profile-section-desc, .vsshow-section-desc, .richtext-guest-view');
                    return root || document.body;
                }

                // ── Walk text nodes and collect matches ──
                function collectTextNodes(root) {
                    var list = [];
                    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
                        acceptNode: function (node) {
                            if (block.contains(node)) return NodeFilter.FILTER_REJECT;
                            if (node.parentElement && (node.parentElement.closest('.rte-toc-block') || node.parentElement.closest('.rte-search-block'))) {
                                return NodeFilter.FILTER_REJECT;
                            }
                            if (node.parentElement && (node.parentElement.tagName === 'SCRIPT' || node.parentElement.tagName === 'STYLE' || node.parentElement.tagName === 'TEXTAREA')) {
                                return NodeFilter.FILTER_REJECT;
                            }
                            return NodeFilter.FILTER_ACCEPT;
                        }
                    }, false);
                    while (walker.nextNode()) {
                        list.push(walker.currentNode);
                    }
                    return list;
                }

                function doSearch() {
                    var q = input.value.trim();
                    doClear();
                    if (!q) return;

                    var root = getSearchRoot();
                    var textNodes = collectTextNodes(root);
                    var regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');

                    var matchesToHighlight = [];

                    textNodes.forEach(function (node) {
                        var val = node.nodeValue;
                        if (regex.test(val)) {
                            matchesToHighlight.push(node);
                        }
                    });

                    matchesToHighlight.forEach(function (node) {
                        var val = node.nodeValue;
                        var parent = node.parentNode;
                        if (!parent) return;

                        var frag = document.createDocumentFragment();
                        var parts = val.split(regex);
                        parts.forEach(function (part) {
                            if (part.toLowerCase() === q.toLowerCase()) {
                                var mark = document.createElement('mark');
                                mark.className = 'rte-search-highlight';
                                mark.textContent = part;
                                frag.appendChild(mark);
                                currentMatches.push(mark);
                            } else {
                                frag.appendChild(document.createTextNode(part));
                            }
                        });
                        parent.replaceChild(frag, node);
                    });

                    if (currentMatches.length > 0) {
                        currentIndex = 0;
                        highlightActiveMatch();
                        updateStatus();
                    } else {
                        if (status) status.textContent = 'Tidak ditemukan hasil.';
                    }
                }

                function highlightActiveMatch() {
                    currentMatches.forEach(function (m, idx) {
                        if (idx === currentIndex) {
                            m.classList.add('rte-search-highlight-active');
                            // Scroll match into view safely
                            m.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            m.classList.remove('rte-search-highlight-active');
                        }
                    });
                }

                function updateStatus() {
                    if (status) {
                        status.textContent = 'Hasil ' + (currentIndex + 1) + ' dari ' + currentMatches.length;
                    }
                }

                function goNext() {
                    if (currentMatches.length === 0) return;
                    currentIndex = (currentIndex + 1) % currentMatches.length;
                    highlightActiveMatch();
                    updateStatus();
                }

                function goPrev() {
                    if (currentMatches.length === 0) return;
                    currentIndex = (currentIndex - 1 + currentMatches.length) % currentMatches.length;
                    highlightActiveMatch();
                    updateStatus();
                }

                function doClear() {
                    currentMatches = [];
                    currentIndex = -1;
                    if (status) status.textContent = '';
                    
                    var root = getSearchRoot();
                    var marks = root.querySelectorAll('mark.rte-search-highlight');
                    marks.forEach(function (mark) {
                        var parent = mark.parentNode;
                        if (parent) {
                            parent.replaceChild(document.createTextNode(mark.textContent), mark);
                            parent.normalize();
                        }
                    });
                }

                submitBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    doSearch();
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        doSearch();
                    }
                });

                if (prevBtn) {
                    prevBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        goPrev();
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        goNext();
                    });
                }

                if (clearBtn) {
                    clearBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = '';
                        doClear();
                    });
                }
            }
        });
    </script>
</body>

</html>