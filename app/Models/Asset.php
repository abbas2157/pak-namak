<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_name',
        'category',
        'quantity',
        'purchase_price',
        'purchase_date',
        'description',
        'status',
        'condition',
        'location',
        'image',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity'      => 'integer',
        'purchase_price'=> 'decimal:2',
    ];

    public function totalValue(): float
    {
        return (float) $this->quantity * (float) $this->purchase_price;
    }
}
