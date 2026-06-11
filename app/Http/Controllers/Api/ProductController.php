<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $cacheKey = 'products.api.' . md5($search ?? '');

        $products = Cache::remember($cacheKey, 300, function () use ($search) {
            $query = Product::with('stocks');

            if ($search) {
                $query->where('description', 'like', "%{$search}%");
            }

            return $query->get()->map(fn($p) => [
                'sku' => $p->sku,
                'description' => $p->description,
                'size' => $p->size,
                'photo' => $p->photo,
                'tags' => $p->tags,
                'total_stock' => $p->total_stock,
                'updated_at' => $p->product_updated_at,
            ]);
        });

        return response()->json(['data' => $products]);
    }

    public function show(string $sku): JsonResponse
    {
        $product = Cache::remember("products.api.{$sku}", 300, function () use ($sku) {
            return Product::with('stocks')->where('sku', $sku)->firstOrFail();
        });

        return response()->json([
            'data' => [
                'sku' => $product->sku,
                'description' => $product->description,
                'size' => $product->size,
                'photo' => $product->photo,
                'tags' => $product->tags,
                'total_stock' => $product->total_stock,
                'stocks' => $product->stocks,
                'updated_at' => $product->product_updated_at,
            ],
        ]);
    }
}
