<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAbsence;
use Illuminate\Http\Request;

class EmployeeAbsenceController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'date' => 'required|date',
            'portion' => 'nullable|in:1,1.00,0.5,0.50,0.25',
            'paid' => 'nullable|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        if (Employee::isHoliday($request->date)) {
            return response()->json(['success' => false, 'message' => 'This date is a holiday — absence not recorded.'], 422);
        }

        $exists = $employee->absences()->whereDate('date', $request->date)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'An absence for this date is already recorded.'], 422);
        }

        $absence = $employee->absences()->create([
            'date' => $request->date,
            'portion' => (float) ($request->portion ?: 1),
            'paid' => $request->boolean('paid'),
            'note' => $request->note,
        ]);

        return response()->json(['success' => true, 'absence' => $absence]);
    }

    public function destroy(Employee $employee, EmployeeAbsence $absence)
    {
        if ($absence->employee_id !== $employee->id) {
            abort(404);
        }

        $absence->delete();

        return response()->json(['success' => true]);
    }
}
