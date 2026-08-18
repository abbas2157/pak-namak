<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Account;
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
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.employees.advance-form', compact('employees', 'accounts'));
    }

    /**
     * Record a mid-month advance against this employee's pay.
     */
    public function storeAdvance(Request $request, Employee $employee)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id'   => 'nullable|exists:accounts,id',
            'note'         => 'nullable|string|max:500',
        ]);

        $month = Carbon::parse($request->payment_date)->startOfMonth()->toDateString();

        $salary = $employee->salaries()->create([
            'type'       => 'advance',
            'account_id' => $request->account_id,
            'month'      => $month,
            'amount'     => $request->amount,
            'paid_at'    => $request->payment_date,
            'note'       => $request->note,
        ]);

        return response()->json(['success' => true, 'salary' => $salary]);
    }

    /**
     * Settle a month: gross salary minus that month's advances minus
     * unpaid-absence deduction (salary/30 per day), and record it.
     */
    public function storeSalary(Request $request, Employee $employee)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'month'      => 'required',
            'paid_at'    => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
            'note'       => 'nullable|string|max:500',
        ]);

        $month = Carbon::parse($request->month)->startOfMonth()->toDateString();

        $exists = $employee->salaries()->where('type', 'salary')->whereDate('month', $month)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Salary for this month is already recorded.'], 422);
        }

        $breakdown = $this->computeBreakdown($employee, $month);

        $salary = $employee->salaries()->create([
            'type'             => 'salary',
            'account_id'       => $request->account_id,
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

    /**
     * Printable payslip for a settled month. Only valid for type='salary'
     * rows — advances have no gross/deduction breakdown to show.
     */
    public function payslip(Employee $employee, EmployeeSalary $salary)
    {
        abort_unless($salary->employee_id === $employee->id && $salary->type === 'salary', 404);

        $start = $salary->month->copy()->startOfMonth();
        $end   = $salary->month->copy()->endOfMonth();

        $paidLeaveDays = $employee->absences()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('paid', true)
            ->count();

        $daysPresent = max(30 - $salary->absent_days - $paidLeaveDays, 0);

        $totalDeductions = (float) $salary->advance_deducted + (float) $salary->absence_deducted;

        $netInWords = $this->numberToWords((int) round($salary->amount)) . ' Rupees Only';

        return view('admin.employees.payslip', compact(
            'employee', 'salary', 'paidLeaveDays', 'daysPresent', 'totalDeductions', 'netInWords'
        ));
    }

    /**
     * Convert a whole number of rupees to words using the
     * Pakistani/Indian numbering system (Lakh / Crore), e.g.
     * 125000 => "One Lakh Twenty Five Thousand".
     */
    private function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $twoDigits = function (int $n) use ($ones, $tens): string {
            if ($n < 20) {
                return $ones[$n];
            }
            $ten = intdiv($n, 10);
            $rest = $n % 10;
            return trim($tens[$ten] . ' ' . $ones[$rest]);
        };

        $threeDigits = function (int $n) use ($twoDigits, $ones): string {
            if ($n < 100) {
                return $twoDigits($n);
            }
            $hundred = intdiv($n, 100);
            $rest = $n % 100;
            $words = $ones[$hundred] . ' Hundred';
            if ($rest > 0) {
                $words .= ' ' . $twoDigits($rest);
            }
            return $words;
        };

        $crore = intdiv($number, 10000000);
        $number %= 10000000;
        $lakh = intdiv($number, 100000);
        $number %= 100000;
        $thousand = intdiv($number, 1000);
        $number %= 1000;
        $hundred = $number;

        $parts = [];
        if ($crore > 0)    $parts[] = $threeDigits($crore) . ' Crore';
        if ($lakh > 0)     $parts[] = $threeDigits($lakh) . ' Lakh';
        if ($thousand > 0) $parts[] = $threeDigits($thousand) . ' Thousand';
        if ($hundred > 0)  $parts[] = $threeDigits($hundred);

        return implode(' ', $parts);
    }
}
