<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpiceSale;

class SpiceSaleReceiptController extends Controller
{
    public function __invoke($id)
    {
        $sale = SpiceSale::with([
            'shop',
            'items.spiceType',
        ])->findOrFail($id);

        return view('admin.spice-sales.receipt', compact('sale'));
    }
}
