<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please sign in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Auth::attempt() regenerates the session ID, so we must save it
        // beforehand – the guest cart in the DB is keyed by this old ID.
        $sessionIdBeforeLogin = session()->getId();

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Incorrect email or password.',
            ]);
        }

        $this->mergeGuestCartIntoUserCart($sessionIdBeforeLogin);
        $this->mergeGuestFavoritesIntoUser();

        return redirect('/')->with('success', 'Welcome back!');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function destroy()
    {
        $user = auth()->user();
        auth()->logout();
        $user->delete();

        return redirect('/')->with('success', 'Your account has been successfully deleted.');
    }

    private function mergeGuestFavoritesIntoUser(): void
    {
        $guestFavorites = session()->get('guest_favorites', []);

        if (empty($guestFavorites)) {
            return;
        }

        $user        = auth()->user();
        $existingIds = $user->favorites()->pluck('products.id')->toArray();
        $toAttach    = array_diff($guestFavorites, $existingIds);

        foreach ($toAttach as $productId) {
            $user->favorites()->attach($productId, ['created_at' => now()]);
        }

        session()->forget('guest_favorites');
    }

    private function mergeGuestCartIntoUserCart(string $sessionId): void
    {
        $guestCart = Cart::with('items')->where('session_id', $sessionId)->first();
        $userCart  = Cart::firstOrCreate(['user_id' => auth()->id()]);

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity'   => $guestItem->quantity,
                ]);
            }
        }

        $guestCart->delete();
    }
}
