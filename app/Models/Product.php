<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku', 'description', 'size', 'photo', 'tags', 'product_updated_at'];

    protected $casts = [
        'tags' => 'array',
        'product_updated_at' => 'datetime',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'sku', 'sku');
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stocks->sum('stock');
    }
}
