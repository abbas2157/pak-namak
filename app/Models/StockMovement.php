<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_type', 'size', 'bundle_size', 'quantity', 'quantity_kg',
        'reason', 'reference_type', 'reference_id', 'note', 'created_by',
    ];

    /**
     * Log a stock change and keep the cached `stocks` balance in sync.
     * $quantity/$quantityKg are signed: positive = stock in, negative = stock out.
     * Caller is expected to already be inside a DB transaction.
     */
    public static function record(
        string $productType,
        ?int $size,
        float $quantity,
        float $quantityKg,
        string $reason,
        ?Model $reference = null,
        ?string $note = null,
        ?int $createdBy = null,
        ?int $bundleSize = null,
    ): self {
        $movement = static::create([
            'product_type'   => $productType,
            'size'           => $size,
            'bundle_size'    => $bundleSize,
            'quantity'       => $quantity,
            'quantity_kg'    => $quantityKg,
            'reason'         => $reason,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id'   => $reference?->getKey(),
            'note'           => $note,
            'created_by'     => $createdBy,
        ]);

        $stock = Stock::firstOrCreate(
            ['product_type' => $productType, 'size' => $size, 'bundle_size' => $bundleSize],
            ['quantity' => 0, 'quantity_kg' => 0]
        );
        $stock->increment('quantity', $quantity);
        $stock->increment('quantity_kg', $quantityKg);

        return $movement;
    }

    /**
     * Reverse every 'sale' movement previously logged for the given model
     * (adds the stock back) and deletes those ledger rows. Used when a sale
     * is edited (before re-applying fresh deductions) or deleted.
     */
    public static function reverseFor(Model $reference, string $reason = 'sale'): void
    {
        static::where('reference_type', get_class($reference))
            ->where('reference_id', $reference->getKey())
            ->where('reason', $reason)
            ->get()
            ->each(function (self $movement) {
                $stock = Stock::firstOrCreate(
                    ['product_type' => $movement->product_type, 'size' => $movement->size, 'bundle_size' => $movement->bundle_size],
                    ['quantity' => 0, 'quantity_kg' => 0]
                );
                $stock->increment('quantity', -$movement->quantity);
                $stock->increment('quantity_kg', -$movement->quantity_kg);
                $movement->delete();
            });
    }
}
