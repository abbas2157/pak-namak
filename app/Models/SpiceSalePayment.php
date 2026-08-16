<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceSalePayment extends Model
{
    protected $fillable = ['spice_sale_id', 'account_id', 'amount', 'payment_date', 'payment_method', 'note'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(SpiceSale::class, 'spice_sale_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $payment) {
            $shopName = $payment->sale?->shop?->name ?? 'Unknown Shop';
            CashLedger::sync('spice_sale_payment', $payment->id, 'in', (float) $payment->amount, $payment->payment_date, "Payment from {$shopName} (Spices)", $payment->account_id);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $payment) => CashLedger::remove('spice_sale_payment', $payment->id));
    }
}
