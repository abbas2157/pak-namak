<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['name', 'type', 'bank_name', 'account_number', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Common Pakistani banks, for the "Bank Name" dropdown when adding a
     * type=bank account. Not exhaustive — "Other" covers anything missing.
     */
    public const BANKS = [
        'HBL - Habib Bank Limited',
        'UBL - United Bank Limited',
        'MCB Bank',
        'Meezan Bank',
        'Allied Bank',
        'Bank Alfalah',
        'Askari Bank',
        'Faysal Bank',
        'Bank Al Habib',
        'Standard Chartered Pakistan',
        'JS Bank',
        'Soneri Bank',
        'Bank of Punjab',
        'National Bank of Pakistan',
        'Silk Bank',
        'Summit Bank',
        'Other',
    ];

    public function ledgerEntries()
    {
        return $this->hasMany(CashLedger::class);
    }

    public function currentBalance(): float
    {
        $in  = (float) $this->ledgerEntries()->where('type', 'in')->sum('amount');
        $out = (float) $this->ledgerEntries()->where('type', 'out')->sum('amount');

        return $in - $out;
    }

    public function label(): string
    {
        return $this->type === 'bank' && $this->bank_name
            ? "{$this->name} ({$this->bank_name})"
            : $this->name;
    }

    /**
     * Legacy "payment method" text label some tables still display
     * (e.g. Expense::payment_method), derived from the account's type
     * instead of asking the user to pick it twice.
     */
    public function paymentMethodLabel(): string
    {
        return match ($this->type) {
            'cash'      => 'Cash',
            'bank'      => 'Bank',
            'jazzcash'  => 'JazzCash',
            'easypaisa' => 'EasyPaisa',
            default     => 'Other',
        };
    }
}
