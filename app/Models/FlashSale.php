<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FlashSale extends Model
{
    protected $fillable = ['product_id', 'variant_id', 'sale_price', 'start_at', 'end_at', 'is_active'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function isRunning()
    {
        $now = Carbon::now();
        return $this->is_active
            && $now->gte($this->start_at)
            && $now->lte($this->end_at);
    }
}
