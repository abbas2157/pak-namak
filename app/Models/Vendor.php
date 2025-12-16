<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendor extends Model
{
    protected $fillable =[
        'name',
        'shop',
        'phone',
        'address'
    ];
}
