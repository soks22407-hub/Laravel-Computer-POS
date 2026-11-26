<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'storage',
        'ram',
        'price',
        'quantity',
        'barcode',
        'image',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // belongs to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}
