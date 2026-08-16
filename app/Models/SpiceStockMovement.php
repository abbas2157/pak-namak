<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiceStockMovement extends Model
{
    protected $fillable = [
        'spice_type_id', 'size', 'quantity', 'quantity_kg',
        'reason', 'reference_type', 'reference_id', 'note', 'created_by',
    ];

    public function spiceType()
    {
        return $this->belongsTo(SpiceType::class);
    }

    /**
     * Log a stock change and keep the cached `spice_stocks` balance in sync.
     * $quantity/$quantityKg are signed: positive = stock in, negative = stock out.
     * Caller is expected to already be inside a DB transaction.
     */
    public static function record(
        int $spiceTypeId,
        int $size,
        float $quantity,
        float $quantityKg,
        string $reason,
        ?Model $reference = null,
        ?string $note = null,
        ?int $createdBy = null,
    ): self {
        $movement = static::create([
            'spice_type_id'  => $spiceTypeId,
            'size'           => $size,
            'quantity'       => $quantity,
            'quantity_kg'    => $quantityKg,
            'reason'         => $reason,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id'   => $reference?->getKey(),
            'note'           => $note,
            'created_by'     => $createdBy,
        ]);

        $stock = SpiceStock::firstOrCreate(
            ['spice_type_id' => $spiceTypeId, 'size' => $size],
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
                $stock = SpiceStock::firstOrCreate(
                    ['spice_type_id' => $movement->spice_type_id, 'size' => $movement->size],
                    ['quantity' => 0, 'quantity_kg' => 0]
                );
                $stock->increment('quantity', -$movement->quantity);
                $stock->increment('quantity_kg', -$movement->quantity_kg);
                $movement->delete();
            });
    }
}
