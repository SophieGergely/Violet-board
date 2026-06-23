<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'box' ? 'Box Collect' : 'Courier Delivery' }} – Violet Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    @include('partials.header')

    @php
        use App\Models\Cart;
        if (auth()->check()) {
            $cartModel = Cart::with('items.product.discount')->where('user_id', auth()->id())->first();
        } else {
            $cartModel = Cart::with('items.product.discount')->where('session_id', session()->getId())->first();
        }
        $cartItems = $cartModel?->items ?? collect();
        $cartTotal = $cartItems->sum(fn($i) => $i->product->effectivePrice() * $i->quantity);
    @endphp

    <div class="container" style="max-width:700px;padding-top:32px;">

        <div class="price-container text-center mb-3">
            <span class="text-muted" style="font-size:0.9rem;">Order Total</span>
            <div style="font-size:1.6rem;font-weight:700;color:var(--color-primary-dark);">
                {{ number_format($cartTotal, 2) }} €
            </div>
        </div>

        <div class="mb-4">
            <div class="fw-semibold mb-2" style="color:var(--color-text-muted);">Delivery Method</div>
            <div class="d-flex flex-column flex-sm-row gap-3">
                <div class="shipping-option {{ $mode === 'kurier' ? 'active' : '' }} flex-fill"
                     onclick="window.location.href='{{ route('delivery.courier') }}'">
                    <span class="radio-button {{ $mode === 'kurier' ? 'selected' : '' }}"></span>
                    Courier Service
                </div>
                <div class="shipping-option {{ $mode === 'box' ? 'active' : '' }} flex-fill"
                     onclick="window.location.href='{{ route('boxcollect.form') }}'">
                    <span class="radio-button {{ $mode === 'box' ? 'selected' : '' }}"></span>
                    Box Collect
                </div>
            </div>
        </div>

        <div class="bg-white p-4 mb-5" style="border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);">
            <h5 class="fw-semibold mb-4" style="color:var(--color-primary-dark);">Delivery Details</h5>

            @if($mode === 'box')
                <form id="shipping-form" method="GET" action="{{ route('checkout') }}" class="row g-3" novalidate>
            @else
                <form id="shipping-form" class="row g-3" novalidate>
            @endif

                <div class="col-md-6">
                    <label class="form-label fw-medium">First Name</label>
                    <input type="text" class="form-control" name="first_name" placeholder="John"
                        value="{{ auth()->user()?->first_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Last Name</label>
                    <input type="text" class="form-control" name="last_name" placeholder="Doe"
                        value="{{ auth()->user()?->last_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="your@email.com"
                        value="{{ auth()->user()?->email ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">Phone</label>
                    <input type="tel" class="form-control" name="phone" placeholder="+421901234567" required>
                </div>

                @if($mode === 'box')
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Country</label>
                        <select name="country" class="form-select" required>
                            <option value="Slovakia">Slovakia</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Select Box</label>
                        <select name="box_location" class="form-select" required>
                            <option value="" disabled selected>Choose a location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location['name'] }}"
                                        data-street="{{ $location['street'] }}"
                                        data-city="{{ $location['city'] }}">
                                    {{ $location['name'] }} – {{ $location['street'] }}, {{ $location['city'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="box_street" id="box-street" value="">
                        <input type="hidden" name="box_city"   id="box-city"   value="">
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Street & Number</label>
                        <input type="text" class="form-control" id="street" placeholder="Main St 42" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">City</label>
                        <input type="text" class="form-control" id="city" placeholder="Bratislava" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">State / Region</label>
                        <input type="text" class="form-control" id="state" placeholder="Slovakia" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Postal Code</label>
                        <input type="text" class="form-control" id="postal-code" placeholder="81101" required>
                    </div>
                @endif

                <div class="col-12 text-center mt-4">
                    @if($mode === 'box')
                        <button type="submit" id="submit-btn" class="btn btn-primary px-5">Continue to Payment</button>
                    @else
                        <button type="button" id="next-button" class="btn btn-primary px-5">Continue to Payment</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if($mode === 'box')
        <script src="{{ asset('js/boxcollect.js') }}"></script>
    @else
        <script src="{{ asset('js/kurierska.js') }}"></script>
    @endif
</body>
</html>
