<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'cnic',
        'designation',
        'salary',
        'address',
        'joining_date',
        'leave_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date:Y-m-d',
        'leave_date'   => 'date:Y-m-d',
    ];

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class)->orderByDesc('month');
    }

    public function absences()
    {
        return $this->hasMany(EmployeeAbsence::class);
    }

    /**
     * True if the given date is a non-working day: the fixed weekly off
     * (config('admin.weekly_holiday'), 0=Sunday) or a listed company holiday.
     */
    public static function isHoliday(string $date): bool
    {
        $day = Carbon::parse($date)->dayOfWeek;

        if ($day === (int) config('admin.weekly_holiday', 0)) {
            return true;
        }

        return CompanyHoliday::whereDate('date', $date)->exists();
    }
}
