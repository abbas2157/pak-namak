<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceSaleItem extends Model
{
    protected $fillable = [
        'spice_sale_id',
        'spice_type_id',
        'packet_gram',
        'quantity',
        'total_kg',
        'price_per_unit',
        'sub_total',
    ];

    public function sale()
    {
        return $this->belongsTo(SpiceSale::class, 'spice_sale_id');
    }

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }
}
