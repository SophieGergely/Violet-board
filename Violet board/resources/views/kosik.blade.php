<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Košík</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    @include('partials.header')

    <div class="container cart-container" style="margin-top: 24px;">
        @php $cart = session('cart', []); @endphp

        <div class="row">
            <div class="col-md-8">
                <h2 style="font-family:var(--font-body);font-size:2rem;font-weight:600;color:var(--color-primary-dark);padding-bottom:4px;margin-bottom:4px;display:flex;align-items:center;justify-content:center;gap:10px;">
                    Košík
                </h2>
                <p class="text-center mb-4" style="color:var(--color-text-muted); border-bottom:2px solid var(--color-primary); padding-bottom:12px;">
                    @php $totalItems = collect($cart)->sum('quantity'); @endphp
                    Počet produktov v košíku: <span class="cart-grand-count">{{ $totalItems }}</span>
                </p>

                @forelse ($cart as $id => $item)
                    {{-- Flowbite card style cart item --}}
                    <div class="cart-item d-flex align-items-center gap-3" data-product-id="{{ $id }}">
                        <a href="{{ route('product.show', $id) }}?from_label={{ urlencode('Košík') }}&from_url={{ urlencode(url()->current()) }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark flex-grow-1">
                            <img src="{{ asset('Pictures/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                class="rounded" style="width:60px;height:60px;object-fit:cover;">

                            <div class="fw-medium">{{ $item['name'] }}</div>
                        </a>

                        {{-- Quantity controls --}}
                        <div class="d-flex align-items-center gap-1">
                            <form action="{{ route('cart.update', ['id' => $id]) }}" method="POST" class="d-inline ajax-cart-form">
                                @csrf
                                <input type="hidden" name="action" value="decrease">
                                <button class="btn btn-sm btn-outline-secondary px-2" type="submit">−</button>
                            </form>
                            <input
                                type="number"
                                class="quantity js-cart-qty-input"
                                value="{{ $item['quantity'] }}"
                                min="1"
                                data-update-url="{{ route('cart.update', ['id' => $id]) }}"
                            >
                            <form action="{{ route('cart.update', ['id' => $id]) }}" method="POST" class="d-inline ajax-cart-form">
                                @csrf
                                <input type="hidden" name="action" value="increase">
                                <button class="btn btn-sm btn-outline-secondary px-2" type="submit">+</button>
                            </form>
                        </div>

                        {{-- Price --}}
                        <div class="fw-semibold item-total" style="min-width:80px;text-align:right;">
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
                    <div class="text-center py-5">
                        <div class="empty-state-card">
                            <div style="margin-bottom:16px; display:flex; justify-content:center;">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 8h6l8 28h28l6-20H18" stroke="#6D28D9" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                    <circle cx="26" cy="52" r="4" fill="#6D28D9"/>
                                    <circle cx="44" cy="52" r="4" fill="#6D28D9"/>
                                </svg>
                            </div>
                            <h4 style="color:var(--color-primary);font-weight:600;margin-bottom:8px">
                                Váš košík je prázdny
                            </h4>
                            <p style="color:var(--color-text-muted);margin-bottom:24px">
                                Pridajte produkty do košíka a nakupujte u nás.
                            </p>
                            <a href="/shop" class="btn btn-primary px-5">Prejsť do obchodu</a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Summary --}}
            <div class="col-md-4">
                <div class="summary p-4">
                    <h5 class="fw-semibold mb-3">Súhrn objednávky</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Spolu:</span>
                        <span class="fw-bold cart-grand-total">{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }} €</span>
                    </div>
                    <hr>
                    <button onclick="checkCart()" class="btn btn-primary w-100 mt-2">
                        Vybrať spôsob doručenia
                    </button>
                    <a href="/shop" class="btn w-100 mt-2" style="background:var(--color-primary-light);color:var(--color-primary);border-radius:var(--radius-full);font-weight:500;">
                        ← Späť do obchodu
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Theme-matching popup modal (e.g. for an empty cart) --}}
    <div id="themedAlert" class="themed-alert" role="alert" onclick="if (event.target === this) closeThemedAlert()">
        <div class="themed-alert-card">
            <button type="button" class="themed-alert-close" onclick="closeThemedAlert()" aria-label="Zavrieť">&times;</button>
            <div class="themed-alert-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="themed-alert-message" id="themedAlertMessage"></div>
        </div>
    </div>

    @include('partials.footer')

    <script>window.cartData = @json(session('cart', []));</script>
    <script>
        // Exposes showThemedAlert()/closeThemedAlert() globally so kosik.js
        // (and any other script on this page) can trigger the themed modal
        // instead of using the native browser alert().
        (function () {
            const themedAlert = document.getElementById('themedAlert');
            const themedAlertMessage = document.getElementById('themedAlertMessage');

            window.showThemedAlert = function (message) {
                themedAlertMessage.textContent = message;
                themedAlert.classList.add('show');
                document.body.style.overflow = 'hidden';
            };

            window.closeThemedAlert = function () {
                themedAlert.classList.remove('show');
                document.body.style.overflow = '';
            };

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && themedAlert.classList.contains('show')) {
                    window.closeThemedAlert();
                }
            });
        })();
    </script>
    <script src="{{ asset('js/kosik.js') }}"></script>
    <script src="{{ asset('js/cart-ajax.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
