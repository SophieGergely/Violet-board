@php $cart = $headerCart ?? session('cart', []); @endphp
@if (count($cart))
    <div class="navbar-cart-preview-items">
        @foreach ($cart as $cartItemId => $cartItem)
            <a href="{{ route('product.show', $cartItemId) }}" class="navbar-cart-preview-item">
                <img src="{{ asset('Pictures/' . $cartItem['image']) }}" alt="{{ $cartItem['name'] }}">
                <span class="navbar-cart-preview-name">{{ $cartItem['name'] }}</span>
                <span class="navbar-cart-preview-qty">×{{ $cartItem['quantity'] }}</span>
                <span class="navbar-cart-preview-price">{{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }} €</span>
            </a>
        @endforeach
    </div>
    <div class="navbar-cart-preview-total">
        <span>Spolu</span>
        <span>{{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }} €</span>
    </div>
    <a href="/kosik" class="navbar-cart-preview-cta">Prejsť do košíka</a>
@else
    <div class="navbar-cart-preview-empty">Váš košík je prázdny</div>
@endif
