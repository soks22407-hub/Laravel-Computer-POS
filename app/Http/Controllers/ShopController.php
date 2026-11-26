<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $maxPrice = Product::max('regular_price');
        $max_price = $request->query('max') ? $request->query('max') : $maxPrice;
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'sale_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'sale_price';
                $o_order = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderby('name', 'ASC')->get();
        $products = Product::with(['category', 'reviews'])
            ->where(function ($query) use ($f_brands) {
                $query->whereIn('brand_id', explode(',', $f_brands))->orwhereRaw("'" . $f_brands . "'=''");
            })
            ->where(function ($query) use ($f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories))->orwhereRaw("'" . $f_categories . "'=''");
            })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])
                    ->orwhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)->paginate(12);

        $banners = Banner::where('status', 1)
            ->orderByDesc('id')
            ->get();
        $activeFlashSales = FlashSale::where('is_active', true)
            ->where('start_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->pluck('sale_price', 'product_id');
        return view('shop', compact('products', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'maxPrice', 'max_price', 'banners','activeFlashSales'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(8);
        // Previous product (smaller ID)
        $prevProduct = Product::where('id', '<', $product->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextProduct = Product::where('id', '>', $product->id)
            ->orderBy('id', 'asc')
            ->first();

        $reviews = $product->reviews()->latest()->get();
        $avgRating  = $reviews->count() ? round($reviews->avg('rating'), 1) : 0;
        $userReview = Auth::check()
            ? $reviews->where('user_id', Auth::id())->first()
            : null;

        $flashSale = FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('start_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->first();
        return view('details', compact('product', 'rproducts', 'prevProduct', 'nextProduct', 'reviews', 'avgRating', 'userReview', 'flashSale'));
    }
}
