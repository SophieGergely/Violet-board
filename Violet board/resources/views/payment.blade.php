<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout – Violet Board</title>
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

    <div class="container" style="max-width:560px;padding-top:32px;">

        <div class="price-container text-center mb-3">
            <span class="text-muted" style="font-size:0.9rem;">Order Total</span>
            <div style="font-size:1.6rem;font-weight:700;color:var(--color-primary-dark);">
                {{ number_format($cartTotal, 2) }} €
            </div>
        </div>

        <div class="bg-white p-4 mb-5" style="border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);">
            <h5 class="fw-semibold mb-3" style="color:var(--color-primary-dark);">Payment Method</h5>

            <div class="d-flex flex-column gap-2 mb-4">
                <div class="payment-option active" id="option-card" onclick="selectPayment('card')">
                    <span class="radio-button selected" id="radio-card"></span>
                    Credit / Debit Card
                </div>
                <div class="payment-option" id="option-cash" onclick="selectPayment('cash')">
                    <span class="radio-button" id="radio-cash"></span>
                    Cash on Delivery
                </div>
            </div>

            <div id="card-fields">
                <div class="mb-3">
                    <label class="form-label fw-medium">Card Number</label>
                    <input type="text" class="form-control" id="card-number"
                        placeholder="**** **** **** ****" maxlength="19" inputmode="numeric">
                    <div class="invalid-feedback" id="err-number"></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-medium">Expiry Date</label>
                        <input type="text" class="form-control" id="card-expiry" placeholder="MM/YY">
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
                <input type="hidden" name="first_name" value="{{ request('first_name', auth()->user()?->first_name ?? '') }}">
                <input type="hidden" name="last_name"  value="{{ request('last_name',  auth()->user()?->last_name  ?? '') }}">
                <input type="hidden" name="email"      value="{{ request('email',      auth()->user()?->email      ?? '') }}">
                <input type="hidden" name="phone"      value="{{ request('phone', '') }}">
                <input type="hidden" name="street"     value="{{ request('box_street', request('street', 'N/A')) }}">
                <input type="hidden" name="city"       value="{{ request('box_city',   request('city',   'N/A')) }}">
                <input type="hidden" name="state"      value="{{ request('state', request('country', 'Slovakia')) }}">
                <input type="hidden" name="delivery_method" value="{{ request('box_location') ? 'box_collect' : 'courier' }}">
                <input type="hidden" name="payment_method" id="payment_method_input" value="card">

                <button type="submit" id="submit-btn" class="btn btn-primary w-100 mt-3">
                    Place Order
                </button>
            </form>
        </div>
    </div>

    {{-- Success modal --}}
    <div id="thankYouModal" style="
        display:none; position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
        <div style="
            background:white; border-radius:var(--radius-lg);
            box-shadow:0 8px 32px rgba(109,40,217,0.18);
            padding:40px 32px; max-width:420px; width:90%; text-align:center;">
            <div style="margin-bottom:16px;">
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="4" width="64" height="64" rx="14" fill="#EDE9FE" stroke="#6D28D9" stroke-width="3.5"/>
                    <circle cx="22" cy="22" r="5.5" fill="#6D28D9"/>
                    <circle cx="50" cy="22" r="5.5" fill="#6D28D9"/>
                    <circle cx="22" cy="36" r="5.5" fill="#6D28D9"/>
                    <circle cx="50" cy="36" r="5.5" fill="#6D28D9"/>
                    <circle cx="22" cy="50" r="5.5" fill="#6D28D9"/>
                    <circle cx="50" cy="50" r="5.5" fill="#6D28D9"/>
                </svg>
            </div>
            <h3 class="fw-bold mb-2" style="color:var(--color-primary-dark);">Thank you for your order!</h3>
            <p class="text-muted mb-4">Your order has been successfully placed.</p>
            <div class="d-flex flex-column gap-2">
                <button class="btn btn-primary" onclick="window.location.href='/'">Go to Home Page</button>
                <button class="btn btn-dark"    onclick="window.location.href='/shop'">Continue Shopping</button>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/platba.js') }}"></script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: new FormData(form),
            })
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    const modal = document.getElementById('thankYouModal');
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(() => form.submit());
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
