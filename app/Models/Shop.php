<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
