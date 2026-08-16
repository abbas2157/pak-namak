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
        'paid_amount',
        'pending_amount',
        'remarks',
        'is_investment',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'is_investment' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    protected static function booted(): void
    {
        // purchase_payments.purchase_id cascades at the DB level, which never
        // fires PurchasePayment's Eloquent `deleted` hook — remove the
        // matching ledger rows explicitly before that cascade wipes them out.
        static::deleting(function (self $purchase) {
            CashLedger::where('source_type', 'purchase_payment')
                ->whereIn('source_id', $purchase->payments()->pluck('id'))
                ->delete();
        });
    }
}
