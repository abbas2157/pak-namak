<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashLedger extends Model
{
    protected $table = 'cash_ledger';

    protected $fillable = [
        'account_id', 'type', 'amount', 'source_type', 'source_id',
        'transaction_date', 'description', 'is_investment', 'note', 'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'is_investment'    => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Idempotently create/update the ledger row for a given source record,
     * so lifecycle hooks can call this from both `created` and `updated`
     * without ever producing a duplicate.
     */
    public static function sync(
        string $sourceType,
        $sourceId,
        string $type,
        float $amount,
        $date,
        string $description,
        ?int $accountId = null,
        bool $isInvestment = false,
        ?int $createdBy = null,
    ): self {
        return static::updateOrCreate(
            ['source_type' => $sourceType, 'source_id' => $sourceId],
            [
                'type'             => $type,
                'amount'           => $amount,
                'transaction_date' => $date,
                'description'      => $description,
                'account_id'       => $accountId,
                'is_investment'    => $isInvestment,
                'created_by'       => $createdBy ?? auth()->id(),
            ]
        );
    }

    public static function remove(string $sourceType, $sourceId): void
    {
        static::where('source_type', $sourceType)->where('source_id', $sourceId)->delete();
    }

    public static function currentBalance(?int $accountId = null): float
    {
        $base = static::query();
        if ($accountId) {
            $base->where('account_id', $accountId);
        }

        $in  = (float) (clone $base)->where('type', 'in')->sum('amount');
        $out = (float) (clone $base)->where('type', 'out')->sum('amount');

        return $in - $out;
    }
}
