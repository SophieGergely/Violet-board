<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $categoryTitle ?? 'Shop' }} – Violet Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
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
        .shop-product-card .product-image { height: 120px; }
        .shop-product-card .product-details { padding: 10px; margin: 8px; font-size: 0.8rem; }
        .shop-product-card h5 { font-size: 0.85rem; margin-bottom: 4px; }
    </style>
</head>

<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">

        @php
            use App\Models\Cart;

            // Breadcrumb
            $isBaseShop = ($categoryTitle ?? 'Shop') === 'Shop';
            $breadcrumbItems = [['label' => 'Home', 'url' => url('/')]];
            if ($isBaseShop) {
                $breadcrumbItems[] = ['label' => 'Shop'];
            } else {
                $breadcrumbItems[] = ['label' => 'Shop', 'url' => url('/shop')];
                $breadcrumbItems[] = ['label' => $categoryTitle];
            }

            // Sort labels
            $sortLabels = [
                'asc'        => 'Name A–Z',
                'desc'       => 'Name Z–A',
                'price_asc'  => 'Price: Low to High',
                'price_desc' => 'Price: High to Low',
            ];
            $currentSortLabel = $sortLabels[$sort ?? ''] ?? null;

            // Price bounds (based on base price)
            $priceBounds     = \App\Models\Product::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
            $priceBoundsMin  = (int) floor($priceBounds->min_price ?? 0);
            $priceBoundsMax  = (int) ceil($priceBounds->max_price ?? 100);
            $selectedMinPrice = (int) request('min_price', $priceBoundsMin);
            $selectedMaxPrice = (int) request('max_price', $priceBoundsMax);

            // Active filters
            $activeFilterLabels = [];
            if ($selectedMinPrice > $priceBoundsMin || $selectedMaxPrice < $priceBoundsMax) {
                $activeFilterLabels[] = 'Price ' . $selectedMinPrice . '–' . $selectedMaxPrice . ' €';
            }
            if (request()->filled('max_age')) {
                $activeFilterLabels[] = 'Age ≤ ' . request('max_age');
            }
            if (request()->filled('players')) {
                $activeFilterLabels[] = 'Players ≥ ' . request('players');
            }
            $hasActiveFilters = count($activeFilterLabels) > 0;
            $filterSummary    = implode(', ', $activeFilterLabels);
            $clearFiltersUrl  = url()->current() . '?' . http_build_query(
                request()->except(['min_price', 'max_price', 'max_age', 'players'])
            );

            // Cart product IDs for this user/session
            if (auth()->check()) {
                $cartModel = Cart::with('items')->where('user_id', auth()->id())->first();
            } else {
                $cartModel = Cart::with('items')->where('session_id', session()->getId())->first();
            }
            $cartProductIds     = $cartModel?->items->pluck('product_id')->toArray() ?? [];
            $cartItemQuantities = $cartModel?->items->pluck('quantity', 'product_id')->toArray() ?? [];

            // Favorite product IDs for this user
            $favoriteIds = auth()->check()
                ? auth()->user()->favorites()->pluck('products.id')->toArray()
                : session()->get('guest_favorites', []);
        @endphp

        @include('partials.breadcrumb', ['items' => $breadcrumbItems, 'extraClass' => 'page-breadcrumb--clear-toggle'])

        <div class="category-title">{{ $categoryTitle ?? 'Shop' }}</div>

        {{-- Sort & Filter --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button id="sortBtn" type="button" data-dropdown-toggle="sortMenu" class="filter-pill">
                    {{ $currentSortLabel ? 'Sort: ' . $currentSortLabel : 'Sort' }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                <div id="sortMenu" class="z-10 hidden bg-white rounded-lg shadow-lg w-44" style="z-index:300">
                    <ul class="py-2 text-sm">
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" class="block px-4 py-2 hover:bg-purple-50">Name A–Z</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Name Z–A</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="block px-4 py-2 hover:bg-purple-50">Price: Low to High</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Price: High to Low</a></li>
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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                            </svg>
                        @endif
                    </button>
                    @if ($hasActiveFilters)
                        <a href="{{ $clearFiltersUrl }}" class="filter-pill-clear" title="Clear filters" aria-label="Clear filters">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>

                <div id="filterMenu" class="z-10 hidden bg-white rounded-lg shadow-lg p-4" style="min-width:260px;z-index:300">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Price</span>
                                <span class="d-flex align-items-center gap-2">
                                    <span id="priceRangeDisplay">{{ $selectedMinPrice }} € – {{ $selectedMaxPrice }} €</span>
                                    <button type="button" class="filter-field-clear" data-clear="price" title="Reset price range" aria-label="Reset price range">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </label>
                            <div class="price-range-slider" data-min="{{ $priceBoundsMin }}" data-max="{{ $priceBoundsMax }}">
                                <div class="price-range-track"></div>
                                <input type="range" class="price-range-input price-range-input-min"
                                    min="{{ $priceBoundsMin }}" max="{{ $priceBoundsMax }}" step="1"
                                    value="{{ $selectedMinPrice }}" aria-label="Minimum price">
                                <input type="range" class="price-range-input price-range-input-max"
                                    min="{{ $priceBoundsMin }}" max="{{ $priceBoundsMax }}" step="1"
                                    value="{{ $selectedMaxPrice }}" aria-label="Maximum price">
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
                                <span>Max. Age</span>
                                <button type="button" class="filter-field-clear" data-clear-input="max_age" title="Clear" aria-label="Clear age filter">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </label>
                            <input type="number" class="form-control" name="max_age" value="{{ request('max_age') }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Min. Players</span>
                                <button type="button" class="filter-field-clear" data-clear-input="players" title="Clear" aria-label="Clear players filter">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </label>
                            <input type="number" class="form-control" name="players" value="{{ request('players') }}" min="0">
                        </div>

                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Product grid --}}
        <div class="row g-3">
            @forelse ($products as $product)
                @php
                    $effectivePrice = $product->effectivePrice();
                    $originalPrice  = (float) $product->price;
                    $hasDiscount    = $effectivePrice < $originalPrice;
                    $inCart         = in_array($product->id, $cartProductIds);
                    $qty            = $cartItemQuantities[$product->id] ?? 1;
                    $isFav          = in_array($product->id, $favoriteIds);
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="shop-product-card h-100">

                        <form action="{{ route('favorite.toggle', $product->id) }}" method="POST">
                            @csrf
                            <button class="heart-icon" type="submit">
                                <svg viewBox="0 0 24 24" fill="{{ $isFav ? '#DC2626' : '#D1D5DB' }}" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>

                        <a href="{{ route('product.show', $product->id) }}?from_label={{ urlencode($categoryTitle ?? 'Shop') }}&from_url={{ urlencode(url()->current()) }}" class="text-decoration-none text-dark d-block">
                            <div class="product-image">
                                <img src="{{ $product->images->first()?->url ?? '' }}" alt="{{ $product->name }}">
                            </div>
                        </a>

                        <div class="product-details" style="{{ $hasDiscount ? 'background:#DCFCE7;' : '' }}">
                            <a href="{{ route('product.show', $product->id) }}?from_label={{ urlencode($categoryTitle ?? 'Shop') }}&from_url={{ urlencode(url()->current()) }}" class="text-decoration-none text-dark d-block">
                                <h5>{{ $product->name }}</h5>
                                <p class="mb-1">
                                    @if ($hasDiscount)
                                        <span class="text-decoration-line-through text-muted small">{{ number_format($originalPrice, 2) }}€</span>
                                        <span class="text-success fw-bold ms-1">{{ number_format($effectivePrice, 2) }}€</span>
                                    @else
                                        <span>{{ number_format($effectivePrice, 2) }}€</span>
                                    @endif
                                </p>
                            </a>

                            <div class="cart-control" data-product-id="{{ $product->id }}">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="ajax-cart-form ajax-add-form" style="display:{{ $inCart ? 'none' : 'block' }};">
                                    @csrf
                                    <button class="btn btn-primary w-100 btn-sm" type="submit">Add to Cart</button>
                                </form>
                                <div class="cart-counter" style="display:{{ $inCart ? 'flex' : 'none' }};">
                                    <form action="{{ route('cart.update', $product->id) }}" method="POST" class="ajax-cart-form">
                                        @csrf
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="cart-counter-btn">−</button>
                                    </form>
                                    <input type="number" class="cart-counter-input js-cart-qty-input"
                                        value="{{ $qty }}" min="1"
                                        data-update-url="{{ route('cart.update', $product->id) }}">
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
                            <div style="margin-bottom:16px;display:flex;justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                                    <circle cx="27" cy="27" r="18" stroke="#6D28D9" stroke-width="3.5"/>
                                    <line x1="40" y1="40" x2="56" y2="56" stroke="#6D28D9" stroke-width="3.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">No products found</h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                @if (request()->filled('query'))
                                    No results for "{{ request('query') }}".
                                @else
                                    Try a different search term.
                                @endif
                            </p>
                        @elseif (request()->is('shop/favorites'))
                            <div style="margin-bottom:16px;display:flex;justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 56l-3.6-3.3C14 39.6 4 30.8 4 20 4 11.2 11.2 4 20 4c4.8 0 9.4 2.2 12 5.6C34.6 6.2 39.2 4 44 4 52.8 4 60 11.2 60 20c0 10.8-10 19.6-24.4 32.7L32 56z"
                                        fill="#6D28D9" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">No favorites yet</h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                Click the ❤ icon on any product to add it to your favorites.
                            </p>
                        @else
                            <div style="margin-bottom:16px;display:flex;justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                                    <path d="M8 22l24-12 24 12-24 12-24-12z" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round"/>
                                    <path d="M8 22v20l24 12V34M56 22v20L32 54" stroke="#6D28D9" stroke-width="3.5" stroke-linejoin="round" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">No products found</h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                No products in this category at the moment.
                            </p>
                        @endif
                        <a href="/shop" class="btn btn-primary px-5">Browse All Games</a>
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
