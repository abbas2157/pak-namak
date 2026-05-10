<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class EmployeeSalaryController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        // Normalize "YYYY-MM" input to "YYYY-MM-01" for date storage
        $month = Carbon::parse($request->month)->startOfMonth()->toDateString();

        $request->validate([
            'amount'  => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
            'note'    => 'nullable|string|max:500',
        ]);

        $exists = $employee->salaries()->whereDate('month', $month)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Salary for this month is already recorded.'], 422);
        }

        $salary = $employee->salaries()->create([
            'month'   => $month,
            'amount'  => $request->amount,
            'paid_at' => $request->paid_at ?: null,
            'note'    => $request->note,
        ]);

        return response()->json(['success' => true, 'salary' => $salary]);
    }

    public function destroy(Employee $employee, EmployeeSalary $salary)
    {
        $salary->delete();
        return response()->json(['success' => true]);
    }
}
