<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Vendor, Purchase, PurchasePayment, VendorAdvance, Account};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('purchases')
            ->withSum('purchases', 'pending_amount')
            ->withSum(['advances' => fn ($q) => $q->where('remaining_amount', '>', 0)], 'remaining_amount')
            ->orderByDesc('id')
            ->get();
        $totalVendors = $vendors->count();
        $totalSpent   = Purchase::sum('grand_total');
        $topVendor    = $vendors->sortByDesc('purchases_count')->first();
        $accounts     = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.vendors.index', compact('vendors', 'totalVendors', 'totalSpent', 'topVendor', 'accounts'));
    }

    public function create()
    {
        return redirect()->route('admin.vendors.index');
    }

    /**
     * Standalone "Record Payment" page — pick any vendor and log a lump-sum
     * payment without going through the Vendors list.
     */
    public function paymentForm()
    {
        $vendors = Vendor::withSum('purchases', 'pending_amount')
            ->orderByDesc('id')
            ->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.vendors.payment-form', compact('vendors', 'accounts'));
    }

    /**
     * Record a lump-sum payment against a vendor's overall pending balance,
     * spreading it across their pending purchases oldest-first (FIFO).
     */
    public function recordPayment(Request $request, Vendor $vendor)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id'   => 'nullable|exists:accounts,id',
            'note'         => 'nullable|string|max:500',
        ]);

        $accountId = $request->account_id;

        $pendingPurchases = $vendor->purchases()
            ->where('pending_amount', '>', 0)
            ->orderBy('purchase_date')
            ->orderBy('id')
            ->get();

        $totalPending = $pendingPurchases->sum('pending_amount');

        if ($totalPending <= 0) {
            return response()->json(['success' => false, 'message' => 'This vendor has no pending amount.'], 422);
        }

        $remaining = min((float) $request->amount, (float) $totalPending);
        $purchasesPaid = 0;

        DB::transaction(function () use ($pendingPurchases, &$remaining, $request, &$purchasesPaid, $accountId) {
            foreach ($pendingPurchases as $purchase) {
                if ($remaining <= 0) {
                    break;
                }

                $allocated = min($remaining, (float) $purchase->pending_amount);

                PurchasePayment::create([
                    'purchase_id'  => $purchase->id,
                    'account_id'   => $accountId,
                    'amount'       => $allocated,
                    'payment_date' => $request->payment_date,
                    'note'         => $request->note,
                ]);

                $paid = $purchase->payments()->sum('amount');
                $purchase->update([
                    'paid_amount'    => $paid,
                    'pending_amount' => $purchase->grand_total - $paid,
                ]);

                $remaining -= $allocated;
                $purchasesPaid++;
            }
        });

        return response()->json(['success' => true, 'purchases_paid' => $purchasesPaid]);
    }

    /**
     * Standalone "Send Advance" page — pick any vendor and send them money
     * before any purchase/order exists (e.g. to fund dispatch). The credit
     * created here can later be drawn on from a purchase's "Record Payment"
     * flow instead of a fresh account transaction.
     */
    public function advanceForm()
    {
        $vendors = Vendor::orderByDesc('id')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.vendors.advance-form', compact('vendors', 'accounts'));
    }

    public function storeAdvance(Request $request, Vendor $vendor)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'advance_date' => 'required|date',
            'account_id'   => 'nullable|exists:accounts,id',
            'note'         => 'nullable|string|max:500',
        ]);

        $advance = $vendor->advances()->create([
            'account_id'       => $request->account_id,
            'amount'           => $request->amount,
            'remaining_amount' => $request->amount,
            'advance_date'     => $request->advance_date,
            'note'             => $request->note,
            'created_by'       => auth()->id(),
        ]);

        return response()->json(['success' => true, 'advance' => $advance]);
    }

    /**
     * Only lets an advance be removed while it's still fully unapplied —
     * once any of it has been drawn on by a purchase payment, deleting it
     * would leave that payment pointing at a vanished source.
     */
    public function destroyAdvance(Vendor $vendor, VendorAdvance $advance)
    {
        if ($advance->vendor_id !== $vendor->id) {
            abort(404);
        }

        if ((float) $advance->remaining_amount < (float) $advance->amount) {
            return response()->json([
                'success' => false,
                'message' => 'This advance has already been partly or fully applied to a purchase — it can no longer be deleted.',
            ], 422);
        }

        $advance->delete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'shop'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::create($request->only(['name', 'shop', 'phone', 'address']));
        return response()->json(['success' => true, 'vendor' => $vendor]);
    }

    public function edit($id)
    {
        return response()->json(Vendor::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'shop'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->only(['name', 'shop', 'phone', 'address']));
        return response()->json(['success' => true, 'vendor' => $vendor]);
    }

    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
