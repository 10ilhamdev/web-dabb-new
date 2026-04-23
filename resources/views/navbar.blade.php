<nav class="top-nav">
    <div class="container">
        <div class="brand">
            <img src="{{ asset('image/logo_anri.png') }}" alt="ANRI">
            <div>
            <strong>{{ __('home.site_name') }}</strong>
            </div>
        </div>

        <button type="button" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" onclick="this.closest('.top-nav').classList.toggle('open'); this.setAttribute('aria-expanded', this.closest('.top-nav').classList.contains('open'));">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-right">
            <div class="menu">
                @foreach ($navFeatures as $feature)
                    @php
                        $label = app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name;
                        $isHome = $feature->path === '/' || strtolower($feature->name) === 'beranda';
                    @endphp

                    @if ($feature->type === 'dropdown' && $feature->subfeatures->count())
                        @php
                            $dropdownActive = $feature->subfeatures->contains(function ($sub) {
                                return $sub->path && request()->is(ltrim($sub->path, '/'));
                            });
                        @endphp
                        <div class="nav-dropdown">
                            <a href="#" class="nav-dropdown-toggle {{ $dropdownActive ? 'active' : '' }}">
                                {{ $label }}
                                <svg class="nav-chevron" viewBox="0 0 20 20" fill="currentColor" width="12" height="12">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <div class="nav-dropdown-menu">
                                @foreach ($feature->subfeatures as $sub)
                                    @php
                                        $subLabel = app()->getLocale() === 'en' && $sub->name_en ? $sub->name_en : $sub->name;
                                    @endphp
                                    @if ($sub->type === 'dropdown' && $sub->subfeatures->count())
                                        <div class="nav-submenu-item">
                                            <a href="#" class="nav-submenu-toggle">
                                                {{ $subLabel }}
                                                <svg class="nav-chevron-right" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                            <div class="nav-submenu">
                                                @foreach ($sub->subfeatures as $subSub)
                                                    @php
                                                        $subSubLabel = app()->getLocale() === 'en' && $subSub->name_en ? $subSub->name_en : $subSub->name;
                                                    @endphp
                                                    <a href="{{ $subSub->path ?? '#' }}">{{ $subSubLabel }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ $sub->path ?? '#' }}">{{ $subLabel }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        @php
                            $isActive = $isHome
                                ? request()->routeIs('home')
                                : ($feature->path && request()->is(ltrim($feature->path, '/')));
                        @endphp
                        <a href="{{ $isHome ? route('home') : ($feature->path ?? '#') }}"
                           class="{{ $isActive ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="auth-utils">
                @auth
                    <a href="{{ route('dashboard') }}" class="login-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">{{ __('home.nav.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="login-link {{ request()->routeIs('login', 'register', 'password.*') ? 'active' : '' }}">{{ __('home.nav.login') }}</a>
                @endauth
                <div class="lang-switch">
                    <a href="{{ route('locale.switch', 'id') }}"
                        class="{{ app()->getLocale() === 'id' ? 'current' : '' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}"
                        class="{{ app()->getLocale() === 'en' ? 'current' : '' }}">EN</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    (function () {
        // Mobile: tap a dropdown toggle to expand inline instead of relying on hover.
        var mql = window.matchMedia('(max-width: 1280px)');
        document.querySelectorAll('.top-nav .nav-dropdown-toggle, .top-nav .nav-submenu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                if (!mql.matches) return;
                e.preventDefault();
                var parent = toggle.closest('.nav-dropdown, .nav-submenu-item');
                if (parent) parent.classList.toggle('open');
            });
        });
        // Close the mobile menu when a real link is tapped.
        document.querySelectorAll('.top-nav .menu a:not(.nav-dropdown-toggle):not(.nav-submenu-toggle)').forEach(function (link) {
            link.addEventListener('click', function () {
                if (!mql.matches) return;
                var nav = link.closest('.top-nav');
                if (nav) nav.classList.remove('open');
            });
        });
    })();
</script>
