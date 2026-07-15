<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['product_type', 'size', 'bundle_size', 'quantity', 'quantity_kg'];

    /**
     * The fixed set of dalla/thaila/package lines the business sells,
     * sourced from config so it stays in sync with the sale/order forms.
     * Packages are tracked per gram size AND bundle size (10-pack / 20-pack),
     * since those are distinct physical products.
     */
    public static function catalog(): array
    {
        $lines = [['product_type' => 'dalla', 'size' => null, 'bundle_size' => null]];

        foreach (config('admin.thaila_sizes', []) as $size) {
            $lines[] = ['product_type' => 'thaila', 'size' => $size, 'bundle_size' => null];
        }

        foreach (config('admin.package_grams', []) as $gram) {
            foreach (array_keys(config('admin.bundles', [])) as $bundleSize) {
                $lines[] = ['product_type' => 'package', 'size' => $gram, 'bundle_size' => $bundleSize];
            }
        }

        return $lines;
    }

    public static function key(string $productType, ?int $size, ?int $bundleSize = null): string
    {
        return $productType . ':' . $size . ':' . $bundleSize;
    }

    /**
     * Current balance for every catalog line, defaulting to zero when no
     * stock row exists yet.
     */
    public static function levels()
    {
        $existing = static::all()->keyBy(fn ($s) => static::key($s->product_type, $s->size, $s->bundle_size));

        return collect(static::catalog())->map(function ($line) use ($existing) {
            $stock = $existing->get(static::key($line['product_type'], $line['size'], $line['bundle_size']));

            return array_merge($line, [
                'quantity'    => $stock->quantity ?? 0,
                'quantity_kg' => $stock->quantity_kg ?? 0,
            ]);
        });
    }
}
