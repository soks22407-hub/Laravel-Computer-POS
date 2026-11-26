<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\FlashSale;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\ShippingZone;
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

class CEOController extends Controller
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
        $lowStockCount = Product::where('quantity', '<=', 5)
            ->whereDoesntHave('variants')
            ->count()
            +
            ProductVariant::where('quantity', '<=', 5)->count();

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

        return view('ceo.index', compact('orders', 'dashboardDatas', 'monthlyDatas', 'AmountM', 'OrderedAmountM', 'DeliveredAmountM', 'CanceledAmountM', 'TotalAmount', 'TotalOrderedAmount', 'TotalDeliveredAmount', 'TotalCanceledAmount', 'lowStockCount', 'posTodayRevenue', 'posMonthRevenue', 'posTotalRevenue', 'posTotalOrders', 'PosAmountM', 'topOverall', 'topMonth', 'topProducts', 'posTop', 'todayOrders', 'todayRevenue', 'todayDelivered'));
    }

    public function brands(Request $request)
    {
        $query = $request->query('name');

        $brands = Brand::query();

        $brands = Brand::withCount('products') // <-- important
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->appends(['name' => $query]);
        return view('ceo.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('ceo.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        // if (Brand::where('slug', $brand->slug)->exists()) {
        //     return redirect()->back()->withErrors(['name' => 'This brand already exists!']);
        // }
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('ceo.brands')->with('status', 'Brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('ceo.brand-edit', compact('brand'));
    }


    public function brand_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $id,
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $brand = Brand::findOrFail($id);
        // $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('ceo.brands')->with('status', 'Brand has been updated successfully!');
    }


    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(600, 600, 'center')
            ->save($destinationPath . '/' . $imageName);
    }


    public function brand_delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('ceo.brands')->with('status', 'Brand has been deleted successfully!');
    }

    public function byBrand($id)
    {
        $brand = Brand::findOrFail($id);

        $products = Product::where('brand_id', $id)
            ->with('brand', 'category') // eager load relations
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('ceo.byBrand', compact('brand', 'products'));
    }

    public function brand_search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Brand::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->take(8)
            ->get(['id', 'name', 'image', 'slug']);

        return response()->json($results);
    }

    public function categories(Request $request)
    {
        $query = $request->query('name');

        $categories = Category::query();

        $categories = Category::withCount('products') // add product count
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->appends(['name' => $query]);
        return view('ceo.categories', compact('categories'));
    }

    public function add_categories()
    {
        return view('ceo.categories-add');
    }

    public function categories_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $categories = new Category();
        $categories->name = $request->name;
        $categories->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        // $this->GenerateCategoryThumbailsImage($image,$file_name);
        $image->move(public_path('uploads/categories'), $file_name);
        $categories->image = $file_name;
        $categories->save();
        return redirect()->route('ceo.categories')->with('status', 'Category has been added successfully!');
    }

    public function categories_edit($id)
    {
        $categories = Category::find($id);
        return view('ceo.categories-edit', compact('categories'));
    }


    public function categories_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $id,
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $categories = Category::findOrFail($id);
        $categories->name = $request->name;
        $categories->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $categories->image)) {
                File::delete(public_path('uploads/categories') . '/' . $categories->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $categories->image = $file_name;
        }
        $categories->save();
        return redirect()->route('ceo.categories')->with('status', 'Category has been updated successfully!');
    }


    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(124, 124, 'center')
            ->save($destinationPath . '/' . $imageName);
    }

    public function categories_delete($id)
    {
        $categories = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $categories->image)) {
            File::delete(public_path('uploads/categories') . '/' . $categories->image);
        }
        $categories->delete();
        return redirect()->route('ceo.categories')->with('status', 'Category has been deleted successfully!');
    }

    public function byCategory($id)
    {
        $category = Category::findOrFail($id);

        $products = Product::where('category_id', $id)
            ->with('brand', 'category') // eager load relations
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('ceo.byCategory', compact('category', 'products'));
    }

    public function cate_search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Category::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->take(8)
            ->get(['id', 'name', 'image', 'slug']);

        return response()->json($results);
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
        return view('ceo.products', compact('products', 'categories', 'brands'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('ceo.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric',
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
        $product->sale_price = $request->sale_price ?? null;
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
        return redirect()->route('ceo.products')->with('status', 'Product has been added successfully!');
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
        return view('ceo.product-edit', compact('product', 'categories', 'brands'));
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
            'sale_price' => 'nullable|numeric',
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
        $product->sale_price = $request->sale_price ?? null;
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

        return redirect()->route('ceo.products')->with('status', 'Product has been updated successfully!');
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
        return redirect()->route('ceo.products')->with('status', 'Product has been deleted successfully!');
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

        return view('ceo.product-show', compact('product', 'categories', 'brands'));
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

        return view('ceo.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('ceo.order-details', compact('order', 'orderItems', 'transaction'));
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
        $request->validate([
            'order_id'     => 'required|exists:orders,id',
            'order_status' => 'required|in:ordered,shipped,out_for_delivery,delivered,canceled',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Prevent updating a canceled/delivered order
        if (in_array($order->status, ['canceled', 'delivered'])) {
            return back()->with('error', 'Cannot update a ' . $order->status . ' order.');
        }

        // Restore stock if ceo cancels the order
        if ($request->order_status === 'canceled' && $order->status !== 'canceled') {
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->quantity += $item->quantity;
                        $variant->save();
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->quantity += $item->quantity;
                        $product->stock_status = 'instock';
                        $product->save();
                    }
                }

                // Recalculate main product quantity from variants
                $product = Product::find($item->product_id);
                if ($product && $product->variants()->count() > 0) {
                    $product->quantity = $product->variants()->sum('quantity');
                    $product->stock_status = $product->quantity > 0 ? 'instock' : 'outofstock';
                    $product->save();
                }
            }
        }

        // Set status timestamp dynamically
        $timestampMap = [
            'shipped'          => 'shipped_date',
            'out_for_delivery' => 'out_for_delivery_date',
            'delivered'        => 'delivered_date',
            'canceled'         => 'canceled_date',
        ];

        $order->status = $request->order_status;
        $order->staff_id = Auth::id();

        if (isset($timestampMap[$request->order_status])) {
            $order->{$timestampMap[$request->order_status]} = Carbon::now();
        }

        $order->save();

        // Approve transaction on delivery
        if ($request->order_status === 'delivered') {
            Transaction::where('order_id', $order->id)
                ->first()
                ?->update(['status' => 'approved']);
        }

        return back()->with('status', 'Status changed successfully!');
    }

    //Invoice
    public function invoice($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        $transaction = Transaction::where('order_id', $id)->first();

        return view('ceo.invoice-order', compact('order', 'transaction'));
    }


    public function slides(Request $request)
    {

        $query = $request->query('name');

        $slides = Slide::query();

        if ($query) {
            $slides->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $slides = $slides->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['name' => $query]);
        return view('ceo.slides', compact('slides'));
    }

    public function slide_add()
    {
        return view('ceo.slide-add');
    }

    public function slide_store(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        // $this->GenerateCategoryThumbailsImage($image,$file_name);
        $image->move(public_path('uploads/slides'), $file_name);
        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('ceo.slides')->with('status', 'Slide has been added successfully!');
    }

    public function GenerateSlideThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(950, 800, 'center')
            ->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        $slide = Slide::find($id);
        return view('ceo.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {

        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateSlideThumbailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('ceo.slides')->with('status', 'Slide has been updated successfully!');
    }

    public function slide_delete($id)
    {
        $slide = Slide::find($id);
        if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
            File::delete(public_path('uploads/slides') . '/' . $slide->image);
        }
        $slide->delete();
        return redirect()->route('ceo.slides')->with('status', 'Slide has been deleted successfully!');
    }

    public function slide_search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Slide::query()
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('tagline', 'LIKE', "%{$query}%")
            ->orWhere('subtitle', 'LIKE', "%{$query}%")
            ->take(8)
            ->get(['id', 'title', 'image', 'link']);

        return response()->json($results);
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
        return view('ceo.contacts', compact('contacts'));
    }

    public function contact_show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('ceo.contacts.show', compact('contact'));
    }


    public function contact_delete($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('ceo.contacts')->with("status", "Contact Deleted Successfully!");
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

    public function user(Request $request)
    {
        $search = $request->query('name');

        $users = User::withCount('orders');

        if ($search) {
            $users->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('utype')) {
            $users->where('utype', $request->utype);
        }

        if ($request->filled('orders')) {
            switch ($request->orders) {
                case '0':
                    $users->has('orders', '=', 0);
                    break;
                case '1-5':
                    $users->has('orders', '>=', 1)->has('orders', '<=', 5);
                    break;
                case '6-10':
                    $users->has('orders', '>=', 6)->has('orders', '<=', 10);
                    break;
                case '10+':
                    $users->has('orders', '>', 10);
                    break;
            }
        }

        if ($request->filled('from')) {
            $users->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $users->whereDate('created_at', '<=', $request->to);
        }

        $users = $users->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('ceo.account-user', compact('users'));
    }

    public function user_create()
    {
        return view('ceo.account_add');
    }

    public function user_store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'mobile'   => 'required|string|max:20|unique:users,mobile',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'utype'    => 'required|in:USR,ADM,EMP,CEO',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->utype = $request->utype;
        $user->save();

        // New employees start with zero rows in the permissions table, which means
        // can_do() denies everything by default. Copy in whatever's currently applied
        // to the rest of the employees so a new hire isn't locked out until ceo
        // happens to re-save the Permissions page.
        if ($user->utype === 'EMP') {
            $templateUserId = User::where('utype', 'EMP')
                ->where('id', '!=', $user->id)
                ->orderBy('id')
                ->value('id');

            if ($templateUserId) {
                $templatePermissions = DB::table('permissions')->where('user_id', $templateUserId)->get();

                foreach ($templatePermissions as $perm) {
                    DB::table('permissions')->updateOrInsert(
                        ['user_id' => $user->id, 'module' => $perm->module],
                        [
                            'can_view'   => $perm->can_view,
                            'can_add'    => $perm->can_add,
                            'can_edit'   => $perm->can_edit,
                            'can_delete' => $perm->can_delete,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }

        return redirect()->route('ceo.user.account')->with('success', 'User created successfully.');
    }


    public function user_edit($id)
    {
        $user = User::findOrFail($id);
        return view('ceo.account_edit', compact('user'));
    }

    public function user_update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Only allow ceo or account owner
        if (auth()->user()->utype !== 'CEO' && auth()->id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'new_password' => 'nullable|confirmed|min:6',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        // Only ceo can change role
        if (auth()->user()->utype === 'CEO' && $request->has('utype')) {
            $user->utype = $request->utype;
        }


        // If password change
        if ($request->filled('old_password') && $request->filled('new_password')) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return back()->with('error', 'Old password does not match.');
            }
        }

        $user->save();


        return redirect()->route('ceo.user.account')->with('success', 'Account updated successfully.');
    }

    public function user_delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('ceo.user.account')->with("status", "Account Deleted Successfully!");
    }

    public function search_user(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    //profile ceo
    public function profile()
    {
        $user = auth()->user();
        return view('ceo.account_profile', compact('user'));
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
            $filename = time() . '_' . uniqid() . '.' . pathinfo($photo->getClientOriginalName(), PATHINFO_EXTENSION);

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

        return redirect()->route('ceo.profile')->with('success', 'Profile updated successfully!');
    }



    public function notification()
    {
        $notifications = Notification::where('is_read', 0)
            ->latest()
            ->get();

        // dd(get_class($notifications));


        $unreadCount = Notification::where('is_read', 0)->count();
        return view('ceo.notifications', compact('notifications', 'unreadCount'));
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
        return view('ceo.notification-order-detail', [
            'order'        => $notification->order,
            'notification' => $notification,
        ]);
    }

    public function notification_delete($id)
    {
        Notification::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }

    //setting
    public function settings(Request $request)
    {
        $query = $request->query('site_name');

        $settings = Setting::query();

        if ($query) {
            $settings->where(function ($q) use ($query) {
                $q->where('site_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $settings = $settings->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['site_name' => $query]);
        return view('ceo.settings.setting', compact('settings'));
    }

    public function setting_add()
    {
        return view('ceo.settings.setting-add');
    }

    public function storeSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
        ]);
        $settings = new Setting();
        $settings->site_name = $request->site_name;
        $settings->email = $request->email;
        $settings->phone = $request->phone;
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $image->move(public_path('uploads/setting'), $file_name);
            $settings->logo = $file_name;
        }
        $settings->save();

        return redirect()->route('ceo.settings')->with('status', 'Slide has been added successfully!');
    }


    public function setting_edit($id)
    {
        $settings = Setting::find($id);
        return view('ceo.settings.setting-edit', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
        ]);

        $settings = Setting::findOrFail($request->id);
        $settings->site_name = $request->site_name;
        $settings->email = $request->email;
        $settings->phone = $request->phone;
        if ($request->hasFile('logo')) {
            if (File::exists(public_path('uploads/setting') . '/' . $settings->logo)) {
                File::delete(public_path('uploads/setting') . '/' . $settings->logo);
            }
            $image = $request->file('logo');
            $file_extention = $request->file('logo')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $image->move(public_path('uploads/setting'), $file_name);
            $settings->logo = $file_name;
        }
        $settings->save();

        return redirect()->route('ceo.settings')->with('success', 'Settings updated successfully!');
    }

    public function setting_delete($id)
    {
        $settings = Setting::find($id);
        if (File::exists(public_path('uploads/setting') . '/' . $settings->image)) {
            File::delete(public_path('uploads/setting') . '/' . $settings->image);
        }
        $settings->delete();
        return redirect()->route('ceo.settings')->with('status', 'Setting has been deleted successfully!');
    }

    public function search_setting(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $settings = Setting::query()
            ->where('site_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'site_name', 'logo', 'email']);

        return response()->json($settings);
    }

    //taskboard
    public function task()
    {
        $tasks = Task::with('user')->get();
        $users = User::all();
        return view('ceo.tasks', compact('tasks', 'users'));
    }

    public function task_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,in_progress,done',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'todo',
            'assigned_to' => $request->assigned_to,
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    public function task_update(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Task status updated!');
    }

    public function task_delete(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted!');
    }

    public function task_show(Task $task)
    {
        $task->load('user'); // assigned user
        return view('ceo.task-show', compact('task'));
    }

    public function support()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('ceo.support', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('ceo.support-show', compact('user'));
    }


    public function coupons(Request $request)
    {
        $query = $request->query('code');
        $coupons = Coupon::query();
        if ($query) {
            $coupons->where('code', 'like', "%{$query}%");
        }

        $coupons = $coupons
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->appends(['code' => $query]);
        // $coupons = Coupon::orderBy('expiry_date','DESC')->paginate(12);
        return view('ceo.coupons', compact('coupons'));
    }

    public function coupon_add()
    {
        return view('ceo.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('ceo.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function coupon_edit($id)
    {
        $coupon = Coupon::find($id);
        return view('ceo.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('ceo.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('ceo.coupons')->with('status', 'Coupon has been deleted successfully!');
    }

    public function search_coupon(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $coupon = Coupon::query()
            ->where('code', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'code']);

        return response()->json($coupon);
    }

    //banner slide
    public function banners(Request $request)
    {

        $query = $request->query('name');

        $banners = Banner::query();

        if ($query) {
            $banners->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('subtitle', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        $banners = $banners->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['name' => $query]);
        return view('ceo.banners', compact('banners'));
    }

    public function banner_add()
    {
        return view('ceo.banner-add');
    }

    public function banner_store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'subtitle' => 'nullable',
            'description' => 'nullable',
            'type' => 'required|in:promotion,discount,new_product',
            'status' => 'required|boolean',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->description = $request->description;
        $banner->type = $request->type;
        $banner->status = $request->status;
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $image->move(public_path('uploads/banners'), $file_name);
        $banner->image = $file_name;
        $banner->save();
        return redirect()->route('ceo.banners')->with('status', 'Banner has been added successfully!');
    }

    public function GenerateBannerThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/banners');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->cover(950, 800, 'center')
            ->save($destinationPath . '/' . $imageName);
    }

    public function banner_edit($id)
    {
        $banner = Banner::find($id);
        return view('ceo.banner-edit', compact('banner'));
    }

    public function banner_update(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'subtitle' => 'nullable',
            'description' => 'nullable',
            'type' => 'required|in:promotion,discount,new_product',
            'status' => 'required|boolean',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $banner = Banner::find($request->id);
        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->description = $request->description;
        $banner->type = $request->type;
        $banner->status = $request->status;
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/banners') . '/' . $banner->image)) {
                File::delete(public_path('uploads/banners') . '/' . $banner->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBannerThumbailsImage($image, $file_name);
            $banner->image = $file_name;
        }
        $banner->save();
        return redirect()->route('ceo.banners')->with('status', 'Banner has been updated successfully!');
    }

    public function banner_delete($id)
    {
        $banner = Banner::find($id);
        if (File::exists(public_path('uploads/banners') . '/' . $banner->image)) {
            File::delete(public_path('uploads/banners') . '/' . $banner->image);
        }
        $banner->delete();
        return redirect()->route('ceo.banners')->with('status', 'Banner has been deleted successfully!');
    }

    public function banner_search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Banner::query()
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('subtitle', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->take(8)
            ->get(['id', 'title', 'image', 'type']);

        return response()->json($results);
    }


    //discount form
    public function discounts(Request $request)
    {
        $query = $request->query('name');
        $discounts = Discount::query();
        if ($query) {
            $discounts->where('name', 'like', "%{$query}%");
        }

        $discounts = $discounts
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->appends(['name' => $query]);
        return view('ceo.discounts', compact('discounts'));
    }

    public function discount_add()
    {
        return view('ceo.discount-add');
    }

    public function discount_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean'
        ]);

        $discount = new Discount();
        $discount->name = $request->name;
        $discount->type = $request->type;
        $discount->value = $request->value;
        $discount->min_amount = $request->min_amount;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->status = $request->status;
        $discount->save();
        return redirect()->route('ceo.discounts')->with('status', 'Discount has been added successfully!');
    }

    public function discount_edit($id)
    {
        $discount = Discount::find($id);
        return view('ceo.discount-edit', compact('discount'));
    }

    public function discount_update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|boolean'
        ]);

        $discount = Discount::find($request->id);
        $discount->name = $request->name;
        $discount->type = $request->type;
        $discount->value = $request->value;
        $discount->min_amount = $request->min_amount;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->status = $request->status;
        $discount->save();
        return redirect()->route('ceo.discounts')->with('status', 'Discount has been updated successfully!');
    }

    public function discount_delete($id)
    {
        $discount = Discount::find($id);
        $discount->delete();
        return redirect()->route('ceo.discounts')->with('status', 'Discount has been deleted successfully!');
    }

    public function discount_search(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $discount = Discount::query()
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($discount);
    }

    // Reviews
    public function reviews(Request $request)
    {
        $query = $request->query('search');

        $reviews = \App\Models\Review::with(['user', 'product'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$query}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$query}%"));
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $query]);

        return view('ceo.reviews', compact('reviews'));
    }

    public function review_delete($id)
    {
        $review = \App\Models\Review::findOrFail($id);

        if ($review->photo && file_exists(public_path('uploads/reviews/' . $review->photo))) {
            unlink(public_path('uploads/reviews/' . $review->photo));
        }

        $review->delete();
        return redirect()->route('ceo.reviews')->with('status', 'Review deleted successfully!');
    }

    public function search_review(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $reviews = \App\Models\Review::with(['user', 'product'])
            ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'user_name'    => $r->user->name ?? 'N/A',
                'product_name' => $r->product->name ?? 'N/A',
                'rating'       => $r->rating,
            ]);

        return response()->json($reviews);
    }

    public function review_show($id)
    {
        $review = \App\Models\Review::with(['user', 'product'])->findOrFail($id);
        return view('ceo.review-show', compact('review'));
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

        return view('ceo.stock-alert', compact('lowStockProducts', 'lowStockVariantProducts'));
    }

    //shipping_zones
    public function shipping_zones()
    {
        $zones = ShippingZone::orderBy('province')->paginate(30);
        return view('ceo.shipping-zones', compact('zones'));
    }

    public function shipping_zone_update(Request $request)
    {
        $request->validate([
            'zones'                    => 'required|array',
            'zones.*.fee'              => 'required|numeric|min:0',
            'zones.*.free_shipping_above' => 'nullable|numeric|min:0',
            'zones.*.is_active'        => 'nullable',
        ]);

        foreach ($request->zones as $id => $data) {
            ShippingZone::where('id', $id)->update([
                'fee'                 => $data['fee'],
                'free_shipping_above' => $data['free_shipping_above'] ?? null,
                'is_active'           => isset($data['is_active']) ? 1 : 0,
            ]);
        }

        return redirect()->back()->with('status', 'Shipping zones updated successfully!');
    }

    //flash sale 

    public function flash_sales()
    {
        $flashSales = FlashSale::with(['product', 'variant'])->latest()->paginate(10);
        $products   = Product::with('variants')->orderBy('name')->get();
        return view('ceo.flash-sales', compact('flashSales', 'products'));
    }

    public function flash_sale_store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'sale_price' => 'required|numeric|min:0',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
        ]);

        FlashSale::create([
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'sale_price' => $request->sale_price,
            'start_at'   => $request->start_at,
            'end_at'     => $request->end_at,
            'is_active'  => true,
        ]);

        return redirect()->route('ceo.flash.sales')->with('status', 'Flash Sale created!');
    }

    public function flash_sale_edit($id)
    {
        $flashSale = FlashSale::findOrFail($id);
        $products  = Product::with('variants')->orderBy('name')->get();
        return view('ceo.flash-sale-edit', compact('flashSale', 'products'));
    }

    public function flash_sale_update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'sale_price' => 'required|numeric|min:0',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
        ]);

        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update([
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'sale_price' => $request->sale_price,
            'start_at'   => $request->start_at,
            'end_at'     => $request->end_at,
        ]);

        return redirect()->route('ceo.flash.sales')->with('status', 'Flash Sale updated successfully!');
    }

    public function flash_sale_delete($id)
    {
        FlashSale::findOrFail($id)->delete();
        return redirect()->route('ceo.flash.sales')->with('status', 'Flash Sale deleted!');
    }

    public function flash_sale_toggle($id)
    {
        $sale = FlashSale::findOrFail($id);
        $sale->is_active = !$sale->is_active;
        $sale->save();
        return redirect()->back()->with('status', 'Flash Sale updated!');
    }

    //staff reports
    public function staff_reports(Request $request)
    {
        $staff = User::whereIn('utype', ['EMP', 'ADM'])
            ->withCount(['onlineOrders' => function ($q) use ($request) {
                $q->where('status', 'delivered');
                $this->applyDateFilter($q, $request);
            }])
            ->withSum(['onlineOrders as online_sales' => function ($q) use ($request) {
                $q->where('status', 'delivered');
                $this->applyDateFilter($q, $request);
            }], 'total')
            ->withCount(['posOrders' => function ($q) use ($request) {
                $q->where('status', 'completed');
                $this->applyDateFilter($q, $request);
            }])
            ->withSum(['posOrders as pos_sales' => function ($q) use ($request) {
                $q->where('status', 'completed');
                $this->applyDateFilter($q, $request);
            }], 'total')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $paymentBreakdown = PosOrder::where('status', 'completed')
            ->whereIn('user_id', $staff->pluck('id'))
            ->when(true, function ($q) use ($request) {
                $this->applyDateFilter($q, $request);
            })
            ->select('user_id', 'payment_method', DB::raw('SUM(total) as amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'payment_method')
            ->get()
            ->groupBy('user_id');

        return view('ceo.staff-reports', compact('staff', 'paymentBreakdown'));
    }

    // Products sold by a specific staff member (POS + Online)
    public function staff_report_products($id, Request $request)
    {
        $staff = User::findOrFail($id);

        $posProducts = PosOrderItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue'),
            DB::raw('COUNT(DISTINCT pos_order_id) as order_count'),
            DB::raw("'POS' as source")
        )
            ->whereHas('posOrder', function ($q) use ($id, $request) {
                $q->where('user_id', $id)->where('status', 'completed');
                $this->applyDateFilter($q, $request);
            })
            ->groupBy('product_id', 'product_name')
            ->get();

        $onlineProducts = OrderItem::select(
            'order_items.product_id',
            DB::raw('COALESCE(products.name, "Deleted Product") as product_name'),
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
            DB::raw("'Online' as source")
        )
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereHas('order', function ($q) use ($id, $request) {
                $q->where('staff_id', $id)->where('status', 'delivered');
                $this->applyDateFilter($q, $request);
            })
            ->groupBy('order_items.product_id', 'products.name')
            ->get();

        $products = $posProducts->concat($onlineProducts)
            ->sortByDesc('total_revenue')
            ->values();

        // Manual pagination since we merged two collections
        $page = $request->get('page', 1);
        $perPage = 15;
        $paged = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('ceo.staff-report-products', ['staff' => $staff, 'products' => $paged]);
    }

    // Payment details for a specific staff member (POS only)
    public function staff_report_payments($id, Request $request)
    {
        $staff = User::findOrFail($id);

        $onlinePayments = Order::where('staff_id', $id)
            ->where('status', 'delivered')
            ->when(true, function ($q) use ($request) {
                $this->applyDateFilter($q, $request);
            })
            ->with('transaction')
            ->get()
            ->map(function ($order) {
                return (object)[
                    'id'              => $order->id,
                    'customer_name'   => $order->name,
                    'customer_phone'  => $order->phone,
                    'payment_method'  => $order->transaction->mode ?? 'N/A',
                    'total'           => $order->total,
                    'created_at'      => $order->created_at,
                    'source'          => 'Online',
                ];
            });

        $posPayments = PosOrder::where('user_id', $id)
            ->where('status', 'completed')
            ->when(true, function ($q) use ($request) {
                $this->applyDateFilter($q, $request);
            })
            ->select('id', 'customer_name', 'customer_phone', 'payment_method', 'total', 'created_at')
            ->get()
            ->map(function ($order) {
                $order->source = 'POS';
                return $order;
            });

        $payments = $posPayments->concat($onlinePayments)
            ->sortByDesc('created_at')
            ->values();

        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 15;
        $paged = new \Illuminate\Pagination\LengthAwarePaginator(
            $payments->forPage($page, $perPage),
            $payments->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('ceo.staff-report-payments', ['staff' => $staff, 'payments' => $paged]);
    }

    private function applyDateFilter($query, Request $request)
    {
        // Quick presets
        if ($request->filled('preset')) {
            switch ($request->preset) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
            }
            return;
        }

        // Month/Year filter
        if ($request->filled('month') || $request->filled('year')) {
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }
            return;
        }

        // From/To date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
    }

    //product report
    public function product_reports(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();
        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        // ── helpers ──────────────────────────────────────────────────
        // POS sold qty + revenue per product
        $posData = PosOrderItem::whereBetween('created_at', [$from, $to])
            ->selectRaw('product_id,
                          SUM(quantity)         AS qty,
                          SUM(quantity * price) AS revenue')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Online sold qty + revenue per product
        $onlineData = OrderItem::whereBetween('created_at', [$from, $to])
            ->selectRaw('product_id,
                          SUM(quantity)         AS qty,
                          SUM(quantity * price) AS revenue')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // All products with variants + category
        $products = Product::with(['variants', 'category'])->get()->map(function ($p) use ($posData, $onlineData) {
            $pos    = $posData[$p->id]    ?? null;
            $online = $onlineData[$p->id] ?? null;

            $posSold      = (int)   ($pos->qty      ?? 0);
            $posRevenue   = (float) ($pos->revenue  ?? 0);
            $onlineSold   = (int)   ($online->qty     ?? 0);
            $onlineRevenue = (float) ($online->revenue ?? 0);
            $totalSold    = $posSold + $onlineSold;
            $totalRevenue = $posRevenue + $onlineRevenue;

            $totalStock   = $p->variants->sum('quantity');
            $stockStatus  = $totalStock == 0
                ? 'out'
                : ($totalStock <= 5 ? 'low' : 'ok');

            return (object) [
                'id'             => $p->id,
                'name'           => $p->name,
                'sku'            => $p->SKU,
                'image'          => $p->image,
                'category'       => $p->category->name ?? '—',
                'variants_count' => $p->variants->count(),
                'total_stock'    => $totalStock,
                'stock_status'   => $stockStatus,
                'pos_sold'       => $posSold,
                'pos_revenue'    => $posRevenue,
                'online_sold'    => $onlineSold,
                'online_revenue' => $onlineRevenue,
                'total_sold'     => $totalSold,
                'total_revenue'  => $totalRevenue,
                'variants'       => $p->variants,
            ];
        });

        // ── Tab 1 : Top Selling (top 10 by total units sold) ─────────
        $topSelling = $products->sortByDesc('total_sold')->take(10)->values();

        // ── Tab 2 : Low Selling (bottom 10 that have at least 1 sale) ─
        $lowSelling = $products
            ->filter(fn($p) => $p->total_sold > 0)
            ->sortBy('total_sold')
            ->take(10)
            ->values();

        // ── Tab 3 : Stock Report (all products) ──────────────────────
        $stockReport = $products->sortBy('total_stock')->values();

        // ── Tab 4 : Out of Stock ─────────────────────────────────────
        $outOfStock = $products->where('stock_status', 'out')->values();

        // ── Tab 5 : Revenue Report (sorted by revenue desc) ──────────
        $revenueReport = $products->sortByDesc('total_revenue')->values();

        // ── Summary KPIs ─────────────────────────────────────────────
        $summary = [
            'total_products'  => $products->count(),
            'total_revenue'   => $products->sum('total_revenue'),
            'total_sold'      => $products->sum('total_sold'),
            'out_of_stock'    => $products->where('stock_status', 'out')->count(),
            'low_stock'       => $products->where('stock_status', 'low')->count(),
        ];

        $onlineOrders = Order::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn($o) => (object) [
                'id' => $o->id,
                'date' => $o->created_at,
                'customer_name' => $o->user->name ?? 'Guest',
                'channel' => 'Online',
                'total' => $o->total,
                'address' => $o->province ?? 'No Address',
                'status' => in_array($o->status, ['paid', 'completed', 'delivered']) ? 'Paid' : 'Unpaid',
            ]);

        $posOrders = PosOrder::whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn($o) => (object) [
                'id' => $o->id,
                'date' => $o->created_at,
                'customer_name' => $o->customer_name ?? 'Walk-in',
                'channel' => 'POS',
                'total' => $o->total,
                'address' => 'Walk In',
                'status' => ($o->amount_owed ?? 0) <= 0 ? 'Paid' : 'Unpaid',
            ]);

        $invoices = $onlineOrders->concat($posOrders)->sortByDesc('date')->values();

        return view('ceo.reports-products', compact(
            'topSelling',
            'lowSelling',
            'stockReport',
            'outOfStock',
            'revenueReport',
            'summary',
            'from',
            'to',
            'invoices'
        ));
    }

    // Customer invoice report (Online + POS) In Product report
    public function customer_invoices(Request $request)
    {
        $from = $request->from
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();
        $to = $request->to
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        // ── Online orders ────────────────────────────────────────────
        $onlineOrders = Order::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(function ($o) {
                return (object) [
                    'id'            => $o->id,
                    'date'          => $o->created_at,
                    'customer_name' => $o->user->name ?? 'Guest',
                    'channel'       => 'Online',
                    'total'         => $o->total,
                    'address'       => $o->address ?? '—',
                    'status'        => in_array($o->status, ['paid', 'completed', 'delivered'])
                        ? 'Paid' : 'Unpaid',
                ];
            });

        // ── POS orders ───────────────────────────────────────────────
        $posOrders = PosOrder::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(function ($o) {
                return (object) [
                    'id'            => $o->id,
                    'date'          => $o->created_at,
                    'customer_name' => $o->user->name ?? 'Walk-in',
                    'channel'       => 'POS',
                    'total'         => $o->total,
                    'address'       => '—',
                    'status'        => ($o->amount_owed ?? 0) <= 0 ? 'Paid' : 'Unpaid',
                ];
            });

        // ── Merge + sort by date desc ────────────────────────────────
        $invoices = $onlineOrders->concat($posOrders)
            ->sortByDesc('date')
            ->values();

        // ── Summary KPIs ─────────────────────────────────────────────
        $summary = [
            'total_invoices' => $invoices->count(),
            'total_revenue'  => $invoices->sum('total'),
            'paid_count'     => $invoices->where('status', 'Paid')->count(),
            'unpaid_count'   => $invoices->where('status', 'Unpaid')->count(),
            'online_count'   => $invoices->where('channel', 'Online')->count(),
            'pos_count'      => $invoices->where('channel', 'POS')->count(),
        ];

        return view('ceo.reports-invoices', compact(
            'invoices',
            'summary',
            'from',
            'to'
        ));
    }

    //summary report
    public function summary_report(Request $request)
    {
        // --- Date range ---
        $from    = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to      = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $groupBy = in_array($request->group_by, ['daily', 'weekly', 'monthly'])
            ? $request->group_by
            : 'daily';

        // --- Build grouped rows ---
        $rows = $this->buildSummaryRows($from, $to, $groupBy);

        // --- Summary totals ---
        $summary = [
            'gross_sales'   => collect($rows)->sum('gross_sales'),
            'return_sales'  => collect($rows)->sum('return_sales'),
            'discounts'     => collect($rows)->sum('discounts'),
            'pos_sales'     => collect($rows)->sum('pos_sales'),
            'online_sales'  => collect($rows)->sum('online_sales'),
            'net_sales'     => collect($rows)->sum('net_sales'),
            'total_sales'   => collect($rows)->sum('total_sales'),
            'shipping_fee'   => collect($rows)->sum('shipping_fee'),
        ];

        // --- CSV Export ---
        if ($request->export === 'csv') {
            return $this->exportSummaryCsv($rows, $summary, $groupBy);
        }

        return view('ceo.reports-summary', compact('rows', 'summary', 'from', 'to', 'groupBy'));
    }

    // ── 2. Build rows helper ──────────────────────────────────────
    private function buildSummaryRows(Carbon $from, Carbon $to, string $groupBy): array
    {
        $rows   = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            // Period boundaries
            if ($groupBy === 'daily') {
                $periodStart = $cursor->copy()->startOfDay();
                $periodEnd   = $cursor->copy()->endOfDay();
                $label       = $cursor->format('M j, Y');
                $next        = $cursor->copy()->addDay();
            } elseif ($groupBy === 'weekly') {
                $periodStart = $cursor->copy()->startOfWeek();
                $periodEnd   = $cursor->copy()->endOfWeek()->min($to);
                $label       = $periodStart->format('M j') . ' – ' . $periodEnd->format('M j, Y');
                $next        = $cursor->copy()->addWeek()->startOfWeek();
            } else { // monthly
                $periodStart = $cursor->copy()->startOfMonth();
                $periodEnd   = $cursor->copy()->endOfMonth()->min($to);
                $label       = $cursor->format('F Y');
                $next        = $cursor->copy()->addMonth()->startOfMonth();
            }

            // Clamp to the overall range
            $periodStart = $periodStart->max($from);
            $periodEnd   = $periodEnd->min($to);

            // ── POS revenue (completed orders only) ──
            $posSales = PosOrder::where('status', 'completed')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('total');

            $posSubtotal = PosOrder::where('status', 'completed')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('subtotal');

            $posDiscounts = PosOrder::where('status', 'completed')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('discount');

            // ── Online orders (single, consistent status scope) ──
            $onlineOrders = Order::whereIn('status', ['paid', 'completed', 'delivered'])
                ->whereBetween('created_at', [$periodStart, $periodEnd]);

            $onlineGross    = (clone $onlineOrders)->sum('total');       // items(sale price) + shipping
            $onlineShipping = (clone $onlineOrders)->sum('shipping');

            $onlineItemDiscounts = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->whereIn('orders.status', ['paid', 'completed', 'delivered'])
                ->whereBetween('orders.created_at', [$periodStart, $periodEnd])
                ->whereNotNull('products.sale_price')
                ->whereRaw('products.regular_price > products.sale_price')
                ->selectRaw('SUM((products.regular_price - products.sale_price) * order_items.quantity) as disc')
                ->value('disc') ?? 0;

            $onlineDiscounts = max(0, $onlineItemDiscounts);

            // Strip shipping out first, THEN add back the discount to get true items-only regular-price subtotal
            $onlineSubtotal = ($onlineGross - $onlineShipping) + $onlineDiscounts;

            // Return sales
            $returnSales = Order::where('status', 'canceled')
                ->whereBetween('canceled_date', [$periodStart, $periodEnd])
                ->sum('total');

            // ── Combined figures ──
            $grossSales  = $onlineSubtotal + $posSubtotal;
            $discounts   = $posDiscounts + $onlineDiscounts;
            $onlineSales = max(0, $onlineGross - $returnSales);
            $netSales    = $onlineSales + $posSales;
            $totalSales  = $netSales;

            $rows[] = [
                'label'        => $label,
                'period_from'  => $periodStart->format('Y-m-d'),
                'period_to'    => $periodEnd->format('Y-m-d'),
                'gross_sales'  => round($grossSales,  2),
                'shipping_fee'  => round($onlineShipping, 2),
                'return_sales' => round($returnSales,  2),
                'discounts'    => round($discounts,    2),
                'pos_sales'    => round($posSales,     2),
                'online_sales' => round($onlineSales,  2),
                'net_sales'    => round($netSales,     2),
                'total_sales'  => round($totalSales,   2),
            ];

            $cursor = $next;
            if ($cursor->gt($to)) break;
        }

        return $rows;
    }

    // ── 3. CSV export helper ──────────────────────────────────────
    private function exportSummaryCsv(array $rows, array $summary, string $groupBy): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'sales-summary-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows, $summary, $groupBy) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                ucfirst($groupBy),
                'Gross Sales',
                'Return Sales',
                'Net Discounts',
                'POS Sales',
                'Online Sales',
                'Net Sales',
                'Total Sales',
            ]);

            // Summary row
            fputcsv($handle, [
                'Summary',
                number_format($summary['gross_sales'],  2, '.', ''),
                '-' . number_format($summary['return_sales'], 2, '.', ''),
                '-' . number_format($summary['discounts'],    2, '.', ''),
                number_format($summary['pos_sales'],    2, '.', ''),
                number_format($summary['online_sales'], 2, '.', ''),
                number_format($summary['net_sales'],    2, '.', ''),
                number_format($summary['total_sales'],  2, '.', ''),
            ]);

            // Data rows
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['label'],
                    number_format($row['gross_sales'],  2, '.', ''),
                    $row['return_sales'] > 0 ? '-' . number_format($row['return_sales'], 2, '.', '') : '0.00',
                    $row['discounts']    > 0 ? '-' . number_format($row['discounts'],    2, '.', '') : '0.00',
                    number_format($row['pos_sales'],    2, '.', ''),
                    number_format($row['online_sales'], 2, '.', ''),
                    number_format($row['net_sales'],    2, '.', ''),
                    number_format($row['total_sales'],  2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    //summary detail report
    public function summary_detail(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        // POS orders
        $posOrders = PosOrder::with('items.product')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Online orders
        $onlineOrders = Order::with('orderItems.product')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // Item-level discounts from products
        $itemDiscounts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotNull('products.sale_price')
            ->whereRaw('products.regular_price > products.sale_price')
            ->selectRaw('orders.id as order_id, SUM((products.regular_price - products.sale_price) * order_items.quantity) as disc')
            ->groupBy('orders.id')
            ->pluck('disc', 'order_id');

        return view('ceo.reports-summary-detail', compact(
            'posOrders',
            'onlineOrders',
            'itemDiscounts',
            'from',
            'to'
        ));
    }
}
