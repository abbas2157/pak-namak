<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashLedger;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class CashLedgerController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth      = $request->month;
        $selectedSource     = $request->source;
        $selectedAccount    = $request->account;
        $selectedInvestment = $request->investment;

        // Running balance must reflect the TRUE overall ledger regardless of
        // any filter, so it's computed once from every row, ascending, then
        // looked up per displayed row — filters only decide what's shown.
        $runningBalances = [];
        $balance = 0.0;
        CashLedger::orderBy('transaction_date')->orderBy('id')
            ->get(['id', 'type', 'amount'])
            ->each(function ($row) use (&$balance, &$runningBalances) {
                $balance += $row->type === 'in' ? (float) $row->amount : -(float) $row->amount;
                $runningBalances[$row->id] = $balance;
            });

        $filtered = CashLedger::with('account');
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $filtered->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
        }
        if ($selectedSource) {
            $filtered->where('source_type', $selectedSource);
        }
        if ($selectedAccount) {
            $filtered->where('account_id', $selectedAccount);
        }
        if ($selectedInvestment !== null && $selectedInvestment !== '') {
            $filtered->where('is_investment', (bool) $selectedInvestment);
        }

        $all = $filtered->orderByDesc('transaction_date')->orderByDesc('id')->get();
        $all->each(fn ($row) => $row->running_balance = $runningBalances[$row->id] ?? null);

        $perPage = 30;
        $page = $request->get('page', 1);
        $entries = new LengthAwarePaginator(
            $all->forPage($page, $perPage),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $currentBalance = CashLedger::currentBalance();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();
        $thisMonthIn  = (float) CashLedger::where('type', 'in')->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
        $thisMonthOut = (float) CashLedger::where('type', 'out')->whereBetween('transaction_date', [$monthStart, $monthEnd])->sum('amount');
        $allTimeIn    = (float) CashLedger::where('type', 'in')->sum('amount');
        $allTimeOut   = (float) CashLedger::where('type', 'out')->sum('amount');

        // Breakdown panels respect the month filter (so they match what the
        // table is showing) but ignore source/account filters (a breakdown of
        // one source/account in isolation isn't useful).
        $breakdownQuery = CashLedger::query();
        if ($selectedMonth) {
            [$year, $month] = explode('-', $selectedMonth);
            $breakdownQuery->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
        }
        $breakdownRows = (clone $breakdownQuery)->selectRaw('type, source_type, SUM(amount) as total')
            ->groupBy('type', 'source_type')
            ->get();

        $inBreakdown  = $breakdownRows->where('type', 'in')->sortByDesc('total');
        $outBreakdown = $breakdownRows->where('type', 'out')->sortByDesc('total');
        $inTotal  = $inBreakdown->sum('total');
        $outTotal = $outBreakdown->sum('total');

        $months = CashLedger::selectRaw("DATE_FORMAT(transaction_date,'%Y-%m') as value, DATE_FORMAT(transaction_date,'%M %Y') as label")
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        // Every account with its own live balance and (if set) opening entry.
        $accounts = Account::orderByDesc('is_active')->orderBy('name')->get()->map(function ($account) {
            $account->balance = $account->currentBalance();
            $account->opening = CashLedger::where('source_type', 'opening_balance')->where('source_id', $account->id)->first();
            return $account;
        });

        return view('admin.cash-ledger.index', compact(
            'entries', 'currentBalance', 'thisMonthIn', 'thisMonthOut', 'allTimeIn', 'allTimeOut',
            'inBreakdown', 'outBreakdown', 'inTotal', 'outTotal', 'accounts',
            'months', 'selectedMonth', 'selectedSource', 'selectedAccount', 'selectedInvestment'
        ));
    }

    public function setOpeningBalance(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount'     => 'required|numeric|min:0',
            'date'       => 'required|date',
        ]);

        // Keyed by the account itself — one opening balance per account, not
        // one blended figure for the whole business.
        CashLedger::sync(
            'opening_balance',
            $request->account_id,
            'in',
            (float) $request->amount,
            $request->date,
            'Opening Balance',
            (int) $request->account_id,
        );

        return response()->json(['success' => true]);
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'account_id'  => 'required|exists:accounts,id',
            'type'        => 'required|in:in,out',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'required|string|max:255',
            'note'        => 'nullable|string|max:500',
        ]);

        CashLedger::create([
            'account_id'       => $request->account_id,
            'type'             => $request->type,
            'amount'           => $request->amount,
            'source_type'      => 'manual',
            'source_id'        => null,
            'transaction_date' => $request->date,
            'description'      => $request->description,
            'note'             => $request->note,
            'created_by'       => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyManual(CashLedger $ledger)
    {
        if (!in_array($ledger->source_type, ['manual', 'opening_balance'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This entry is linked to a real record — delete or edit that record instead.',
            ], 422);
        }

        $ledger->delete();

        return response()->json(['success' => true]);
    }
}
