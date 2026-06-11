<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $cacheKey = 'products.web.' . md5($search ?? '');

        $products = Cache::remember($cacheKey, 300, function () use ($search) {
            $query = Product::withCount(['stocks as total_stock' => function ($q) {
                $q->selectRaw('sum(stock)');
            }]);

            if ($search) {
                $query->where('description', 'like', "%{$search}%");
            }

            return $query->orderBy('sku')->paginate(20)->withQueryString();
        });

        return view('products.index', compact('products', 'search'));
    }

    public function show(string $sku)
    {
        $product = Cache::remember("products.web.{$sku}", 300, function () use ($sku) {
            return Product::with('stocks')->where('sku', $sku)->firstOrFail();
        });

        return view('products.show', compact('product'));
    }
}
