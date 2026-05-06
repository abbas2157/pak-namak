<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class SaleReceiptController extends Controller
{
    public function __invoke($id)
    {
        $sale = Sale::with([
            'shop',
            'dalla',
            'thailas',
            'packages',
        ])->findOrFail($id);

        return view('admin.sales.receipt', compact('sale'));
    }
}

