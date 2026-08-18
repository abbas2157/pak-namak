<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAdvance extends Model
{
    protected $fillable = [
        'vendor_id', 'account_id', 'amount', 'remaining_amount', 'advance_date', 'note', 'created_by',
    ];

    protected $casts = [
        'advance_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $advance) {
            if (!$advance->account_id) {
                CashLedger::remove('vendor_advance', $advance->id);
                return;
            }

            $vendorName = $advance->vendor?->name ?? 'Unknown Vendor';
            CashLedger::sync('vendor_advance', $advance->id, 'out', (float) $advance->amount, $advance->advance_date, "Advance to {$vendorName}", $advance->account_id);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $advance) => CashLedger::remove('vendor_advance', $advance->id));
    }
}
