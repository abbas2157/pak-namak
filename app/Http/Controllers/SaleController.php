<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaltType;
use App\Models\Shop;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('saltType','shop')->get();
        $shops = Shop::all();
        $types = SaltType::all();

        return view('admin.sales.index', compact('sales','shops','types'));
    }

    public function create()
    {
        return view('admin.sales.index');
    }

    public function store(Request $request)
    {
        $sale = new Sale();
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
