<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Sale, SaltType, Shop};

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('salt_type','shop')->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $shops = Shop::get();
        $types = SaltType::get();
        return view('admin.sales.create', compact('shops','types'));
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
