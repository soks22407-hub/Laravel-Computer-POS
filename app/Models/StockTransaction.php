<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'unit_cost',
        'reason',
        'transaction_date',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
