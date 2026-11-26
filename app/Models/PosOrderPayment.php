<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrderPayment extends Model
{
    protected $fillable = ['pos_order_id','payment_refer', 'amount', 'payment_method', 'note', 'received_by'];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }
}