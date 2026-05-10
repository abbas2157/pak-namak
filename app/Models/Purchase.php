<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'salt_purchases';
    protected $fillable = [
        'vendor_id',
        'purchase_date',
        'salt_quantity',
        'salt_quantity_kg',
        'rate_per_kg',
        'total_cost',
        'transport_cost',
        'loading_unloading_cost',
        'grand_total',
        'remarks',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}
