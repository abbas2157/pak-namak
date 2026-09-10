<?php

namespace App\Models\Concerns;

/**
 * Shared reference generation for the public order portals.
 *
 * References are random rather than sequential for two reasons:
 * /order/confirm/{reference} is public, so a countable reference let anyone
 * read other customers' orders by incrementing a number; and a count-based
 * sequence collides with the unique index as soon as an order is deleted, or
 * when two orders are placed in the same moment.
 *
 * Lives in a trait rather than on one order model so the salt and spice
 * modules stay independent of each other (see ARCHITECTURE.md §2).
 */
trait GeneratesOrderReference
{
    /** Omits 0/O and 1/I so a reference can be read aloud over the phone. */
    private const REFERENCE_ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    private const REFERENCE_LENGTH = 6;

    abstract protected static function referencePrefix(): string;

    public static function generateReference(): string
    {
        $year = date('Y');
        $max = strlen(self::REFERENCE_ALPHABET) - 1;
        $prefix = static::referencePrefix();

        do {
            $token = '';
            for ($i = 0; $i < self::REFERENCE_LENGTH; $i++) {
                $token .= self::REFERENCE_ALPHABET[random_int(0, $max)];
            }
            $reference = "{$prefix}-{$year}-{$token}";
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
