<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_name',
        'quantity',
        'purchase_date',
        'purchase_price',
        'description'
    ];
}
