<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosOrderPayment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CEOPosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock_status', 'instock')
            ->where('quantity', '>', 0)
            ->with('variants')
            ->orderBy('name')
            ->get();
        return view('ceo.pos', compact('products'));
    }

    public function search(Request $request)
    {
        $query   = $request->query('query');
        $barcode = $request->query('barcode');

        //  Check variant barcode first
        if ($barcode) {

            $variant = ProductVariant::where('barcode', $barcode)
                ->where('quantity', '>', 0)
                ->with('product')
                ->first();

            if ($variant) {
                return response()->json([[
                    'id'            => $variant->product->id,
                    'name'          => $variant->product->name . " ({$variant->color} {$variant->storage})",
                    'SKU'           => $variant->product->SKU,
                    'barcode'       => $variant->barcode,
                    'sale_price'    => $variant->price,
                    'regular_price' => $variant->price,
                    'image'         => $variant->product->image,
                    'quantity'      => $variant->quantity,
                    'variant_id'    => $variant->id,
                ]]);
            }


            // Check product barcode
            $exact = Product::where('stock_status', 'instock')
                ->where('quantity', '>', 0)
                ->where(function ($q) use ($barcode) {
                    $q->where('barcode', $barcode)
                        ->orWhere('SKU', $barcode);
                })
                ->with('variants')
                ->first();

            if ($exact) {
                return response()->json([$exact]);
            }
        }

        //  Fuzzy search
        $products = Product::where('stock_status', 'instock')
            ->where('quantity', '>', 0)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('SKU', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->with('variants')
            ->get(['id', 'name', 'SKU', 'barcode', 'sale_price', 'regular_price', 'image', 'quantity']);

        $variant = ProductVariant::where('quantity', '>', 0)
            ->where(function ($q) use ($query) {
                $q->where('barcode', 'like', "%{$query}%")
                    ->orWhere('color', 'like', "%{$query}%")
                    ->orWhere('storage', 'like', "%{$query}%");
            })
            ->with('product')
            ->get();

        return response()->json(
            $products->concat($variant)->values()
        );
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->quantity < $item['qty']) {
                    throw new \Exception("'{$product->name}' only has {$product->quantity} left in stock.");
                }

                $price    = $product->sale_price ?: $product->regular_price;
                $itemTotal = $price * $item['qty'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product'      => $product,
                    'product_name' => $product->name,
                    'price'        => $price,
                    'quantity'     => $item['qty'],
                    'subtotal'     => $itemTotal,
                ];

                //  Deduct variant stock if variant selected
                if (!empty($item['variant_id'])) {
                    $variant = \App\Models\ProductVariant::lockForUpdate()->find($item['variant_id']);
                    if ($variant) {
                        $variant->quantity -= $item['qty'];
                        if ($variant->quantity < 0) $variant->quantity = 0;
                        $variant->save();
                    }
                    // Recalculate main product quantity from variants
                    $product->quantity = $product->variants()->sum('quantity');
                } else {
                    $product->quantity -= $item['qty'];
                }

                if ($product->quantity <= 0) {
                    $product->quantity    = 0;
                    $product->stock_status = 'outofstock';
                }
                $product->save();
            }

            $discount     = $request->discount ?? 0;
            $total        = $subtotal - $discount;
            $amountPaid   = $request->amount_paid;
            $change       = $amountPaid - $total;
            $amountOwed = max(0, $total - $amountPaid);

            $posOrder = PosOrder::create([
                'user_id'        => Auth::id(),
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $amountPaid,
                'change_amount'  => max(0, $change),
                'amount_owed'    => $amountOwed,
                'status'         => $amountOwed > 0 ? 'credit' : 'completed',
                'note'           => $request->note,
            ]);

            foreach ($orderItems as $item) {
                PosOrderItem::create([
                    'pos_order_id' => $posOrder->id,
                    'product_id'   => $item['product']->id,
                    'product_name' => $item['product_name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $posOrder->id,
                'change'   => max(0, $change),
                'message'  => 'Sale completed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function receipt($id)
    {
        $order = PosOrder::with('items.product', 'cashier')->findOrFail($id);
        return view('ceo.pos-receipt', compact('order'));
    }

    public function payment_receipt($id)
    {
        $order = PosOrder::with('items.product', 'cashier', 'payments.receivedBy')->findOrFail($id);
        return view('ceo.pos-payment-receipt', compact('order'));
    }

    public function history(Request $request)
    {
        $query = PosOrder::with(['items', 'cashier'])->latest();

        // Search by customer name or phone
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                    ->orWhere('customer_phone', 'like', "%{$request->search}%")
                    ->orWhere('id', "%{$request->search}%")
                    ->orWhereRaw("CONCAT('INV', LPAD(id, 6, '0')) LIKE ?", "%{$request->search}%");
            });
        }

        // Filter by payment method
        if ($request->payment) {
            $query->where('payment_method', $request->payment);
        }

        // Filter by date range
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate(15);

        //  Summary stats
        $todayRevenue = PosOrder::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        $monthRevenue = PosOrder::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $totalRevenue = PosOrder::where('status', 'completed')->sum('total');

        return view('ceo.pos-history', compact(
            'orders',
            'todayRevenue',
            'monthRevenue',
            'totalRevenue'
        ));
    }

    //  Cancel sale + restore stock
    public function cancel($id)
    {
        $order = PosOrder::with('items')->findOrFail($id);

        if ($order->status == 'cancelled') {
            return redirect()->back()->with('status', 'Order already cancelled.');
        }

        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->stock_status = 'instock';
                    $product->save();
                }
            }

            $order->status = 'cancelled';
            $order->save();

            DB::commit();
            return redirect()->back()->with('status', 'Sale cancelled and stock restored!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getDiscounts()
    {
        $discounts = Discount::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', today());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', today());
            })
            ->get(['id', 'name', 'type', 'value', 'min_amount']);

        return response()->json($discounts);
    }

    //Add Payment
    public function showAddPaymentForm($id)
    {
        $order = PosOrder::findOrFail($id);
        if ($order->status !== 'credit' || $order->amount_owed <= 0) {
            return redirect()->back()->with('error', 'This sale does not have any outstanding balance to pay.');
        }

        return view('ceo.pos-add-payment', compact('order'));
    }

    // Process the Add Payment Request
    public function addPayment(Request $request, $id)
    {
        $order = PosOrder::lockForUpdate()->findOrFail($id);

        if ($order->status !== 'credit' || $order->amount_owed <= 0) {
            return redirect()->back()->with('error', 'This sale does not have any outstanding balance to pay.');
        }


        $request->validate([
            'payment_received' => 'required|numeric|min:0.01|max:' . $order->amount_owed,
            'payment_method'   => 'required|string',
            'note'             => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $received = $request->payment_received;
            $paymentNumber = $order->payments()->count() + 1;

            // Log this payment
            PosOrderPayment::create([
                'pos_order_id'   => $order->id,
                'payment_refer'  => 'INV' . str_pad($paymentNumber, 6, '0', STR_PAD_LEFT),
                'amount'         => $received,
                'payment_method' => $request->payment_method,
                'note'           => $request->note,
                'received_by'    => Auth::id(),
            ]);

            $order->amount_paid += $received;
            $order->amount_owed = max(0, $order->amount_owed - $received);
            

            if ($request->payment_method) {
                $order->payment_method = $request->payment_method;
            }

            if ($request->note) {
                $order->note = $request->note;
            }

            if ($order->amount_owed <= 0) {
                $order->status = 'completed';
            }

            $order->save();
            DB::commit();

            return redirect()->route('ceo.pos.history')->with('status', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed processing data: ' . $e->getMessage());
        }
    }

    public function edit_pos($id)
    {
        $order = PosOrder::with('items.product', 'cashier')->findOrFail($id);
        return view('ceo.pos-edit', compact('order'));
    }

    // Process Edit Order Update
    public function update_pos(Request $request, $id)
    {
        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric|min:0',
            'discount'             => 'nullable|numeric|min:0',
            'payment_method'       => 'required|string',
            'amount_paid'          => 'required|numeric|min:0',
            'status'               => 'required|in:completed,credit,cancelled',
            'note'                 => 'nullable|string|max:2000',
        ]);

        $order = PosOrder::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // ── Restore old stock ──
            foreach ($order->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->quantity += $oldItem->quantity;
                    $product->stock_status = 'instock';
                    $product->save();
                }
            }

            // ── Delete old items ──
            $order->items()->delete();

            // ── Recalculate with new items ──
            $subtotal   = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);


                if ($request->status !== 'cancelled' && $product->quantity < $item['quantity']) {
                    throw new \Exception("'{$product->name}' only has {$product->quantity} left in stock.");
                }

                $price     = $item['price'];
                $itemTotal = $price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product'      => $product,
                    'product_name' => $product->name,
                    'price'        => $price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $itemTotal,
                ];


                if ($request->status !== 'cancelled') {
                    $product->quantity -= $item['quantity'];
                    if ($product->quantity <= 0) {
                        $product->quantity    = 0;
                        $product->stock_status = 'outofstock';
                    }
                    $product->save();
                }
            }

            $discount   = $request->discount ?? 0;
            $total      = max(0, $subtotal - $discount);
            $amountPaid = $request->amount_paid;
            $change     = $amountPaid - $total;
            $amountOwed = max(0, $total - $amountPaid);

            // ── Update order ──
            $order->update([
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $amountPaid,
                'change_amount'  => max(0, $change),
                'amount_owed'    => $amountOwed,
                'status'         => $request->status,
                'note'           => $request->note,
            ]);

            // ── Save new items ──
            foreach ($orderItems as $item) {
                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'product_id'   => $item['product']->id,
                    'product_name' => $item['product_name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();
            return redirect()->route('ceo.pos.history')->with('status', 'Order updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
