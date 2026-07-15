<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class EmployeeSalaryController extends Controller
{
    /**
     * Standalone "Record Advance" page — pick any employee and log an
     * advance without going through their individual profile page.
     */
    public function advanceForm()
    {
        $employees = Employee::where('status', 'working')->orderBy('name')->get();

        return view('admin.employees.advance-form', compact('employees'));
    }

    /**
     * Record a mid-month advance against this employee's pay.
     */
    public function storeAdvance(Request $request, Employee $employee)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:500',
        ]);

        $month = Carbon::parse($request->payment_date)->startOfMonth()->toDateString();

        $salary = $employee->salaries()->create([
            'type'     => 'advance',
            'month'    => $month,
            'amount'   => $request->amount,
            'paid_at'  => $request->payment_date,
            'note'     => $request->note,
        ]);

        return response()->json(['success' => true, 'salary' => $salary]);
    }

    /**
     * Settle a month: gross salary minus that month's advances minus
     * unpaid-absence deduction (salary/30 per day), and record it.
     */
    public function storeSalary(Request $request, Employee $employee)
    {
        $request->validate([
            'month'   => 'required',
            'paid_at' => 'nullable|date',
            'note'    => 'nullable|string|max:500',
        ]);

        $month = Carbon::parse($request->month)->startOfMonth()->toDateString();

        $exists = $employee->salaries()->where('type', 'salary')->whereDate('month', $month)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Salary for this month is already recorded.'], 422);
        }

        $breakdown = $this->computeBreakdown($employee, $month);

        $salary = $employee->salaries()->create([
            'type'             => 'salary',
            'month'            => $month,
            'amount'           => $breakdown['net'],
            'gross_amount'     => $breakdown['gross'],
            'advance_deducted' => $breakdown['advance_total'],
            'absent_days'      => $breakdown['absent_days'],
            'absence_deducted' => $breakdown['absence_deducted'],
            'paid_at'          => $request->paid_at ?: null,
            'note'             => $request->note,
        ]);

        return response()->json(['success' => true, 'salary' => $salary]);
    }

    /**
     * Preview the settlement breakdown for a month before committing it.
     */
    public function preview(Request $request, Employee $employee)
    {
        $request->validate(['month' => 'required']);

        $month = Carbon::parse($request->month)->startOfMonth()->toDateString();

        $alreadySettled = $employee->salaries()->where('type', 'salary')->whereDate('month', $month)->exists();

        return response()->json(array_merge(
            $this->computeBreakdown($employee, $month),
            ['already_settled' => $alreadySettled]
        ));
    }

    private function computeBreakdown(Employee $employee, string $month): array
    {
        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        $gross = (float) $employee->salary;

        $advanceTotal = (float) $employee->salaries()
            ->where('type', 'advance')
            ->whereDate('month', $month)
            ->sum('amount');

        $absentDays = $employee->absences()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('paid', false)
            ->count();

        $absenceDeducted = round($gross / 30 * $absentDays, 2);

        $net = max($gross - $advanceTotal - $absenceDeducted, 0);

        return [
            'gross'            => $gross,
            'advance_total'    => $advanceTotal,
            'absent_days'      => $absentDays,
            'absence_deducted' => $absenceDeducted,
            'net'              => $net,
        ];
    }

    public function destroy(Employee $employee, EmployeeSalary $salary)
    {
        $salary->delete();
        return response()->json(['success' => true]);
    }
}
