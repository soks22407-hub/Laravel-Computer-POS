<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PosOrder.php
class PosOrder extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'amount_owed',
        'amount_returned',
        'status',
        'note'
    ];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(PosOrderPayment::class);
    }
}
