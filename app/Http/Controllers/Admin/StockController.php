<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $levels = Stock::levels();

        $movements = StockMovement::orderByDesc('id')->paginate(20);

        return view('admin.stocks.index', compact('levels', 'movements'));
    }

    /**
     * Add stock for as many dalla/thaila/package lines as were filled in at once
     * (e.g. all thaila sizes in one go), instead of one product+size per request.
     */
    public function storeAddition(Request $request)
    {
        $request->validate([
            'dalla_qty'      => 'nullable|numeric|min:0',
            'thaila'         => 'nullable|array',
            'thaila.*'       => 'nullable|numeric|min:0',
            'package'        => 'nullable|array',
            'package.*.10'   => 'nullable|numeric|min:0',
            'package.*.20'   => 'nullable|numeric|min:0',
            'note'           => 'nullable|string|max:500',
        ]);

        $lines = [];

        $dallaQty = (float) $request->input('dalla_qty', 0);
        if ($dallaQty > 0) {
            $lines[] = ['dalla', null, null, $dallaQty, $dallaQty * 40];
        }

        foreach (config('admin.thaila_sizes', []) as $size) {
            $qty = (float) $request->input("thaila.$size", 0);
            if ($qty > 0) {
                $lines[] = ['thaila', $size, null, $qty, $qty * $size];
            }
        }

        foreach (config('admin.package_grams', []) as $gram) {
            foreach (array_keys(config('admin.bundles', [])) as $bundleSize) {
                $qty = (float) $request->input("package.$gram.$bundleSize", 0);
                if ($qty > 0) {
                    $lines[] = ['package', $gram, $bundleSize, $qty, ($gram / 1000) * $bundleSize * $qty];
                }
            }
        }

        if (empty($lines)) {
            return response()->json(['success' => false, 'message' => 'Enter a quantity for at least one item.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($lines as [$productType, $size, $bundleSize, $quantity, $quantityKg]) {
                StockMovement::record(
                    $productType,
                    $size,
                    $quantity,
                    $quantityKg,
                    'addition',
                    null,
                    $request->note,
                    auth()->id(),
                    $bundleSize,
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
        $request->validate(['note' => 'required|string|max:500']);
        $data = $this->validateLine($request, signed: true);

        DB::beginTransaction();
        try {
            StockMovement::record(
                $data['product_type'],
                $data['size'],
                $data['quantity'],
                $data['quantity_kg'],
                'adjustment',
                null,
                $request->note,
                auth()->id(),
                $data['bundle_size'],
            );

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Could not adjust stock.'], 422);
        }
    }

    /**
     * Validate a product_type/size/bundle_size/quantity line and compute its quantity_kg.
     */
    private function validateLine(Request $request, bool $signed = false): array
    {
        $qtyRule = $signed ? 'required|numeric|not_in:0' : 'required|numeric|min:0.01';

        $request->validate([
            'product_type' => 'required|in:dalla,thaila,package',
            'size'         => 'required_unless:product_type,dalla|nullable|integer',
            'bundle_size'  => 'required_if:product_type,package|nullable|integer',
            'quantity'     => $qtyRule,
        ]);

        $productType = $request->product_type;
        $size        = $productType === 'dalla' ? null : (int) $request->size;
        $bundleSize  = $productType === 'package' ? (int) $request->bundle_size : null;
        $quantity    = (float) $request->quantity;

        $quantityKg = match ($productType) {
            'dalla'   => $quantity * 40,
            'thaila'  => $quantity * $size,
            'package' => ($size / 1000) * $bundleSize * $quantity,
        };

        return [
            'product_type' => $productType,
            'size'         => $size,
            'bundle_size'  => $bundleSize,
            'quantity'     => $quantity,
            'quantity_kg'  => $quantityKg,
        ];
    }
}
