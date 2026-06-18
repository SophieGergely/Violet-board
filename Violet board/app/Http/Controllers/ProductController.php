<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;



class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with([
            'images' => function($query) {
                $query->orderBy('filename');
            },
            'categories',
        ])->findOrFail($id);

        return view('detaily', [
            'product' => $product,
            'isInCart' => ProductController::isInCart($product->id),
            'fromLabel' => request('from_label'),
            'fromUrl' => request('from_url'),
        ]);
    }



    public function index(Request $request, $category = null)
    {
        $sort = $request->query('sort', 'asc');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $vekovaKategoria = $request->query('vekova_kategoria');
        $pocetHracov = $request->query('hracov');

        $query = Product::query();

        if ($category) {
            $query->where('category', $category);
        }

        if ($vekovaKategoria) {
            $query->where('min_age', '<=', $vekovaKategoria);
        }

        if ($pocetHracov) {
            $query->where('max_players', '>=' ,$pocetHracov);
        }

        $query->with('images');

        if ($sort === 'desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'asc') {
            $query->orderBy('name', 'asc');
        }

        $products = $query->get()->filter(function ($product) use ($minPrice, $maxPrice) {
            $effectivePrice = $product->is_discounted && $product->discounted_price
                ? $product->discounted_price
                : $product->price;

            if ($minPrice !== null && $effectivePrice < $minPrice) {
                return false;
            }
            if ($maxPrice !== null && $effectivePrice > $maxPrice) {
                return false;
            }

            $product->effective_price = $effectivePrice;
            return true;
        });

        if ($sort === 'price_asc') {
            $products = $products->sortBy('effective_price')->values();
        } elseif ($sort === 'price_desc') {
            $products = $products->sortByDesc('effective_price')->values();
        }

        $perPage = 42;
        $page = $request->input('page', 1);
        $pagedItems = $products->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedItems,
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('shop', [
            'products' => $paginated,
            'categoryTitle' => $category ? ucfirst($category) : 'Shop',
            'sort' => $sort
        ]);
    }



    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $price = $product->is_discounted && $product->discounted_price
            ? $product->discounted_price
            : $product->price;

        if (!Auth::check()) {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    "name" => $product->name,
                    "quantity" => 1,
                    "price" => $price,
                    "original_price" => $product->price,
                    "is_discounted" => $product->is_discounted,
                    "image" => $product->images->first()->filename ?? 'placeholder.jpg'
                ];
            }
            session()->put('cart', $cart);
        } else {
            $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
            if ($cart) {
                $cartItems = json_decode($cart->items, true);
            } else {
                $cartItems = [];
            }

            if (isset($cartItems[$id])) {
                $cartItems[$id]['quantity']++;
            } else {
                $cartItems[$id] = [
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => $product->is_discounted && $product->discounted_price
                        ? $product->discounted_price
                        : $product->price,
                    'original_price' => $product->price,
                    'is_discounted' => $product->is_discounted,
                    'image' => $product->images->first()->filename ?? 'placeholder.jpg'
                ];
            }

            \App\Models\Cart::updateOrCreate(
                ['user_id' => auth()->id()],
                ['items' => json_encode($cartItems)]
            );

            session()->put('cart', $cartItems);
        }

        return redirect()->back();
    }



    public function toggleFavorite($id)
    {
        $product = Product::findOrFail($id);
        $product->is_favorite = !$product->is_favorite;
        $product->save();

        return redirect()->back();
    }



    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);
        $this->saveCartToDatabase();
        return redirect()->back();
    }



    public function updateCartQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($request->action === 'increase') {
                $cart[$id]['quantity']++;
            } elseif ($request->action === 'decrease' && $cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            }
        }

        session()->put('cart', $cart);
        $this->saveCartToDatabase();
        return redirect()->back();
    }



    private function saveCartToDatabase()
    {
        if (auth()->check()) {
            \App\Models\Cart::updateOrCreate(
                ['user_id' => auth()->id()],
                ['items' => json_encode(session('cart', []))]
            );
        }
    }



    public static function isInCart($productId)
    {
        if (auth()->check()) {
            $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
            if ($cart) {
                $items = json_decode($cart->items, true);
                return isset($items[$productId]);
            }
        } else {
            $cart = session()->get('cart', []);
            return isset($cart[$productId]);
        }

        return false;
    }
}
