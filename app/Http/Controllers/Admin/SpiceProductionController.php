<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\SpiceProduction;
use App\Models\SpiceType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Spice production — kept separate from salt Production, like every other
 * Spices screen. One record = one spice type packed on one day.
 */
class SpiceProductionController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfMonth();

        $query = SpiceProduction::with(['items', 'spiceType'])
            ->whereBetween('production_date', [$from->toDateString(), $to->toDateString()]);

        if ($request->spice_type_id) {
            $query->where('spice_type_id', $request->spice_type_id);
        }

        $productions = $query->orderByDesc('production_date')->orderByDesc('id')->get();

        $totalRaw = $productions->sum('raw_spice_used');
        $totalFinished = $productions->sum('finished_spice');
        $totalWastage = $productions->sum('wastage');
        $totalCost = $productions->sum('electricity_fuel_cost');
        $efficiency = $totalRaw > 0 ? round(($totalFinished / $totalRaw) * 100, 1) : 0;
        $totalPackets = $productions->sum(fn ($p) => $p->packetCount());
        $totalPackedKg = $productions->sum(fn ($p) => $p->packedKg());

        // One row per day per spice: packets made that day.
        $daily = $productions
            ->groupBy(fn ($p) => Carbon::parse($p->production_date)->toDateString().'|'.$p->spice_type_id)
            ->map(fn ($group) => [
                'date' => Carbon::parse($group->first()->production_date)->toDateString(),
                'spice' => $group->first()->spiceType?->title ?? '—',
                'batches' => $group->count(),
                'raw' => $group->sum('raw_spice_used'),
                'finished' => $group->sum('finished_spice'),
                'packets' => $group->sum(fn ($p) => $p->packetCount()),
                'packed_kg' => $group->sum(fn ($p) => $p->packedKg()),
                'cost' => $group->sum('electricity_fuel_cost'),
            ])
            ->sortBy([['date', 'desc'], ['spice', 'asc']])
            ->values();

        $spiceTypes = SpiceType::orderBy('title')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.spice-productions.index', compact(
            'productions', 'daily', 'from', 'to', 'spiceTypes', 'accounts',
            'totalRaw', 'totalFinished', 'totalWastage', 'totalCost', 'efficiency',
            'totalPackets', 'totalPackedKg'
        ));
    }

    public function store(Request $request)
    {
        $this->validateProduction($request);

        DB::beginTransaction();
        try {
            $production = SpiceProduction::create($request->only([
                'spice_type_id', 'production_date', 'raw_spice_used', 'finished_spice',
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

    public function edit(SpiceProduction $spiceProduction)
    {
        return response()->json($spiceProduction->load('items'));
    }

    public function update(Request $request, SpiceProduction $spiceProduction)
    {
        $this->validateProduction($request);

        DB::beginTransaction();
        try {
            $spiceProduction->update($request->only([
                'spice_type_id', 'production_date', 'raw_spice_used', 'finished_spice',
                'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks', 'account_id',
            ]));
            // Items are always re-posted, even when unchanged: the spice type
            // on the header may have moved and stock is keyed by it.
            $spiceProduction->syncItems($this->parseLines($request));

            DB::commit();

            return response()->json(['success' => true, 'production' => $spiceProduction->load('items')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json(['success' => false, 'message' => 'Could not update production.'], 422);
        }
    }

    public function destroy(SpiceProduction $spiceProduction)
    {
        DB::transaction(fn () => $spiceProduction->delete());

        return response()->json(['success' => true]);
    }

    private function validateProduction(Request $request): void
    {
        $request->merge([
            'account_id' => $request->account_id ?: null,
            'wastage' => $request->wastage ?? 0,
            'electricity_fuel_cost' => $request->electricity_fuel_cost ?? 0,
        ]);

        $request->validate([
            'spice_type_id' => 'required|exists:spice_types,id',
            'production_date' => 'required|date',
            'raw_spice_used' => 'required|numeric|min:0',
            'finished_spice' => 'required|numeric|min:0',
            'wastage' => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
            'account_id' => 'nullable|exists:accounts,id',
            'package' => 'nullable|array',
            'package.*' => 'nullable|numeric|min:0',
        ]);
    }

    /** Packet grid → item lines; same KG maths as SpiceStockController. */
    private function parseLines(Request $request): array
    {
        $lines = [];

        foreach (config('admin.spice_sizes', []) as $gram) {
            $qty = (float) $request->input("package.$gram", 0);
            if ($qty > 0) {
                $lines[] = [
                    'size' => $gram,
                    'quantity' => $qty,
                    'quantity_kg' => ($gram / 1000) * $qty,
                ];
            }
        }

        return $lines;
    }
}
