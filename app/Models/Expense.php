<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'account_id',
        'expense_date',
        'category',
        'payment_method',
        'amount',
        'description',
        'remarks',
        'is_investment',
    ];

    protected $casts = [
        'is_investment' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $expense) {
            $prefix = $expense->is_investment ? 'Investment' : 'Expense';
            $desc = "{$prefix}: {$expense->category}" . ($expense->description ? " — {$expense->description}" : '');
            CashLedger::sync('expense', $expense->id, 'out', (float) $expense->amount, $expense->expense_date, $desc, $expense->account_id, (bool) $expense->is_investment);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $expense) => CashLedger::remove('expense', $expense->id));
    }
}
