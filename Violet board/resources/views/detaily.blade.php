<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detaily o produkte - {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

@php
    use Illuminate\Support\Str;
    $slug = Str::slug($product->name);
@endphp

<script src="{{ asset('js/detaily.js') }}"></script>


<body>
    @include('partials.header')

    <div class="container product-container">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex justify-content-center">

                <div class="product-image-wrapper position-relative">
                    <button class="arrow-button left-arrow position-absolute top-50 start-0 translate-middle-y" onclick="changeImage(-1)">&#10094;</button>
                    <div class="product-image" id="productImage">
                        <img
                            id="activeImage"
                            src="{{ asset('Pictures/' . $slug . '1.jpg') }}"
                            alt="{{ $product->name }}"
                            data-basename="{{ $slug }}"
                            data-imagefolder="{{ asset('Pictures') . '/' }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <button class="arrow-button right-arrow position-absolute top-50 end-0 translate-middle-y" onclick="changeImage(1)">&#10095;</button>

                </div>
            </div>
            <div class="col-md-6">
                <h2>{{ $product->name }}</h2>
                <p class="price">

                    @if ($product->is_discounted && $product->discounted_price)
                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }} €</span>
                        <span class="text-success fw-bold ms-2">{{ number_format($product->discounted_price, 2) }} €</span>
                    @else
                        <span>{{ number_format($product->price, 2) }} €</span>
                    @endif

                </p>
                <div class="d-flex align-items-center gap-2 mt-2">

                    @if ($isInCart)
                    <form action="{{ route('cart.remove', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Odobrať z košíka</button>
                    </form>
                    @else
                    <form action="{{ route('cart.add', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark">Pridať do košíka</button>
                    </form>
                    @endif

                    <form action="{{ route('favorite.toggle', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="heart-icon btn btn-light">
                            {{ $product->is_favorite ? '💔' : '❤️' }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="product-description" style="margin-bottom: 80px;">
            <h3>Popis produktu</h3>
            <p>{!! $product->description !!}</p>
        </div>
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/detaily.js') }}"></script>

</body>
</html>

