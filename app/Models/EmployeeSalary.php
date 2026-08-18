<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'account_id',
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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        $sync = function (self $salary) {
            if (!in_array($salary->type, ['advance', 'salary'], true)) {
                return;
            }

            if (!$salary->account_id) {
                CashLedger::remove('employee_salary', $salary->id);
                return;
            }

            $name = $salary->employee?->name ?? 'Unknown Employee';
            $desc = $salary->type === 'advance'
                ? "Advance to {$name}"
                : "Salary ({$salary->month->format('M Y')}) — {$name}";
            $date = $salary->paid_at ?? $salary->month;

            CashLedger::sync('employee_salary', $salary->id, 'out', (float) $salary->amount, $date, $desc, $salary->account_id);
        };

        static::created($sync);
        static::updated($sync);
        static::deleted(fn (self $salary) => CashLedger::remove('employee_salary', $salary->id));
    }
}
