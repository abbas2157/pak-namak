<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Account, Expense};
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        $query = Expense::orderByDesc('id');

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
            'expenses', 'categoryTotals', 'grandTotal', 'selectedMonth', 'months', 'accounts'
        ));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        return view('admin.expenses.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date'  => 'required|date',
            'category'      => 'required|string|max:255',
            'account_id'    => 'required|exists:accounts,id',
            'amount'        => 'required|numeric|min:0',
            'is_investment' => 'boolean',
        ]);

        $account = Account::find($request->account_id);

        $expense = Expense::create([
            'expense_date'   => $request->expense_date,
            'category'       => $request->category,
            'account_id'     => $account->id,
            'payment_method' => $account->paymentMethodLabel(),
            'amount'         => $request->amount,
            'description'    => $request->description,
            'remarks'        => $request->remarks,
            'is_investment'  => $request->boolean('is_investment'),
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
            'expense_date'  => 'required|date',
            'category'      => 'required|string|max:255',
            'account_id'    => 'required|exists:accounts,id',
            'amount'        => 'required|numeric|min:0',
            'is_investment' => 'boolean',
        ]);

        $account = Account::find($request->account_id);

        $expense->update([
            'expense_date'   => $request->expense_date,
            'category'       => $request->category,
            'account_id'     => $account->id,
            'payment_method' => $account->paymentMethodLabel(),
            'amount'         => $request->amount,
            'description'    => $request->description,
            'remarks'        => $request->remarks,
            'is_investment'  => $request->boolean('is_investment'),
        ]);

        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['success' => true]);
    }
}
