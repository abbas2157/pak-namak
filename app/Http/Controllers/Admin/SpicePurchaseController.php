<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\{SpicePurchase, SpicePurchasePayment, SpiceType, Vendor, VendorAdvance, Account};
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

        // Vendor advance credit still available to draw on, keyed by vendor —
        // powers the "pay from advance" option in the Record Payment modal.
        $vendorAdvanceCredit = VendorAdvance::where('remaining_amount', '>', 0)
            ->selectRaw('vendor_id, SUM(remaining_amount) as total')
            ->groupBy('vendor_id')
            ->pluck('total', 'vendor_id');

        return view('admin.spice-purchases.index', compact(
            'purchases', 'vendors', 'accounts', 'spiceTypes',
            'totalSpent', 'totalPaid', 'totalPending', 'totalQtyKg', 'totalEntries',
            'months', 'selectedMonth', 'vendorAdvanceCredit'
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
        $request->merge(['account_id' => $request->account_id ?: null]);

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

        // is_investment may have just been toggled — re-sync existing payments'
        // Cash & Bank ledger entries to match (removed if now investment,
        // (re)created if no longer investment).
        $purchase->payments->each->syncLedger();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        SpicePurchase::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Record an additional payment against a purchase (clamped to not
     * exceed what's currently pending). Can either draw fresh money from
     * a Cash & Bank account, or draw down an existing vendor advance —
     * money already sent to this vendor before the purchase existed.
     */
    public function recordPayment(Request $request, SpicePurchase $purchase)
    {
        $useAdvance = $request->boolean('use_advance_credit');
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

        if ($useAdvance) {
            $applied = $this->applyAdvanceCredit($purchase, $amount, $request->payment_date, $request->note);
            if ($applied <= 0) {
                return response()->json(['success' => false, 'message' => 'This vendor has no advance credit available.'], 422);
            }
            return response()->json(['success' => true, 'purchase' => $purchase->fresh()]);
        }

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

    /**
     * Draw down this vendor's oldest available advance credit first,
     * spreading the requested amount across as many advance records as
     * needed. Returns the amount actually applied (capped by whatever
     * credit genuinely exists).
     */
    private function applyAdvanceCredit(SpicePurchase $purchase, float $amount, string $date, ?string $note): float
    {
        return DB::transaction(function () use ($purchase, $amount, $date, $note) {
            $advances = VendorAdvance::where('vendor_id', $purchase->vendor_id)
                ->where('remaining_amount', '>', 0)
                ->orderBy('advance_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $remaining = min($amount, (float) $advances->sum('remaining_amount'));
            $totalApplied = 0.0;

            foreach ($advances as $advance) {
                if ($remaining <= 0) {
                    break;
                }

                $allocate = min($remaining, (float) $advance->remaining_amount);

                $purchase->payments()->create([
                    'account_id'        => $advance->account_id,
                    'vendor_advance_id' => $advance->id,
                    'amount'            => $allocate,
                    'payment_date'      => $date,
                    'note'              => $note,
                ]);

                $advance->decrement('remaining_amount', $allocate);

                $remaining -= $allocate;
                $totalApplied += $allocate;
            }

            if ($totalApplied > 0) {
                $this->recalcTotals($purchase);
            }

            return $totalApplied;
        });
    }
}
