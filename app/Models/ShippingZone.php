<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['province', 'fee', 'free_shipping_above', 'is_active'];
}