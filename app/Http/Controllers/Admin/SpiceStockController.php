<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpiceStock;
use App\Models\SpiceStockMovement;
use App\Models\SpiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpiceStockController extends Controller
{
    public function index()
    {
        $spiceTypes = SpiceType::orderBy('title')->get();
        $levels = SpiceStock::levels();

        $movements = SpiceStockMovement::with('spiceType')->orderByDesc('id')->paginate(20);

        return view('admin.spice-stock.index', compact('spiceTypes', 'levels', 'movements'));
    }

    /**
     * Add stock for as many size lines of one spice type as were filled in
     * at once, mirroring StockController::storeAddition().
     */
    public function storeAddition(Request $request)
    {
        $request->validate([
            'spice_type_id' => 'required|exists:spice_types,id',
            'package'       => 'nullable|array',
            'note'          => 'nullable|string|max:500',
        ]);

        $spiceTypeId = (int) $request->spice_type_id;
        $lines = [];

        foreach (config('admin.spice_sizes', []) as $gram) {
            $qty = (float) $request->input("package.$gram", 0);
            if ($qty > 0) {
                $lines[] = [$gram, $qty, ($gram / 1000) * $qty];
            }
        }

        if (empty($lines)) {
            return response()->json(['success' => false, 'message' => 'Enter a quantity for at least one item.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($lines as [$gram, $quantity, $quantityKg]) {
                SpiceStockMovement::record(
                    $spiceTypeId,
                    $gram,
                    $quantity,
                    $quantityKg,
                    'addition',
                    null,
                    $request->note,
                    auth()->id(),
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'lines' => count($lines)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Could not add stock.'], 422);
        }
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'spice_type_id' => 'required|exists:spice_types,id',
            'size'          => 'required|integer',
            'quantity'      => 'required|numeric|not_in:0',
            'note'          => 'required|string|max:500',
        ]);

        $gram = (int) $request->size;
        $quantity = (float) $request->quantity;
        $quantityKg = ($gram / 1000) * $quantity;

        DB::beginTransaction();
        try {
            SpiceStockMovement::record(
                (int) $request->spice_type_id,
                $gram,
                $quantity,
                $quantityKg,
                'adjustment',
                null,
                $request->note,
                auth()->id(),
            );

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Could not adjust stock.'], 422);
        }
    }
}
