<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name'];

    public function areas()
    {
        return $this->hasMany(Area::class)->orderBy('name');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
