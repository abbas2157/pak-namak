<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'category',
        'payment_method',
        'amount',
        'description',
        'remarks',
    ];
}
