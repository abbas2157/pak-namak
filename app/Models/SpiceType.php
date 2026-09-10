<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceType extends Model
{
    protected $fillable = [
        'title',
    ];

    protected static function booted(): void
    {
        // Deleting a spice type cascades its purchases and their payments at the
        // DB level, bypassing SpicePurchasePayment's Eloquent `deleted` hook —
        // clear the matching ledger rows first so no phantom cash is left behind.
        static::deleting(function (self $type) {
            $purchaseIds = SpicePurchase::where('spice_type_id', $type->id)->pluck('id');
            if ($purchaseIds->isEmpty()) {
                return;
            }

            CashLedger::where('source_type', 'spice_purchase_payment')
                ->whereIn('source_id', SpicePurchasePayment::whereIn('spice_purchase_id', $purchaseIds)->pluck('id'))
                ->delete();
        });
    }
}
