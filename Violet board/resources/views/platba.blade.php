<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Platba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    @include('partials.header')

    @php $cart = session('cart', []); @endphp

    <div class="container" style="max-width: 560px; padding-top: 32px;">

        {{-- Cena --}}
        <div class="price-container text-center mb-3">
            <span class="text-muted" style="font-size: 0.9rem;">Celková suma objednávky</span>
            <div style="font-size: 1.6rem; font-weight: 700; color: var(--color-primary-dark);">
                {{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }} €
            </div>
        </div>

        {{-- Platobná karta / Dobierka --}}
        <div class="bg-white p-4 mb-5" style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">

            <h5 class="fw-semibold mb-3" style="color: var(--color-primary-dark);">Spôsob platby</h5>

            <div class="d-flex flex-column gap-2 mb-4">
                <div class="payment-option active" id="option-card" onclick="selectPayment('card')">
                    <span class="radio-button selected" id="radio-card"></span>
                    Platobná karta
                </div>
                <div class="payment-option" id="option-cash" onclick="selectPayment('cash')">
                    <span class="radio-button" id="radio-cash"></span>
                    Osobne pri doručení
                </div>
            </div>

            {{-- Karta polia --}}
            <div id="card-fields">
                <div class="mb-3">
                    <label class="form-label fw-medium">Číslo karty</label>
                    <input type="text" class="form-control" id="card-number"
                        placeholder="**** **** **** ****" maxlength="19" inputmode="numeric">
                    <div class="invalid-feedback" id="err-number"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-medium">Dátum platnosti</label>
                        <input type="text" class="form-control" id="card-expiry" placeholder="MM/RR">
                        <div class="invalid-feedback" id="err-expiry"></div>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-medium">CVC</label>
                        <input type="text" class="form-control" id="card-cvc" placeholder="***" maxlength="3">
                        <div class="invalid-feedback" id="err-cvc"></div>
                    </div>
                </div>
            </div>

            <form id="paymentForm" method="POST" action="{{ route('place.order') }}">
                @csrf
                <button type="submit" id="submit-btn" class="btn btn-primary w-100 mt-3">
                    Dokončiť objednávku
                </button>
            </form>

        </div>
    </div>

    {{-- Ďakujeme modal --}}
    <div id="dakujemeModal" style="
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.45);
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 32px rgba(109,40,217,0.18);
            padding: 40px 32px;
            max-width: 420px;
            width: 90%;
            text-align: center;
        ">
            <div style="margin-bottom: 16px;">
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="4" width="64" height="64" rx="14" fill="#EDE9FE" stroke="#6D28D9" stroke-width="3.5"/>
                    <!-- top-left -->
                    <circle cx="22" cy="22" r="5.5" fill="#6D28D9"/>
                    <!-- top-right -->
                    <circle cx="50" cy="22" r="5.5" fill="#6D28D9"/>
                    <!-- middle-left -->
                    <circle cx="22" cy="36" r="5.5" fill="#6D28D9"/>
                    <!-- middle-right -->
                    <circle cx="50" cy="36" r="5.5" fill="#6D28D9"/>
                    <!-- bottom-left -->
                    <circle cx="22" cy="50" r="5.5" fill="#6D28D9"/>
                    <!-- bottom-right -->
                    <circle cx="50" cy="50" r="5.5" fill="#6D28D9"/>
                </svg>
            </div>
            <h3 class="fw-bold mb-2" style="color: var(--color-primary-dark);">Ďakujeme za Váš nákup!</h3>
            <p class="text-muted mb-4">Vaša objednávka bola úspešne prijatá.</p>
            <div class="d-flex flex-column gap-2">
                <button class="btn btn-primary" onclick="window.location.href='/'">Prejsť na domovskú stránku</button>
                <button class="btn btn-dark" onclick="window.location.href='/shop'">Prejsť na zoznam produktov</button>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/platba.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
