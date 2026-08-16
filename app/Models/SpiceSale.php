<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceSale extends Model
{
    protected $fillable = [
        'shop_id',
        'spice_order_id',
        'sale_date',
        'total_amount',
        'received_amount',
        'pending_amount',
        'remarks',
        'bill_image',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function order()
    {
        return $this->belongsTo(SpiceOrder::class, 'spice_order_id');
    }

    public function items()
    {
        return $this->hasMany(SpiceSaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SpiceSalePayment::class);
    }

    protected static function booted(): void
    {
        // spice_sale_payments.spice_sale_id cascades at the DB level, which
        // never fires SpiceSalePayment's Eloquent `deleted` hook — remove the
        // matching ledger rows explicitly before that cascade wipes them out.
        static::deleting(function (self $sale) {
            CashLedger::where('source_type', 'spice_sale_payment')
                ->whereIn('source_id', $sale->payments()->pluck('id'))
                ->delete();
        });
    }
}
