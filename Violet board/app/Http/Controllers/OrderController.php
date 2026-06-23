<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|max:255',
            'phone'           => 'required|string|max:20',
            'street'          => 'required|string|max:200',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'delivery_method' => 'required|string|max:50',
            'payment_method'  => 'required|string|max:50',
        ]);

        // Support both authenticated and guest users
        if (Auth::check()) {
            $cart = Cart::with('items.product.discount')
                ->where('user_id', Auth::id())
                ->first();
        } else {
            $cart = Cart::with('items.product.discount')
                ->where('session_id', session()->getId())
                ->first();
        }

        if (!$cart || $cart->items->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
            }
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $totalPrice = $cart->items->sum(
            fn($item) => $item->product->effectivePrice() * $item->quantity
        );

        $order = Order::create([
            'user_id'         => Auth::id(), // null for guests – make sure the column is nullable
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'street'          => $request->street,
            'city'            => $request->city,
            'state'           => $request->state,
            'delivery_method' => $request->delivery_method,
            'payment_method'  => $request->payment_method,
            'total_price'     => $totalPrice,
        ]);

        foreach ($cart->items as $item) {
            $order->products()->create([
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
            ]);
        }

        $cart->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/')->with('success', 'Thank you for your order!');
    }
}
