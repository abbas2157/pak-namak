<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\{Purchase, PurchasePayment, Vendor};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('vendor');

        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('purchase_date', $year)->whereMonth('purchase_date', $month);
        }

        $purchases = $query->orderByDesc('id')->get();
        $vendors   = Vendor::orderBy('name')->get();

        $totalSpent   = $purchases->sum('grand_total');
        $totalPaid    = $purchases->sum('paid_amount');
        $totalPending = $purchases->sum('pending_amount');
        $totalQtyKg   = $purchases->sum('salt_quantity_kg');
        $totalQtyTon  = $purchases->sum('salt_quantity');
        $totalEntries = $purchases->count();

        $months = Purchase::selectRaw("DATE_FORMAT(purchase_date,'%Y-%m') as value, DATE_FORMAT(purchase_date,'%M %Y') as label")
            ->whereNotNull('purchase_date')
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        $selectedMonth = $request->month;

        return view('admin.purchases.index', compact(
            'purchases', 'vendors',
            'totalSpent', 'totalPaid', 'totalPending', 'totalQtyKg', 'totalQtyTon', 'totalEntries',
            'months', 'selectedMonth'
        ));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.purchases.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'salt_quantity' => 'required|numeric|min:0',
            'rate_per_kg'   => 'required|numeric|min:0',
            'total_cost'    => 'required|numeric|min:0',
            'grand_total'   => 'required|numeric|min:0',
            'amount_paid'   => 'nullable|numeric|min:0',
        ]);

        $purchase = DB::transaction(function () use ($request) {
            $purchaseDate = $request->purchase_date ?: now()->toDateString();

            $purchase = Purchase::create([
                'vendor_id'              => $request->vendor_id,
                'purchase_date'          => $purchaseDate,
                'salt_quantity'          => $request->salt_quantity,
                'salt_quantity_kg'       => $request->salt_quantity_kg,
                'rate_per_kg'            => $request->rate_per_kg,
                'total_cost'             => $request->total_cost,
                'transport_cost'         => $request->transport_cost ?? 0,
                'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
                'grand_total'            => $request->grand_total,
                'remarks'                => $request->remarks,
            ]);

            $amountPaid = min((float) ($request->amount_paid ?? 0), (float) $request->grand_total);
            if ($amountPaid > 0) {
                $purchase->payments()->create([
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
        $purchase = Purchase::findOrFail($id);
        $data = $purchase->toArray();
        $data['purchase_date'] = $purchase->purchase_date
            ? $purchase->purchase_date->format('Y-m-d')
            : null;
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'salt_quantity' => 'required|numeric|min:0',
            'rate_per_kg'   => 'required|numeric|min:0',
            'total_cost'    => 'required|numeric|min:0',
            'grand_total'   => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'vendor_id'              => $request->vendor_id,
            'purchase_date'          => $request->purchase_date ?: $purchase->purchase_date,
            'salt_quantity'          => $request->salt_quantity,
            'salt_quantity_kg'       => $request->salt_quantity_kg,
            'rate_per_kg'            => $request->rate_per_kg,
            'total_cost'             => $request->total_cost,
            'transport_cost'         => $request->transport_cost ?? 0,
            'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
            'grand_total'            => $request->grand_total,
            'remarks'                => $request->remarks,
        ]);

        // grand_total may have changed — recompute pending against the same paid_amount
        $this->recalcTotals($purchase);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Purchase::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Record an additional payment against a purchase (clamped to not
     * exceed what's currently pending).
     */
    public function recordPayment(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:500',
        ]);

        if ($purchase->pending_amount <= 0) {
            return response()->json(['success' => false, 'message' => 'This purchase has no pending amount.'], 422);
        }

        $amount = min((float) $request->amount, (float) $purchase->pending_amount);

        DB::transaction(function () use ($purchase, $request, $amount) {
            $purchase->payments()->create([
                'amount'       => $amount,
                'payment_date' => $request->payment_date,
                'note'         => $request->note,
            ]);

            $this->recalcTotals($purchase);
        });

        return response()->json(['success' => true, 'purchase' => $purchase->fresh()]);
    }

    public function payments(Purchase $purchase)
    {
        $payments = $purchase->payments()->orderByDesc('payment_date')->orderByDesc('id')->get();

        return response()->json(['success' => true, 'payments' => $payments]);
    }

    public function destroyPayment(Purchase $purchase, PurchasePayment $payment)
    {
        if ($payment->purchase_id !== $purchase->id) {
            abort(404);
        }

        DB::transaction(function () use ($purchase, $payment) {
            $payment->delete();
            $this->recalcTotals($purchase);
        });

        return response()->json(['success' => true]);
    }

    private function recalcTotals(Purchase $purchase): void
    {
        $paid = $purchase->payments()->sum('amount');
        $purchase->update([
            'paid_amount'    => $paid,
            'pending_amount' => $purchase->grand_total - $paid,
        ]);
    }
}
