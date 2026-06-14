<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>



<body>

    @include('partials.header')

    <div class="container-fluid mt-4">
        <div class="row">

            @include('partials.sidebar')

            <section class="col">
                <div class="category-title">{{ $categoryTitle ?? 'Shop' }}</div>

                <div class="d-flex justify-content-between align-items-center mb-3 px-3">

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Zoradiť
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}">A-Z</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}">Z-A</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">Cena vzostupne</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">Cena zostupne</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter
                        </button>
                        <div class="dropdown-menu p-3" style="min-width: 250px;">
                            <form method="GET" action="{{ url()->current() }}">

                                <div class="mb-2">
                                    <label for="min_price" class="form-label">Cena od:</label>
                                    <input type="number" step="0.01" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}">
                                </div>

                                <div class="mb-2">
                                    <label for="max_price" class="form-label">Cena do:</label>
                                    <input type="number" step="0.01" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}">
                                </div>

                                <div class="mb-2">
                                    <label for="vekova_kategoria" class="form-label">Maximálna veková kategória:</label>
                                    <input type="number" class="form-control" name="vekova_kategoria" id="vekova_kategoria" value="{{ request('vekova_kategoria') }}" min="0">
                                </div>

                                <div class="mb-2">
                                    <label for="hracov" class="form-label">Minimálny počet hráčov:</label>
                                    <input type="number" class="form-control" name="hracov" id="hracov" value="{{ request('hracov') }}" min="0">
                                </div>

                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                                <button type="submit" class="btn btn-primary w-100">Použiť</button>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="container pt-1">
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
                                                <button class="btn btn-primary" type="submit">Pridať do košíka</button>
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

                </div>
            </section>
        </div>
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
