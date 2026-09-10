<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Account, PackagingPurchase, PackagingPurchasePayment, Vendor};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackagingPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $query = PackagingPurchase::with('vendor');

        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('purchase_date', $year)->whereMonth('purchase_date', $month);
        }

        if ($request->kind) {
            $query->where('kind', $request->kind);
        }

        $purchases = $query->orderByDesc('id')->get();
        $vendors = Vendor::orderBy('name')->get();

        $totalSpent = $purchases->sum('grand_total');
        $totalPaid = $purchases->sum('paid_amount');
        $totalPending = $purchases->sum('pending_amount');
        $totalQty = $purchases->sum('quantity');
        $totalEntries = $purchases->count();

        $months = PackagingPurchase::selectRaw("DATE_FORMAT(purchase_date,'%Y-%m') as value, DATE_FORMAT(purchase_date,'%M %Y') as label")
            ->whereNotNull('purchase_date')
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        $selectedMonth = $request->month;
        $selectedKind = $request->kind;

        return view('admin.packaging-purchases.index', compact(
            'purchases', 'vendors', 'accounts',
            'totalSpent', 'totalPaid', 'totalPending', 'totalQty', 'totalEntries',
            'months', 'selectedMonth', 'selectedKind'
        ));
    }

    public function store(Request $request)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $data = $request->validate([
            'vendor_id'      => 'required|exists:vendors,id',
            'kind'           => 'required|in:thaila,packet',
            'size'           => 'required|integer|min:1',
            'quantity'       => 'required|numeric|min:0',
            'rate_per_unit'  => 'required|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'amount_paid'    => 'nullable|numeric|min:0',
            'account_id'     => 'nullable|exists:accounts,id',
            'is_investment'  => 'boolean',
        ]);

        $purchase = DB::transaction(function () use ($request, $data) {
            $purchaseDate = $request->purchase_date ?: now()->toDateString();
            [$totalCost, $grandTotal] = $this->computeTotals($data);

            $purchase = PackagingPurchase::create([
                'vendor_id'      => $data['vendor_id'],
                'kind'           => $data['kind'],
                'size'           => $data['size'],
                'quantity'       => $data['quantity'],
                'rate_per_unit'  => $data['rate_per_unit'],
                'total_cost'     => $totalCost,
                'transport_cost' => $data['transport_cost'] ?? 0,
                'grand_total'    => $grandTotal,
                'purchase_date'  => $purchaseDate,
                'remarks'        => $request->remarks,
                'is_investment'  => $request->boolean('is_investment'),
            ]);

            $amountPaid = min((float) ($request->amount_paid ?? 0), $grandTotal);
            if ($amountPaid > 0) {
                $purchase->payments()->create([
                    'account_id'   => $request->account_id,
                    'amount'       => $amountPaid,
                    'payment_date' => $purchaseDate,
                    'note'         => 'Initial payment',
                ]);
            }

            $this->recalcTotals($purchase);

            return $purchase;
        });

        return response()->json(['success' => true, 'purchase' => $purchase]);
    }

    public function edit($id)
    {
        $purchase = PackagingPurchase::findOrFail($id);
        $data = $purchase->toArray();
        $data['purchase_date'] = $purchase->purchase_date
            ? $purchase->purchase_date->format('Y-m-d')
            : null;

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor_id'      => 'required|exists:vendors,id',
            'kind'           => 'required|in:thaila,packet',
            'size'           => 'required|integer|min:1',
            'quantity'       => 'required|numeric|min:0',
            'rate_per_unit'  => 'required|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'is_investment'  => 'boolean',
        ]);

        $purchase = PackagingPurchase::findOrFail($id);
        [$totalCost, $grandTotal] = $this->computeTotals($data);

        $purchase->update([
            'vendor_id'      => $data['vendor_id'],
            'kind'           => $data['kind'],
            'size'           => $data['size'],
            'quantity'       => $data['quantity'],
            'rate_per_unit'  => $data['rate_per_unit'],
            'total_cost'     => $totalCost,
            'transport_cost' => $data['transport_cost'] ?? 0,
            'grand_total'    => $grandTotal,
            'purchase_date'  => $request->purchase_date ?: $purchase->purchase_date,
            'remarks'        => $request->remarks,
            'is_investment'  => $request->boolean('is_investment'),
        ]);

        // grand_total may have changed — recompute pending against the same paid_amount
        $this->recalcTotals($purchase);

        // is_investment may have just been toggled — re-sync existing payments'
        // ledger entries to match (removed if now investment, recreated if not).
        $purchase->payments->each->syncLedger();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PackagingPurchase::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function recordPayment(Request $request, PackagingPurchase $purchase)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id'   => 'nullable|exists:accounts,id',
            'note'         => 'nullable|string|max:500',
        ]);

        if ($purchase->pending_amount <= 0) {
            return response()->json(['success' => false, 'message' => 'This purchase has no pending amount.'], 422);
        }

        $amount = min((float) $request->amount, (float) $purchase->pending_amount);

        DB::transaction(function () use ($purchase, $request, $amount) {
            $purchase->payments()->create([
                'account_id'   => $request->account_id,
                'amount'       => $amount,
                'payment_date' => $request->payment_date,
                'note'         => $request->note,
            ]);

            $this->recalcTotals($purchase);
        });

        return response()->json(['success' => true, 'purchase' => $purchase->fresh()]);
    }

    public function payments(PackagingPurchase $purchase)
    {
        $payments = $purchase->payments()->orderByDesc('payment_date')->orderByDesc('id')->get();

        return response()->json(['success' => true, 'payments' => $payments]);
    }

    public function destroyPayment(PackagingPurchase $purchase, PackagingPurchasePayment $payment)
    {
        if ($payment->packaging_purchase_id !== $purchase->id) {
            abort(404);
        }

        DB::transaction(function () use ($purchase, $payment) {
            $payment->delete();
            $this->recalcTotals($purchase);
        });

        return response()->json(['success' => true]);
    }

    /**
     * Cost is always derived server-side from quantity × rate (+ transport),
     * so a tampered or stale client-side total can never be persisted.
     *
     * @return array{0: float, 1: float} [total_cost, grand_total]
     */
    private function computeTotals(array $data): array
    {
        $totalCost = round((float) $data['quantity'] * (float) $data['rate_per_unit'], 2);
        $grandTotal = round($totalCost + (float) ($data['transport_cost'] ?? 0), 2);

        return [$totalCost, $grandTotal];
    }

    private function recalcTotals(PackagingPurchase $purchase): void
    {
        $paid = $purchase->payments()->sum('amount');
        $purchase->update([
            'paid_amount'    => $paid,
            'pending_amount' => $purchase->grand_total - $paid,
        ]);
    }
}
