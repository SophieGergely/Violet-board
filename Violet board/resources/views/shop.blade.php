<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    {{-- Toggle button – shop only --}}
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">&#9664;</button>

    <main class="main-content" id="mainContent">

        <div class="category-title">{{ $categoryTitle ?? 'Shop' }}</div>

        {{-- Sort & Filter --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button id="sortBtn" data-dropdown-toggle="sortMenu"
                    class="btn btn-outline-secondary dropdown-toggle">
                    Zoradiť
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
                <button id="filterBtn" data-dropdown-toggle="filterMenu"
                    class="btn btn-outline-secondary dropdown-toggle">
                    Filter
                </button>
                <div id="filterMenu" class="z-10 hidden bg-white rounded-lg shadow-lg p-4" style="min-width:260px;z-index:300">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cena od:</label>
                            <input type="number" step="0.01" class="form-control" name="min_price" value="{{ request('min_price') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cena do:</label>
                            <input type="number" step="0.01" class="form-control" name="max_price" value="{{ request('max_price') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max. veková kategória:</label>
                            <input type="number" class="form-control" name="vekova_kategoria" value="{{ request('vekova_kategoria') }}" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Min. počet hráčov:</label>
                            <input type="number" class="form-control" name="hracov" value="{{ request('hracov') }}" min="0">
                        </div>
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <button type="submit" class="btn btn-primary w-100">Použiť</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Product grid --}}
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="shop-product-card">
                            <form action="{{ route('product.favorite', $product->id) }}" method="POST">
                                @csrf
                                <button class="heart-icon" type="submit">❤️</button>
                            </form>
                            <div class="product-image">
                                <img src="{{ asset('Pictures/' . $product->images->first()->filename) }}" alt="{{ $product->name }}">
                            </div>
                            <div class="product-details">
                                <h5>{{ $product->name }}</h5>
                                <p class="mb-1">
                                    @if($product->is_discounted && $product->discounted_price)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }}€</span>
                                        <span class="text-success fw-bold ms-1">{{ number_format($product->discounted_price, 2) }}€</span>
                                    @else
                                        <span>{{ number_format($product->price, 2) }}€</span>
                                    @endif
                                </p>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary w-100 btn-sm" type="submit">Pridať do košíka</button>
                                </form>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="pagination-container mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>

    </main>

    @include('partials.footer')

    <script>
        const sidebar     = document.getElementById('sidebar');
        const toggle      = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        toggle.addEventListener('click', () => {
            const collapsed = sidebar.classList.toggle('collapsed');
            toggle.classList.toggle('collapsed', collapsed);
            toggle.innerHTML = collapsed ? '&#9654;' : '&#9664;';
            mainContent.classList.toggle('expanded', collapsed);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
