<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\{Sale, SaleDalla, SaleThaila, SalePackage, SalePayment, SaltType, Shop};

class SaleController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        $shops = Shop::all();
        $sales = Sale::get();
        return view('admin.sales.index', compact('sales','shops'));
=======
        $sales = Sale::orderBy('id', 'desc')->get();
        return view('admin.sales.index', compact('sales'));
>>>>>>> 01f0bc2180f4f7f3e8747ad56e87a7ac4bba9628
    }

    public function create()
    {
        $shops = Shop::orderBy('id', 'desc')->get();
        $types = SaltType::get();
        return view('admin.sales.create', compact('shops','types'));
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
            $sale->update([
                'total_amount'   => $grandTotal,
                'pending_amount' => $grandTotal, // udhaar (no payment yet)
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
        $sale->load('shop','saltType');
        return response()->json($sale);
    }

    public function update(Request $request, Sale $sale)
    {
        $sale->shop_id = $request->shop_id;
        $sale->salt_type_id = $request->salt_type_id;
        $sale->product_size = $request->product_size;
        $sale->quantity_sold = $request->quantity_sold;
        $sale->rate_per_pack = $request->rate_per_pack;
        $sale->total_sales_amount = $request->total_sales_amount;
        $sale->date = $request->date;
        $sale->remarks = $request->remarks;
        $sale->save();

        return response()->json(['success' => true,'data' => $sale ]);
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(['success' => true, 'message' => 'Sale deleted successfully']);
    }
}
