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
        // Adding/editing an asset no longer auto-deducts its value from the
        // selected account's Cash & Bank balance — assets are tracked as
        // records only. Still clean up any ledger rows synced under the old
        // behavior if such an asset is deleted.
        static::deleted(fn (self $asset) => CashLedger::remove('asset', $asset->id));
    }
}
