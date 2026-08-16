<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceOrderItem extends Model
{
    protected $fillable = ['spice_order_id', 'spice_type_id', 'size', 'quantity', 'price', 'sub_total'];

    public function order()
    {
        return $this->belongsTo(SpiceOrder::class, 'spice_order_id');
    }

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }

    public function getLabelAttribute(): string
    {
        $title = $this->spiceType?->title ?? 'Spice';
        return "{$title}: {$this->quantity} packets × {$this->size}g";
    }
}
