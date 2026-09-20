<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'account_id',
        'production_date',
        'raw_salt_used',
        'finished_salt',
        'wastage',
        'machine_used',
        'electricity_fuel_cost',
        'remarks',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class);
    }

    /**
     * Replace this batch's packaged output (thaila / package lines) and keep
     * the salt Stock in sync: every line is posted as a 'production' stock-in
     * movement, and any movements from a previous save are reversed first so
     * edits never double-count. Caller must be inside a DB transaction.
     *
     * @param  array<int, array{product_type:string, size:int, bundle_size:?int, quantity:float, quantity_kg:float}>  $lines
     */
    public function syncItems(array $lines): void
    {
        StockMovement::reverseFor($this, 'production');
        $this->items()->delete();

        foreach ($lines as $line) {
            $this->items()->create($line);

            StockMovement::record(
                $line['product_type'],
                $line['size'],
                $line['quantity'],
                $line['quantity_kg'],
                'production',
                $this,
                "Production {$this->production_date}",
                auth()->id(),
                $line['bundle_size'],
            );
        }
    }

    /** Thaila count / package bundle count / packed KG for this batch. */
    public function thailaCount(): float
    {
        return (float) $this->items->where('product_type', 'thaila')->sum('quantity');
    }

    public function packageCount(): float
    {
        return (float) $this->items->where('product_type', 'package')->sum('quantity');
    }

    public function packedKg(): float
    {
        return (float) $this->items->sum('quantity_kg');
    }

    protected static function booted(): void
    {
        $sync = function (self $production) {
            if ((float) $production->electricity_fuel_cost <= 0 || ! $production->account_id) {
                CashLedger::remove('production', $production->id);

                return;
            }

            CashLedger::sync(
                'production',
                $production->id,
                'out',
                (float) $production->electricity_fuel_cost,
                $production->production_date,
                "Production: {$production->machine_used} (Electricity/Fuel)",
                $production->account_id
            );
        };

        static::created($sync);
        static::updated($sync);

        // production_items cascade at the DB level; the stock movements do
        // not — hand the produced quantities back out of Stock explicitly.
        static::deleting(fn (self $production) => StockMovement::reverseFor($production, 'production'));
        static::deleted(fn (self $production) => CashLedger::remove('production', $production->id));
    }
}
