<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = ['sale_id', 'account_id', 'amount', 'payment_date', 'payment_method', 'note'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $payment) {
            $shopName = $payment->sale?->shop?->name ?? 'Unknown Shop';
            CashLedger::sync('sale_payment', $payment->id, 'in', (float) $payment->amount, $payment->payment_date, "Payment from {$shopName}", $payment->account_id);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $payment) => CashLedger::remove('sale_payment', $payment->id));
    }
}
