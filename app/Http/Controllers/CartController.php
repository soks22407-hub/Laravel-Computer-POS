<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        $shipping = 3;
        return view('cart', compact('items', 'shipping'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add(
        $request->id,
        $request->name,
        $request->quantity,
        $request->price,
        ['variant_id' => $request->variant_id ?? null]  
    )->associate('App\Models\Product');

        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => ['required', 'regex:/^[0-9]{9,10}$/'],
                'province' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required',

            ]);

            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->province = $request->province;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = 'Cambodia';
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountforCheckout();
        $checkout = Session::get('checkout');

        if (!$checkout) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }


        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->total = Session::get('checkout')['total'];
        $order->shipping = Session::get('checkout')['shipping'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->province = $address->province;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $product = Product::lockForUpdate()->find($item->id);
            $variantId = $item->options->variant_id ?? null;

            if ($variantId) {
                // Has variant — decrement variant quantity
                $variant = ProductVariant::lockForUpdate()->find($variantId);

                if ($variant) {
                    if ($variant->quantity < $item->qty) {
                        return redirect()->route('cart.checkout')
                            ->with('error', "'{$product->name}' variant មានតែ {$variant->quantity} នៅសល់ក្នុង Stock។");
                    }

                    $variant->quantity -= $item->qty;

                    if ($variant->quantity <= 0) {
                        $variant->quantity = 0;
                    }

                    $variant->save();
                }
            } else {
                // No variant — decrement main product quantity
                if ($product) {
                    if ($product->quantity < $item->qty) {
                        return redirect()->route('cart.checkout')
                            ->with('error', "'{$product->name}' មានតែ {$product->quantity} នៅសល់ក្នុង Stock។");
                    }

                    $product->quantity -= $item->qty;

                    if ($product->quantity <= 0) {
                        $product->quantity = 0;
                        $product->stock_status = 'outofstock';
                    }

                    $product->save();
                }
            }

            OrderItem::create([
                'product_id' => $item->id,
                'order_id'   => $order->id,
                'price'      => $item->price,
                'quantity'   => $item->qty,
                'variant_id' => $variantId,
            ]);
        }

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('order_id');

        Notification::create([
            'type' => 'order',
            'title' => 'New Order Placed',
            'message' => 'Customer ' . Auth::user()->name . ' placed Order #' . $order->id,
            'order_id' => $order->id,
            'is_read' => false,
        ]);

        // foreach(Cart::instance('cart')->content() as $item)
        // {
        //     $orderItem = new OrderItem();
        //     $orderItem->product_id = $item->id;
        //     $orderItem->order_id = $order->id;
        //     $orderItem->price = $item->price;
        //     $orderItem->quantity = $item->qty;
        //     $orderItem->save();
        // }

        if ($request->mode == "Cart") {
            return redirect()->route('payment.card.demo.form', $order->id);
        } else if ($request->mode == "ABA") {
            //
        } else if ($request->mode == "cod") {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
        }


        // Cart::instance('cart')->destroy();
        // Session::forget('checkout');
        Session::put('order_id', $order->id);
        return redirect()->route('cart.order.confirmation');
    }

    public function setAmountforCheckout()
    {
        if (Cart::instance('cart')->count() == 0) {
            Session::forget('checkout');
            return;
        }

        $subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));


        $shipping = 3;
        $user = Auth::user();

        if ($user) {
            $address = Address::where('user_id', $user->id)
                ->where('isdefault', true)
                ->first();

            if ($address) {
                $zone = ShippingZone::where('province', $address->province)
                    ->where('is_active', true)
                    ->first();

                if ($zone) {

                    if ($zone->free_shipping_above && $subtotal >= $zone->free_shipping_above) {
                        $shipping = 0; // free shipping!
                    } else {
                        $shipping = $zone->fee;
                    }
                }
            }
        }

        $total = $subtotal + $shipping;

        Session::put('checkout', [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shipping' => number_format($shipping, 2, '.', ''),
            'total'    => number_format($total, 2, '.', ''),
        ]);
    }

    //showcard debit
    public function showCardForm(Order $order)
    {
        return view('payment.card-demo', compact('order'));
    }

    //process debit
    public function processCardDemo(Request $request, Order $order)
    {
        // Find or create transaction
        $transaction = Transaction::firstOrCreate(
            ['order_id' => $order->id, 'user_id' => $order->user_id],
            ['mode' => 'Cart', 'status' => 'pending']
        );

        // Simulate payment success
        $transaction->status = 'approved';
        $transaction->save();

        return redirect()->route('cart.order.confirmation');
    }
    //success
    public function paymentSuccess(Order $order)
    {
        return redirect()->route('cart.order.confirmation', $order->id);
    }
    //failed
    public function paymentFailed()
    {
        return view('payment.failed');
    }


    public function order_confirmation()
    {
        $order = Order::where('user_id', Auth::id())
            ->latest()
            ->first();

        if (!$order) {
            return redirect()->route('cart.index');
        }

        return view('order_confirmation', compact('order'));
        // return redirect()->route('cart.index');
    }
    //invoice
    public function print_invoice($id)
    {
        $order = Order::with('orderItems.product', 'transaction')->findOrFail($id);

        $pdf = Pdf::loadView('invoice', compact('order'));

        // Open PDF in browser with Print/Save options
        return $pdf->stream('Invoice_' . $order->id . '.pdf');
    }
}
