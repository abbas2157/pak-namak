<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\{SpiceSale, SpiceSaleItem, SpiceSalePayment, SpiceType, Shop, Area, City, SpiceOrder, SpiceStock, SpiceStockMovement, Account};

class SpiceSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = SpiceSale::with(['shop', 'items.spiceType'])->orderByDesc('id');

        // Filters: month (legacy), date range, shop, area — all optional and combinable.
        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('sale_date', $year)->whereMonth('sale_date', $month);
        }
        if ($request->from) {
            $query->whereDate('sale_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('sale_date', '<=', $request->to);
        }
        if ($request->shop_id) {
            $query->where('shop_id', $request->shop_id);
        }
        if ($request->city_id) {
            $query->whereHas('shop', fn ($q) => $q->where('city_id', $request->city_id));
        }
        if ($request->area_id) {
            $query->whereHas('shop', fn ($q) => $q->where('area_id', $request->area_id));
        }

        $sales = $query->get();

        $totalRevenue  = $sales->sum('total_amount');
        $totalReceived = $sales->sum('received_amount');
        $totalPending  = $sales->sum('pending_amount');
        $totalCount    = $sales->count();

        // Month options built in PHP so the query stays portable (MySQL + SQLite tests).
        $months = SpiceSale::whereNotNull('sale_date')->pluck('sale_date')
            ->map(fn ($d) => \Carbon\Carbon::parse($d)->format('Y-m'))
            ->unique()->sortDesc()->values()
            ->map(fn ($ym) => (object) ['value' => $ym, 'label' => \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('F Y')]);

        $selectedMonth = $request->month;
        $filters = $request->only(['month', 'from', 'to', 'shop_id', 'city_id', 'area_id']);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        $shops = Shop::with('area')->orderBy('name')->get();
        $areas = Area::with('city:id,name')->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin.spice-sales.index', compact(
            'sales', 'totalRevenue', 'totalReceived', 'totalPending', 'totalCount',
            'months', 'selectedMonth', 'shops', 'areas', 'cities', 'filters', 'hasFilters'
        ));
    }

    public function create()
    {
        $shops  = Shop::with('area')->orderBy('id', 'desc')->get();
        $spiceTypes = SpiceType::orderBy('title')->get();
        $prefill = null;

        if (session('prefill_spice_order_id')) {
            $prefill = SpiceOrder::with(['items', 'shop'])->find(session('prefill_spice_order_id'));
            session()->forget('prefill_spice_order_id');
        }

        $stockLevels = SpiceStock::levels()->keyBy(fn ($l) => SpiceStock::key($l['spice_type_id'], $l['size']));
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.spice-sales.create', compact('shops', 'spiceTypes', 'prefill', 'stockLevels', 'accounts'));
    }

    public function show($id)
    {
        $sale = SpiceSale::with([
            'shop',
            'items.spiceType',
            'payments' => fn ($q) => $q->orderByDesc('payment_date')->orderByDesc('id'),
        ])->findOrFail($id);

        return view('admin.spice-sales.partials.sale-detail', compact('sale'));
    }

    public function store(Request $request)
    {
        // Validated before the transaction — a ValidationException raised inside
        // the try below would be caught by its handler and the real field
        // messages lost. Mirrors SaleController::store().
        $request->validate([
            'shop_id'                 => 'required|exists:shops,id',
            'sale_date'               => 'required|date',
            'spice_order_id'          => 'nullable|exists:spice_orders,id',
            'remarks'                 => 'nullable|string|max:1000',
            'received_amount'         => 'nullable|numeric|min:0',
            'account_id'              => 'nullable|exists:accounts,id',
            'bill_image'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'package.*.*.qty'         => 'nullable|numeric|min:0',
            'package.*.*.rate_per_kg' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $grandTotal = 0;

            $sale = SpiceSale::create([
                'shop_id'         => $request->shop_id,
                'spice_order_id'  => $request->spice_order_id ?: null,
                'sale_date'       => $request->sale_date,
                'total_amount'    => 0,
                'received_amount' => 0,
                'pending_amount'  => 0,
                'remarks'         => $request->remarks,
                'bill_image'      => $this->storeBillImage($request),
            ]);

            if (!empty($request->package)) {
                foreach ($request->package as $spiceTypeId => $grams) {
                    foreach ($grams as $gram => $item) {
                        $qty = $item['qty'] ?? null;

                        if (empty($qty)) {
                            continue;
                        }

                        $totalKg = ($gram / 1000) * $qty;
                        $ratePerKg = (float) ($item['rate_per_kg'] ?? 0);
                        $subTotal = $totalKg * $ratePerKg;

                        SpiceSaleItem::create([
                            'spice_sale_id'   => $sale->id,
                            'spice_type_id'   => $spiceTypeId,
                            'packet_gram'     => $gram,
                            'quantity'        => $qty,
                            'total_kg'        => $totalKg,
                            'price_per_kg'    => $ratePerKg,
                            'sub_total'       => $subTotal,
                        ]);

                        SpiceStockMovement::record((int) $spiceTypeId, (int) $gram, -$qty, -$totalKg, 'sale', $sale);

                        $grandTotal += $subTotal;
                    }
                }
            }

            $initialReceived = min((float) ($request->received_amount ?? 0), $grandTotal);
            if ($initialReceived > 0) {
                $account = Account::find($request->account_id);
                SpiceSalePayment::create([
                    'spice_sale_id'  => $sale->id,
                    'account_id'     => $account?->id,
                    'amount'         => $initialReceived,
                    'payment_date'   => $sale->sale_date,
                    'payment_method' => $account?->paymentMethodLabel() ?? 'Other',
                    'note'           => 'Initial payment at sale creation',
                ]);
            }

            $receivedAmount = $sale->payments()->sum('amount');
            $sale->update([
                'total_amount'    => $grandTotal,
                'received_amount' => $receivedAmount,
                'pending_amount'  => $grandTotal - $receivedAmount,
            ]);

            DB::commit();

            return redirect()->route('admin.spice-sales.index')->with('success', 'Sale created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.spice-sales.index')->with('error', 'Sale not created successfully.');
        }
    }

    public function edit(SpiceSale $spiceSale)
    {
        $spiceSale->load(['shop', 'items.spiceType']);
        $shops = Shop::with('area')->orderBy('name')->get();
        $spiceTypes = SpiceType::orderBy('title')->get();

        $stockLevels = SpiceStock::levels()
            ->keyBy(fn ($l) => SpiceStock::key($l['spice_type_id'], $l['size']))
            ->all();

        // Add back this sale's own already-reserved quantities, since update()
        // reverses them before re-deducting.
        foreach ($spiceSale->items as $item) {
            $key = SpiceStock::key($item->spice_type_id, $item->packet_gram);
            if (isset($stockLevels[$key])) {
                $stockLevels[$key]['quantity'] += $item->quantity;
            }
        }

        return view('admin.spice-sales.edit', compact('spiceSale', 'shops', 'spiceTypes', 'stockLevels'));
    }

    public function update(Request $request, SpiceSale $spiceSale)
    {
        $request->validate([
            'shop_id'   => 'required|exists:shops,id',
            'sale_date' => 'required|date',
            'remarks'   => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $grandTotal = 0;

            $spiceSale->update([
                'shop_id'   => $request->shop_id,
                'sale_date' => $request->sale_date,
                'remarks'   => $request->remarks,
            ]);

            SpiceStockMovement::reverseFor($spiceSale);
            $spiceSale->items()->delete();

            if (!empty($request->package)) {
                foreach ($request->package as $spiceTypeId => $grams) {
                    foreach ($grams as $gram => $item) {
                        $qty = $item['qty'] ?? null;
                        if (empty($qty)) continue;

                        $totalKg = ($gram / 1000) * $qty;
                        $ratePerKg = (float) ($item['rate_per_kg'] ?? 0);
                        $subTotal = $totalKg * $ratePerKg;

                        SpiceSaleItem::create([
                            'spice_sale_id'   => $spiceSale->id,
                            'spice_type_id'   => $spiceTypeId,
                            'packet_gram'     => $gram,
                            'quantity'        => $qty,
                            'total_kg'        => $totalKg,
                            'price_per_kg'    => $ratePerKg,
                            'sub_total'       => $subTotal,
                        ]);

                        SpiceStockMovement::record((int) $spiceTypeId, (int) $gram, -$qty, -$totalKg, 'sale', $spiceSale);

                        $grandTotal += $subTotal;
                    }
                }
            }

            $receivedAmount = $spiceSale->payments()->sum('amount');
            $spiceSale->update([
                'total_amount'    => $grandTotal,
                'received_amount' => $receivedAmount,
                'pending_amount'  => $grandTotal - $receivedAmount,
            ]);

            DB::commit();
            return redirect()->route('admin.spice-sales.index')->with('success', 'Sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Could not update sale: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mirrors SaleController's bill upload. Returns null when no file was sent,
     * so it can be assigned straight into the create payload.
     */
    private function storeBillImage(Request $request): ?string
    {
        if (!$request->hasFile('bill_image')) {
            return null;
        }

        $uploadDir = public_path('uploads/spice-sales/bills');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $request->file('bill_image');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $fileName = 'spice_sale_bill_'.time().'_'.bin2hex(random_bytes(6)).'.'.$ext;

        $file->move($uploadDir, $fileName);

        return 'uploads/spice-sales/bills/'.$fileName;
    }

    public function quickUpdate(Request $request, SpiceSale $spiceSale)
    {
        $request->validate([
            'shop_id'   => 'required|exists:shops,id',
            'sale_date' => 'required|date',
            'remarks'   => 'nullable|string',
        ]);

        $spiceSale->update($request->only(['shop_id', 'sale_date', 'remarks']));

        return response()->json(['success' => true]);
    }

    public function addPayment(Request $request, SpiceSale $spiceSale)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id'   => 'nullable|exists:accounts,id',
            'note'         => 'nullable|string|max:500',
        ]);

        if ($spiceSale->pending_amount <= 0) {
            return response()->json(['success' => false, 'message' => 'This sale has no pending amount.'], 422);
        }

        DB::transaction(function () use ($request, $spiceSale) {
            $amount = min((float) $request->amount, (float) $spiceSale->pending_amount);
            $account = Account::find($request->account_id);

            SpiceSalePayment::create([
                'spice_sale_id'  => $spiceSale->id,
                'account_id'     => $account?->id,
                'amount'         => $amount,
                'payment_date'   => $request->payment_date,
                'payment_method' => $account?->paymentMethodLabel() ?? 'Other',
                'note'           => $request->note,
            ]);

            $received = $spiceSale->payments()->sum('amount');
            $spiceSale->update([
                'received_amount' => $received,
                'pending_amount'  => $spiceSale->total_amount - $received,
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function destroyPayment(SpiceSale $spiceSale, SpiceSalePayment $payment)
    {
        if ($payment->spice_sale_id !== $spiceSale->id) {
            abort(404);
        }

        DB::transaction(function () use ($spiceSale, $payment) {
            $payment->delete();

            $received = $spiceSale->payments()->sum('amount');
            $spiceSale->update([
                'received_amount' => $received,
                'pending_amount'  => $spiceSale->total_amount - $received,
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function destroy(SpiceSale $spiceSale)
    {
        DB::transaction(function () use ($spiceSale) {
            SpiceStockMovement::reverseFor($spiceSale);
            $spiceSale->delete();
        });

        return response()->json(['success' => true, 'message' => 'Sale deleted successfully']);
    }
}
