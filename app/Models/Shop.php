<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'email',
        'phone_number',
        'address',
        'city',
        'city_id',
        'area_id',
        'status',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function spiceSales()
    {
        return $this->hasMany(SpiceSale::class);
    }

    public function cityRecord()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * "Area, City" for dropdowns and order screens — area first because that is
     * how the salesmen know a shop; city alone when no area is set. Callers
     * should eager-load `area` when rendering lists.
     */
    public function getLocationAttribute(): string
    {
        return collect([$this->area?->name, $this->city])->filter()->unique()->implode(', ');
    }

    protected static function booted(): void
    {
        // Deleting a shop cascades its spice sales and their payments at the DB
        // level, which never fires the payment models' Eloquent `deleted` hooks.
        // Without this the matching cash_ledger rows survive as phantom cash-in
        // that permanently inflates the balance with no UI to find it.
        static::deleting(function (self $shop) {
            $saleIds = $shop->sales()->pluck('id');
            if ($saleIds->isNotEmpty()) {
                CashLedger::where('source_type', 'sale_payment')
                    ->whereIn('source_id', SalePayment::whereIn('sale_id', $saleIds)->pluck('id'))
                    ->delete();
            }

            $spiceSaleIds = $shop->spiceSales()->pluck('id');
            if ($spiceSaleIds->isNotEmpty()) {
                CashLedger::where('source_type', 'spice_sale_payment')
                    ->whereIn('source_id', SpiceSalePayment::whereIn('spice_sale_id', $spiceSaleIds)->pluck('id'))
                    ->delete();
            }
        });
    }
}
