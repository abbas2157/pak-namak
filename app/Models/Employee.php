<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
