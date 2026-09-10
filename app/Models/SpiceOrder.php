<?php

namespace App\Models;

use App\Models\Concerns\GeneratesOrderReference;
use Illuminate\Database\Eloquent\Model;

class SpiceOrder extends Model
{
    use GeneratesOrderReference;

    protected $fillable = [
        'reference', 'shop_id', 'customer_name', 'phone',
        'city', 'remarks', 'status', 'ip_address',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(SpiceOrderItem::class);
    }

    public function sale()
    {
        return $this->hasOne(SpiceSale::class);
    }

    protected static function referencePrefix(): string
    {
        return 'SPC';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->shop?->name ?? $this->customer_name ?? 'Unknown';
    }

    public function getDisplayPhoneAttribute(): string
    {
        return $this->shop?->phone_number ?? $this->phone ?? '—';
    }
}
