<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosOrder;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class EmployeeController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get()->take(10);
        $dashboardDatas = DB::select("Select sum(total) As TotalAmount, 
                                    sum(if(status='ordered', total,0)) As TotalOrderedAmount,
                                    sum(if(status='delivered', total,0)) As TotalDeliveredAmount,
                                    sum(if(status='canceled', total,0)) As TotalCanceledAmount,
                                    Count(*) As Total,
                                    sum(if(status='ordered', 1,0)) As TotalOrdered,
                                    sum(if(status='delivered', 1,0)) As TotalDelivered,
                                    sum(if(status='canceled', 1,0)) As TotalCanceled
                                    From Orders
                                    ");

        $monthlyDatas = DB::select("SELECT M.id As MonthNo, M.name As MonthName,
                            IFNULL(D.TotalAmount, 0) As TotalAmount,
                            IFNULL(D.TotalOrderedAmount, 0) As TotalOrderedAmount,
                            IFNULL(D.TotalDeliveredAmount, 0) As TotalDeliveredAmount,
                            IFNULL(D.TotalCanceledAmount, 0) As TotalCanceledAmount FROM month_names M
                            LEFT JOIN (SELECT 
                            MONTH(created_at) As MonthNo,
                            SUM(total) As TotalAmount,
                            SUM(IF(status = 'ordered', total, 0)) As TotalOrderedAmount,
                            SUM(IF(status = 'delivered', total, 0)) As TotalDeliveredAmount,
                            SUM(IF(status = 'canceled', total, 0)) As TotalCanceledAmount
                            From Orders WHERE YEAR(created_at) = YEAR(NOW()) 
                            GROUP BY MONTH(created_at)
                            ) D On D.MonthNo = M.id
                            ORDER BY M.id ASC");

        //  POS Stats
        $posTodayRevenue = PosOrder::where('status', 'completed')
            ->whereDate('created_at', today())->sum('total');

        $posMonthRevenue = PosOrder::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $posTotalRevenue = PosOrder::where('status', 'completed')->sum('total');

        $posTotalOrders = PosOrder::where('status', 'completed')->count();

        // Monthly POS for chart
        $posMonthlyData = DB::select("SELECT M.id As MonthNo,
                        IFNULL(P.PosAmount, 0) As PosAmount FROM month_names M
                        LEFT JOIN (SELECT MONTH(created_at) As MonthNo,
                        SUM(total) As PosAmount
                        From pos_orders WHERE status='completed' AND YEAR(created_at) = YEAR(NOW())
                        GROUP BY MONTH(created_at)) P On P.MonthNo = M.id
                        ORDER BY M.id ASC");

        $PosAmountM = implode(',', collect($posMonthlyData)->pluck('PosAmount')->toArray());

        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $OrderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $CanceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());

        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');
        $lowStockCount = Product::where('quantity', '<=', 5)->count();

        $topOverall = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('product_id')
            ->whereHas('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(2)
            ->with('product')
            ->get();

        $topMonth = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereNotNull('product_id')
            ->whereHas('product')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(2)
            ->with('product')
            ->get();

        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(price * quantity) as revenue')
        )
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        $posTop = \App\Models\PosOrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as qty')
        )
            ->whereHas('posOrder', fn($q) => $q->where('status', 'completed'))
            ->whereNotNull('product_id')
            ->whereHas('product')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->take(8)
            ->with('product')
            ->get();

        $todayOrders   = Order::whereDate('created_at', today())->count();
        $todayRevenue  = Order::whereDate('created_at', today())->sum('total');
        $todayDelivered = Order::where('status', 'delivered')
            ->whereDate('delivered_date', today())->count();

        return view('employee.index', compact('orders', 'dashboardDatas', 'monthlyDatas', 'AmountM', 'OrderedAmountM', 'DeliveredAmountM', 'CanceledAmountM', 'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount', 'TotalCanceledAmount', 'lowStockCount', 'posTodayRevenue', 'posMonthRevenue', 'posTotalRevenue', 'posTotalOrders', 'PosAmountM', 'topOverall', 'topMonth', 'topProducts', 'posTop', 'todayOrders', 'todayRevenue', 'todayDelivered'));
    }

    public function products(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->orderBy('created_at', 'DESC');

        // Search by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter: Brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter: Stock Status
        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }

        $products   = $query->paginate(10)->withQueryString();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands     = Brand::select('id', 'name')->orderBy('name')->get();
        return view('employee.products', compact('products', 'categories', 'brands'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('employee.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required',
            'barcode' => 'nullable|string|max:255',
            'quantity' => 'nullable',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $product = new Product();
        $product->code = $request->code;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->filled('sale_price') ? $request->sale_price : null;
        $product->cost_price = $request->cost_price ?? null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->barcode = $request->barcode;
        $product->quantity = 0;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $product->image = $imageName;
        }

        // $gallery_arr = [];
        // if ($request->hasFile('images')) {
        //     $counter = 1;
        //     foreach ($request->file('images') as $file) {
        //         if ($file->isValid()) {
        //             $gextension = $file->getClientOriginalExtension();
        //             $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
        //             $this->GenerateProductThumbailsImage($file, $gfileName);
        //             $gallery_arr[] = $gfileName;
        //             $counter++;
        //         }
        //     }
        // }
        // $gallery_images = implode(',', $gallery_arr);

        // $product->images = $gallery_images;
        $product->save();

        // foreach ($request->input('attributes', []) as $name => $value) {
        //     if (!empty($value)) {
        //         $product->attributes()->create([
        //             'name' => $name,
        //             'value' => $value
        //         ]);
        //     }
        // }

        foreach ($request->input('attributes', []) as $name => $value) {
            if (!empty($value)) {
                $product->attributes()->create([
                    'name' => $name,
                    'value' => $value
                ]);
            }
        }

        // ADD this new code:
        if ($request->has('specs')) {
            foreach ($request->input('specs') as $group => $fields) {
                foreach ($fields as $name => $value) {
                    if (!empty($value)) {
                        $product->attributes()->create([
                            'name'  => $group . '.' . $name,
                            'value' => $value
                        ]);
                    }
                }
            }
        }

        if ($request->has('variants')) {
            foreach ($request->variants as $key => $variantData) {
                $variant = new ProductVariant();
                $variant->product_id = $product->id;
                $variant->color = $variantData['color'] ?? null;
                $variant->storage = $variantData['storage'] ?? null;
                $variant->price = $variantData['price'] ?? null;
                $variant->quantity = $variantData['quantity'] ?? null;
                $variant->barcode = $variantData['barcode'] ?? null;

                // ✅ Only process if image is uploaded
                if ($request->hasFile("variants.$key.image")) {
                    $image = $request->file("variants.$key.image");
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $this->GenerateVariantImage($image, $imageName);
                    $variant->image = $imageName;
                } else {
                    continue; // ✅ Skip this variant entirely if no image
                }

                $images = [];
                if ($request->hasFile("variants.$key.images")) {
                    foreach ($request->file("variants.$key.images") as $file) {
                        if ($file->isValid()) {
                            $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                            $this->GenerateVariantImage($file, $imageName);
                            $images[] = $imageName;
                        }
                    }
                }

                $variant->images = !empty($images) ? $images : null;
                $variant->save();
            }
        }

        $totalQuantity = collect($request->variants)->sum('quantity');
        $product->quantity = $totalQuantity;
        $product->stock_status = $totalQuantity > 0 ? 'instock' : 'outofstock';
        $product->save();
        return redirect()->route('employee.products')->with('status', 'Product has been added successfully!');
    }

    public function GenerateProductThumbailsImage($image, $imageName)
    {
        $destinationPathThumbail = public_path('uploads/products/Thumbnails');
        $destinationPath = public_path('uploads/products');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if (!file_exists($destinationPathThumbail)) {
            mkdir($destinationPathThumbail, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(800, 800, 'center')
            ->save($destinationPath . '/' . $imageName);

        $img = Image::read($image->getPathname());
        $img->cover(800, 800, 'center')
            ->save($destinationPathThumbail . '/' . $imageName);
    }

    public function GenerateVariantImage($image, $imageName)
    {
        $destinationPathThumbail = public_path('uploads/variants/Thumbnails');
        $destinationPath = public_path('uploads/variants');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if (!file_exists($destinationPathThumbail)) {
            mkdir($destinationPathThumbail, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(800, 800, 'center')
            ->save($destinationPath . '/' . $imageName);

        $img = Image::read($image->getPathname());
        $img->scaleDown(104, 104)
            ->save($destinationPathThumbail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('employee.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required',
            'barcode' => 'nullable|string|max:255',
            'quantity' => 'nullable',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = Product::find($request->id);
        $product->code = $request->code;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->filled('sale_price') ? $request->sale_price : null;
        $product->cost_price = $request->cost_price ?? null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->barcode = $request->barcode;
        $product->quantity = 0;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }

            if (File::exists(public_path('uploads/products/Thumbnails') . '/' . $product->image)) {
                File::delete(public_path('uploads/products/Thumbnails') . '/' . $product->image);
            }

            $image = $request->file('image');
            $imageName = $current_timestamp . '-main.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $product->image = $imageName;
        }

        // $gallery_arr = [];
        // if ($request->hasFile('images')) {
        //     $old_gallery_images = explode(',', $product->images);
        //     foreach ($old_gallery_images as $old_image_name) {
        //         if (File::exists(public_path('uploads/products') . '/' . $old_image_name)) {
        //             File::delete(public_path('uploads/products') . '/' . $old_image_name);
        //         }
        //         if (File::exists(public_path('uploads/products/Thumbnails') . '/' . $old_image_name)) {
        //             File::delete(public_path('uploads/products/Thumbnails') . '/' . $old_image_name);
        //         }
        //     }
        //     $counter = 1;
        //     foreach ($request->file('images') as $file) {
        //         if ($file->isValid()) {
        //             $gextension = $file->getClientOriginalExtension();
        //             $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
        //             $this->GenerateProductThumbailsImage($file, $gfileName);
        //             $gallery_arr[] = $gfileName;
        //             $counter++;
        //         }
        //     }
        // }
        // $gallery_images = implode(',', $gallery_arr);
        // $product->images = $gallery_images;
        $product->save();


        // if ($request->has('product_attributes')) {

        //     foreach ($request->product_attributes as $key => $value) {

        //         if ($value !== null && $value !== '') {

        //             ProductAttribute::create([
        //                 'product_id' => $product->id,
        //                 'name' => $key,
        //                 'value' => $value
        //             ]);
        //         }
        //     }
        // }

        if ($request->has('product_attributes')) {
            foreach ($request->product_attributes as $key => $value) {
                if ($value !== null && $value !== '') {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'name' => $key,
                        'value' => $value
                    ]);
                }
            }
        }
        // ADD this new code:
        $product->attributes()->delete(); // clear old ones first

        if ($request->has('specs')) {
            foreach ($request->input('specs') as $group => $fields) {
                foreach ($fields as $name => $value) {
                    if (!empty($value)) {
                        $product->attributes()->create([
                            'name'  => $group . '.' . $name,
                            'value' => $value
                        ]);
                    }
                }
            }
        }



        if ($request->has('variants')) {
            foreach ($request->variants as $key => $variantData) {

                if (!empty($variantData['id'])) {
                    $variant = ProductVariant::find($variantData['id']);
                    if (!$variant) continue;

                    $variant->color    = $variantData['color'] ?? $variant->color;
                    $variant->storage  = $variantData['storage'] ?? $variant->storage;
                    $variant->price    = $variantData['price'] ?? $variant->price;
                    $variant->quantity = $variantData['quantity'] ?? $variant->quantity;
                    $variant->barcode  = $variantData['barcode'] ?? $variant->barcode;

                    // Only update image if new one uploaded
                    if ($request->hasFile("variants.$key.image")) {
                        $file = $request->file("variants.$key.image");
                        $imageName = time() . '_main_' . uniqid() . '.' . $file->extension();
                        $this->GenerateVariantImage($file, $imageName);
                        $variant->image = $imageName;
                    }

                    // Only update gallery if new ones uploaded
                    if ($request->hasFile("variants.$key.images")) {
                        $images = [];
                        foreach ($request->file("variants.$key.images") as $file) {
                            if ($file->isValid()) {
                                $imgName = time() . '_' . uniqid() . '.' . $file->extension();
                                $this->GenerateVariantImage($file, $imgName);
                                $images[] = $imgName;
                            }
                        }
                        $variant->images = $images;
                    }

                    $variant->save();
                } else {
                    //  New variant row — only save if image is provided
                    if (!$request->hasFile("variants.$key.image")) {
                        continue;
                    }

                    if (empty($variantData['color']) && empty($variantData['storage'])) {
                        continue;
                    }

                    $file = $request->file("variants.$key.image");
                    $imageName = time() . '_main_' . uniqid() . '.' . $file->extension();
                    $this->GenerateVariantImage($file, $imageName);

                    $images = [];
                    if ($request->hasFile("variants.$key.images")) {
                        foreach ($request->file("variants.$key.images") as $file) {
                            if ($file->isValid()) {
                                $imgName = time() . '_' . uniqid() . '.' . $file->extension();
                                $this->GenerateVariantImage($file, $imgName);
                                $images[] = $imgName;
                            }
                        }
                    }

                    $product->variants()->create([
                        'color'    => $variantData['color'] ?? null,
                        'storage'  => $variantData['storage'] ?? null,
                        'price'    => $variantData['price'] ?? 0,
                        'quantity' => $variantData['quantity'] ?? 0,
                        'barcode'  => $variantData['barcode'] ?? null,
                        'image'    => $imageName,
                        'images'   => !empty($images) ? $images : null,
                    ]);
                }
            }
        }


        $totalQuantity = $product->variants()->sum('quantity');
        $product->quantity = $totalQuantity;
        $product->stock_status = $totalQuantity > 0 ? 'instock' : 'outofstock';
        $product->save();

        return redirect()->route('employee.products')->with('status', 'Product has been updated successfully!');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }

        if (File::exists(public_path('uploads/products/Thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/Thumbnails') . '/' . $product->image);
        }

        foreach (explode(',', $product->image) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }

            if (File::exists(public_path('uploads/products/Thumbnails') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/Thumbnails') . '/' . $ofile);
            }
        }

        // delete variants
        $product->variants()->delete();

        // delete attribute
        $product->attributes()->delete();


        $product->delete();
        return redirect()->route('employee.products')->with('status', 'Product has been deleted successfully!');
    }

    public function product_search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Product::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->take(8)
            ->get(['id', 'name', 'image']);

        return response()->json($results);
    }

    public function product_show($id)
    {
        $product = Product::with(['attributes', 'variants'])->findOrFail($id);

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('employee.product-show', compact('product', 'categories', 'brands'));
    }

    public function orders(Request $request)
    {
        $query = Order::query();

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->name}%")
                    ->orWhere('phone', 'like', "%{$request->name}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->orderBy('created_at', 'DESC')
            ->paginate(12)
            ->withQueryString();

        return view('employee.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('employee.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function search_order(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $orders = Order::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($orders);
    }

    public function update_order_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        //Restore stock if employee cancels the order
        if ($request->order_status == 'canceled' && $order->status != 'canceled') {
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->stock_status = 'instock';
                    $product->save();
                }
            }
        }

        $order->status = $request->order_status;

        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        } elseif ($request->order_status == 'shipped') {
            $order->shipped_date = Carbon::now();
        } elseif ($request->order_status == 'out_for_delivery') {
            $order->out_for_delivery_date = Carbon::now();
        }

        $order->save();

        $transaction = Transaction::where('order_id', $request->order_id)->first();
        if ($transaction && $request->order_status == 'delivered') {
            $transaction->status = 'approved';
            $transaction->save();
        }

        return back()->with("status", "Status changed successfully!");
    }

    //Invoice
    public function invoice($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        $transaction = Transaction::where('order_id', $id)->first();

        return view('employee.invoice-order', compact('order', 'transaction'));
    }

    public function contacts(Request $request)
    {
        $query = $request->query('name');

        $contacts = Contact::query();

        if ($query) {
            $contacts->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $contacts = $contacts->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['name' => $query]);
        return view('employee.contacts', compact('contacts'));
    }

    public function contact_show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('employee.contacts.show', compact('contact'));
    }


    public function search_contact(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $contacts = Contact::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($contacts);
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

        $results = Product::where('name', 'LIKE', "%{$query}%")
            ->take(8)
            ->get();

        return response()->json($results);
    }


    //profile employee
    public function profile()
    {
        $user = auth()->user();
        return view('employee.account_profile', compact('user'));
    }



    public function updateProfile(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'mobile' => 'nullable|string|max:20',
            'new_password' => 'nullable|confirmed|min:6',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

            // Store in "public/profile_photos" folder
            $photo->storeAs('profile_photos', $filename, 'public');

            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists('profile_photos/' . $user->profile_photo)) {
                Storage::disk('public')->delete('profile_photos/' . $user->profile_photo);
            }

            $user->profile_photo = $filename; // only store filename
        }
        // 3. Update User Data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        // 4. Update Password (if provided)
        if ($request->filled('old_password') && $request->filled('new_password')) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return back()->with('error', 'Old password does not match.');
            }
        }

        $user->save();

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully!');
    }



    public function notification()
    {
        $notifications = Notification::where('is_read', 0)
            ->latest()
            ->get();

        // dd(get_class($notifications));


        $unreadCount = Notification::where('is_read', 0)->count();
        return view('employee.notifications', compact('notifications', 'unreadCount'));
    }

    public function notification_count()
    {
        $count = Notification::where('is_read', 0)->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function notification_show($id)
    {
        // $notification = Notification::findOrFail($id);
        // $notification->update(['is_read' => true]);
        $notification = Notification::with('order.orderItems.product')
            ->findOrFail($id);
        //mark as read automatically
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        // safety check (in case no order linked)
        if (!$notification->order) {
            return redirect()->back()->with('error', 'Order not found!');
        }
        // return redirect()->back()->with('success', 'Notification marked as read.');
        return view('employee.notification-order-detail', [
            'order'        => $notification->order,
            'notification' => $notification,
        ]);
    }


    //taskboard
    public function tasks()
    {
        $tasks = Task::where('assigned_to', Auth::id())->get();

        return view('employee.tasks', compact('tasks'));
    }

    // Allow employee to update ONLY status
    public function task_update(Request $request, Task $task)
    {
        // Ensure employee can only edit their own task
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Task status updated!');
    }

    //stock-alert
    public function stock_alert()
    {
        // Products with low quantity AND no variants (simple products)
        $lowStockProducts = Product::where('quantity', '<=', 5)
            ->whereDoesntHave('variants')
            ->orderBy('quantity', 'ASC')
            ->with(['category', 'brand'])
            ->get();

        // Variants with low quantity, grouped under their parent product
        $lowStockVariantProducts = Product::whereHas('variants', function ($q) {
            $q->where('quantity', '<=', 5);
        })
            ->with(['category', 'brand', 'variants' => function ($q) {
                $q->where('quantity', '<=', 5)->orderBy('quantity', 'ASC');
            }])
            ->get();

        return view('employee.stock-alert', compact('lowStockProducts', 'lowStockVariantProducts'));
    }
}
