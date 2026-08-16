<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceStock extends Model
{
    protected $fillable = ['spice_type_id', 'size', 'quantity', 'quantity_kg'];

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }

    /**
     * The fixed set of size lines each active spice type is sold in,
     * sourced from config so it stays in sync with the sale/order forms.
     */
    public static function catalog(): array
    {
        $lines = [];

        foreach (SpiceType::all() as $spiceType) {
            foreach (config('admin.spice_sizes', []) as $gram) {
                $lines[] = ['spice_type_id' => $spiceType->id, 'size' => $gram];
            }
        }

        return $lines;
    }

    public static function key(int $spiceTypeId, ?int $size): string
    {
        return $spiceTypeId . ':' . $size;
    }

    /**
     * Current balance for every catalog line, defaulting to zero when no
     * stock row exists yet.
     */
    public static function levels()
    {
        $existing = static::all()->keyBy(fn ($s) => static::key($s->spice_type_id, $s->size));

        return collect(static::catalog())->map(function ($line) use ($existing) {
            $stock = $existing->get(static::key($line['spice_type_id'], $line['size']));

            return array_merge($line, [
                'quantity'    => $stock->quantity ?? 0,
                'quantity_kg' => $stock->quantity_kg ?? 0,
            ]);
        });
    }
}
