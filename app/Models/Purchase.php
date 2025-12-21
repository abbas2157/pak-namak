<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'salt_purchases';
    protected $fillable = [
        'id',
        'supplier_name',
        'salt_quantity',
        'rate_per_kg',
        'total_cost',
        'transport_cost',
        'loading_unloading_cost',
        'grand_total',
        'remarks'
    ];
}
