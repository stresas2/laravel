<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Console\Command;
use JsonMachine\Items;

class ImportProductsStock extends Command
{
    protected $signature = 'products:import-stock {file? : Path to JSON file}';
    protected $description = 'Import products stock from storage/app/public/stocks.json (scheduled)';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (!is_string($path)) {
            $path = storage_path('app/public/stocks.json');
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

                $product = Product::where('sku', $item['sku'])->first();
                if (!$product instanceof Product) {
                    $this->error("Product with SKU {$item['sku']} not found");
                    $failed++;
                    $iterator->next();
                    continue;
                }

                $product->stocks()->updateOrCreate(
                    ['city' => $item['city'] ?? null],
                    ['stock' => $item['stock'] ?? 0]
                );
                $imported++;
            } catch (\Exception $e) {
                $this->error("Failed SKU {$item['sku']}: {$e->getMessage()}");
                $failed++;
            }
            $iterator->next();
        } while ($iterator->valid());

        $this->info("Imported: {$imported} stock entries.");
        if ($failed > 0) {
            $this->warn("Failed: {$failed} stock entries.");
        }

        return self::SUCCESS;
    }
}
