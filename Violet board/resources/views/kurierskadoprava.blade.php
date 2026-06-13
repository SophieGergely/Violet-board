<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuriérska služba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>



<body>
    @include('partials.header')

    @php $cart = session('cart', []); @endphp

    <div class="container mt-5 pt-5" style="margin-top: 120px !important;">
        <div class="price-container text-center">
            Spolu: {{ collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']) }}€ </div>
        <div class="shipping-container p-3 mt-3">
            <div>Spôsob dopravy:</div>
            <div class="d-flex justify-content-around mt-2">

                <div class="shipping-option" onclick="window.location.href='{{ url('/kurierskadoprava') }}'">
                    <span class="radio-button {{ request()->is('kurierskadoprava') ? 'selected' : '' }}" id="radio-kurier"></span>
                    Kuriérska služba
                </div>

                <div class="shipping-option" onclick="window.location.href='{{ url('/boxcollect') }}'">
                    <span class="radio-button {{ request()->is('boxcollect') ? 'selected' : '' }}" id="radio-box"></span>
                    Box collect
                </div>

            </div>
        </div>

        <form id="shipping-form" class="row g-3 mt-4">
            <div class="col-md-6">
                <label class="form-label">Meno a priezvisko</label>
                <input type="text" class="form-control" id="full-name" required pattern="^[A-ZÁÉÍÓÚÝŔĽŇŽŠČĎŤÄÖÜ][a-záéíóúýŕľňžščďťäöü]+( [A-ZÁÉÍÓÚÝŔĽŇŽŠČĎŤÄÖÜ][a-záéíóúýŕľňžščďťäöü]+)+$">
                <div class="invalid-feedback">Zadajte celé meno vo formáte: Meno Priezvisko.</div>

                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="email" required pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$">
                <div class="invalid-feedback">Zadajte platnú emailovú adresu (napr. meno@example.com).</div>

                <label class="form-label">Telefónne číslo</label>
                <input type="tel" class="form-control" id="phone" required pattern="^\+?\d{9,15}$">
                <div class="invalid-feedback">Zadajte platné telefónne číslo (napr. +421901234567).</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Krajina</label>
                <input type="text" class="form-control" id="country" required>
                <div class="invalid-feedback">Zadajte krajinu.</div>

                <label class="form-label">Číslo domu</label>
                <input type="text" class="form-control" id="house-number" required pattern="^[0-9]+[a-zA-Z]?$">
                <div class="invalid-feedback">Zadajte správne číslo domu (napr. 123 alebo 123A).</div>

                <label class="form-label">PSČ</label>
                <input type="text" class="form-control" id="postal-code" required pattern="^\d{5}$">
                <div class="invalid-feedback">Zadajte platné PSČ (napr. 81101).</div>
            </div>

        </form>
        <button id="next-button" type="click" class="btn btn-dark mt-3">Ďalej na platbu</button>
    </div>

    @include('partials.footer')

    <script>
        function selectShipping(option) {
            document.getElementById('radio-kurier').classList.remove('selected');
            document.getElementById('radio-box').classList.remove('selected');
            document.getElementById(`radio-${option}`).classList.add('selected');

            if (option === 'kurier') {
                window.location.href = "{{ url('/kurierskadoprava') }}";
            } else if (option === 'box') {
                window.location.href = "{{ url('/boxcollect') }}";
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/kurierska.js') }}"></script>
</body>
</html>
