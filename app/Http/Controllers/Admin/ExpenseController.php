<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');

        $query = Expense::orderBy('expense_date', 'desc');

        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $query->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
        }

        $expenses = $query->get();

        $categoryTotals = $expenses->groupBy('category')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc();

        $grandTotal = $expenses->sum('amount');

        // Months list from Oct 2025 to current month
        $months = [];
        $start   = Carbon::create(2025, 10, 1);
        $current = $start->copy();
        while ($current <= Carbon::now()) {
            $months[] = ['value' => $current->format('Y-m'), 'label' => $current->format('F Y')];
            $current->addMonth();
        }

        return view('admin.expenses.index', compact(
            'expenses', 'categoryTotals', 'grandTotal', 'selectedMonth', 'months'
        ));
    }

    public function create()
    {
        return view('admin.expenses.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:255',
            'payment_method' => 'required|in:Cash,Bank,JazzCash,EasyPaisa',
            'amount'         => 'required|numeric|min:0',
        ]);

        $expense = Expense::create([
            'expense_date'   => $request->expense_date,
            'category'       => $request->category,
            'payment_method' => $request->payment_method,
            'amount'         => $request->amount,
            'description'    => $request->description,
            'remarks'        => $request->remarks,
        ]);

        return response()->json($expense);
    }

    public function edit(Expense $expense)
    {
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:255',
            'payment_method' => 'required|in:Cash,Bank,JazzCash,EasyPaisa',
            'amount'         => 'required|numeric|min:0',
        ]);

        $expense->update([
            'expense_date'   => $request->expense_date,
            'category'       => $request->category,
            'payment_method' => $request->payment_method,
            'amount'         => $request->amount,
            'description'    => $request->description,
            'remarks'        => $request->remarks,
        ]);

        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['success' => true]);
    }
}
