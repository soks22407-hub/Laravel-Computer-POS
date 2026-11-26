<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrderItem extends Model
{
    protected $fillable = [
        'pos_order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }
}
