<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'shop',
        'phone',
        'address',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'vendor_id');
    }
}
