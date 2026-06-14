<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Košík</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    @include('partials.header')

    <div class="container mt-5 pt-5 cart-container" style="margin-top: 120px !important;">
        @php $cart = session('cart', []); @endphp

        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4 fw-semibold">Košík
                    <span class="badge rounded-pill ms-2" style="background:var(--color-primary);font-size:0.9rem;">
                        {{ count($cart) }}
                    </span>
                </h3>

                @forelse ($cart as $id => $item)
                    {{-- Flowbite card style cart item --}}
                    <div class="cart-item d-flex align-items-center gap-3">
                        <img src="{{ asset('Pictures/' . $item['image']) }}" alt="{{ $item['name'] }}"
                            class="rounded" style="width:60px;height:60px;object-fit:cover;">

                        <div class="grow fw-medium">{{ $item['name'] }}</div>

                        {{-- Quantity controls --}}
                        <div class="d-flex align-items-center gap-1">
                            <form action="{{ route('cart.update', ['id' => $id]) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="action" value="decrease">
                                <button class="btn btn-sm btn-outline-secondary px-2">−</button>
                            </form>
                            <span class="quantity px-3">{{ $item['quantity'] }}</span>
                            <form action="{{ route('cart.update', ['id' => $id]) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="action" value="increase">
                                <button class="btn btn-sm btn-outline-secondary px-2">+</button>
                            </form>
                        </div>

                        {{-- Price --}}
                        <div class="fw-semibold" style="min-width:80px;text-align:right;">
                            @if (!empty($item['is_discounted']) && $item['is_discounted'])
                                <div class="text-decoration-line-through text-muted small">
                                    {{ number_format($item['original_price'] * $item['quantity'], 2) }} €
                                </div>
                                <div class="text-success">
                                    {{ number_format($item['price'] * $item['quantity'], 2) }} €
                                </div>
                            @else
                                {{ number_format($item['price'] * $item['quantity'], 2) }} €
                            @endif
                        </div>

                        {{-- Remove --}}
                        <form action="{{ route('cart.remove', ['id' => $id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger px-2">✕</button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <p class="fs-5">Košík je prázdny.</p>
                        <a href="/shop" class="btn btn-primary mt-2">Prejsť do obchodu</a>
                    </div>
                @endforelse
            </div>

            {{-- Summary --}}
            <div class="col-md-4">
                <div class="summary p-4">
                    <h5 class="fw-semibold mb-3">Súhrn objednávky</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Spolu:</span>
                        <span class="fw-bold">{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }} €</span>
                    </div>
                    <hr>
                    <button onclick="checkCart()" class="btn btn-primary w-100 mt-2">
                        Vybrať spôsob doručenia
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script>window.cartData = @json(session('cart', []));</script>
    <script src="{{ asset('js/kosik.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
