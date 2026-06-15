<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;



class NavBarController extends Controller
{
    public function showAkcie(Request $request)
    {
        $sort = $request->query('sort', 'default');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $maxAge = $request->query('vekova_kategoria');
        $players = $request->query('hracov');

        $products = Product::where('is_discounted', true)
            ->with(['images' => fn($query) => $query->orderBy('filename')]);

        if (!is_null($minPrice)) {
            $products->whereRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) >= ?', [$minPrice]);
        }

        if (!is_null($maxPrice)) {
            $products->whereRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) <= ?', [$maxPrice]);
        }

        if (!is_null($maxAge)) {
            $products->where('min_age', '<=', $maxAge);
        }

        if (!is_null($players)) {
            $products->where('max_players', '>=', $players);
        }

        switch ($sort) {
            case 'price_asc':
                $products->orderByRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) ASC');
                break;
            case 'price_desc':
                $products->orderByRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) DESC');
                break;
            case 'desc':
                $products->orderBy('name', 'desc');
                break;
            case 'asc':
            default:
                $products->orderBy('name', 'asc');
                break;
        }

        $products = $products->paginate(42)->appends($request->query());

        return view('shop', [
            'products' => $products,
            'categoryTitle' => 'Akcie',
            'sort' => $sort,
            'vekova_kategoria' => $maxAge,
            'hracov' => $players,
        ]);
    }


    public function showNovinky(Request $request)
    {
        $sort = $request->query('sort', 'default');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $maxAge = $request->query('vekova_kategoria');
        $players = $request->query('hracov');

        $products = Product::where('is_new', true)
            ->with(['images' => fn($query) => $query->orderBy('filename')]);

        if (!is_null($minPrice)) {
            $products->where('price', '>=', $minPrice);
        }

        if (!is_null($maxPrice)) {
            $products->where('price', '<=', $maxPrice);
        }

        if (!is_null($maxAge)) {
            $products->where('min_age', '<=', $maxAge);
        }

        if (!is_null($players)) {
            $products->where('max_players', '>=', $players);
        }

        switch ($sort) {
            case 'price_asc':
                $products->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $products->orderBy('price', 'desc');
                break;
            case 'desc':
                $products->orderBy('name', 'desc');
                break;
            case 'asc':
            default:
                $products->orderBy('name', 'asc');
                break;
        }

        $products = $products->paginate(42)->appends($request->query());

        return view('shop', [
            'products' => $products,
            'categoryTitle' => 'Novinky',
            'sort' => $sort,
            'vekova_kategoria' => $maxAge,
            'hracov' => $players,
        ]);
    }

    public function showBestSellers(Request $request)
    {
        $sort = $request->query('sort', 'default');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $maxAge = $request->query('vekova_kategoria');
        $players = $request->query('hracov');

        $products = Product::where('is_best_seller', true)
            ->with(['images' => fn($query) => $query->orderBy('filename')]);

        if (!is_null($minPrice)) {
            $products->where('price', '>=', $minPrice);
        }

        if (!is_null($maxPrice)) {
            $products->where('price', '<=', $maxPrice);
        }

        if (!is_null($maxAge)) {
            $products->where('min_age', '<=', $maxAge);
        }

        if (!is_null($players)) {
            $products->where('max_players', '>=', $players);
        }

        switch ($sort) {
            case 'price_asc':
                $products->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $products->orderBy('price', 'desc');
                break;
            case 'desc':
                $products->orderBy('name', 'desc');
                break;
            case 'asc':
            default:
                $products->orderBy('name', 'asc');
                break;
        }

        $products = $products->paginate(42)->appends($request->query());

        return view('shop', [
            'products' => $products,
            'categoryTitle' => 'Best sellers',
            'sort' => $sort,
            'vekova_kategoria' => $maxAge,
            'hracov' => $players,
        ]);
    }

    public function showOblubene(Request $request)
    {
        $sort = $request->query('sort', 'default');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $maxAge = $request->query('vekova_kategoria');
        $players = $request->query('hracov');

        $products = Product::where('is_favorite', true)
            ->with(['images' => fn($query) => $query->orderBy('filename')]);

        if (!is_null($minPrice)) {
            $products->whereRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) >= ?', [$minPrice]);
        }

        if (!is_null($maxPrice)) {
            $products->whereRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) <= ?', [$maxPrice]);
        }

        if (!is_null($maxAge)) {
            $products->where('min_age', '<=', $maxAge);
        }

        if (!is_null($players)) {
            $products->where('max_players', '>=', $players);
        }

        switch ($sort) {
            case 'price_asc':
                $products->orderByRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) ASC');
                break;
            case 'price_desc':
                $products->orderByRaw('(CASE WHEN discounted_price IS NOT NULL THEN discounted_price ELSE price END) DESC');
                break;
            case 'desc':
                $products->orderBy('name', 'desc');
                break;
            case 'asc':
            default:
                $products->orderBy('name', 'asc');
                break;
        }

        $products = $products->paginate(42)->appends($request->query());

        return view('shop', [
            'products' => $products,
            'categoryTitle' => 'Vaše obľúbené produkty',
            'sort' => $sort,
            'vekova_kategoria' => $maxAge,
            'hracov' => $players,
        ]);
    }
}
