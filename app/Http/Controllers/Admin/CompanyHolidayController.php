<?php

namespace App\Http\Controllers\Admin;

use App\Models\CompanyHoliday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyHolidayController extends Controller
{
    public function index()
    {
        $holidays = CompanyHoliday::orderByDesc('date')->get();

        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date|unique:company_holidays,date',
            'title' => 'required|string|max:255',
        ]);

        CompanyHoliday::create($request->only(['date', 'title']));

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday added.');
    }

    public function destroy(CompanyHoliday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday removed.');
    }
}
