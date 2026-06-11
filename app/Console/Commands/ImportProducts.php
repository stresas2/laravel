<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use JsonMachine\Items;

class ImportProducts extends Command
{
    protected $signature = 'products:import {file? : Path to JSON file}';
    protected $description = 'Import products from storage/app/public/products.json';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (!is_string($path)) {
            $path = storage_path('app/public/products.json');
        }

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        try {
            $iterator = Items::fromFile($path)->getIterator();
        } catch (\Exception $e) {
            $this->error("Error reading file: {$e->getMessage()}");
            return self::FAILURE;
        }

        $imported = 0;
        $failed = 0;
        $iterator->rewind();

        do {
            $item = (array) $iterator->current();
            try {
                if (!isset($item['sku']) && !is_string($item['sku'])) {
                    $this->error('Failed import: SKU is missing or invalid: '.json_encode($item));
                    $failed++;
                    $iterator->next();

                    continue;
                }

                Product::updateOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'description' => $item['description'] ?? '',
                        'size' => $item['size'] ?? null,
                        'photo' => $item['photo'] ?? null,
                        'tags' => $item['tags'] ?? [],
                        'product_updated_at' => $item['updated_at'] ?? null,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $this->error("Failed SKU {$item['sku']}: {$e->getMessage()}");
                $failed++;
            }
            $iterator->next();
        } while ($iterator->valid());

        $this->info("Imported: {$imported} products.");
        if ($failed > 0) {
            $this->warn("Failed: {$failed} products.");
        }

        return self::SUCCESS;
    }
}
