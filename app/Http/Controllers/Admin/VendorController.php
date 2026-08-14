<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Vendor, Purchase, PurchasePayment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('purchases')
            ->withSum('purchases', 'pending_amount')
            ->orderByDesc('id')
            ->get();
        $totalVendors = $vendors->count();
        $totalSpent   = Purchase::sum('grand_total');
        $topVendor    = $vendors->sortByDesc('purchases_count')->first();

        return view('admin.vendors.index', compact('vendors', 'totalVendors', 'totalSpent', 'topVendor'));
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

        return view('admin.vendors.payment-form', compact('vendors'));
    }

    /**
     * Record a lump-sum payment against a vendor's overall pending balance,
     * spreading it across their pending purchases oldest-first (FIFO).
     */
    public function recordPayment(Request $request, Vendor $vendor)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:500',
        ]);

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

        DB::transaction(function () use ($pendingPurchases, &$remaining, $request, &$purchasesPaid) {
            foreach ($pendingPurchases as $purchase) {
                if ($remaining <= 0) {
                    break;
                }

                $allocated = min($remaining, (float) $purchase->pending_amount);

                PurchasePayment::create([
                    'purchase_id'  => $purchase->id,
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
