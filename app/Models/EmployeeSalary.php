<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'month',
        'amount',
        'gross_amount',
        'advance_deducted',
        'absent_days',
        'absence_deducted',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'month'   => 'date',
        'paid_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
