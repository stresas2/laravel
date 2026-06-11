<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'products.web.' . md5($request->getQueryString());

        $products = Cache::remember($cacheKey, 300, function () {
            return QueryBuilder::for(Product::class)
                ->allowedFilters([AllowedFilter::partial('description')])
                ->allowedSorts(['sku'])
                ->defaultSort('sku')
                ->paginate(20)
                ->withQueryString();
        });

        return view('products.index', compact('products'));
    }

    public function show(string $sku)
    {
        $product = Cache::remember("products.web.{$sku}", 300, function () use ($sku) {
            return Product::with('stocks')->where('sku', $sku)->firstOrFail();
        });

        return view('products.show', compact('product'));
    }
}
