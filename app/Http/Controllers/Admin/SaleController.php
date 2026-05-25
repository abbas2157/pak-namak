<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\{Sale, SaleDalla, SaleThaila, SalePackage, SalePayment, SaltType, Shop, Order};

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['shop', 'dalla', 'thailas', 'packages'])
            ->orderByDesc('id');

        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('sale_date', $year)->whereMonth('sale_date', $month);
        }

        $sales = $query->get();

        $totalRevenue  = $sales->sum('total_amount');
        $totalReceived = $sales->sum('received_amount');
        $totalPending  = $sales->sum('pending_amount');
        $totalCount    = $sales->count();

        $months = Sale::selectRaw("DATE_FORMAT(sale_date,'%Y-%m') as value, DATE_FORMAT(sale_date,'%M %Y') as label")
            ->whereNotNull('sale_date')
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        $selectedMonth = $request->month;

        $shops = Shop::orderBy('name')->get();

        return view('admin.sales.index', compact(
            'sales', 'totalRevenue', 'totalReceived', 'totalPending', 'totalCount',
            'months', 'selectedMonth', 'shops'
        ));
    }

    public function create()
    {
        $shops  = Shop::orderBy('id', 'desc')->get();
        $types  = SaltType::get();
        $prefill = null;

        if (session('prefill_order_id')) {
            $prefill = Order::with('items')->find(session('prefill_order_id'));
            session()->forget('prefill_order_id');
        }

        return view('admin.sales.create', compact('shops', 'types', 'prefill'));
    }
    public function show($id)
    {
        $sale = Sale::with([
            'shop',
            'dalla',
            'thailas',
            'packages'
        ])->findOrFail($id);

        return view('admin.sales.partials.sale-detail', compact('sale'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $grandTotal = 0;

            $billImagePath = null;

            if ($request->hasFile('bill_image')) {
                $request->validate([
                    'bill_image' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
                ]);

                $uploadDir = public_path('uploads/sales/bills');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file = $request->file('bill_image');
                $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $fileName = 'sale_bill_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;

                $file->move($uploadDir, $fileName);

                $billImagePath = 'uploads/sales/bills/' . $fileName;
            }


            /* ---------------------------
             * CREATE SALE
             * -------------------------- */
            $sale = Sale::create([
                'shop_id'         => $request->shop_id,
                'sale_date'       => $request->sale_date,
                'total_amount'    => 0,
                'received_amount' => 0,
                'pending_amount'  => 0,
                'remarks'         => $request->remarks,
                'bill_image'     => $billImagePath,
            ]);


            /* ---------------------------
             * DALLA
             * -------------------------- */
            if (!empty($request->dalla)) {

                $d = $request->dalla;

                if (
                    !empty($d['sold_quantity_mann']) ||
                    !empty($d['sold_quantity_kilo'])
                ) {
                    $subTotal = ($d['sold_quantity_mann'] ?? 0) * ($d['pirce_per_mann'] ?? 0);

                    SaleDalla::create([
                        'sale_id'        => $sale->id,
                        'quantity_mann'  => $d['sold_quantity_mann'] ?? 0,
                        'quantity_kg'    => $d['sold_quantity_kilo'] ?? 0,
                        'price_per_mann' => $d['pirce_per_mann'] ?? 0,
                        'price_per_kg'   => $d['pirce_per_kg'] ?? 0,
                        'sub_total'      => $subTotal,
                    ]);

                    $grandTotal += $subTotal;
                }
            }

            /* ---------------------------
             * THAILA (5kg, 10kg, 50kg)
             * -------------------------- */
            if (!empty($request->thaila)) {
                foreach ($request->thaila as $kg => $item) {

                    $soldKg = $item["sold_quantity_kilo_{$kg}"] ?? null;

                    if (empty($soldKg)) {
                        continue;
                    }

                    $subTotal = $item['sub_total']
                        ?? ($soldKg * ($item['pirce_per_kg'] ?? 0));

                    SaleThaila::create([
                        'sale_id'       => $sale->id,
                        'bag_size_kg'   => $kg,
                        'quantity'      => $soldKg ,
                        'total_kg'      => $soldKg * $kg,
                        'price_per_bag' => $item['pirce_per_thaila'] ?? 0,
                        'price_per_kg'  => $item['pirce_per_kg'] ?? 0,
                        'sub_total'     => $subTotal,
                    ]);

                    $grandTotal += $subTotal;
                }
            }

            /* ---------------------------
             * PACKAGES (250–700g)
             * -------------------------- */
            if (!empty($request->package)) {
                foreach ($request->package as $gram => $item) {

                    $bundleQty = $item["sold_bundles_quantity_{$gram}_gram"] ?? null;

                    if (empty($bundleQty)) {
                        continue;
                    }

                    $bundleSize = $item["bundle_type_{$gram}_gram"];
                    $totalKg = ($gram / 1000) * $bundleSize * $bundleQty;

                    $subTotal = $item['sub_total']
                        ?? ($bundleQty * ($item['price_per_bundle'] ?? 0));

                    SalePackage::create([
                        'sale_id'          => $sale->id,
                        'packet_gram'      => $gram,
                        'bundle_size'      => $bundleSize,
                        'bundle_quantity'  => $bundleQty,
                        'total_kg'         => $totalKg,
                        'price_per_bundle' => $item['price_per_bundle'] ?? 0,
                        'sub_total'        => $subTotal,
                    ]);

                    $grandTotal += $subTotal;
                }
            }

            /* ---------------------------
             * UPDATE TOTALS
             * -------------------------- */
            $receivedAmount = min((float) ($request->received_amount ?? 0), $grandTotal);
            $sale->update([
                'total_amount'    => $grandTotal,
                'received_amount' => $receivedAmount,
                'pending_amount'  => $grandTotal - $receivedAmount,
            ]);

            DB::commit();

            return redirect()->route('admin.sales.index')->with('success', 'Sale created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.sales.index')->with('error', 'Sale not created successfully.');
        }
    }

    public function edit(Sale $sale)
    {
        $sale->load(['shop', 'dalla', 'thailas', 'packages']);
        $shops = Shop::orderBy('name')->get();
        return view('admin.sales.edit', compact('sale', 'shops'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'shop_id'         => 'required|exists:shops,id',
            'sale_date'       => 'required|date',
            'received_amount' => 'nullable|numeric|min:0',
            'remarks'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $grandTotal = 0;

            // Bill image
            $billImagePath = $sale->bill_image;
            if ($request->hasFile('bill_image')) {
                $request->validate(['bill_image' => 'image|mimes:jpg,jpeg,png,webp|max:4096']);
                if ($billImagePath && file_exists(public_path($billImagePath))) {
                    unlink(public_path($billImagePath));
                }
                $uploadDir = public_path('uploads/sales/bills');
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $file = $request->file('bill_image');
                $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $fileName = 'sale_bill_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $file->move($uploadDir, $fileName);
                $billImagePath = 'uploads/sales/bills/' . $fileName;
            }

            // Update header (totals re-set below)
            $sale->update([
                'shop_id'    => $request->shop_id,
                'sale_date'  => $request->sale_date,
                'remarks'    => $request->remarks,
                'bill_image' => $billImagePath,
            ]);

            // ── DALLA ──
            $sale->dalla()->delete();
            if (!empty($request->dalla)) {
                $d = $request->dalla;
                if (!empty($d['sold_quantity_mann']) || !empty($d['sold_quantity_kilo'])) {
                    $subTotal = ($d['sold_quantity_mann'] ?? 0) * ($d['pirce_per_mann'] ?? 0);
                    SaleDalla::create([
                        'sale_id'        => $sale->id,
                        'quantity_mann'  => $d['sold_quantity_mann'] ?? 0,
                        'quantity_kg'    => $d['sold_quantity_kilo'] ?? 0,
                        'price_per_mann' => $d['pirce_per_mann'] ?? 0,
                        'price_per_kg'   => $d['pirce_per_kg'] ?? 0,
                        'sub_total'      => $subTotal,
                    ]);
                    $grandTotal += $subTotal;
                }
            }

            // ── THAILA ──
            $sale->thailas()->delete();
            if (!empty($request->thaila)) {
                foreach ($request->thaila as $kg => $item) {
                    $soldKg = $item["sold_quantity_kilo_{$kg}"] ?? null;
                    if (empty($soldKg)) continue;
                    $subTotal = $item['sub_total'] ?? ($soldKg * ($item['pirce_per_thaila'] ?? 0));
                    SaleThaila::create([
                        'sale_id'       => $sale->id,
                        'bag_size_kg'   => $kg,
                        'quantity'      => $soldKg,
                        'total_kg'      => $soldKg * $kg,
                        'price_per_bag' => $item['pirce_per_thaila'] ?? 0,
                        'price_per_kg'  => $item['pirce_per_kg'] ?? 0,
                        'sub_total'     => $subTotal,
                    ]);
                    $grandTotal += $subTotal;
                }
            }

            // ── PACKAGES ──
            $sale->packages()->delete();
            if (!empty($request->package)) {
                foreach ($request->package as $gram => $item) {
                    $bundleQty = $item["sold_bundles_quantity_{$gram}_gram"] ?? null;
                    if (empty($bundleQty)) continue;
                    $bundleSize = $item["bundle_type_{$gram}_gram"];
                    $totalKg    = ($gram / 1000) * $bundleSize * $bundleQty;
                    $subTotal   = $item['sub_total'] ?? ($bundleQty * ($item['price_per_bundle'] ?? 0));
                    SalePackage::create([
                        'sale_id'          => $sale->id,
                        'packet_gram'      => $gram,
                        'bundle_size'      => $bundleSize,
                        'bundle_quantity'  => $bundleQty,
                        'total_kg'         => $totalKg,
                        'price_per_bundle' => $item['price_per_bundle'] ?? 0,
                        'sub_total'        => $subTotal,
                    ]);
                    $grandTotal += $subTotal;
                }
            }

            // ── TOTALS ──
            $receivedAmount = min((float) ($request->received_amount ?? 0), $grandTotal);
            $sale->update([
                'total_amount'    => $grandTotal,
                'received_amount' => $receivedAmount,
                'pending_amount'  => $grandTotal - $receivedAmount,
            ]);

            DB::commit();
            return redirect()->route('admin.sales.index')->with('success', 'Sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Could not update sale: ' . $e->getMessage())->withInput();
        }
    }

    public function quickUpdate(Request $request, Sale $sale)
    {
        $request->validate([
            'shop_id'         => 'required|exists:shops,id',
            'sale_date'       => 'required|date',
            'received_amount' => 'nullable|numeric|min:0',
            'remarks'         => 'nullable|string',
        ]);

        $received = min((float) ($request->received_amount ?? 0), $sale->total_amount);
        $sale->update([
            'shop_id'         => $request->shop_id,
            'sale_date'       => $request->sale_date,
            'received_amount' => $received,
            'pending_amount'  => $sale->total_amount - $received,
            'remarks'         => $request->remarks,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(['success' => true, 'message' => 'Sale deleted successfully']);
    }
}
