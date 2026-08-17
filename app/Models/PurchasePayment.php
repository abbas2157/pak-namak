<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = ['purchase_id', 'account_id', 'amount', 'payment_date', 'note'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Investment-flagged purchases are capital funded outside day-to-day
     * cash flow, so their payments never touch the Cash & Bank ledger.
     * Called on create/update, and again from PurchaseController::update()
     * whenever the parent purchase's is_investment flag is toggled, so
     * already-recorded payments stay in sync either direction.
     */
    public function syncLedger(): void
    {
        if ((bool) $this->purchase?->is_investment) {
            CashLedger::remove('purchase_payment', $this->id);
            return;
        }

        $vendorName = $this->purchase?->vendor?->name ?? 'Unknown Vendor';
        $date = $this->payment_date ?? $this->purchase?->purchase_date ?? now();
        CashLedger::sync('purchase_payment', $this->id, 'out', (float) $this->amount, $date, "Payment to {$vendorName}", $this->account_id);
    }

    protected static function booted(): void
    {
        static::created(fn (self $payment) => $payment->syncLedger());
        static::updated(fn (self $payment) => $payment->syncLedger());
        static::deleted(fn (self $payment) => CashLedger::remove('purchase_payment', $payment->id));
    }
}
