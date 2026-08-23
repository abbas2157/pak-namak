<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Expense, Purchase, SpicePurchase, Asset};
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvestmentController extends Controller
{
    /**
     * Investment-flagged Purchases/SpicePurchases and Assets are
     * deliberately kept out of the Cash & Bank ledger (capital funded
     * outside day-to-day cash flow), so this page derives the full list
     * straight from the four source tables instead — the same tables
     * DashboardController::index() already sums for the "Total Investment"
     * card, so the grand total here always matches that card.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');
        $range = null;
        if ($selectedMonth) {
            $range = [
                Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth(),
                Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth(),
            ];
        }

        $expenses = Expense::where('is_investment', true)
            ->when($range, fn ($q) => $q->whereBetween('expense_date', $range))
            ->get()
            ->map(fn ($e) => (object) [
                'source'      => 'expense',
                'source_label'=> 'Expense',
                'date'        => $e->expense_date,
                'label'       => $e->category,
                'sub'         => $e->description ?: $e->remarks,
                'amount'      => (float) $e->amount,
            ]);

        $purchases = Purchase::where('is_investment', true)
            ->when($range, fn ($q) => $q->whereBetween('purchase_date', $range))
            ->with('vendor')
            ->get()
            ->map(fn ($p) => (object) [
                'source'      => 'purchase',
                'source_label'=> 'Salt Purchase',
                'date'        => $p->purchase_date,
                'label'       => $p->vendor?->name ?? 'Unknown Vendor',
                'sub'         => $p->remarks,
                'amount'      => (float) $p->grand_total,
            ]);

        $spicePurchases = SpicePurchase::where('is_investment', true)
            ->when($range, fn ($q) => $q->whereBetween('purchase_date', $range))
            ->with('vendor')
            ->get()
            ->map(fn ($p) => (object) [
                'source'      => 'spice_purchase',
                'source_label'=> 'Spice Purchase',
                'date'        => $p->purchase_date,
                'label'       => $p->vendor?->name ?? 'Unknown Vendor',
                'sub'         => $p->remarks,
                'amount'      => (float) $p->grand_total,
            ]);

        $assets = Asset::where('is_investment', true)
            ->when($range, fn ($q) => $q->whereBetween('purchase_date', $range))
            ->get()
            ->map(fn ($a) => (object) [
                'source'      => 'asset',
                'source_label'=> 'Asset',
                'date'        => $a->purchase_date,
                'label'       => $a->asset_name,
                'sub'         => $a->category,
                'amount'      => $a->totalValue(),
            ]);

        $items = $expenses->concat($purchases)->concat($spicePurchases)->concat($assets)
            ->sortByDesc('date')
            ->values();

        $grandTotal = $items->sum('amount');

        $sourceTotals = $items->groupBy('source_label')->map(fn ($g) => $g->sum('amount'))->sortDesc();

        $months = [];
        $start   = Carbon::create(2025, 10, 1);
        $current = $start->copy();
        while ($current <= Carbon::now()) {
            $months[] = ['value' => $current->format('Y-m'), 'label' => $current->format('F Y')];
            $current->addMonth();
        }

        return view('admin.investments.index', compact(
            'items', 'grandTotal', 'sourceTotals', 'selectedMonth', 'months'
        ));
    }
}
