<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_date',
        'shop_id',
        'total_amount',
        'received_amount',
        'pending_amount',
        'remarks',
        'bill_image',
    ];

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function salt_type(){
        return $this->belongsTo(SaltType::class);
    }
    public function dalla() {
        return $this->hasOne(SaleDalla::class);
    }

    public function thailas() {
        return $this->hasMany(SaleThaila::class);
    }

    public function packages() {
        return $this->hasMany(SalePackage::class);
    }
    public function payments() {
        return $this->hasMany(SalePayment::class);
    }
}
