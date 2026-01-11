<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDalla extends Model
{
    protected $fillable = [
        'sale_id',
        'quantity_mann',
        'quantity_kg',
        'price_per_mann',
        'price_per_kg',
        'sub_total'
    ];
}
