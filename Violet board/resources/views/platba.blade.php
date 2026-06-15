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

    @include('partials.footer')

    <script src="{{ asset('js/platba.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
