<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'account_id',
        'asset_name',
        'category',
        'quantity',
        'purchase_price',
        'purchase_date',
        'is_investment',
        'description',
        'status',
        'condition',
        'location',
        'image',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity'      => 'integer',
        'purchase_price'=> 'decimal:2',
        'is_investment' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function totalValue(): float
    {
        return (float) $this->quantity * (float) $this->purchase_price;
    }

    protected static function booted(): void
    {
        $sync = function (self $asset) {
            $prefix = $asset->is_investment ? 'Investment' : 'Asset';
            CashLedger::sync('asset', $asset->id, 'out', $asset->totalValue(), $asset->purchase_date, "{$prefix}: {$asset->asset_name}", $asset->account_id, (bool) $asset->is_investment);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $asset) => CashLedger::remove('asset', $asset->id));
    }
}
