<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order_id)->first();
            if (!$transaction) {
                // Create a dummy object so view won't crash
                $transaction = (object)[
                    'mode' => 'N/A',
                    'status' => 'none'
                ];
            }
            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }

    public function order_cancel(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        //Restore stock
        foreach ($order->orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity += $item->quantity;
                $product->stock_status = 'instock';
                $product->save();
            }
        }

        $order->status = 'canceled';
        $order->canceled_date = now();
        $order->save();

        Notification::create([
            'type'     => 'order',
            'title'    => 'Order Cancelled by Customer',
            'message'  => 'Customer ' . Auth::user()->name . ' cancelled Order #' . $order->id,
            'order_id' => $order->id,
            'is_read'  => false,
        ]);

        return redirect()->back()->with('status', 'Order has been cancelled.');
    }

    public function address()
    {

        $addresses = Address::where('user_id', Auth::id())->paginate(10);
        return view('user.address', compact('addresses'));
    }

    public function address_edit($id)
    {
        $address = Address::findOrFail($id);
        return view('user.address-edit', compact('address'));
    }

    public function address_update(Request $request)
    {
        $user_id = Auth::user()->id;
        // $address = Address::findOrFail($id);


        $request->validate([
            'name' => 'required|max:100',
            'phone' => ['required', 'regex:/^[0-9]{9,10}$/'],
            'province' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = Address::find($request->id);

        if (!$address || $address->user_id !== $user_id) {
            return redirect()->back()->with('error', 'Address not found or unauthorized.');
        }

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'province' => $request->province,
            'address' => $request->address,
            'locality' => $request->locality,
            'landmark' => $request->landmark,
            'isdefault' => true,
        ]);

        return redirect()->route('user.address')->with('status', 'Address has been updated successfully!');
    }

    public function wishlist()
    {
        $items = Cart::instance('wishlist')->content();
        return view('user.wishlist', compact('items'));
    }

    public function account_detail()
    {
        $user = Auth::user();
        return view('user.account-details', compact('user'));
    }

    public function update_account(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'old_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update profile info
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        // Handle password change if provided
        if ($request->filled('old_password') && $request->filled('password')) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->password);
            } else {
                return back()->withErrors(['old_password' => 'Old password is incorrect.']);
            }
        }

        $user->save();

        return back()->with('success', 'Account updated successfully.');
    }
}
