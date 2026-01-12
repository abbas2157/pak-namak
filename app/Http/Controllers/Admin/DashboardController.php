<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Sale, Purchase, Shop};
class DashboardController extends Controller
{
    public function index() {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        /* -------------------------
         * MONTH SALES
         * ------------------------ */

        $monthSalesTotal = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');

        /* -------------------------
         * TOTAL SHOPS (TILL DATE)
         * ------------------------ */
        $totalShops = Shop::count();

        /* -------------------------
         * THIS MONTH PURCHASES
         * ------------------------ */
        $monthPurchasesTotal = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('grand_total');

        /* -------------------------
         * PROFIT / LOSS
         * ------------------------ */
        $profitLoss = $monthSalesTotal - ($monthPurchasesTotal);

        return view('admin.dashboard.index', compact(
            'totalShops',
            'monthSalesTotal',
            'monthPurchasesTotal',
            'profitLoss'
        ));
    }
}
