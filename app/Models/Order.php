<?php

namespace App\Models;

use App\Models\Concerns\GeneratesOrderReference;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
        return $this->hasMany(OrderItem::class);
    }

    public function sale()
    {
        return $this->hasOne(\App\Models\Sale::class);
    }

    protected static function referencePrefix(): string
    {
        return 'ORD';
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
