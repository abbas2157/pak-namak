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

    protected static function booted(): void
    {
        $sync = function (self $production) {
            if ((float) $production->electricity_fuel_cost <= 0) {
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
        static::deleted(fn (self $production) => CashLedger::remove('production', $production->id));
    }
}
