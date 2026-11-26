<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'value',
        'min_amount',
        'start_date',
        'end_date',
        'status',
        
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean'
    ];

   
    public function isActive()
    {
        $today = now();

        return $this->status &&
            (!$this->start_date || $this->start_date <= $today) &&
            (!$this->end_date || $this->end_date >= $today);
    }

    // Apply discount to price
    public function apply($price)
    {
        if (!$this->isActive()) return $price;

        if ($this->type === 'percent') {
            return $price - ($price * $this->value / 100);
        }

        return max(0, $price - $this->value);
    }
}
