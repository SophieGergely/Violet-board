<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;



class SearchController extends Controller
{
    private function removeAccents($string)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }



    // Shared accent-insensitive name matching, used by both the full
    // results page (index) and the live suggestion dropdown (suggest).
    private function matchingProducts(string $normalizedQuery)
    {
        return Product::with('images')->get()->filter(function ($product) use ($normalizedQuery) {
            $normalizedName = strtolower($this->removeAccents($product->name));
            return str_contains($normalizedName, $normalizedQuery);
        })->values();
    }



    public function index(Request $request)
    {
        $query = $request->input('query');
        $normalizedQuery = strtolower($this->removeAccents($query));

        $sort = $request->query('sort', 'asc');

        $productsCollection = $this->matchingProducts($normalizedQuery)->map(function ($product) {
            $product->effective_price = $product->is_discounted && $product->discounted_price
                ? $product->discounted_price
                : $product->price;
            return $product;
        });

        if ($sort === 'price_asc') {
            $productsCollection = $productsCollection->sortBy('effective_price')->values();
        } elseif ($sort === 'price_desc') {
            $productsCollection = $productsCollection->sortByDesc('effective_price')->values();
        } elseif ($sort === 'desc') {
            $productsCollection = $productsCollection->sortByDesc('name')->values();
        } else {
            $productsCollection = $productsCollection->sortBy('name')->values();
        }

        $perPage = 42;
        $currentPage = $request->input('page', 1);
        $currentItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $currentItems,
            $productsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categoryTitle = "Výsledky hľadania pre: \"$query\"";

        return view('shop', [
            'products' => $products,
            'categoryTitle' => $categoryTitle,
            'sort' => $sort,
        ]);
    }



    // Lightweight JSON endpoint for the live "as you type" search
    // suggestions dropdown in the header search bar.
    public function suggest(Request $request)
    {
        $query = trim((string) $request->input('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $normalizedQuery = strtolower($this->removeAccents($query));

        $results = $this->matchingProducts($normalizedQuery)
            ->take(8)
            ->map(function ($product) {
                $effectivePrice = $product->is_discounted && $product->discounted_price
                    ? $product->discounted_price
                    : $product->price;

                $image = $product->images->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => number_format($effectivePrice, 2),
                    'image' => $image ? asset('Pictures/' . $image->filename) : null,
                    'url' => route('product.show', $product->id),
                ];
            })
            ->values();

        return response()->json($results);
    }
}
