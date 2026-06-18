<nav class="navbar navbar-light fixed-top">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-nowrap px-3">

        {{-- Left: home + search (mindig látható) --}}
        <div class="d-flex align-items-center gap-2">
            <a href="/" class="navbar-home-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>

            <form action="{{ route('search') }}" method="GET" class="d-flex">
                <input type="text" name="query" class="search-bar" placeholder="Zadajte, čo hľadáte...">
            </form>
        </div>

        {{-- Center: nav buttons (rejtve mobilon) --}}
        <div class="d-none d-lg-flex align-items-center gap-2">
            <button class="btn btn-light" onclick="window.location.href='/shop/akcie'">Akcie</button>
            <button class="btn btn-light" onclick="window.location.href='/shop/novinky'">Novinky</button>
            <button class="btn btn-light" onclick="window.location.href='/shop/best-sellers'">Best sellers</button>
        </div>

        {{-- Right: auth + icons --}}
        <div class="d-flex align-items-center gap-2">

            {{-- Asztali gombok --}}
            <div class="d-none d-lg-flex align-items-center gap-2">
                @guest
                    <button class="btn-navbar-action btn-navbar-ghost" onclick="window.location.href='/prihlasenie'">Prihlásenie / Registrácia</button>
                @endguest

                @auth
                    <div class="navbar-user-wrap">
                        <button
                            type="button"
                            class="navbar-icon-btn navbar-user-toggle"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-label="Účet"
                            title="Účet"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.68-10 5v3h20v-3c0-3.32-6.67-5-10-5z"/>
                            </svg>
                        </button>

                        <div class="navbar-user-menu">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="navbar-user-menu-item">Odhlásiť</button>
                            </form>
                            <form action="{{ route('profil.zmazat') }}" method="POST" onsubmit="return confirm('Naozaj chcete vymazať svoj účet?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="navbar-user-menu-item navbar-user-menu-item--danger">Vymazať účet</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <a href="/shop/oblubene" class="navbar-icon-btn" title="Obľúbené">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </a>

                <a href="/kosik" class="navbar-icon-btn" title="Košík">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM7.83 14l.94-2h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L20.1 3H4.21L3.27 1H0v2h2l3.6 7.59L4.25 13c-.16.28-.25.61-.25.95C4 15.1 4.9 16 6 16h14v-2H6.42c-.14 0-.25-.11-.25-.25l.03-.14.55-1.61z"/>
                    </svg>
                </a>
            </div>

            {{-- Mobilon: fiók + kosár + hamburger --}}
            @auth
                <div class="navbar-user-wrap d-lg-none">
                    <button
                        type="button"
                        class="navbar-icon-btn navbar-user-toggle"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="Účet"
                        title="Účet"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.68-10 5v3h20v-3c0-3.32-6.67-5-10-5z"/>
                        </svg>
                    </button>

                    <div class="navbar-user-menu">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="navbar-user-menu-item">Odhlásiť</button>
                        </form>
                        <form action="{{ route('profil.zmazat') }}" method="POST" onsubmit="return confirm('Naozaj chcete vymazať svoj účet?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="navbar-user-menu-item navbar-user-menu-item--danger">Vymazať účet</button>
                        </form>
                    </div>
                </div>
            @endauth

            <a href="/kosik" class="navbar-icon-btn d-lg-none" title="Košík">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM7.83 14l.94-2h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L20.1 3H4.21L3.27 1H0v2h2l3.6 7.59L4.25 13c-.16.28-.25.61-.25.95C4 15.1 4.9 16 6 16h14v-2H6.42c-.14 0-.25-.11-.25-.25l.03-.14.55-1.61z"/>
                </svg>
            </a>

            <button class="navbar-icon-btn d-lg-none" id="navHamburger" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

{{-- Mobil lenyíló menü — a nav-on KÍVÜL, fixed pozícióban a navbar alatt --}}
<div id="mobileMenu" style="
    display: none;
    position: fixed;
    top: var(--navbar-height);
    left: 0;
    right: 0;
    z-index: 199;
    background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);
    padding: 12px 16px 16px;
    border-top: 1px solid rgba(255,255,255,0.2);
    flex-direction: column;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
">
    <div class="d-flex flex-column gap-2">
        <button class="btn btn-light w-100 text-start" onclick="window.location.href='/shop/oblubene'">❤ Obľúbené</button>
        <button class="btn btn-light w-100 text-start" onclick="window.location.href='/shop/akcie'">Akcie</button>
        <button class="btn btn-light w-100 text-start" onclick="window.location.href='/shop/novinky'">Novinky</button>
        <button class="btn btn-light w-100 text-start" onclick="window.location.href='/shop/best-sellers'">Best sellers</button>
    </div>

    <div class="d-flex flex-column gap-2 mt-2" style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px;">
        @guest
            <button class="btn-navbar-action btn-navbar-ghost w-100" onclick="window.location.href='/prihlasenie'">Prihlásenie / Registrácia</button>
        @endguest
    </div>
</div>

<script>
    document.getElementById('navHamburger').addEventListener('click', function () {
        const menu = document.getElementById('mobileMenu');
        const isOpen = menu.style.display === 'flex';
        menu.style.display = isOpen ? 'none' : 'flex';
    });

    document.querySelectorAll('.navbar-user-wrap').forEach(function (wrap) {
        const btn = wrap.querySelector('.navbar-user-toggle');
        const menu = wrap.querySelector('.navbar-user-menu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('show');
            document.querySelectorAll('.navbar-user-menu.show').forEach(m => m.classList.remove('show'));
            menu.classList.toggle('show', !isOpen);
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    document.addEventListener('click', function (e) {
        document.querySelectorAll('.navbar-user-wrap').forEach(function (wrap) {
            if (!wrap.contains(e.target)) {
                const menu = wrap.querySelector('.navbar-user-menu');
                const btn = wrap.querySelector('.navbar-user-toggle');
                if (menu) menu.classList.remove('show');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>
