<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A personal money movement between the owner and Pak Namak:
 *  - taken  = Pak Namak money used for the house
 *  - put_in = owner's own money used for Pak Namak
 *
 * Intentionally has no hooks — it is a standalone record and must not
 * feed the Cash Ledger, dashboard profit or Investments page.
 */
class OwnerTransaction extends Model
{
    public const TYPE_TAKEN  = 'taken';
    public const TYPE_PUT_IN = 'put_in';

    public const PAID_VIA = ['Cash', 'JazzCash', 'EasyPaisa', 'Bank'];

    protected $fillable = [
        'type', 'amount', 'transaction_date', 'purpose', 'paid_via', 'note', 'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date:Y-m-d',
        'amount'           => 'decimal:2',
    ];

    /** Signed effect on the owner balance: put in = +, taken = −. */
    public function signedAmount(): float
    {
        return $this->type === self::TYPE_PUT_IN ? (float) $this->amount : -(float) $this->amount;
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_PUT_IN ? 'Own Money Put In' : 'Taken for Home';
    }
}
