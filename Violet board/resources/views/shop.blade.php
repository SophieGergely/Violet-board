<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        /* Smaller product cards specific to shop page */
        .shop-product-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 2px 8px rgba(109,40,217,0.08);
            overflow: hidden;
            transition: .25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .shop-product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(109,40,217,0.14);
            border-color: var(--color-teal);
        }
        .shop-product-card .product-image {
            height: 120px;
        }
        .shop-product-card .product-details {
            padding: 10px;
            margin: 8px;
            font-size: 0.8rem;
        }
        .shop-product-card h5 {
            font-size: 0.85rem;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">

        @php
            $isBaseShop = ($categoryTitle ?? 'Shop') === 'Shop';
            $breadcrumbItems = [['label' => 'Domov', 'url' => url('/')]];
            if ($isBaseShop) {
                $breadcrumbItems[] = ['label' => 'Shop'];
            } else {
                $breadcrumbItems[] = ['label' => 'Shop', 'url' => url('/shop')];
                $breadcrumbItems[] = ['label' => $categoryTitle];
            }
        @endphp
        @include('partials.breadcrumb', ['items' => $breadcrumbItems, 'extraClass' => 'page-breadcrumb--clear-toggle'])

        <div class="category-title">{{ $categoryTitle ?? 'Shop' }}</div>

        {{-- Sort & Filter --}}
        @php
            $sortLabels = [
                'asc' => 'Názov A–Z',
                'desc' => 'Názov Z–A',
                'price_asc' => 'Cena vzostupne',
                'price_desc' => 'Cena zostupne',
            ];
            $currentSortLabel = $sortLabels[$sort ?? null] ?? null;

            $priceBounds = \App\Models\Product::selectRaw(
                'MIN(CASE WHEN is_discounted = true AND discounted_price IS NOT NULL THEN discounted_price ELSE price END) as min_price,
                 MAX(CASE WHEN is_discounted = true AND discounted_price IS NOT NULL THEN discounted_price ELSE price END) as max_price'
            )->first();
            $priceBoundsMin = (int) floor($priceBounds->min_price ?? 0);
            $priceBoundsMax = (int) ceil($priceBounds->max_price ?? 100);
            $selectedMinPrice = (int) request('min_price', $priceBoundsMin);
            $selectedMaxPrice = (int) request('max_price', $priceBoundsMax);

            $activeFilterLabels = [];
            if ($selectedMinPrice > $priceBoundsMin || $selectedMaxPrice < $priceBoundsMax) {
                $activeFilterLabels[] = 'Cena ' . $selectedMinPrice . '–' . $selectedMaxPrice . ' €';
            }
            if (request()->filled('vekova_kategoria')) {
                $activeFilterLabels[] = 'Vek ≤ ' . request('vekova_kategoria');
            }
            if (request()->filled('hracov')) {
                $activeFilterLabels[] = 'Hráči ≥ ' . request('hracov');
            }
            $hasActiveFilters = count($activeFilterLabels) > 0;
            $filterSummary = implode(', ', $activeFilterLabels);
            $clearFiltersUrl = url()->current() . '?' . http_build_query(
                request()->except(['min_price', 'max_price', 'vekova_kategoria', 'hracov'])
            );
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button id="sortBtn" type="button" data-dropdown-toggle="sortMenu" class="filter-pill">
                    @if ($currentSortLabel)
                        Zoradenie: {{ $currentSortLabel }}
                    @else
                        Zoradenie
                    @endif
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                <div id="sortMenu" class="z-10 hidden bg-white rounded-lg shadow-lg w-44" style="z-index:300">
                    <ul class="py-2 text-sm">
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" class="block px-4 py-2 hover:bg-purple-50">A–Z</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Z–A</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="block px-4 py-2 hover:bg-purple-50">Cena vzostupne</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Cena zostupne</a></li>
                    </ul>
                </div>
            </div>

            <div>
                <div class="filter-pill-group {{ $hasActiveFilters ? 'has-active' : '' }}">
                    <button id="filterBtn" type="button" data-dropdown-toggle="filterMenu" class="filter-pill-trigger">
                        @if ($hasActiveFilters)
                            Filter: {{ $filterSummary }}
                        @else
                            Filter
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                            </svg>
                        @endif
                    </button>
                    @if ($hasActiveFilters)
                        <a href="{{ $clearFiltersUrl }}" class="filter-pill-clear" title="Zrušiť filter" aria-label="Zrušiť filter">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
                <div id="filterMenu" class="z-10 hidden bg-white rounded-lg shadow-lg p-4" style="min-width:260px;z-index:300">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Cena</span>
                                <span class="d-flex align-items-center gap-2">
                                    <span id="priceRangeDisplay">{{ $selectedMinPrice }} € – {{ $selectedMaxPrice }} €</span>
                                    <button type="button" class="filter-field-clear" data-clear="price" title="Obnoviť celý rozsah" aria-label="Obnoviť celý rozsah cien">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </label>
                            <div class="price-range-slider" data-min="{{ $priceBoundsMin }}" data-max="{{ $priceBoundsMax }}">
                                <div class="price-range-track"></div>
                                <input
                                    type="range"
                                    class="price-range-input price-range-input-min"
                                    min="{{ $priceBoundsMin }}"
                                    max="{{ $priceBoundsMax }}"
                                    step="1"
                                    value="{{ $selectedMinPrice }}"
                                    aria-label="Minimálna cena"
                                >
                                <input
                                    type="range"
                                    class="price-range-input price-range-input-max"
                                    min="{{ $priceBoundsMin }}"
                                    max="{{ $priceBoundsMax }}"
                                    step="1"
                                    value="{{ $selectedMaxPrice }}"
                                    aria-label="Maximálna cena"
                                >
                            </div>
                            <div class="d-flex justify-content-between" style="color:var(--color-text-muted);font-size:var(--text-xs);">
                                <span>{{ $priceBoundsMin }} €</span>
                                <span>{{ $priceBoundsMax }} €</span>
                            </div>
                            <input type="hidden" name="min_price" id="minPriceHidden" value="{{ $selectedMinPrice }}">
                            <input type="hidden" name="max_price" id="maxPriceHidden" value="{{ $selectedMaxPrice }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Max. veková kategória</span>
                                <button type="button" class="filter-field-clear" data-clear-input="vekova_kategoria" title="Zrušiť" aria-label="Zrušiť vekové obmedzenie">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </label>
                            <input type="number" class="form-control" name="vekova_kategoria" value="{{ request('vekova_kategoria') }}" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Min. počet hráčov</span>
                                <button type="button" class="filter-field-clear" data-clear-input="hracov" title="Zrušiť" aria-label="Zrušiť obmedzenie počtu hráčov">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </label>
                            <input type="number" class="form-control" name="hracov" value="{{ request('hracov') }}" min="0">
                        </div>
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <button type="submit" class="btn btn-primary w-100">Použiť</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Product grid --}}
        @php $cart = session('cart', []); @endphp
        <div class="row g-3">
            @forelse ($products as $product)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="shop-product-card h-100">
                        <form action="{{ route('product.favorite', $product->id) }}" method="POST">
                            @csrf
                            <button class="heart-icon" type="submit">
                                <svg viewBox="0 0 24 24" fill="{{ $product->is_favorite ? '#DC2626' : '#D1D5DB' }}" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>
                        <a href="{{ route('product.show', $product->id) }}?from_label={{ urlencode($categoryTitle ?? 'Shop') }}&from_url={{ urlencode(url()->current()) }}" class="text-decoration-none text-dark d-block">
                            <div class="product-image">
                                <img src="{{ asset('Pictures/' . $product->images->first()->filename) }}" alt="{{ $product->name }}">
                            </div>
                        </a>
                        <div class="product-details" style="{{ $product->is_discounted && $product->discounted_price ? 'background:#DCFCE7;' : '' }}">
                            <a href="{{ route('product.show', $product->id) }}?from_label={{ urlencode($categoryTitle ?? 'Shop') }}&from_url={{ urlencode(url()->current()) }}" class="text-decoration-none text-dark d-block">
                                <h5>{{ $product->name }}</h5>
                                <p class="mb-1">
                                    @if($product->is_discounted && $product->discounted_price)
                                        <span class="text-decoration-line-through text-muted small">{{ number_format($product->price, 2) }}€</span>
                                        <span class="text-success fw-bold ms-1">{{ number_format($product->discounted_price, 2) }}€</span>
                                    @else
                                        <span>{{ number_format($product->price, 2) }}€</span>
                                    @endif
                                </p>
                            </a>
                                <div class="cart-control" data-product-id="{{ $product->id }}">
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="ajax-cart-form ajax-add-form" style="display:{{ isset($cart[$product->id]) ? 'none' : 'block' }};">
                                        @csrf
                                        <button class="btn btn-primary w-100 btn-sm" type="submit">Pridať do košíka</button>
                                    </form>
                                    <div class="cart-counter" style="display:{{ isset($cart[$product->id]) ? 'flex' : 'none' }};">
                                        <form action="{{ route('cart.update', $product->id) }}" method="POST" class="ajax-cart-form">
                                            @csrf
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="cart-counter-btn">−</button>
                                        </form>
                                        <input
                                            type="number"
                                            class="cart-counter-input js-cart-qty-input"
                                            value="{{ $cart[$product->id]['quantity'] ?? 1 }}"
                                            min="1"
                                            data-update-url="{{ route('cart.update', $product->id) }}"
                                        >
                                        <form action="{{ route('cart.update', $product->id) }}" method="POST" class="ajax-cart-form">
                                            @csrf
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="cart-counter-btn">+</button>
                                        </form>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state-card">
                        @if (request()->is('search'))
                            {{-- Nincs keresési eredmény --}}
                            <div style="margin-bottom:16px; display:flex; justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="27" cy="27" r="18" stroke="#6D28D9" stroke-width="3.5"/>
                                    <line x1="40" y1="40" x2="56" y2="56" stroke="#6D28D9" stroke-width="3.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">
                                Žiadny produkt sa nenašiel
                            </h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                @if (request()->filled('query'))
                                    Pre výraz „{{ request('query') }}“ sme nenašli žiadny produkt.
                                @else
                                    Skúste zadať iný výraz alebo si pozrite celú ponuku.
                                @endif
                            </p>
                            <a href="/shop" class="btn btn-primary px-5">Prejsť do obchodu</a>
                        @elseif (request()->is('shop/oblubene'))
                            {{-- Nincsenek kedvenc termékek --}}
                            <div style="margin-bottom:16px; display:flex; justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M32 56l-3.6-3.3C14 39.6 4 30.8 4 20
                                             4 11.2 11.2 4 20 4c4.8 0 9.4 2.2 12 5.6
                                             C34.6 6.2 39.2 4 44 4 52.8 4 60 11.2 60 20
                                             c0 10.8-10 19.6-24.4 32.7L32 56z"
                                          fill="#6D28D9" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">
                                Zatiaľ žiadne obľúbené produkty
                            </h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                Pridajte si produkty medzi obľúbené kliknutím na
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#DC2626" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;margin: 0 1px;">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                pri produkte.
                            </p>
                            <a href="/shop" class="btn btn-primary px-5">Prejsť do obchodu</a>
                        @else
                            {{-- Nincs termék az adott kategóriában / szűrésnél --}}
                            <div style="margin-bottom:16px; display:flex; justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 22l24-12 24 12-24 12-24-12z" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round"/>
                                    <path d="M8 22v20l24 12V34M56 22v20L32 54" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">
                                Žiadne produkty
                            </h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                V tejto kategórii sa momentálne nenachádzajú žiadne produkty.
                            </p>
                            <a href="/shop" class="btn btn-primary px-5">Prejsť do obchodu</a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pagination-container mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>

    </main>

    @include('partials.footer')

    <script src="{{ asset('js/sidebar-toggle.js') }}"></script>
    <script src="{{ asset('js/price-range.js') }}"></script>
    <script src="{{ asset('js/cart-ajax.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
