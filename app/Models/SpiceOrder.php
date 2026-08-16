<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceOrder extends Model
{
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

    public static function generateReference(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count();
        return 'SPC-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
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
