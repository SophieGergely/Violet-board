<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SideBarController;
use App\Http\Controllers\NavBarController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DakujemeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BoxCollectController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;

// -- Home --
Route::get('/', [HomeController::class, 'index'])->name('home');

// -- Shop --
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/akcie', [NavBarController::class, 'showAkcie']);
Route::get('/shop/novinky', [NavBarController::class, 'showNovinky']);
Route::get('/shop/best-sellers', [NavBarController::class, 'showBestSellers']);
Route::get('/shop/oblubene', [NavBarController::class, 'showOblubene']);
Route::get('/shop/{category}', [SideBarController::class, 'showCategory'])
    ->where('category', '^(?!akcie|novinky|best-sellers).*$');

// -- Product --
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/cart/add/{id}', [ProductController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update/{id}', [ProductController::class, 'updateCartQuantity'])->name('cart.update');
Route::post('/favorite/toggle/{id}', [ProductController::class, 'toggleFavorite'])->name('favorite.toggle');
Route::post('/favorite/{id}', [ProductController::class, 'toggleFavorite'])->name('product.favorite');

// -- Cart --
Route::get('/kosik', function () { return view('kosik'); });

// -- Shipping --
Route::get('/kurierskadoprava', function () {
    return view('doprava', ['mode' => 'kurier', 'locations' => []]);
});
Route::get('/boxcollect', [BoxCollectController::class, 'showForm'])->name('boxcollect.form');

// -- Payment --
Route::get('/platba', function () { return view('platba'); });
Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('place.order');

// -- Auth --
Route::get('/prihlasenie', function () { return view('prihlasenie'); });
Route::get('/registracia', function () { return view('registracia'); });
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', function () {
    session()->forget('cart');
    Auth::logout();
    return redirect('/prihlasenie');
})->name('logout');
Route::delete('/profil/zmazat', [AuthController::class, 'destroy'])->middleware('auth')->name('profil.zmazat');

// -- Search --
Route::get('/search/suggest', [App\Http\Controllers\SearchController::class, 'suggest'])->name('search.suggest');
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
