<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OwnerTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OwnerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');
        $selectedType  = $request->get('type');

        // Running balance is computed over ALL rows oldest → newest so the
        // figure on each row is the true balance as of that entry, even
        // when the list below is filtered.
        $runningBalances = [];
        $balance = 0.0;
        OwnerTransaction::orderBy('transaction_date')->orderBy('id')->get()
            ->each(function ($row) use (&$balance, &$runningBalances) {
                $balance += $row->signedAmount();
                $runningBalances[$row->id] = $balance;
            });

        $query = OwnerTransaction::orderByDesc('transaction_date')->orderByDesc('id');
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
        }
        if (in_array($selectedType, [OwnerTransaction::TYPE_TAKEN, OwnerTransaction::TYPE_PUT_IN], true)) {
            $query->where('type', $selectedType);
        }

        $transactions = $query->get()->each(fn ($row) => $row->running_balance = $runningBalances[$row->id] ?? 0);

        // Period totals follow the filters; net balance is always all-time.
        $periodTaken = (float) $transactions->where('type', OwnerTransaction::TYPE_TAKEN)->sum('amount');
        $periodPutIn = (float) $transactions->where('type', OwnerTransaction::TYPE_PUT_IN)->sum('amount');
        $netBalance  = $balance;

        $months = [];
        $current = Carbon::create(2025, 10, 1);
        while ($current <= Carbon::now()) {
            $months[] = ['value' => $current->format('Y-m'), 'label' => $current->format('F Y')];
            $current->addMonth();
        }

        return view('admin.owner-account.index', compact(
            'transactions', 'periodTaken', 'periodPutIn', 'netBalance',
            'selectedMonth', 'selectedType', 'months'
        ));
    }

    public function store(Request $request)
    {
        $transaction = OwnerTransaction::create($this->validated($request) + ['created_by' => auth()->id()]);

        return response()->json($transaction);
    }

    public function edit(OwnerTransaction $ownerTransaction)
    {
        return response()->json($ownerTransaction);
    }

    public function update(Request $request, OwnerTransaction $ownerTransaction)
    {
        $ownerTransaction->update($this->validated($request));

        return response()->json($ownerTransaction);
    }

    public function destroy(OwnerTransaction $ownerTransaction)
    {
        $ownerTransaction->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type'             => ['required', Rule::in([OwnerTransaction::TYPE_TAKEN, OwnerTransaction::TYPE_PUT_IN])],
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'purpose'          => 'required|string|max:255',
            'paid_via'         => 'nullable|string|max:50',
            'note'             => 'nullable|string|max:1000',
        ]);
    }
}
