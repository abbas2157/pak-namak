<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::withCount('salaries')->orderByDesc('id')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:employees,email',
            'phone'        => 'required|string|max:20',
            'cnic'         => 'nullable|string|max:20|unique:employees,cnic',
            'designation'  => 'nullable|string|max:100',
            'salary'       => 'required|numeric|min:0',
            'address'      => 'nullable|string',
            'joining_date' => 'nullable|date',
            'leave_date'   => 'nullable|date',
            'status'       => 'required|in:working,on_leave,terminated',
        ]);

        Employee::create($request->only([
            'name', 'email', 'phone', 'cnic', 'designation',
            'salary', 'address', 'joining_date', 'leave_date', 'status',
        ]));

        return response()->json(['success' => true]);
    }

    public function show(Employee $employee)
    {
        $employee->load('salaries');
        $totalPaid = $employee->salaries->sum('amount');
        return view('admin.employees.show', compact('employee', 'totalPaid'));
    }

    public function edit(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:employees,email,' . $employee->id,
            'phone'        => 'required|string|max:20',
            'cnic'         => 'nullable|string|max:20|unique:employees,cnic,' . $employee->id,
            'designation'  => 'nullable|string|max:100',
            'salary'       => 'required|numeric|min:0',
            'address'      => 'nullable|string',
            'joining_date' => 'nullable|date',
            'leave_date'   => 'nullable|date',
            'status'       => 'required|in:working,on_leave,terminated',
        ]);

        $employee->update($request->only([
            'name', 'email', 'phone', 'cnic', 'designation',
            'salary', 'address', 'joining_date', 'leave_date', 'status',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['success' => true]);
    }
}
