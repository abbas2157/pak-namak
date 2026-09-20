<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceProduction extends Model
{
    protected $fillable = [
        'spice_type_id',
        'account_id',
        'production_date',
        'raw_spice_used',
        'finished_spice',
        'wastage',
        'machine_used',
        'electricity_fuel_cost',
        'remarks',
    ];

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->hasMany(SpiceProductionItem::class);
    }

    /**
     * Replace this batch's packet lines and keep Spice Stock in sync — same
     * reverse-then-reapply contract as Production::syncItems().
     * Caller must be inside a DB transaction.
     *
     * @param  array<int, array{size:int, quantity:float, quantity_kg:float}>  $lines
     */
    public function syncItems(array $lines): void
    {
        SpiceStockMovement::reverseFor($this, 'production');
        $this->items()->delete();

        foreach ($lines as $line) {
            $this->items()->create($line);

            SpiceStockMovement::record(
                $this->spice_type_id,
                $line['size'],
                $line['quantity'],
                $line['quantity_kg'],
                'production',
                $this,
                "Production {$this->production_date}",
                auth()->id(),
            );
        }
    }

    public function packetCount(): float
    {
        return (float) $this->items->sum('quantity');
    }

    public function packedKg(): float
    {
        return (float) $this->items->sum('quantity_kg');
    }

    protected static function booted(): void
    {
        $sync = function (self $production) {
            if ((float) $production->electricity_fuel_cost <= 0 || ! $production->account_id) {
                CashLedger::remove('spice_production', $production->id);

                return;
            }

            $spice = $production->spiceType?->title ?? 'Spice';

            CashLedger::sync(
                'spice_production',
                $production->id,
                'out',
                (float) $production->electricity_fuel_cost,
                $production->production_date,
                "Spice Production: {$spice} (Electricity/Fuel)",
                $production->account_id
            );
        };

        static::created($sync);
        static::updated($sync);

        static::deleting(fn (self $production) => SpiceStockMovement::reverseFor($production, 'production'));
        static::deleted(fn (self $production) => CashLedger::remove('spice_production', $production->id));
    }
}
