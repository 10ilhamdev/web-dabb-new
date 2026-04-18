{{--
    Partial: Login required modal for protected public pages (/pameran/virtual/*).
    Variables expected from controller:
      $feature            – current Feature model
      $loginModalPreviews – array of preview image URLs for carousel (nullable)
      $loginModalPreview  – fallback single preview URL for single item (nullable)
      $loginModalRoomNames – array of room/page names for carousel (nullable)
      $loginModalRoomName – fallback single room name (nullable)
--}}
<div id="loginRequiredModal"
    style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:1rem;"
    aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);"></div>

    {{-- Card — compact size --}}
    <div
        style="position:relative;background:#fff;border-radius:1.5rem;width:100%;max-width:720px;
                display:flex;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.3);min-height:400px;">

        {{-- ─── Left: Login Form ────────────────────────────────── --}}
        <div
            style="flex:1;padding:2.25rem 2.5rem;display:flex;flex-direction:column;justify-content:center;min-width:0;">

            <h2 style="color:#6c757d;font-size:1.15rem;font-weight:800;margin:0 0 0.75rem;">{{ __('auth.welcome') }}</h2>
            <h3 style="color:#212529;font-size:1.05rem;font-weight:700;margin:0 0 0.25rem;">{{ __('auth.login') }}</h3>
            <p style="color:#6c757d;font-size:0.8rem;margin:0 0 1.1rem;">{{ __('auth.login_subtitle') }}</p>

            @if (session('status'))
                <div
                    style="padding:8px 12px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:6px;font-size:12px;color:#065f46;margin-bottom:0.75rem;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->current() }}">

                {{-- Email --}}
                <div style="margin-bottom:0.85rem;">
                    <label for="lrm_email"
                        style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:0.3rem;">{{ __('Email') }}</label>
                    <input id="lrm_email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:7px;font-size:0.85rem;box-sizing:border-box;font-family:inherit;transition:border-color 0.2s,box-shadow 0.2s;outline:none;"
                        onfocus="this.style.borderColor='#0579cb';this.style.boxShadow='0 0 0 3px rgba(5,121,203,0.12)';"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none';">
                    @error('email')
                        <p style="color:#dc2626;font-size:11px;margin:3px 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div style="margin-bottom:0.6rem;">
                    <label for="lrm_password"
                        style="display:block;font-size:0.8rem;font-weight:600;color:#374151;margin-bottom:0.3rem;">{{ __('Password') }}</label>
                    <input id="lrm_password" type="password" name="password" required autocomplete="current-password"
                        style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:7px;font-size:0.85rem;box-sizing:border-box;font-family:inherit;transition:border-color 0.2s,box-shadow 0.2s;outline:none;"
                        onfocus="this.style.borderColor='#0579cb';this.style.boxShadow='0 0 0 3px rgba(5,121,203,0.12)';"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none';">
                    @error('password')
                        <p style="color:#dc2626;font-size:11px;margin:3px 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        style="color:#3598db;font-size:12px;text-decoration:none;display:inline-block;margin-bottom:1rem;">
                        {{ __('auth.forgot_password') }}
                    </a>
                @endif

                {{-- Submit --}}
                <button type="submit"
                    style="display:block;width:100%;background:#0579cb;color:#fff;border:none;padding:10px;border-radius:7px;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:1rem;box-shadow:0 3px 8px rgba(5,121,203,0.3);transition:background 0.2s,transform 0.15s;"
                    onmouseover="this.style.background='#0466ac';this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.background='#0579cb';this.style.transform='none';">
                    {{ __('auth.submit') }}
                </button>

                {{-- Divider --}}
                <div
                    style="display:flex;align-items:center;gap:0.5rem;color:#9ca3af;font-size:12px;margin-bottom:1rem;">
                    <span style="flex:1;height:1px;background:#e5e7eb;"></span>{{ __('auth.or') }}<span
                        style="flex:1;height:1px;background:#e5e7eb;"></span>
                </div>

                {{-- Google --}}
                <a href="#"
                    style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;background:#fff;color:#374151;border:1px solid #d1d5db;padding:9px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;transition:background 0.2s;box-sizing:border-box;"
                    onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='#fff';">
                    <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" style="height:18px;"
                        alt="G">
                    {{ __('auth.login_google') }}
                </a>

                <p style="text-align:center;margin-top:0.85rem;font-size:12px;color:#6b7280;">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}"
                        style="color:#3598db;font-weight:600;text-decoration:none;">{{ __('auth.register_link') }}</a>
                </p>
            </form>
        </div>

        {{-- ─── Right: Dynamic preview (carousel if multiple, single if one) ─── --}}
        <div
            style="flex:1;position:relative;overflow:hidden;min-height:400px;display:flex;flex-direction:column;background:#000;">

            @php
                // Membersihkan null/kosong dan me-reset index murni
                $rawPreviews = !empty($loginModalPreviews)
                    ? $loginModalPreviews
                    : ($loginModalPreview
                        ? [$loginModalPreview]
                        : []);
                $rawNames = !empty($loginModalRoomNames)
                    ? $loginModalRoomNames
                    : ($loginModalRoomName
                        ? [$loginModalRoomName]
                        : []);

                $validPreviews = [];
                $validNames = [];

                if (is_array($rawPreviews)) {
                    foreach ($rawPreviews as $key => $val) {
                        if (!empty(trim($val))) {
                            $validPreviews[] = $val;
                            $validNames[] = $rawNames[$key] ?? 'Preview';
                        }
                    }
                }

                $allPreviews = array_values($validPreviews);
                $allNames = array_values($validNames);
                $slideCount = count($allPreviews);
            @endphp

            @if ($slideCount > 0)
                <div id="lrm-carousel-wrapper" style="position:absolute;inset:0;z-index:1;">
                    @foreach ($allPreviews as $i => $preview)
                        <div class="lrm-slide"
                            style="position:absolute; inset:0; opacity: {{ $i === 0 ? '1' : '0' }}; transition: opacity 0.6s ease-in-out; z-index: {{ $i === 0 ? '2' : '1' }};">
                            <img src="{{ $preview }}" alt="Preview {{ $i }}"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    @endforeach
                </div>
                <div
                    style="position:absolute;inset:0;z-index:3;background:linear-gradient(to bottom, rgba(15,42,92,0.4) 0%, rgba(15,42,92,0.7) 100%);pointer-events:none;">
                </div>
            @else
                <div
                    style="position:absolute;inset:0;z-index:1;background:url('{{ asset('image/background_login.png') }}') center/cover no-repeat;">
                </div>
            @endif

            {{-- Content --}}
            <div
                style="position:relative;z-index:4;padding:1.75rem;display:flex;flex-direction:column;height:100%;justify-content:space-between;box-sizing:border-box;">

                <div style="display:flex;align-items:center;gap:10px;color:#fff;">
                    <img src="{{ asset('image/logo_anri.png') }}" alt="ANRI"
                        style="height:36px;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.4));">
                    <span
                        style="font-size:0.85rem;font-weight:700;text-shadow:0 1px 3px rgba(0,0,0,0.5);line-height:1.25;">{!! __('auth.banner_title') !!}</span>
                </div>

                <div style="color:#fff;">
                    <div
                        style="display:inline-block;background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.25);border-radius:999px;font-size:11px;font-weight:600;padding:4px 12px;margin-bottom:0.5rem;letter-spacing:0.04em;text-transform:uppercase;">
                        {{ $feature->parent?->name ?? 'Pameran Arsip' }}
                    </div>
                    <h4
                        style="font-size:1.15rem;font-weight:800;margin:0 0 0.3rem;text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                        {{ $feature->name }}
                    </h4>

                    @if ($slideCount > 0)
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <p id="lrm-current-name-wrapper"
                                style="font-size:0.8rem;opacity:0.9;margin:0;display:flex;align-items:center;gap:5px;font-weight:500;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                </svg>
                                <span id="lrm-current-name">{{ $allNames[0] ?? 'Preview' }}</span>
                            </p>
                            @if ($slideCount > 1)
                                <div style="display:flex;gap:4px;">
                                    {{-- PERBAIKAN: Melemparkan "this" agar JS tahu persis tombol mana yang ditekan --}}
                                    <button type="button" onclick="window.lrmPrev(this)"
                                        style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;transition:background 0.2s;backdrop-filter:blur(2px);"
                                        onmouseover="this.style.background='rgba(255,255,255,0.4)';"
                                        onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                                        <svg width="14" height="14" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button type="button" class="lrm-next-btn" onclick="window.lrmNext(this)"
                                        style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;transition:background 0.2s;backdrop-filter:blur(2px);"
                                        onmouseover="this.style.background='rgba(255,255,255,0.4)';"
                                        onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                                        <svg width="14" height="14" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        @if ($slideCount > 1)
                            <div style="display:flex;gap:6px;margin-top:10px;justify-content:center;">
                                @foreach ($allPreviews as $i => $preview)
                                    {{-- PERBAIKAN: Melemparkan "this" ke fungsi --}}
                                    <span class="lrm-dot"
                                        style="width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,{{ $i === 0 ? '1' : '0.4' }});cursor:pointer;transition:background 0.3s;box-shadow:0 1px 2px rgba(0,0,0,0.3);"
                                        onclick="window.lrmGoTo(this, {{ $i }})"></span>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p style="font-size:0.8rem;opacity:0.8;margin:0;">Masuk untuk menjelajahi ruangan virtual 360°
                        </p>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

@if ($slideCount > 1)
    <script>
        // Data Nama Ruangan diambil dari PHP
        window.lrmDataNames = {!! json_encode($allNames, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

        // FUNGSI UTAMA PINDAH SLIDE (Target Dot)
        window.lrmGoTo = function(elemenTombol, targetIndex) {
            // Mencari container modal terdekat dari tombol yang ditekan (Kebal Bug Modal Ganda)
            var modal = elemenTombol.closest('#loginRequiredModal');
            if (!modal) return;

            var slides = modal.querySelectorAll('.lrm-slide');
            var dots = modal.querySelectorAll('.lrm-dot');
            var nameEl = modal.querySelector('#lrm-current-name');

            if (slides.length === 0) return;

            // Pastikan index tidak error/blank
            if (targetIndex >= slides.length) targetIndex = 0;
            if (targetIndex < 0) targetIndex = slides.length - 1;

            // Render Opacity dan Warna
            for (var i = 0; i < slides.length; i++) {
                if (slides[i]) {
                    slides[i].style.opacity = (i === targetIndex) ? '1' : '0';
                    slides[i].style.zIndex = (i === targetIndex) ? '2' : '1';
                }
                if (dots[i]) {
                    dots[i].style.background = (i === targetIndex) ? 'rgba(255,255,255,1)' : 'rgba(255,255,255,0.4)';
                }
            }

            // Render Teks
            if (nameEl && window.lrmDataNames[targetIndex]) {
                nameEl.innerText = window.lrmDataNames[targetIndex];
            }
        };

        // FUNGSI TOMBOL KANAN (Next)
        window.lrmNext = function(elemenTombol) {
            var modal = elemenTombol.closest('#loginRequiredModal');
            if (!modal) return;
            var slides = modal.querySelectorAll('.lrm-slide');

            // Baca langsung dari layar, slide mana yang saat ini menyala (opacity 1)
            var current = 0;
            for (var i = 0; i < slides.length; i++) {
                if (slides[i].style.opacity === '1' || slides[i].style.opacity === 1) {
                    current = i;
                    break;
                }
            }

            window.lrmGoTo(elemenTombol, current + 1);
        };

        // FUNGSI TOMBOL KIRI (Prev)
        window.lrmPrev = function(elemenTombol) {
            var modal = elemenTombol.closest('#loginRequiredModal');
            if (!modal) return;
            var slides = modal.querySelectorAll('.lrm-slide');

            // Baca langsung dari layar, slide mana yang saat ini menyala
            var current = 0;
            for (var i = 0; i < slides.length; i++) {
                if (slides[i].style.opacity === '1' || slides[i].style.opacity === 1) {
                    current = i;
                    break;
                }
            }

            window.lrmGoTo(elemenTombol, current - 1);
        };

        // FUNGSI AUTOPLAY 5 DETIK
        if (window.lrmTimerInterval) clearInterval(window.lrmTimerInterval);
        window.lrmTimerInterval = setInterval(function() {
            var modals = document.querySelectorAll('#loginRequiredModal');
            modals.forEach(function(modal) {
                // Hanya eksekusi autoscroll pada modal yang sedang tampil/terlihat di layar pengguna
                if (modal.offsetWidth > 0 && modal.offsetHeight > 0) {
                    var nextBtn = modal.querySelector('.lrm-next-btn');
                    if (nextBtn) {
                        window.lrmNext(nextBtn); // Klik tombol next secara programatik
                    }
                }
            });
        }, 4000);
    </script>
@endif
