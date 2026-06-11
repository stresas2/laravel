<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Cache::remember('products.api', 300, function () {
            return Product::query()->get(['sku', 'description', 'size', 'photo', 'tags', 'product_updated_at']);
        });

        return response()->json($products);
    }
}
