<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function show(int $id)
    {
        $product = Product::with(['images', 'categories', 'label', 'discount'])
            ->findOrFail($id);

        $isFavorite = auth()->check()
            ? auth()->user()->favorites()->where('product_id', $id)->exists()
            : in_array($id, session()->get('guest_favorites', []));

        return view('details', [
            'product'    => $product,
            'isInCart'   => $this->isInCart($id),
            'isFavorite' => $isFavorite,
            'fromLabel'  => request('from_label'),
            'fromUrl'    => request('from_url'),
        ]);
    }

    public function index(Request $request, ?int $categoryId = null)
    {
        $sort     = $request->query('sort', 'asc');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $maxAge   = $request->query('max_age');
        $players  = $request->query('players');

        $query = Product::with(['images', 'discount']);

        if ($categoryId) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
        }

        if ($maxAge) {
            $query->where('min_age', '<=', $maxAge);
        }

        if ($players) {
            $query->where('max_players', '>=', $players);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $query->orderBy('name', $sort);
        }

        $products = $query->get()->map(function ($product) {
            $product->effective_price = $product->effectivePrice();
            return $product;
        });

        if ($minPrice !== null) {
            $products = $products->filter(fn($p) => $p->effective_price >= $minPrice);
        }

        if ($maxPrice !== null) {
            $products = $products->filter(fn($p) => $p->effective_price <= $maxPrice);
        }

        if ($sort === 'price_asc') {
            $products = $products->sortBy('effective_price')->values();
        } elseif ($sort === 'price_desc') {
            $products = $products->sortByDesc('effective_price')->values();
        }

        $perPage  = 30;
        $page     = $request->input('page', 1);
        $paginated = new LengthAwarePaginator(
            $products->slice(($page - 1) * $perPage, $perPage)->values(),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('shop', [
            'products'      => $paginated,
            'categoryTitle' => 'Shop',
            'sort'          => $sort,
        ]);
    }

    public function addToCart(int $id)
    {
        Product::findOrFail($id);

        $cart     = $this->getOrCreateCart();
        $existing = $cart->items()->where('product_id', $id)->first();

        if ($existing) {
            $existing->increment('quantity');
            $quantity = $existing->fresh()->quantity;
        } else {
            $cart->items()->create(['product_id' => $id, 'quantity' => 1]);
            $quantity = 1;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'quantity'          => $quantity,
                'cart_count'        => $cart->items()->sum('quantity'),
                'cart_preview_html' => view('partials.cart-preview')->render(),
            ]);
        }

        return redirect()->back();
    }

    public function removeFromCart(int $id)
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->where('product_id', $id)->delete();

        if (request()->wantsJson()) {
            $cartItems = $cart->items()->with('product.discount')->get();
            $cartTotal = $cartItems->sum(fn($i) => $i->product->effectivePrice() * $i->quantity);

            return response()->json([
                'removed'           => true,
                'cart_count'        => $cartItems->sum('quantity'),
                'cart_total'        => number_format($cartTotal, 2),
                'cart_preview_html' => view('partials.cart-preview')->render(),
            ]);
        }

        return redirect()->back();
    }

    public function updateCartQuantity(Request $request, int $id)
    {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->where('product_id', $id)->first();
        $removed = false;

        if ($item) {
            if ($request->action === 'increase') {
                $item->increment('quantity');
            } elseif ($request->action === 'decrease') {
                if ($item->quantity <= 1) {
                    $item->delete();
                    $removed = true;
                } else {
                    $item->decrement('quantity');
                }
            } elseif ($request->action === 'set') {
                $newQty = (int) $request->input('quantity', 1);
                if ($newQty <= 0) {
                    $item->delete();
                    $removed = true;
                } else {
                    $item->update(['quantity' => $newQty]);
                }
            }
        }

        if (request()->wantsJson()) {
            $item       = $removed ? null : $item->fresh();
            $cartItems  = $cart->items()->with('product.discount')->get();
            $cartTotal  = $cartItems->sum(fn($i) => $i->product->effectivePrice() * $i->quantity);

            return response()->json([
                'removed'           => $removed,
                'quantity'          => $item?->quantity,
                'cart_total'        => number_format($cartTotal, 2),
                'cart_count'        => $cartItems->sum('quantity'),
                'cart_preview_html' => view('partials.cart-preview')->render(),
            ]);
        }

        return redirect()->back();
    }

    public function toggleFavorite(int $id)
    {
        Product::findOrFail($id);

        if (auth()->check()) {
            auth()->user()->favorites()->toggle($id);
        } else {
            $favorites = session()->get('guest_favorites', []);

            if (in_array($id, $favorites)) {
                $favorites = array_values(array_filter($favorites, fn($fid) => $fid !== $id));
            } else {
                $favorites[] = $id;
            }

            session()->put('guest_favorites', $favorites);
        }

        return redirect()->back();
    }

    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }

        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }

    public function isInCart(int $productId): bool
    {
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if (!$cart) return false;

        return $cart->items()->where('product_id', $productId)->exists();
    }
}
