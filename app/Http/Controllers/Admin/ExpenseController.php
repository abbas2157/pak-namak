<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
     public function index()
    {
        $expenses = Expense::orderBy('expense_date', 'desc')->get();
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:255',
            'payment_method' => 'required|in:Cash,Bank',
            'amount'         => 'required|numeric|min:0',
        ]);

        Expense::create($request->all());
        $expense = new Expense();
        $expense->expense_date = $request->expense_date;
        $expense->category = $request->category;
        $expense->payment_method = $request->payment_method;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->remarks = $request->remarks;
        $expense->save();

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
            'payment_method' => 'required|in:Cash,Bank',
            'amount'         => 'required|numeric|min:0',
        ]);

        $expense->update($request->all());

        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.'
        ]);
    }
}
