<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePackage extends Model
{
    protected $fillable = [
        'sale_id',
        'packet_gram',
        'bundle_size',
        'bundle_quantity',
        'total_kg',
        'price_per_bundle',
        'sub_total'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}

