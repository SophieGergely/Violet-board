<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Platba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body>
    @include('partials.header')

    @php $cart = session('cart', []); @endphp

    <div class="container mt-5 pt-5" style="margin-top: 120px !important;">
        <div class="row justify-content-center">
            <div class="col-md-6" style="margin-bottom: 80px;">
                <div class="bg-white rounded-xl p-5 shadow-lg" style="border:1px solid var(--color-border);">

                    <h3 class="text-center fw-bold mb-1">
                        {{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }} €
                    </h3>
                    <p class="text-center text-muted mb-4">Celková suma objednávky</p>

                    <h5 class="fw-semibold mb-3">Spôsob platby</h5>
                    <div class="payment-methods mb-4">
                        <div class="payment-option" onclick="selectPayment('card')">
                            <span class="radio-button selected" id="radio-card"></span>
                            <span>💳 Platobná karta</span>
                        </div>
                        <div class="payment-option mt-2" onclick="selectPayment('cash')">
                            <span class="radio-button" id="radio-cash"></span>
                            <span>💵 Osobne pri doručení</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Číslo karty</label>
                        <input type="text" class="form-control" id="card-number"
                            placeholder="**** **** **** ****" maxlength="19" inputmode="numeric">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Dátum platnosti</label>
                            <input type="text" class="form-control" id="card-expiry" placeholder="MM/RR">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">CVC</label>
                            <input type="text" class="form-control" id="card-cvc" placeholder="***" maxlength="3">
                        </div>
                    </div>

                    <form id="paymentForm" method="POST" action="{{ route('place.order') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            Dokončiť objednávku
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script>
        function selectPayment(option) {
            document.getElementById('radio-card').classList.remove('selected');
            document.getElementById('radio-cash').classList.remove('selected');
            document.getElementById(`radio-${option}`).classList.add('selected');
        }
    </script>
    <script src="{{ asset('js/platba.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
