<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::all();
        return view('admin.employees.index', compact('employees'));
    }
    public function create()
    {
        return view('admin.employees.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'salary' => 'required|numeric',
            'address' => 'required',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->salary = $request->salary;
        $employee->address = $request->address;
        $employee->status = $request->status;
        $employee->save();
        return response()->json(['success' => true]);
    }
    public function edit(Employee $employee)
    {
        return response()->json($employee);
    }
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'salary' => 'required|numeric',
            'address' => 'required',
        ]);
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->salary = $request->salary;
        $employee->address = $request->address;
        $employee->status = $request->status;
        $employee->save();

        return response()->json(['success' => true]);
    }
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['success' => true]);
    }
}
