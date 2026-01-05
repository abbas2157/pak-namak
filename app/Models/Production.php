<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'production_date',
        'raw_salt_used',
        'finished_salt',
        'wastage',
        'machine_used',
        'electricity_fuel_cost',
        'remarks',
    ];
}
