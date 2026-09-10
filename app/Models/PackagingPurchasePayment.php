<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingPurchasePayment extends Model
{
    protected $fillable = ['packaging_purchase_id', 'account_id', 'amount', 'payment_date', 'note'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(PackagingPurchase::class, 'packaging_purchase_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Investment-flagged purchases are capital funded outside day-to-day cash
     * flow, so their payments never touch the Cash & Bank ledger. Called on
     * create/update, and again from PackagingPurchaseController::update()
     * whenever the parent purchase's is_investment flag is toggled.
     *
     * A blank account_id means "Other / Not from Cash & Bank" was chosen —
     * money that moved outside the tracked accounts, so it stays out too.
     */
    public function syncLedger(): void
    {
        if ((bool) $this->purchase?->is_investment || !$this->account_id) {
            CashLedger::remove('packaging_purchase_payment', $this->id);

            return;
        }

        $vendorName = $this->purchase?->vendor?->name ?? 'Unknown Vendor';
        $item = $this->purchase?->sizeLabel() ?? 'Packaging';
        $date = $this->payment_date ?? $this->purchase?->purchase_date ?? now();
        CashLedger::sync('packaging_purchase_payment', $this->id, 'out', (float) $this->amount, $date, "Payment to {$vendorName} ({$item})", $this->account_id);
    }

    protected static function booted(): void
    {
        static::created(fn (self $payment) => $payment->syncLedger());
        static::updated(fn (self $payment) => $payment->syncLedger());
        static::deleted(fn (self $payment) => CashLedger::remove('packaging_purchase_payment', $payment->id));
    }
}
