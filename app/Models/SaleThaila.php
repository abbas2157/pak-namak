<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleThaila extends Model
{
    protected $fillable = [
        'sale_id',
        'bag_size_kg',
        'quantity',
        'total_kg',
        'price_per_bag',
        'price_per_kg',
        'sub_total'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}

