<nav class="navbar navbar-light fixed-top">
    <div class="container-fluid d-flex align-items-center flex-nowrap" style="gap: 0;">

        {{-- Bal: home + search --}}
        <div class="d-flex align-items-center gap-2 me-3">
            <a href="/" class="navbar-home-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>
            <form action="{{ route('search') }}" method="GET" class="d-flex">
                <input type="text" name="query" class="search-bar" placeholder="Zadajte, čo hľadáte...">
            </form>
        </div>

        {{-- Közép: nav gombok --}}
        <div class="d-flex align-items-center gap-2 mx-auto">
            <button class="btn btn-light" onclick="window.location.href='/shop/akcie'">Akcie</button>
            <button class="btn btn-light" onclick="window.location.href='/shop/novinky'">Novinky</button>
            <button class="btn btn-light" onclick="window.location.href='/shop/best-sellers'">Best sellers</button>
        </div>

        {{-- Jobb: auth + ikonok --}}
        <div class="d-flex align-items-center gap-2 ms-3">
            @if(Auth::check())
                <form action="{{ route('profil.zmazat') }}" method="POST" onsubmit="return confirm('Naozaj chcete vymazať svoj účet?');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Vymazať účet</button>
                </form>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-dark btn-sm">Odhlásiť</button>
                </form>
            @else
                <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='/prihlasenie'">Prihlásenie/Registrácia</button>
            @endif
            <button class="btn btn-outline-danger btn-sm" onclick="window.location.href='/shop/oblubene'">❤️</button>
            <button class="btn btn-outline-dark btn-sm" onclick="window.location.href='/kosik'">🛒</button>
        </div>

    </div>
</nav>
