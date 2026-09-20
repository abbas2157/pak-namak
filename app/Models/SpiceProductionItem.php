<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceProductionItem extends Model
{
    protected $fillable = ['spice_production_id', 'size', 'quantity', 'quantity_kg'];

    public function spiceProduction()
    {
        return $this->belongsTo(SpiceProduction::class);
    }

    /** "250g" / "1kg" — same scale the Spice Stock page uses. */
    public function label(): string
    {
        return $this->size >= 1000 ? ($this->size / 1000).'kg' : $this->size.'g';
    }
}
