<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\{SpicePurchase, SpicePurchasePayment, SpiceType, Vendor, Account};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SpicePurchaseController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $spiceTypes = SpiceType::orderBy('title')->get();
        $query = SpicePurchase::with(['vendor', 'spiceType']);

        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('purchase_date', $year)->whereMonth('purchase_date', $month);
        }

        $purchases = $query->orderByDesc('id')->get();
        $vendors   = Vendor::orderBy('name')->get();

        $totalSpent   = $purchases->sum('grand_total');
        $totalPaid    = $purchases->sum('paid_amount');
        $totalPending = $purchases->sum('pending_amount');
        $totalQtyKg   = $purchases->sum('quantity_kg');
        $totalEntries = $purchases->count();

        $months = SpicePurchase::selectRaw("DATE_FORMAT(purchase_date,'%Y-%m') as value, DATE_FORMAT(purchase_date,'%M %Y') as label")
            ->whereNotNull('purchase_date')
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        $selectedMonth = $request->month;

        return view('admin.spice-purchases.index', compact(
            'purchases', 'vendors', 'accounts', 'spiceTypes',
            'totalSpent', 'totalPaid', 'totalPending', 'totalQtyKg', 'totalEntries',
            'months', 'selectedMonth'
        ));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $spiceTypes = SpiceType::orderBy('title')->get();
        return view('admin.spice-purchases.index', compact('vendors', 'accounts', 'spiceTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'spice_type_id' => 'required|exists:spice_types,id',
            'quantity_kg'   => 'required|numeric|min:0',
            'rate_per_kg'   => 'required|numeric|min:0',
            'total_cost'    => 'required|numeric|min:0',
            'grand_total'   => 'required|numeric|min:0',
            'amount_paid'   => 'nullable|numeric|min:0',
            'account_id'    => 'nullable|exists:accounts,id',
            'is_investment' => 'boolean',
        ]);

        $purchase = DB::transaction(function () use ($request) {
            $purchaseDate = $request->purchase_date ?: now()->toDateString();

            $purchase = SpicePurchase::create([
                'vendor_id'              => $request->vendor_id,
                'spice_type_id'          => $request->spice_type_id,
                'purchase_date'          => $purchaseDate,
                'quantity_kg'            => $request->quantity_kg,
                'rate_per_kg'            => $request->rate_per_kg,
                'total_cost'             => $request->total_cost,
                'transport_cost'         => $request->transport_cost ?? 0,
                'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
                'grand_total'            => $request->grand_total,
                'remarks'                => $request->remarks,
                'is_investment'          => $request->boolean('is_investment'),
            ]);

            $amountPaid = min((float) ($request->amount_paid ?? 0), (float) $request->grand_total);
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
        $purchase = SpicePurchase::findOrFail($id);
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
            'spice_type_id' => 'required|exists:spice_types,id',
            'quantity_kg'   => 'required|numeric|min:0',
            'rate_per_kg'   => 'required|numeric|min:0',
            'total_cost'    => 'required|numeric|min:0',
            'grand_total'   => 'required|numeric|min:0',
            'is_investment' => 'boolean',
        ]);

        $purchase = SpicePurchase::findOrFail($id);
        $purchase->update([
            'vendor_id'              => $request->vendor_id,
            'spice_type_id'          => $request->spice_type_id,
            'purchase_date'          => $request->purchase_date ?: $purchase->purchase_date,
            'quantity_kg'            => $request->quantity_kg,
            'rate_per_kg'            => $request->rate_per_kg,
            'total_cost'             => $request->total_cost,
            'transport_cost'         => $request->transport_cost ?? 0,
            'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
            'grand_total'            => $request->grand_total,
            'remarks'                => $request->remarks,
            'is_investment'          => $request->boolean('is_investment'),
        ]);

        $this->recalcTotals($purchase);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        SpicePurchase::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function recordPayment(Request $request, SpicePurchase $purchase)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id'   => 'required|exists:accounts,id',
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

    public function payments(SpicePurchase $purchase)
    {
        $payments = $purchase->payments()->orderByDesc('payment_date')->orderByDesc('id')->get();

        return response()->json(['success' => true, 'payments' => $payments]);
    }

    public function destroyPayment(SpicePurchase $purchase, SpicePurchasePayment $payment)
    {
        if ($payment->spice_purchase_id !== $purchase->id) {
            abort(404);
        }

        DB::transaction(function () use ($purchase, $payment) {
            $payment->delete();
            $this->recalcTotals($purchase);
        });

        return response()->json(['success' => true]);
    }

    private function recalcTotals(SpicePurchase $purchase): void
    {
        $paid = $purchase->payments()->sum('amount');
        $purchase->update([
            'paid_amount'    => $paid,
            'pending_amount' => $purchase->grand_total - $paid,
        ]);
    }
}
