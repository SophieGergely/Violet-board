<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content">

        <div class="category-title">{{ $categoryTitle ?? 'Shop' }}</div>

        {{-- Sort & Filter --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div class="relative">
                <button id="sortBtn" data-dropdown-toggle="sortMenu"
                    class="btn btn-outline-secondary dropdown-toggle">
                    Zoradiť
                </button>
                <div id="sortMenu" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-44">
                    <ul class="py-2 text-sm text-gray-700">
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" class="block px-4 py-2 hover:bg-purple-50">A–Z</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Z–A</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="block px-4 py-2 hover:bg-purple-50">Cena vzostupne</a></li>
                        <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="block px-4 py-2 hover:bg-purple-50">Cena zostupne</a></li>
                    </ul>
                </div>
            </div>

            <div class="relative">
                <button id="filterBtn" data-dropdown-toggle="filterMenu"
                    class="btn btn-outline-secondary dropdown-toggle">
                    Filter
                </button>
                <div id="filterMenu" class="z-10 hidden bg-white rounded-lg shadow-lg p-4" style="min-width: 260px;">
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
        <div class="row">
            @foreach ($products as $product)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark">
                        <div class="product-card">
                            <form action="{{ route('product.favorite', $product->id) }}" method="POST">
                                @csrf
                                <button class="heart-icon" type="submit">❤️</button>
                            </form>
                            <div class="product-image">
                                <img src="{{ asset('Pictures/' . $product->images->first()->filename) }}" alt="{{ $product->name }}">
                            </div>
                            <div class="product-details">
                                <h5>{{ $product->name }}</h5>
                                <p>
                                    @if($product->is_discounted && $product->discounted_price)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }}€</span>
                                        <span class="text-success fw-bold ms-2">{{ number_format($product->discounted_price, 2) }}€</span>
                                    @else
                                        <span>{{ number_format($product->price, 2) }}€</span>
                                    @endif
                                </p>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary w-100" type="submit">Pridať do košíka</button>
                                </form>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="pagination-container">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>

    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
