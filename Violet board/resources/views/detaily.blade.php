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

    <div class="container" style="padding-top: 16px;">
        @php
            $breadcrumbItems = [['label' => 'Domov', 'url' => url('/')]];
            if (!empty($fromLabel) && !empty($fromUrl)) {
                $breadcrumbItems[] = ['label' => $fromLabel, 'url' => $fromUrl];
            } else {
                $breadcrumbItems[] = ['label' => 'Shop', 'url' => url('/shop')];
            }
            $breadcrumbItems[] = ['label' => $product->name];
        @endphp
        @include('partials.breadcrumb', ['items' => $breadcrumbItems])
    </div>

    <div class="container product-container">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex justify-content-center align-items-center">

                <div class="product-image-wrapper position-relative" style="width: 100%; max-width: 480px;">
                    <button class="arrow-section position-absolute top-50 start-0 translate-middle-y" style="z-index:10; margin-left: 8px;" onclick="changeImage(-1)">&#9664;</button>
                    <div class="product-image" id="productImage" style="height: 360px;">
                        <img
                            id="activeImage"
                            src="{{ asset('Pictures/' . $slug . '1.jpg') }}"
                            alt="{{ $product->name }}"
                            data-basename="{{ $slug }}"
                            data-imagefolder="{{ asset('Pictures') . '/' }}"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <button class="arrow-section position-absolute top-50 end-0 translate-middle-y" style="z-index:10; margin-right: 8px;" onclick="changeImage(1)">&#9654;</button>
                </div>
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center">

                @php
                    $frameLabels = [];
                    if ($product->is_discounted) $frameLabels[] = 'Akcia';
                    if ($product->is_new) $frameLabels[] = 'Novinka';
                    if ($product->is_best_seller) $frameLabels[] = 'Best seller';
                    $frameClass = $product->is_discounted
                        ? 'product-frame--sale'
                        : (count($frameLabels) ? 'product-frame--highlight' : '');
                @endphp
                <div class="product-frame {{ $frameClass }}">
                    <h2>{{ $product->name }}</h2>
                    @if (count($frameLabels))
                        <div class="product-frame-label">{{ implode(' · ', $frameLabels) }}</div>
                    @endif
                </div>

                <div class="product-badges">
                    @foreach ($product->categories as $cat)
                        <a href="{{ url('/shop/' . $cat->slug) }}" class="badge-pill badge-category">{{ $cat->name }}</a>
                    @endforeach
                </div>

                <p class="price">

                    @if ($product->is_discounted && $product->discounted_price)
                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2) }} €</span>
                        <span class="text-success fw-bold ms-2">{{ number_format($product->discounted_price, 2) }} €</span>
                    @else
                        <span>{{ number_format($product->price, 2) }} €</span>
                    @endif

                </p>

                <div class="stock-status {{ $product->in_stock ? 'in-stock' : 'out-of-stock' }}">
                    @if ($product->in_stock)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 13 4 4L19 7"/>
                        </svg>
                        Skladom
                    @else
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                        Vypredané
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2 mt-2">

                    @if (!$product->in_stock)
                    <button type="button" class="btn btn-secondary" disabled>Vypredané</button>
                    @elseif ($isInCart)
                    <form action="{{ route('cart.remove', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Odobrať z košíka</button>
                    </form>
                    @else
                    <form action="{{ route('cart.add', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Pridať do košíka</button>
                    </form>
                    @endif

                    <form action="{{ route('favorite.toggle', ['id' => $product->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="heart-icon" style="position:static; width:40px; height:40px; background:white; border-radius:50%; border: 1px solid var(--color-border); box-shadow: var(--shadow-sm); display:flex; align-items:center; justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="{{ $product->is_favorite ? '#DC2626' : '#D1D5DB' }}" xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                    </form>

                </div>

                <div class="mt-3">
                    <a href="{{ url('/shop') }}" class="btn" style="background:var(--color-primary-light);color:var(--color-primary);border-radius:var(--radius-full);font-weight:500;">← Späť do obchodu</a>
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
