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

    <div class="container" style="max-width: 700px; padding-top: 32px;">

        {{-- Cena --}}
        <div class="price-container text-center mb-3">
            <span class="text-muted" style="font-size: 0.9rem;">Spolu</span>
            <div style="font-size: 1.6rem; font-weight: 700; color: var(--color-primary-dark);">
                {{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }} €
            </div>
        </div>

        {{-- Výber dopravy --}}
        <div class="mb-4">
            <div class="fw-semibold mb-2" style="color: var(--color-text-muted);">Spôsob dopravy</div>
            <div class="d-flex flex-column flex-sm-row gap-3">
                <div class="shipping-option active flex-fill" onclick="window.location.href='{{ url('/kurierskadoprava') }}'">
                    <span class="radio-button selected"></span>
                    Kuriérska služba
                </div>
                <div class="shipping-option flex-fill" onclick="window.location.href='{{ url('/boxcollect') }}'">
                    <span class="radio-button"></span>
                    Box collect
                </div>
            </div>
        </div>

        {{-- Formulár --}}
        <div class="bg-white rounded-xl p-4 mb-5" style="border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
            <h5 class="fw-semibold mb-4" style="color: var(--color-primary-dark);">Doručovacie údaje</h5>
            <form id="shipping-form" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Meno a priezvisko</label>
                    <input type="text" class="form-control" id="full-name" required
                        pattern="^[A-ZÁÉÍÓÚÝŔĽŇŽŠČĎŤÄÖÜ][a-záéíóúýŕľňžščďťäöü]+( [A-ZÁÉÍÓÚÝŔĽŇŽŠČĎŤÄÖÜ][a-záéíóúýŕľňžščďťäöü]+)+$"
                        placeholder="Ján Novák">
                    <div class="invalid-feedback">Zadajte celé meno vo formáte: Meno Priezvisko.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" class="form-control" id="email" required
                        pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                        placeholder="jan@email.com">
                    <div class="invalid-feedback">Zadajte platnú emailovú adresu.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Telefónne číslo</label>
                    <input type="tel" class="form-control" id="phone" required
                        pattern="^\+?\d{9,15}$"
                        placeholder="+421901234567">
                    <div class="invalid-feedback">Zadajte platné telefónne číslo.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Krajina</label>
                    <input type="text" class="form-control" id="country" required placeholder="Slovensko">
                    <div class="invalid-feedback">Zadajte krajinu.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Číslo domu</label>
                    <input type="text" class="form-control" id="house-number" required
                        pattern="^[0-9]+[a-zA-Z]?$"
                        placeholder="123">
                    <div class="invalid-feedback">Zadajte správne číslo domu.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">PSČ</label>
                    <input type="text" class="form-control" id="postal-code" required
                        pattern="^\d{5}$"
                        placeholder="81101">
                    <div class="invalid-feedback">Zadajte platné PSČ (napr. 81101).</div>
                </div>

                <div class="col-12 text-center mt-4">
                    <button id="next-button" type="button" class="btn btn-primary px-5">Ďalej na platbu</button>
                </div>
            </form>
        </div>

    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/kurierska.js') }}"></script>
</body>
</html>
