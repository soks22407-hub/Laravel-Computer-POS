<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CEOStockTransactionController extends Controller
{


    private function currentQty(Product $product, ?int $variantId): int
    {
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            return $variant->quantity;
        }
        return $product->quantity;
    }

    // Apply a quantity change to product or variant, and keep product total in sync
    private function applyQtyChange(Product $product, ?int $variantId, int $newQty): void
    {
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $variant->quantity = $newQty;
            $variant->save();

            // Recalculate parent product total from all variants
            $product->quantity = $product->variants()->sum('quantity');
        } else {
            $product->quantity = $newQty;
        }

        $product->stock_status = $product->quantity > 0 ? 'instock' : 'outofstock';
        $product->save();
    }

    // ========================================
    // STOCK IN
    // ========================================

    public function stockInForm()
    {
        $products = Product::with('variants')
            ->select('id', 'name', 'SKU', 'quantity', 'cost_price')
            ->orderBy('name')
            ->get();

        return view('ceo.stock-in', compact('products'));
    }

    public function stockInStore(Request $request)
    {
        $request->validate([
            'transaction_date'     => 'required|date',
            'reason'                => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.variant_id'    => 'nullable|exists:product_variants,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_cost'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $variantId = $item['variant_id'] ?? null;

                $before = $this->currentQty($product, $variantId);
                $after  = $before + (int) $item['quantity'];

                StockTransaction::create([
                    'product_id'       => $product->id,
                    'variant_id'       => $variantId,
                    'type'             => 'in',
                    'quantity_before'  => $before,
                    'quantity_change'  => (int) $item['quantity'],
                    'quantity_after'   => $after,
                    'unit_cost'        => $item['unit_cost'],
                    'reason'           => $request->reason,
                    'transaction_date' => $request->transaction_date,
                    'user_id'          => Auth::id(),
                ]);

                $this->applyQtyChange($product, $variantId, $after);
            }
        });

        return redirect()->route('ceo.stock-in')->with('status', 'Stock in recorded successfully!');
    }

    // ========================================
    // STOCK OUT
    // ========================================

    public function stockOutForm()
    {
        $products = Product::with('variants')
            ->select('id', 'name', 'SKU', 'quantity', 'cost_price')
            ->orderBy('name')
            ->get();

        return view('ceo.stock-out', compact('products'));
    }

    public function stockOutStore(Request $request)
    {
        $request->validate([
            'transaction_date'     => 'required|date',
            'reason'                => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.variant_id'    => 'nullable|exists:product_variants,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_cost'     => 'required|numeric|min:0',
        ]);

        // Pre-check stock availability before committing anything
        foreach ($request->items as $item) {
            $product   = Product::findOrFail($item['product_id']);
            $variantId = $item['variant_id'] ?? null;
            $available = $this->currentQty($product, $variantId);

            if ($item['quantity'] > $available) {
                return back()->withErrors([
                    'items' => "Not enough stock for {$product->name}. Available: {$available}, requested: {$item['quantity']}."
                ])->withInput();
            }
        }

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $variantId = $item['variant_id'] ?? null;

                $before = $this->currentQty($product, $variantId);
                $after  = max(0, $before - (int) $item['quantity']);

                StockTransaction::create([
                    'product_id'       => $product->id,
                    'variant_id'       => $variantId,
                    'type'             => 'out',
                    'quantity_before'  => $before,
                    'quantity_change'  => -(int) $item['quantity'],
                    'quantity_after'   => $after,
                    'unit_cost'        => $item['unit_cost'],
                    'reason'           => $request->reason,
                    'transaction_date' => $request->transaction_date,
                    'user_id'          => Auth::id(),
                ]);

                $this->applyQtyChange($product, $variantId, $after);
            }
        });

        return redirect()->route('ceo.stock-out')->with('status', 'Stock out recorded successfully!');
    }

    // ========================================
    // STOCK ADJUSTMENT
    // ========================================

    public function stockAdjustmentForm()
    {
        $products = Product::with('variants')
            ->select('id', 'name', 'SKU', 'quantity', 'cost_price')
            ->orderBy('name')
            ->get();

        return view('ceo.stock-adjustment', compact('products'));
    }

    public function stockAdjustmentStore(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'reason'           => 'required|string|max:255',
            'product_id'       => 'required|exists:products,id',
            'variant_id'       => 'nullable|exists:product_variants,id',
            'new_quantity'     => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $product   = Product::findOrFail($request->product_id);
            $variantId = $request->variant_id ?: null;

            $before = $this->currentQty($product, $variantId);
            $after  = (int) $request->new_quantity;
            $change = $after - $before;

            StockTransaction::create([
                'product_id'       => $product->id,
                'variant_id'       => $variantId,
                'type'             => 'adjustment',
                'quantity_before'  => $before,
                'quantity_change'  => $change,
                'quantity_after'   => $after,
                'unit_cost'        => $product->cost_price,
                'reason'           => $request->reason,
                'transaction_date' => $request->transaction_date,
                'user_id'          => Auth::id(),
            ]);

            $this->applyQtyChange($product, $variantId, $after);
        });

        return redirect()->route('ceo.stock-adjustment')->with('status', 'Stock adjustment applied successfully!');
    }

    // ========================================
    // HISTORY
    // ========================================

    public function history(Request $request)
    {
        $movements = StockTransaction::with(['product', 'variant', 'user'])
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('from'), fn($q) => $q->whereDate('transaction_date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('transaction_date', '<=', $request->to))
            ->latest('transaction_date')
            ->paginate(15);

        return view('ceo.stock-history', compact('movements'));
    }

    //report stock
    public function stockReport(Request $request)
    {
        $from = $request->filled('from')
            ? \Carbon\Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? \Carbon\Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        // ── Daily breakdown ──────────────────────────────────────
        $rows = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd   = $cursor->copy()->endOfDay();

            $dayMovements = StockTransaction::whereBetween('transaction_date', [$dayStart, $dayEnd])->get();

            $inQty     = $dayMovements->where('type', 'in')->sum('quantity_change');
            $inValue   = $dayMovements->where('type', 'in')->sum(fn($m) => $m->quantity_change * $m->unit_cost);

            $outQty    = abs($dayMovements->where('type', 'out')->sum('quantity_change'));
            $outValue  = abs($dayMovements->where('type', 'out')->sum(fn($m) => $m->quantity_change * $m->unit_cost));

            $adjustments = $dayMovements->where('type', 'adjustment');
            $adjustQty   = $adjustments->sum('quantity_change');
            $adjustCount = $adjustments->count();

            $rows[] = [
                'date'          => $dayStart->format('M j, Y'),
                'date_raw'      => $dayStart->format('Y-m-d'),
                'in_qty'        => $inQty,
                'in_value'      => round($inValue, 2),
                'out_qty'       => $outQty,
                'out_value'     => round($outValue, 2),
                'adjust_qty'    => $adjustQty,
                'adjust_count'  => $adjustCount,
                'net_change'    => $inQty - $outQty + $adjustQty,
            ];

            $cursor->addDay();
        }

        // ── Summary KPIs ──────────────────────────────────────────
        $periodMovements = StockTransaction::whereBetween('transaction_date', [$from, $to])->get();

        $summary = [
            'total_in_qty'    => $periodMovements->where('type', 'in')->sum('quantity_change'),
            'total_in_value'  => round($periodMovements->where('type', 'in')->sum(fn($m) => $m->quantity_change * $m->unit_cost), 2),
            'total_out_qty'   => abs($periodMovements->where('type', 'out')->sum('quantity_change')),
            'total_out_value' => round(abs($periodMovements->where('type', 'out')->sum(fn($m) => $m->quantity_change * $m->unit_cost)), 2),
            'total_adjustments' => $periodMovements->where('type', 'adjustment')->count(),
            'adjustment_qty'  => $periodMovements->where('type', 'adjustment')->sum('quantity_change'),
            'current_inventory_value' => round(
                Product::with('variants')->get()->sum(function ($p) {
                    return $p->variants->count() > 0
                        ? $p->variants->sum('quantity') * ($p->cost_price ?? 0)
                        : $p->quantity * ($p->cost_price ?? 0);
                }),
                2
            ),
        ];

        $summary['net_change'] = $summary['total_in_qty'] - $summary['total_out_qty'] + $summary['adjustment_qty'];

        return view('ceo.stock-report', compact('rows', 'summary', 'from', 'to'));
    }
}
