<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Sale extends Model
{
    protected $fillable = [
        'sale_date',
        'shop_id',
        'order_id',
        'total_amount',
        'received_amount',
        'pending_amount',
        'remarks',
        'bill_image',
    ];

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
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

    protected static function booted(): void
    {
        // sale_payments.sale_id cascades at the DB level, which never fires
        // SalePayment's Eloquent `deleted` hook — remove the matching
        // ledger rows explicitly before that cascade wipes them out.
        static::deleting(function (self $sale) {
            CashLedger::where('source_type', 'sale_payment')
                ->whereIn('source_id', $sale->payments()->pluck('id'))
                ->delete();
        });
    }
}
