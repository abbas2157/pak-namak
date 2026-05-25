<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'type', 'size', 'quantity'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getLabelAttribute(): string
    {
        return match ($this->type) {
            'dalla'   => $this->quantity . ' Mann — Dalla (ڈلہ)',
            'thaila'  => $this->quantity . ' bags × ' . $this->size . ' KG — Thaila (تھیلا)',
            'package' => $this->quantity . ' bundles × ' . $this->size . 'g — Package (پیکٹ)',
            default   => $this->type,
        };
    }
}
