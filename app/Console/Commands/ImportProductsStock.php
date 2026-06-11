<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportProductsStock extends Command
{
    protected $signature = 'products:import-stock';
    protected $description = 'Import products stock from storage/app/public/stocks.json (scheduled)';

    public function handle(): int
    {
        $path = storage_path('app/public/stocks.json');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            $this->error('Invalid JSON format.');
            return self::FAILURE;
        }

        $skus = Product::pluck('sku')->flip();
        $imported = 0;

        Stock::truncate();

        foreach ($data as $item) {
            if (!isset($skus[$item['sku']])) {
                continue;
            }

            Stock::create([
                'sku' => $item['sku'],
                'stock' => $item['stock'],
                'city' => $item['city'] ?? null,
            ]);
            $imported++;
        }

        Cache::forget('products.all');

        $this->info("Imported {$imported} stock entries.");
        return self::SUCCESS;
    }
}
