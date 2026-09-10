<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingPurchase extends Model
{
    protected $fillable = [
        'vendor_id',
        'kind',
        'size',
        'quantity',
        'rate_per_unit',
        'total_cost',
        'transport_cost',
        'grand_total',
        'paid_amount',
        'pending_amount',
        'purchase_date',
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
        return $this->hasMany(PackagingPurchasePayment::class);
    }

    /**
     * "50kg Thaila" / "200g Packet" — kind decides the unit the size is in.
     */
    public function sizeLabel(): string
    {
        return $this->kind === 'thaila'
            ? "{$this->size}kg Thaila"
            : "{$this->size}g Packet";
    }

    protected static function booted(): void
    {
        // packaging_purchase_payments.packaging_purchase_id cascades at the DB
        // level, which never fires the payment's Eloquent `deleted` hook —
        // remove the matching ledger rows explicitly before that cascade.
        static::deleting(function (self $purchase) {
            CashLedger::where('source_type', 'packaging_purchase_payment')
                ->whereIn('source_id', $purchase->payments()->pluck('id'))
                ->delete();
        });
    }
}
