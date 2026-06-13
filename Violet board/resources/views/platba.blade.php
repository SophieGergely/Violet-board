<!DOCTYPE html>
<html lang="en">
<>
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

    <div class="container mt-5 pt-5" style="margin-top: 120px !important;">
        <div class="row justify-content-center">
            <div class="col-md-6" style="margin-bottom: 80px;">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-center">Spolu: {{ collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']) }}€</h3>
                    <h4 class="text-center mt-4">Spôsob platby</h4>
                    <div class="payment-methods d-flex justify-content-between mt-3">

                        <div class="payment-option" onclick="selectPayment('card')">
                            <span class="radio-button selected" id="radio-card"></span>
                            <span>Platobná karta</span>
                        </div>

                        <div class="payment-option" onclick="selectPayment('cash')">
                            <span class="radio-button" id="radio-cash"></span>
                            <span>Osobne pri doručení</span>
                        </div>

                    </div>
                    <div class="mt-3">

                        <div class="mb-3">
                            <label>Číslo karty</label>
                            <input type="text" class="form-control" id="card-number" placeholder="**** **** **** ****" maxlength="19" inputmode="numeric" pattern="\d{4} \d{4} \d{4} \d{4}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Dátum platnosti</label>
                                <input type="text" class="form-control" id="card-expiry" placeholder="MM/RR" pattern="(0[1-9]|1[0-2])/\d{2}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>CVC</label>
                                <input type="text" class="form-control" id="card-cvc" placeholder="***" maxlength="3" pattern="\d{3}">
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-center">
                            <form id="paymentForm" method="POST" action="{{ route('place.order') }}">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-narrow">Dokončenie objednávky</button>
                            </form>
                        </div>
                    </div>
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

</body>
</html>
