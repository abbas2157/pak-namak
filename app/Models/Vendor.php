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

    public function advances()
    {
        return $this->hasMany(VendorAdvance::class);
    }

    public function spicePurchases()
    {
        return $this->hasMany(SpicePurchase::class, 'vendor_id');
    }

    public function packagingPurchases()
    {
        return $this->hasMany(PackagingPurchase::class, 'vendor_id');
    }

    protected static function booted(): void
    {
        // Deleting a vendor cascades its advances and spice purchases (and those
        // purchases' payments) at the DB level, which never fires the payment
        // models' Eloquent `deleted` hooks. Clear every ledger row those records
        // own first, otherwise they linger as phantom cash-out forever.
        static::deleting(function (self $vendor) {
            $advanceIds = $vendor->advances()->pluck('id');
            if ($advanceIds->isNotEmpty()) {
                CashLedger::where('source_type', 'vendor_advance')
                    ->whereIn('source_id', $advanceIds)
                    ->delete();
            }

            $spicePurchaseIds = $vendor->spicePurchases()->pluck('id');
            if ($spicePurchaseIds->isNotEmpty()) {
                CashLedger::where('source_type', 'spice_purchase_payment')
                    ->whereIn('source_id', SpicePurchasePayment::whereIn('spice_purchase_id', $spicePurchaseIds)->pluck('id'))
                    ->delete();
            }

            $packagingPurchaseIds = $vendor->packagingPurchases()->pluck('id');
            if ($packagingPurchaseIds->isNotEmpty()) {
                CashLedger::where('source_type', 'packaging_purchase_payment')
                    ->whereIn('source_id', PackagingPurchasePayment::whereIn('packaging_purchase_id', $packagingPurchaseIds)->pluck('id'))
                    ->delete();
            }
        });
    }
}
