<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    protected $fillable = ['production_id', 'product_type', 'size', 'bundle_size', 'quantity', 'quantity_kg'];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    /** e.g. "10kg" for a thaila line, "500g × 20" for a package line. */
    public function label(): string
    {
        return $this->product_type === 'thaila'
            ? "{$this->size}kg"
            : "{$this->size}g × {$this->bundle_size}";
    }
}
