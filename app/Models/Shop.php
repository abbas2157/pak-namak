<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
     protected $table = 'shops';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
    ];
}
