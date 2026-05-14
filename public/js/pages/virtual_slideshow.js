/**
 * virtual_slideshow.js — Pameran Arsip Virtual SlideShow
 * Handles: scroll animations, carousels, video embeds, info popups
 */

(function () {
    'use strict';

    /* ========================================================
       1. SCROLL PROGRESS BAR
    ======================================================== */
    const progressBar = document.getElementById('vss-progress');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const total    = document.documentElement.scrollHeight - window.innerHeight;
            progressBar.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
        }, { passive: true });
    }

    /* ========================================================
       2. SCROLL ANIMATIONS (IntersectionObserver)
       Unified system: .vsshow-enter + data-swipe + data-enter-delay
    ======================================================== */
    const animEls = document.querySelectorAll('.vsshow-enter, .vsshow-hero-anim');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.classList.add('vsshow-visible');
            } else {
                // Remove to allow re-animation when scrolling back
                entry.target.classList.remove('vsshow-visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    animEls.forEach(el => observer.observe(el));

    /* ========================================================
       3. CAROUSEL
    ======================================================== */
    document.querySelectorAll('.vsshow-carousel').forEach(carousel => {
        const track  = carousel.querySelector('.vsshow-carousel-track');
        const slides = carousel.querySelectorAll('.vsshow-carousel-slide');
        const dots   = carousel.querySelectorAll('.vsshow-dot');
        const prevBtn = carousel.querySelector('.vsshow-carousel-btn.prev');
        const nextBtn = carousel.querySelector('.vsshow-carousel-btn.next');
        const pauseBtn = carousel.querySelector('.vsshow-carousel-btn.pause-play');
        let current = 0;
        let autoTimer = null;
        let isPlaying = true;

        function goTo(idx) {
            current = (idx + slides.length) % slides.length;
            track.style.transform = `translateX(-${current * 100}%)`;
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
        }

        function togglePause() {
            if (isPlaying) {
                clearInterval(autoTimer);
                isPlaying = false;
                if (pauseBtn) {
                    const pauseIcon = pauseBtn.querySelector('.pause-icon');
                    const playIcon = pauseBtn.querySelector('.play-icon');
                    if (pauseIcon) pauseIcon.style.display = 'none';
                    if (playIcon) playIcon.style.display = 'block';
                }
            } else {
                startAuto();
                isPlaying = true;
                if (pauseBtn) {
                    const pauseIcon = pauseBtn.querySelector('.pause-icon');
                    const playIcon = pauseBtn.querySelector('.play-icon');
                    if (pauseIcon) pauseIcon.style.display = 'block';
                    if (playIcon) playIcon.style.display = 'none';
                }
            }
        }

        if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetAuto(); });
        if (pauseBtn) pauseBtn.addEventListener('click', togglePause);

        dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); resetAuto(); }));

        // Touch/swipe
        let touchStartX = 0;
        carousel.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        carousel.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) { diff > 0 ? goTo(current + 1) : goTo(current - 1); resetAuto(); }
        }, { passive: true });

        // Keyboard
        carousel.setAttribute('tabindex', '0');
        carousel.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft')  { goTo(current - 1); resetAuto(); }
            if (e.key === 'ArrowRight') { goTo(current + 1); resetAuto(); }
            if (e.key === ' ') { e.preventDefault(); togglePause(); }
        });

        // Auto-play (4 sec) only if more than 1 slide
        function startAuto() {
            if (slides.length <= 1) return;
            clearInterval(autoTimer);
            autoTimer = setInterval(() => goTo(current + 1), 4000);
        }

        function resetAuto() {
            clearInterval(autoTimer);
            if (isPlaying) {
                startAuto();
            }
        }

        // Visibility-aware auto-play
        const carouselObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (isPlaying) startAuto();
                } else {
                    clearInterval(autoTimer);
                }
            });
        }, { threshold: 0.1 });

        carouselObserver.observe(carousel);

        // Individual tab visibility sync
        const handleVisibility = () => {
            if (document.hidden) clearInterval(autoTimer);
            else if (isPlaying && carousel.getBoundingClientRect().top < window.innerHeight) {
                startAuto();
            }
        };
        document.addEventListener('visibilitychange', handleVisibility);

        goTo(0);
    });

    /* ========================================================
       4. INFO POPUP
    ======================================================== */
    let popupOverlay  = document.getElementById('vss-popup-overlay');
    let popupCard     = document.getElementById('vss-popup-card');
    let popupBody     = document.getElementById('vss-popup-body');
    let popupText     = document.getElementById('vss-popup-text');
    let popupImgEl    = document.getElementById('vss-popup-img');
    let popupCloseBtn = document.getElementById('vss-popup-close');

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function renderQaAccordion(items) {
        var html = '<div class="vsshow-qa-list">';
        items.forEach(function(item, idx) {
            html += '<div class="vsshow-qa-item">' +
                '<button type="button" class="vsshow-qa-question" data-qa-idx="' + idx + '">' +
                    '<span>' + escapeHtml(item.question || '') + '</span>' +
                    '<svg class="vsshow-qa-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>' +
                    '</svg>' +
                '</button>' +
                '<div class="vsshow-qa-answer">' + (item.answer || '') + '</div>' +
            '</div>';
        });
        html += '</div>';
        return html;
    }

    function showPopup(text, imgSrc) {
        if (!popupCard || !popupOverlay) return;

        // Try to parse as JSON for multi Q&A mode
        var parsed = null;
        if (text && text.charAt(0) === '{') {
            try { parsed = JSON.parse(text); } catch(e) { parsed = null; }
        }

        if (parsed && parsed.type === 'multi' && Array.isArray(parsed.items) && parsed.items.length > 0) {
            popupText.innerHTML = renderQaAccordion(parsed.items);
            // Bind accordion toggle
            popupText.querySelectorAll('.vsshow-qa-question').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var qaItem = btn.closest('.vsshow-qa-item');
                    qaItem.classList.toggle('open');
                });
            });
        } else {
            popupText.innerHTML = text || '';
        }

        if (imgSrc && popupImgEl) {
            popupImgEl.src = imgSrc;
            popupImgEl.style.display = 'block';
        } else if (popupImgEl) {
            popupImgEl.style.display = 'none';
        }

        popupOverlay.classList.add('active');
        // Trigger CSS transition
        requestAnimationFrame(() => {
            popupCard.style.display = 'block';
            requestAnimationFrame(() => popupCard.classList.add('active'));
        });
        document.body.style.overflow = 'hidden';
    }

    function hidePopup() {
        if (!popupCard || !popupOverlay) return;
        popupCard.classList.remove('active');
        popupOverlay.classList.remove('active');
        setTimeout(() => {
            popupCard.style.display = 'none';
            if (popupText) popupText.innerHTML = '';
        }, 300);
        document.body.style.overflow = '';
    }

    // Bind all info buttons
    document.querySelectorAll('.vsshow-info-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const text   = btn.dataset.popup || '';
            const imgSrc = btn.dataset.imgSrc || '';
            showPopup(text, imgSrc);
        });
    });

    if (popupCloseBtn) popupCloseBtn.addEventListener('click', hidePopup);
    if (popupOverlay)  popupOverlay.addEventListener('click', hidePopup);

    // ESC to close
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hidePopup(); });

    /* ========================================================
       5. HERO PARTICLES
    ======================================================== */
    const particleContainer = document.getElementById('vss-particles');
    if (particleContainer) {
        const sizes  = [40, 60, 80, 100, 130, 180];
        const count  = 12;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('div');
            p.className = 'vsshow-hero-particle';
            const size = sizes[Math.floor(Math.random() * sizes.length)];
            p.style.cssText = `
                width: ${size}px; height: ${size}px;
                left: ${Math.random() * 100}%;
                bottom: -${size}px;
                animation-duration: ${10 + Math.random() * 20}s;
                animation-delay: ${Math.random() * 15}s;
                opacity: ${0.04 + Math.random() * 0.06};
            `;
            particleContainer.appendChild(p);
        }
    }

    /* ========================================================
       6. VIDEO — Lazy load (play when visible)
    ======================================================== */
    const videoWrappers = document.querySelectorAll('.vsshow-video-iframe-wrap[data-src]');
    if (videoWrappers.length > 0) {
        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const wrap = entry.target;
                    const iframe = wrap.querySelector('iframe[data-src]');
                    if (iframe && iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                    }
                    videoObserver.unobserve(wrap);
                }
            });
        }, { threshold: 0.3 });

        videoWrappers.forEach(w => videoObserver.observe(w));
    }

})();
