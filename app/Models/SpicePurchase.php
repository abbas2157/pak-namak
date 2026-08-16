<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpicePurchase extends Model
{
    protected $fillable = [
        'vendor_id',
        'spice_type_id',
        'purchase_date',
        'quantity_kg',
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
        return $this->belongsTo(Vendor::class);
    }

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }

    public function payments()
    {
        return $this->hasMany(SpicePurchasePayment::class);
    }

    protected static function booted(): void
    {
        // spice_purchase_payments.spice_purchase_id cascades at the DB level,
        // which never fires SpicePurchasePayment's Eloquent `deleted` hook —
        // remove the matching ledger rows explicitly before that cascade wipes them out.
        static::deleting(function (self $purchase) {
            CashLedger::where('source_type', 'spice_purchase_payment')
                ->whereIn('source_id', $purchase->payments()->pluck('id'))
                ->delete();
        });
    }
}
