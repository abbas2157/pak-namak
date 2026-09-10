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
     * Undo this movement's effect on the cached balance, then remove it.
     * Caller is expected to already be inside a DB transaction.
     */
    public function revert(): void
    {
        $stock = Stock::firstOrCreate(
            ['product_type' => $this->product_type, 'size' => $this->size, 'bundle_size' => $this->bundle_size],
            ['quantity' => 0, 'quantity_kg' => 0]
        );
        $stock->increment('quantity', -$this->quantity);
        $stock->increment('quantity_kg', -$this->quantity_kg);

        $this->delete();
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
            ->each(fn (self $movement) => $movement->revert());
    }
}
