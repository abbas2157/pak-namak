<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpicePurchasePayment extends Model
{
    protected $fillable = ['spice_purchase_id', 'account_id', 'amount', 'payment_date', 'note'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(SpicePurchase::class, 'spice_purchase_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $payment) {
            $vendorName = $payment->purchase?->vendor?->name ?? 'Unknown Vendor';
            $spiceName = $payment->purchase?->spiceType?->title ?? 'Spice';
            $date = $payment->payment_date ?? $payment->purchase?->purchase_date ?? now();
            $isInvestment = (bool) $payment->purchase?->is_investment;
            $desc = ($isInvestment ? 'Investment — ' : '') . "Payment to {$vendorName} ({$spiceName})";
            CashLedger::sync('spice_purchase_payment', $payment->id, 'out', (float) $payment->amount, $date, $desc, $payment->account_id, $isInvestment);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $payment) => CashLedger::remove('spice_purchase_payment', $payment->id));
    }
}
