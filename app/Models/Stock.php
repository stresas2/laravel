<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['sku', 'stock', 'city'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }
}
