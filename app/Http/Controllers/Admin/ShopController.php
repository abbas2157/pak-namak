<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\City;
use App\Models\Order;
use App\Models\SalePayment;
use App\Models\Shop;
use App\Models\SpiceOrder;
use App\Models\SpiceSalePayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('cityRecord', 'area')
            ->withCount('sales')
            ->withCount('spiceSales')
            ->withSum('sales', 'total_amount')
            ->withSum('sales', 'pending_amount')
            ->withSum('spiceSales', 'total_amount')
            ->withSum('spiceSales', 'pending_amount')
            ->orderByDesc('id');

        // Filters: city, area, and sales activity (either product line).
        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->area_id) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->sales === 'with') {
            $query->where(fn ($q) => $q->has('sales')->orHas('spiceSales'));
        } elseif ($request->sales === 'none') {
            $query->doesntHave('sales')->doesntHave('spiceSales');
        }

        $shops = $query->get();

        // Pending is a computed sum across two relations, so it's filtered in memory.
        if ($request->sales === 'pending') {
            $shops = $shops->filter(fn ($s) => (float) $s->sales_sum_pending_amount + (float) $s->spice_sales_sum_pending_amount > 0)->values();
        }

        $filters = $request->only(['city_id', 'area_id', 'sales']);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        // A shop's money owed spans both product lines — reporting salt alone
        // understated what every shop actually owes.
        $shops->each(function (Shop $shop) {
            $shop->combined_total_amount = (float) $shop->sales_sum_total_amount
                + (float) $shop->spice_sales_sum_total_amount;
            $shop->combined_pending_amount = (float) $shop->sales_sum_pending_amount
                + (float) $shop->spice_sales_sum_pending_amount;
        });

        $totalShops = $shops->count();
        $activeShops = $shops->where('status', 'active')->count();
        $totalRevenue = $shops->sum('combined_total_amount');
        $totalPending = $shops->sum('combined_pending_amount');

        $cities = City::with('areas')->orderBy('name')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.shops.index', compact(
            'shops', 'totalShops', 'activeShops', 'totalRevenue', 'totalPending', 'cities', 'accounts', 'filters', 'hasFilters'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.shops.index');
    }

    /**
     * Standalone "Record Payment" page — pick any shop and log a payment
     * against its pending balance without going through the Shops list.
     */
    public function paymentForm()
    {
        $shops = Shop::with('area')
            ->withSum('sales', 'pending_amount')
            ->withSum('spiceSales', 'pending_amount')
            ->get()
            ->each(function (Shop $shop) {
                $shop->salt_pending = (float) $shop->sales_sum_pending_amount;
                $shop->spice_pending = (float) $shop->spice_sales_sum_pending_amount;
                $shop->combined_pending_amount = $shop->salt_pending + $shop->spice_pending;
            })
            ->sortByDesc('combined_pending_amount')
            ->values();

        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.shops.payment-form', compact('shops', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|unique:shops,email|max:255',
            'address' => 'required|string|max:500',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'status' => 'required|in:active,inactive',
        ]);

        $shop = Shop::create($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city_id', 'area_id', 'status',
        ]));

        $shop->load('cityRecord', 'area');
        $shop->loadCount('sales');
        $shop->sales_sum_total_amount = 0;
        $shop->sales_sum_pending_amount = 0;

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function show($id)
    {
        $shop = Shop::with([
            'sales.dalla',
            'sales.thailas',
            'sales.packages',
        ])->findOrFail($id);

        return view('admin.shops.sales', compact('shop'));
    }

    public function edit($id)
    {
        return response()->json(Shop::with('cityRecord', 'area')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|unique:shops,email,'.$id.'|max:255',
            'address' => 'required|string|max:500',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'status' => 'required|in:active,inactive',
        ]);

        $shop->update($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city_id', 'area_id', 'status',
        ]));

        $shop->load('cityRecord', 'area');

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function destroy($id)
    {
        Shop::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Record a lump-sum payment against a shop's overall pending balance,
     * spreading it across that shop's pending sales oldest-first (FIFO)
     * until the amount is used up or every sale is settled.
     */
    public function recordPayment(Request $request, Shop $shop)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'note' => 'nullable|string|max:500',
            'product_line' => 'nullable|in:salt,spice,both',
        ]);

        $account = Account::find($request->account_id);
        $line = $request->input('product_line', 'both');

        // The payer says which udhaar this money is for. Within the chosen
        // line(s) it is settled oldest-first; "both" is one queue across salt
        // and spice so a lump sum clears the shop's genuine outstanding balance.
        $pendingSales = collect();
        if ($line !== 'spice') {
            $pendingSales = $pendingSales->concat($shop->sales()->where('pending_amount', '>', 0)->get());
        }
        if ($line !== 'salt') {
            $pendingSales = $pendingSales->concat($shop->spiceSales()->where('pending_amount', '>', 0)->get());
        }
        $pendingSales = $pendingSales->sortBy(fn ($sale) => [(string) $sale->sale_date, $sale->id])->values();

        $totalPending = $pendingSales->sum('pending_amount');

        if ($totalPending <= 0) {
            $label = ['salt' => 'salt', 'spice' => 'spice', 'both' => ''][$line];

            return response()->json(['success' => false, 'message' => "This shop has no pending {$label} amount."], 422);
        }

        if ((float) $request->amount > (float) $totalPending + 0.001) {
            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds the pending balance for the selected line ('.number_format($totalPending, 0).').',
            ], 422);
        }

        $remaining = min((float) $request->amount, (float) $totalPending);
        $salesPaid = 0;

        DB::transaction(function () use ($pendingSales, &$remaining, $request, &$salesPaid, $account) {
            foreach ($pendingSales as $sale) {
                if ($remaining <= 0) {
                    break;
                }

                $allocated = min($remaining, (float) $sale->pending_amount);

                // Works for both Sale and SpiceSale — each one's payments()
                // relation creates the right payment model and sets its own FK.
                $sale->payments()->create([
                    'account_id' => $account?->id,
                    'amount' => $allocated,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $account?->paymentMethodLabel() ?? 'Other',
                    'note' => $request->note,
                ]);

                $received = $sale->payments()->sum('amount');
                $sale->update([
                    'received_amount' => $received,
                    'pending_amount' => $sale->total_amount - $received,
                ]);

                $remaining -= $allocated;
                $salesPaid++;
            }
        });

        return response()->json(['success' => true, 'sales_paid' => $salesPaid]);
    }

    /**
     * Payment history for one shop — salt and spice payments merged, newest
     * first — for the Record Payment page.
     */
    public function payments(Shop $shop): JsonResponse
    {
        $saltIds = $shop->sales()->pluck('id');
        $spiceIds = $shop->spiceSales()->pluck('id');

        $salt = SalePayment::with(['account', 'sale:id,sale_date,total_amount'])
            ->whereIn('sale_id', $saltIds)->get()
            ->map(fn ($p) => $this->paymentRow($p, 'Salt', $p->sale));

        $spice = SpiceSalePayment::with(['account', 'sale:id,sale_date,total_amount'])
            ->whereIn('spice_sale_id', $spiceIds)->get()
            ->map(fn ($p) => $this->paymentRow($p, 'Spice', $p->sale));

        $payments = $salt->concat($spice)
            ->sortByDesc(fn ($r) => [$r['payment_date'], $r['id']])
            ->values();

        return response()->json([
            'payments' => $payments->take(100)->values(),
            'count' => $payments->count(),
            'total' => (float) $payments->sum('amount'),
            'salt_total' => (float) $salt->sum('amount'),
            'spice_total' => (float) $spice->sum('amount'),
        ]);
    }

    private function paymentRow($payment, string $line, $sale): array
    {
        return [
            'id' => $payment->id,
            'line' => $line,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date ? Carbon::parse($payment->payment_date)->toDateString() : null,
            'date_label' => $payment->payment_date ? Carbon::parse($payment->payment_date)->format('d M Y') : '—',
            'account' => $payment->account?->label() ?? ($payment->payment_method ?: 'Other'),
            'note' => $payment->note,
            'sale_id' => $sale?->id,
            'sale_date' => $sale?->sale_date ? Carbon::parse($sale->sale_date)->format('d M Y') : '—',
            'sale_total' => (float) ($sale?->total_amount ?? 0),
        ];
    }

    public function info(Shop $shop): JsonResponse
    {
        $sumsFor = fn ($relation) => $relation
            ->selectRaw('COALESCE(SUM(total_amount),0) as total_amount, COALESCE(SUM(received_amount),0) as received_amount, COALESCE(SUM(pending_amount),0) as pending_amount')
            ->first();

        $stats = $sumsFor($shop->sales());
        $spiceStats = $sumsFor($shop->spiceSales());

        $orders = Order::where('shop_id', $shop->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('sale')
            ->withCount('items')
            ->orderByDesc('id')
            ->get(['id', 'reference', 'status', 'created_at', 'remarks']);

        // Same for the spice side, so the spice sale form can list them.
        $spiceOrders = SpiceOrder::where('shop_id', $shop->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('sale')
            ->withCount('items')
            ->orderByDesc('id')
            ->get(['id', 'reference', 'status', 'created_at', 'remarks']);

        // Both product lines, tagged so the popup can show which is which.
        $pendingSales = $shop->sales()
            ->where('pending_amount', '>', 0)
            ->get(['id', 'sale_date', 'pending_amount'])
            ->map(fn ($s) => tap($s, fn ($sale) => $sale->product_line = 'Salt'))
            ->concat(
                $shop->spiceSales()
                    ->where('pending_amount', '>', 0)
                    ->get(['id', 'sale_date', 'pending_amount'])
                    ->map(fn ($s) => tap($s, fn ($sale) => $sale->product_line = 'Spices'))
            )
            ->sortBy(fn ($sale) => [(string) $sale->sale_date, $sale->id])
            ->values();

        return response()->json([
            'shop' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'phone_number' => $shop->phone_number,
                'location' => $shop->location,
            ],
            'financials' => [
                'total_amount' => (float) $stats->total_amount + (float) $spiceStats->total_amount,
                'received_amount' => (float) $stats->received_amount + (float) $spiceStats->received_amount,
                'pending_amount' => (float) $stats->pending_amount + (float) $spiceStats->pending_amount,
                'salt_pending' => (float) $stats->pending_amount,
                'spice_pending' => (float) $spiceStats->pending_amount,
            ],
            'orders' => $orders->map(fn ($o) => [
                'id' => $o->id,
                'reference' => $o->reference,
                'status' => $o->status,
                'created_at' => $o->created_at->format('d M Y'),
                'items_count' => $o->items_count,
            ])->values(),
            'spice_orders' => $spiceOrders->map(fn ($o) => [
                'id' => $o->id,
                'reference' => $o->reference,
                'status' => $o->status,
                'created_at' => $o->created_at->format('d M Y'),
                'items_count' => $o->items_count,
            ])->values(),
            'pending_sales' => $pendingSales->map(fn ($s) => [
                'id' => $s->id,
                'sale_date' => $s->sale_date ? Carbon::parse($s->sale_date)->format('d M Y') : '-',
                'pending_amount' => (float) $s->pending_amount,
                'product_line' => $s->product_line,
            ])->values(),
        ]);
    }
}
