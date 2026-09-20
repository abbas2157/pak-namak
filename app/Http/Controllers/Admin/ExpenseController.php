<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        $query = Expense::orderByDesc('id');

        // Filters: month, date range, category, account, investment flag — all combinable.
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $query->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
        }
        if ($request->from) {
            $query->whereDate('expense_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('expense_date', '<=', $request->to);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->type === 'investment') {
            $query->where('is_investment', true);
        } elseif ($request->type === 'operating') {
            $query->where('is_investment', false);
        }

        $expenses = $query->get();

        $filters = $request->only(['month', 'from', 'to', 'category', 'account_id', 'type']);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        // Categories actually used, so the filter never offers an empty choice.
        $categories = Expense::select('category')->distinct()->orderBy('category')->pluck('category');

        $categoryTotals = $expenses->groupBy('category')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc();

        $grandTotal = $expenses->sum('amount');

        // Months list from Oct 2025 to current month
        $months = [];
        $start = Carbon::create(2025, 10, 1);
        $current = $start->copy();
        while ($current <= Carbon::now()) {
            $months[] = ['value' => $current->format('Y-m'), 'label' => $current->format('F Y')];
            $current->addMonth();
        }

        return view('admin.expenses.index', compact(
            'expenses', 'categoryTotals', 'grandTotal', 'selectedMonth', 'months', 'accounts',
            'filters', 'hasFilters', 'categories'
        ));
    }

    public function create()
    {
        // Expenses are added from the modal on the list page.
        return redirect()->route('admin.expenses.index');
    }

    public function store(Request $request)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'is_investment' => 'boolean',
        ]);

        $account = Account::find($request->account_id);

        $expense = Expense::create([
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'account_id' => $account?->id,
            'payment_method' => $account?->paymentMethodLabel() ?? 'Other',
            'amount' => $request->amount,
            'description' => $request->description,
            'remarks' => $request->remarks,
            'is_investment' => $request->boolean('is_investment'),
        ]);

        return response()->json($expense);
    }

    public function edit(Expense $expense)
    {
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'is_investment' => 'boolean',
        ]);

        $account = Account::find($request->account_id);

        $expense->update([
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'account_id' => $account?->id,
            'payment_method' => $account?->paymentMethodLabel() ?? 'Other',
            'amount' => $request->amount,
            'description' => $request->description,
            'remarks' => $request->remarks,
            'is_investment' => $request->boolean('is_investment'),
        ]);

        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json(['success' => true]);
    }
}
