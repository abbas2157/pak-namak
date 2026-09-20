<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        // Daily-basis view: default to the current month, overridable by range.
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfMonth();

        $productions = Production::with('items')
            ->whereBetween('production_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('production_date')
            ->orderByDesc('id')
            ->get();

        $totalRaw = $productions->sum('raw_salt_used');
        $totalFinished = $productions->sum('finished_salt');
        $totalWastage = $productions->sum('wastage');
        $totalCost = $productions->sum('electricity_fuel_cost');
        $efficiency = $totalRaw > 0 ? round(($totalFinished / $totalRaw) * 100, 1) : 0;

        $totalThaila = $productions->sum(fn ($p) => $p->thailaCount());
        $totalPackages = $productions->sum(fn ($p) => $p->packageCount());
        $totalPackedKg = $productions->sum(fn ($p) => $p->packedKg());

        // One row per day: how many thaila / packages came off the line.
        $daily = $productions
            ->groupBy(fn ($p) => Carbon::parse($p->production_date)->toDateString())
            ->map(fn ($group, $date) => [
                'date' => $date,
                'batches' => $group->count(),
                'raw' => $group->sum('raw_salt_used'),
                'finished' => $group->sum('finished_salt'),
                'thaila' => $group->sum(fn ($p) => $p->thailaCount()),
                'packages' => $group->sum(fn ($p) => $p->packageCount()),
                'packed_kg' => $group->sum(fn ($p) => $p->packedKg()),
                'cost' => $group->sum('electricity_fuel_cost'),
            ])
            ->sortKeysDesc()
            ->values();

        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.productions.index', compact(
            'productions', 'daily', 'from', 'to',
            'totalRaw', 'totalFinished', 'totalWastage', 'totalCost', 'efficiency',
            'totalThaila', 'totalPackages', 'totalPackedKg', 'accounts'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.productions.index');
    }

    public function store(Request $request)
    {
        $this->validateProduction($request);

        DB::beginTransaction();
        try {
            $production = Production::create($request->only([
                'production_date', 'raw_salt_used', 'finished_salt',
                'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks', 'account_id',
            ]));
            $production->syncItems($this->parseLines($request));

            DB::commit();

            return response()->json(['success' => true, 'production' => $production->load('items')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json(['success' => false, 'message' => 'Could not save production.'], 422);
        }
    }

    public function edit(Production $production)
    {
        return response()->json($production->load('items'));
    }

    public function update(Request $request, Production $production)
    {
        $this->validateProduction($request);

        DB::beginTransaction();
        try {
            $production->update($request->only([
                'production_date', 'raw_salt_used', 'finished_salt',
                'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks', 'account_id',
            ]));
            $production->syncItems($this->parseLines($request));

            DB::commit();

            return response()->json(['success' => true, 'production' => $production->load('items')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json(['success' => false, 'message' => 'Could not update production.'], 422);
        }
    }

    public function destroy(Production $production)
    {
        DB::transaction(fn () => $production->delete());

        return response()->json(['success' => true]);
    }

    private function validateProduction(Request $request): void
    {
        // Blank optional numbers arrive as null (ConvertEmptyStringsToNull);
        // store them as 0 so the columns' NOT NULL/defaults hold.
        $request->merge([
            'account_id' => $request->account_id ?: null,
            'wastage' => $request->wastage ?? 0,
            'electricity_fuel_cost' => $request->electricity_fuel_cost ?? 0,
        ]);

        $request->validate([
            'production_date' => 'required|date',
            'raw_salt_used' => 'required|numeric|min:0',
            'finished_salt' => 'required|numeric|min:0',
            'wastage' => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
            'account_id' => 'nullable|exists:accounts,id',
            'thaila' => 'nullable|array',
            'thaila.*' => 'nullable|numeric|min:0',
            'package' => 'nullable|array',
            'package.*.10' => 'nullable|numeric|min:0',
            'package.*.20' => 'nullable|numeric|min:0',
        ]);
    }

    /**
     * Thaila / package grids → item lines. Same input shape and KG maths as
     * StockController::storeAddition() so the two never drift.
     */
    private function parseLines(Request $request): array
    {
        $lines = [];

        foreach (config('admin.thaila_sizes', []) as $size) {
            $qty = (float) $request->input("thaila.$size", 0);
            if ($qty > 0) {
                $lines[] = [
                    'product_type' => 'thaila',
                    'size' => $size,
                    'bundle_size' => null,
                    'quantity' => $qty,
                    'quantity_kg' => $qty * $size,
                ];
            }
        }

        foreach (config('admin.package_grams', []) as $gram) {
            foreach (array_keys(config('admin.bundles', [])) as $bundleSize) {
                $qty = (float) $request->input("package.$gram.$bundleSize", 0);
                if ($qty > 0) {
                    $lines[] = [
                        'product_type' => 'package',
                        'size' => $gram,
                        'bundle_size' => $bundleSize,
                        'quantity' => $qty,
                        'quantity_kg' => ($gram / 1000) * $bundleSize * $qty,
                    ];
                }
            }
        }

        return $lines;
    }
}
