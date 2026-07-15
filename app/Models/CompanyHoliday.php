<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHoliday extends Model
{
    protected $fillable = ['date', 'title'];

    protected $casts = [
        'date' => 'date',
    ];
}
