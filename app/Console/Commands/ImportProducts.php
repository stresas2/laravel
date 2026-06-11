<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImportProducts extends Command
{
    protected $signature = 'products:import';
    protected $description = 'Import products from storage/app/public/products.json';

    public function handle(): int
    {
        $path = storage_path('app/public/products.json');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            $this->error('Invalid JSON format.');
            return self::FAILURE;
        }

        $imported = 0;

        foreach ($data as $item) {
            Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'description' => $item['description'],
                    'size' => $item['size'] ?? null,
                    'photo' => $item['photo'] ?? null,
                    'tags' => $item['tags'] ?? [],
                    'product_updated_at' => $item['updated_at'] ?? null,
                ]
            );
            $imported++;
        }

        Cache::forget('products.all');

        $this->info("Imported {$imported} products.");
        return self::SUCCESS;
    }
}
