<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    protected $fillable = ['employee_id', 'date', 'paid', 'note'];

    protected $casts = [
        'date' => 'date',
        'paid' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
