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
        'status',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
