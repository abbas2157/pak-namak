<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'email',
        'phone_number',
        'address',
        'city',
        'city_id',
        'area_id',
        'status',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function cityRecord()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
