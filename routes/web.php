<?php

use App\Http\Controllers\Admin\ProductReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CEOController;
use App\Http\Controllers\CEOPosController;
use App\Http\Controllers\CEOStockTransactionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePOSController;
// use App\Http\Controllers\AuthAdmin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\Ceo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;










// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/index', function () {
//     return view('index');
// });

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
//cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('cart/increase_quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('cart/decrease_quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');
//Debit or Credit Card
Route::get('/payment/card-demo/{order}', [CartController::class, 'showCardForm'])->name('payment.card.demo.form');
// process fake payment
Route::post('/payment/card-demo/{order}', [CartController::class, 'processCardDemo'])->name('payment.card.demo.pay');
// result pages
Route::get('/payment/success', [CartController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failed', [CartController::class, 'paymentFailed'])->name('payment.failed');

//wishlist
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.items.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');
//cart
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place_an_order', [CartController::class, 'place_an_order'])->name('cart.place.an.order');
Route::get('/order_confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');

//invoice
Route::get('/order/{id}/invoice', [CartController::class, 'print_invoice'])->name('order.invoice');

//contact
Route::get('/contact-us', [HomeController::class, 'contact'])->name('home.contact');
Route::post('/contact/store', [HomeController::class, 'contact_store'])->name('home.contact.store');
//search
Route::get('/search', [HomeController::class, 'search'])->name('home.search');
//about-us
Route::get('/about_us', [HomeController::class, 'about_us'])->name('home.about');

//privacy&legal
Route::view('/privacy-policy', 'legal.privacy')->name('legal.privacy');
Route::view('/terms-of-service', 'legal.terms')->name('legal.terms');
Route::view('/cookie-policy', 'legal.cookie')->name('legal.cookie');
Route::view('/disclaimer', 'legal.disclaimer')->name('legal.disclaimer');




Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    //order
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
    Route::get('/user/address', [UserController::class, 'address'])->name('user.address');
    Route::get('/user/address/edit/{id}', [UserController::class, 'address_edit'])->name('user.address.edit');
    Route::put('/user/address/update', [UserController::class, 'address_update'])->name('user.address.update');
    Route::get('/user/wishlist', [UserController::class, 'wishlist'])->name('user.wishlist');
    Route::get('/user/account', [UserController::class, 'account_detail'])->name('user.account');
    Route::put('/user/account/update', [UserController::class, 'update_account'])->name('user.account.update');

    //Rate & Review
    Route::post('/review/{product_id}', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/review/{id}/delete', [ReviewController::class, 'destroy'])->name('review.delete');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    //brand
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/{id}', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');
    Route::get('/admin/search/brand', [AdminController::class, 'brand_search'])->name('admin.search.brand');
    Route::get('admin/products/brand/{id}', [AdminController::class, 'byBrand'])->name('admin.products.byBrand');
    // categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/categories/add', [AdminController::class, 'add_categories'])->name('admin.categories.add');
    Route::post('/admin/categories/store', [AdminController::class, 'categories_store'])->name('admin.categories.store');
    Route::get('/admin/categories/edit/{id}', [AdminController::class, 'categories_edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{id}', [AdminController::class, 'categories_update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}/delete', [AdminController::class, 'categories_delete'])->name('admin.categories.delete');
    Route::get('/admin/search/categories', [AdminController::class, 'cate_search'])->name('admin.search.categories');
    Route::get('admin/products/category/{id}', [AdminController::class, 'byCategory'])->name('admin.products.byCategory');

    // product
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/show/{id}', [AdminController::class, 'product_show'])->name('admin.product.show');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');
    Route::get('/admin/search/product', [AdminController::class, 'product_search'])->name('admin.search.product');
    //order
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order.details');
    Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');
    Route::get('/admin/search/order', [AdminController::class, 'search_order'])->name('admin.search.order');
    //invoice admin
    Route::get('/admin/orders/{id}/invoice', [AdminController::class, 'invoice'])->name('admin.orders.invoice');
    //slide
    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
    Route::get('/admin/slide/{id}/edit', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update', [AdminController::class, 'slide_update'])->name('admin.slide.update');
    Route::delete('/admin/slide/{id}/delete', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');
    Route::get('/admin/search/slide', [AdminController::class, 'slide_search'])->name('admin.search.slide');
    //contactshowinadminpage
    Route::get('/admin/contact', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::get('/admin/contact/{id}', [AdminController::class, 'contact_show'])->name('admin.contact.show');
    Route::delete('/admin/contact/{id}/delete', [AdminController::class, 'contact_delete'])->name('admin.contact.delete');
    Route::get('/admin/search/contact', [AdminController::class, 'search_contact'])->name('admin.search.contact');
    //searchinadmin
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    //user
    Route::get('/admin/account', [AdminController::class, 'user'])->name('admin.user.account');
    Route::get('/admin/account/create', [AdminController::class, 'user_create'])->name('admin.account.create');
    Route::post('/admin/account/store', [AdminController::class, 'user_store'])->name('admin.account.store');
    Route::get('/admin/account/edit/{id}', [AdminController::class, 'user_edit'])->name('admin.account.edit');
    Route::put('/admin/account/update/{id}', [AdminController::class, 'user_update'])->name('admin.account.update');
    Route::delete('/admin/account/{id}/delete', [AdminController::class, 'user_delete'])->name('admin.account.delete');
    Route::get('/admin/search/user', [AdminController::class, 'search_user'])->name('admin.search.user');
    // Admin personal profile
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

    //notification
    Route::get('/admin/inbox', [AdminController::class, 'notification'])->name('admin.inbox');
    Route::get('/admin/inbox/count', [AdminController::class, 'notification_count'])->name('admin.inbox.count');
    Route::get('/admin/inbox/show/{id}', [AdminController::class, 'notification_show'])->name('admin.inbox.show');
    Route::delete('/admin/inbox/delete/{id}', [AdminController::class, 'notification_delete'])->name('admin.inbox.delete');
    //setting
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/admin/settings/add', [AdminController::class, 'setting_add'])->name('admin.settings.add');
    Route::post('/admin/settings/store', [AdminController::class, 'storeSettings'])->name('admin.settings.store');
    Route::get('/admin/settings/{id}/edit', [AdminController::class, 'setting_edit'])->name('admin.settings.edit');
    Route::put('/admin/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::delete('/admin/settings/{id}/delete', [AdminController::class, 'setting_delete'])->name('admin.settings.delete');
    Route::get('/admin/search/setting', [AdminController::class, 'search_setting'])->name('admin.search.setting');

    //taskboard
    Route::get('/admin/tasks', [AdminController::class, 'task'])->name('admin.tasks');
    Route::post('/admin/tasks', [AdminController::class, 'task_store'])->name('admin.tasks.store');
    Route::put('/admin/tasks/{task}/status', [AdminController::class, 'task_update'])->name('admin.tasks.update');
    Route::delete('/admin/tasks/{task}', [AdminController::class, 'task_delete'])->name('admin.tasks.delete');
    Route::get('/admin/tasks/{task}/show', [AdminController::class, 'task_show'])->name('admin.tasks.show');

    //support
    Route::get('/admin/support', [AdminController::class, 'support'])->name('admin.support');
    Route::get('/admin/support/users/{id}', [AdminController::class, 'show'])->name('admin.users.show');

    //coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [AdminController::class, 'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/edit/{id}', [AdminController::class, 'coupon_edit'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/update', [AdminController::class, 'coupon_update'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'coupon_delete'])->name('admin.coupon.delete');
    Route::get('/admin/search/coupon', [AdminController::class, 'search_coupon'])->name('admin.search.coupon');

    //banner
    Route::get('/admin/banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::get('/admin/banner/add', [AdminController::class, 'banner_add'])->name('admin.banner.add');
    Route::post('/admin/banner/store', [AdminController::class, 'banner_store'])->name('admin.banner.store');
    Route::get('/admin/banner/{id}/edit', [AdminController::class, 'banner_edit'])->name('admin.banner.edit');
    Route::put('/admin/banner/update', [AdminController::class, 'banner_update'])->name('admin.banner.update');
    Route::delete('/admin/banner/{id}/delete', [AdminController::class, 'banner_delete'])->name('admin.banner.delete');
    Route::get('/admin/search/banner', [AdminController::class, 'banner_search'])->name('admin.search.banner');

    //discount
    Route::get('/admin/discounts', [AdminController::class, 'discounts'])->name('admin.discounts');
    Route::get('/admin/discount/add', [AdminController::class, 'discount_add'])->name('admin.discount.add');
    Route::post('/admin/discount/store', [AdminController::class, 'discount_store'])->name('admin.discount.store');
    Route::get('/admin/discount/edit/{id}', [AdminController::class, 'discount_edit'])->name('admin.discount.edit');
    Route::put('/admin/discount/update', [AdminController::class, 'discount_update'])->name('admin.discount.update');
    Route::delete('/admin/discount/{id}/delete', [AdminController::class, 'discount_delete'])->name('admin.discount.delete');
    Route::get('/admin/search/discount', [AdminController::class, 'discount_search'])->name('admin.search.discount');

    //Review
    Route::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/admin/review/{id}/delete', [AdminController::class, 'review_delete'])->name('admin.review.delete');
    Route::get('/admin/search/review', [AdminController::class, 'search_review'])->name('admin.search.review');
    Route::get('/admin/review/{id}/show', [AdminController::class, 'review_show'])->name('admin.review.show');

    //stock alert
    Route::get('/admin/stock-alert', [AdminController::class, 'stock_alert'])->name('admin.stock.alert');

    // Stock In
    Route::get('/admin/stock-in', [StockTransactionController::class, 'stockInForm'])->name('admin.stock-in');
    Route::post('/admin/stock-in', [StockTransactionController::class, 'stockInStore'])->name('admin.stock-in.store');

    // Stock Out
    Route::get('/admin/stock-out', [StockTransactionController::class, 'stockOutForm'])->name('admin.stock-out');
    Route::post('/admin/stock-out', [StockTransactionController::class, 'stockOutStore'])->name('admin.stock-out.store');

    // Stock Adjustment
    Route::get('/admin/stock-adjustment', [StockTransactionController::class, 'stockAdjustmentForm'])->name('admin.stock-adjustment');
    Route::post('/admin/stock-adjustment', [StockTransactionController::class, 'stockAdjustmentStore'])->name('admin.stock-adjustment.store');

    // Stock History 
    Route::get('/admin/stock-history', [StockTransactionController::class, 'history'])->name('admin.stock-history');

    //Stock report
    Route::get('/admin/stock-report', [StockTransactionController::class, 'stockReport'])->name('admin.stock-report');

    //stock report
    Route::get('/admin/purchase-requests', [PurchaseRequestController::class, 'adminIndex'])->name('admin.purchase-requests.index');
    Route::get('/admin/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('admin.purchase-requests.create');
    Route::post('/admin/purchase-requests', [PurchaseRequestController::class, 'store'])->name('admin.purchase-requests.store');
    Route::get('/admin/purchase-requests/{id}', [PurchaseRequestController::class, 'adminShow'])->name('admin.purchase-requests.show');
    Route::post('/admin/purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('admin.purchase-requests.approve');
    Route::post('/admin/purchase-requests/{id}/reject', [PurchaseRequestController::class, 'reject'])->name('admin.purchase-requests.reject');
    Route::get('/admin/purchase-requests/{id}/export-excel', [PurchaseRequestController::class, 'exportExcel'])->name('admin.purchase-requests.export.excel');
    Route::get('/admin/purchase-requests/{id}/export-pdf', [PurchaseRequestController::class, 'exportPdf'])->name('admin.purchase-requests.export.pdf');


    //shipping_zones
    Route::get('/admin/shipping-zones', [AdminController::class, 'shipping_zones'])->name('admin.shipping.zones');
    Route::post('/admin/shipping-zones/update', [AdminController::class, 'shipping_zone_update'])->name('admin.shipping.zones.update');

    //flash sale 
    Route::get('/admin/flash-sales', [AdminController::class, 'flash_sales'])->name('admin.flash.sales');
    Route::post('/admin/flash-sales/store', [AdminController::class, 'flash_sale_store'])->name('admin.flash.sales.store');
    Route::get('/admin/flash-sales/{id}/edit', [AdminController::class, 'flash_sale_edit'])->name('admin.flash.sales.edit');
    Route::put('/admin/flash-sales/{id}/update', [AdminController::class, 'flash_sale_update'])->name('admin.flash.sales.update');
    Route::delete('/admin/flash-sales/{id}/delete', [AdminController::class, 'flash_sale_delete'])->name('admin.flash.sales.delete');
    Route::put('/admin/flash-sales/{id}/toggle', [AdminController::class, 'flash_sale_toggle'])->name('admin.flash.sales.toggle');

    Route::get('/admin/pos', [PosController::class, 'index'])->name('admin.pos');
    Route::get('/admin/pos/search', [PosController::class, 'search'])->name('admin.pos.search');
    Route::post('/admin/pos/checkout', [PosController::class, 'checkout'])->name('admin.pos.checkout');
    Route::get('/admin/pos/receipt/{id}', [PosController::class, 'receipt'])->name('admin.pos.receipt');
    Route::get('/admin/pos/history', [PosController::class, 'history'])->name('admin.pos.history');
    Route::put('/admin/pos/{id}/cancel', [PosController::class, 'cancel'])->name('admin.pos.cancel');
    Route::get('admin/pos/discounts', [PosController::class, 'getDiscounts'])->name('admin.pos.discounts');
    Route::get('/admin/pos/payment/{id}', [PosController::class, 'showAddPaymentForm'])->name('admin.pos.payment');
    Route::post('/admin/pos/payment/{id}', [PosController::class, 'addPayment'])->name('admin.pos.payment.submit');
    Route::get('/admin/pos/order/{id}/edit',   [PosController::class, 'edit_pos'])->name('admin.pos.edit');
    Route::put('/admin/pos/order/{id}/update', [PosController::class, 'update_pos'])->name('admin.pos.update');
    Route::get('/admin/pos/payment-receipt/{id}', [PosController::class, 'payment_receipt'])->name('admin.pos.payment.receipts');

    //staff report
    Route::get('/admin/staff-reports', [AdminController::class, 'staff_reports'])->name('admin.staff.reports');
    Route::get('/admin/staff-reports/{id}/products', [AdminController::class, 'staff_report_products'])->name('admin.staff.report.products');
    Route::get('/admin/staff-reports/{id}/payments', [AdminController::class, 'staff_report_payments'])->name('admin.staff.report.payments');

    //product report
    Route::get('/admin/reports/products', [AdminController::class, 'product_reports'])->name('admin.reports.products');
    Route::get('/reports/invoices', [AdminController::class, 'customer_invoices'])->name('admin.reports.invoices');

    //summary report
    Route::get('/admin/reports/summary', [AdminController::class, 'summary_report'])->name('admin.reports.summary');
    //summary detail report
    Route::get('/admin/reports/summary/detail', [AdminController::class, 'summary_detail'])->name('admin.reports.summary.detail');

    //permission
    Route::get('/admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::post('/admin/permissions', [PermissionController::class, 'update'])->name('admin.permissions.update');
});


//Employee 
Route::middleware(['auth', 'employee'])->group(function () {

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
 
    // product
    Route::get('/employee/products', [EmployeeController::class, 'products'])->name('employee.products')->middleware('permission:products,view');
    Route::get('/employee/product/add', [EmployeeController::class, 'product_add'])->name('employee.product.add')->middleware('permission:products,add');
    Route::post('/employee/product/store', [EmployeeController::class, 'product_store'])->name('employee.product.store')->middleware('permission:products,add');
    Route::get('/employee/product/edit/{id}', [EmployeeController::class, 'product_edit'])->name('employee.product.edit')->middleware('permission:products,edit');
    Route::put('/employee/product/update', [EmployeeController::class, 'product_update'])->name('employee.product.update')->middleware('permission:products,edit');
    Route::get('/employee/search/product', [EmployeeController::class, 'product_search'])->name('employee.search.product')->middleware('permission:products,view');
    Route::get('/employee/product/show/{id}', [EmployeeController::class, 'product_show'])->name('employee.product.show')->middleware('permission:products,view');
    Route::delete('/employee/product/{id}/delete', [EmployeeController::class, 'product_delete'])->name('employee.product.delete')->middleware('permission:products,delete');
    //order
    Route::get('/employee/orders', [EmployeeController::class, 'orders'])->name('employee.orders')->middleware('permission:orders,view');
    Route::get('/employee/order/{order_id}/details', [EmployeeController::class, 'order_details'])->name('employee.order.details')->middleware('permission:orders,view');
    Route::put('/employee/order/update-status', [EmployeeController::class, 'update_order_status'])->name('employee.order.status.update')->middleware('permission:orders,edit');
    Route::get('/employee/search/order', [EmployeeController::class, 'search_order'])->name('employee.search.order')->middleware('permission:orders,view');
    //invoice admin
    Route::get('/employee/orders/{id}/invoice', [EmployeeController::class, 'invoice'])->name('employee.orders.invoice')->middleware('permission:orders,view');
    //contactshowinadminpage
    Route::get('/employee/contact', [EmployeeController::class, 'contacts'])->name('employee.contacts')->middleware('permission:contacts,view');
    Route::get('/employee/contact/{id}', [EmployeeController::class, 'contact_show'])->name('employee.contact.show')->middleware('permission:contacts,view');
    Route::get('/employee/search/contact', [EmployeeController::class, 'search_contact'])->name('employee.search.contact')->middleware('permission:contacts,view');
    Route::get('/employee/search', [EmployeeController::class, 'search'])->name('employee.search');
    //notification
    Route::get('/employee/inbox', [EmployeeController::class, 'notification'])->name('employee.inbox');
    Route::get('/employee/inbox/count', [EmployeeController::class, 'notification_count'])->name('employee.inbox.count');
    Route::get('/employee/inbox/show/{id}', [EmployeeController::class, 'notification_show'])->name('employee.inbox.show');
    Route::delete('/employee/inbox/delete/{id}', [EmployeeController::class, 'notification_delete'])->name('employee.inbox.delete');
    // employee personal profile
    Route::get('/employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
    Route::put('/employee/profile/update', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
    //employee task
    Route::get('/employee/tasks', [EmployeeController::class, 'tasks'])->name('employee.tasks')->middleware('permission:tasks,view');
    Route::put('/employee/tasks/{task}/status', [EmployeeController::class, 'task_update'])->name('employee.tasks.update')->middleware('permission:tasks,edit');
    //stock alert
    Route::get('/employee/stock-alert', [EmployeeController::class, 'stock_alert'])->name('employee.stock.alert')->middleware('permission:stock_alert,view');
 
    //stock report
    Route::get('/employee/purchase-requests', [PurchaseRequestController::class, 'employeeIndex'])->name('employee.purchase-requests.index')->middleware('permission:purchase_requests,view');
    Route::get('/employee/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('employee.purchase-requests.create')->middleware('permission:purchase_requests,add');
    Route::post('/employee/purchase-requests', [PurchaseRequestController::class, 'store'])->name('employee.purchase-requests.store')->middleware('permission:purchase_requests,add');
    Route::get('/employee/purchase-requests/{id}', [PurchaseRequestController::class, 'employeeShow'])->name('employee.purchase-requests.show')->middleware('permission:purchase_requests,view');
    Route::get('/employee/purchase-requests/{id}/export-pdf', [PurchaseRequestController::class, 'exportPdf'])->name('employee.purchase-requests.export.pdf')->middleware('permission:purchase_requests,view');
 
    //POS
    Route::get('/employee/pos', [EmployeePOSController::class, 'index'])->name('employee.pos')->middleware('permission:pos,view');
    Route::get('/employee/pos/search', [EmployeePOSController::class, 'search'])->name('employee.pos.search')->middleware('permission:pos,view');
    Route::post('/employee/pos/checkout', [EmployeePOSController::class, 'checkout'])->name('employee.pos.checkout')->middleware('permission:pos,add');
    Route::get('/employee/pos/receipt/{id}', [EmployeePOSController::class, 'receipt'])->name('employee.pos.receipt')->middleware('permission:pos,view');
    Route::get('/employee/pos/history', [EmployeePOSController::class, 'history'])->name('employee.pos.history')->middleware('permission:pos,view');
    Route::put('/employee/pos/{id}/cancel', [EmployeePOSController::class, 'cancel'])->name('employee.pos.cancel')->middleware('permission:pos,delete');
    Route::get('employee/pos/discounts', [EmployeePOSController::class, 'getDiscounts'])->name('employee.pos.discounts')->middleware('permission:pos,view');
    Route::get('/employee/pos/payment/{id}', [EmployeePOSController::class, 'showAddPaymentForm'])->name('employee.pos.payment')->middleware('permission:pos,edit');
    Route::post('/employee/pos/payment/{id}', [EmployeePOSController::class, 'addPayment'])->name('employee.pos.payment.submit')->middleware('permission:pos,edit');
    Route::get('/employee/pos/order/{id}/edit',   [EmployeePOSController::class, 'edit_pos'])->name('employee.pos.edit')->middleware('permission:pos,edit');
    Route::put('/employee/pos/order/{id}/update', [EmployeePOSController::class, 'update_pos'])->name('employee.pos.update')->middleware('permission:pos,edit');
    Route::get('/employee/pos/payment-receipt/{id}', [EmployeePOSController::class, 'payment_receipt'])->name('employee.pos.payment.receipts')->middleware('permission:pos,view');

});

// CEO routes
Route::middleware(['auth', 'ceo'])->group(function () {
    Route::get('/ceo', [CEOController::class, 'index'])->name('ceo.index');

    //brand
    Route::get('/ceo/brands', [CEOController::class, 'brands'])->name('ceo.brands');
    Route::get('/ceo/brand/add', [CEOController::class, 'add_brand'])->name('ceo.brand.add');
    Route::post('/ceo/brand/store', [CEOController::class, 'brand_store'])->name('ceo.brand.store');
    Route::get('/ceo/brand/edit/{id}', [CEOController::class, 'brand_edit'])->name('ceo.brand.edit');
    Route::put('/ceo/brand/{id}', [CEOController::class, 'brand_update'])->name('ceo.brand.update');
    Route::delete('/ceo/brand/{id}/delete', [CEOController::class, 'brand_delete'])->name('ceo.brand.delete');
    Route::get('/ceo/search/brand', [CEOController::class, 'brand_search'])->name('ceo.search.brand');
    Route::get('ceo/products/brand/{id}', [CEOController::class, 'byBrand'])->name('ceo.products.byBrand');
    // categories
    Route::get('/ceo/categories', [CEOController::class, 'categories'])->name('ceo.categories');
    Route::get('/ceo/categories/add', [CEOController::class, 'add_categories'])->name('ceo.categories.add');
    Route::post('/ceo/categories/store', [CEOController::class, 'categories_store'])->name('ceo.categories.store');
    Route::get('/ceo/categories/edit/{id}', [CEOController::class, 'categories_edit'])->name('ceo.categories.edit');
    Route::put('/ceo/categories/{id}', [CEOController::class, 'categories_update'])->name('ceo.categories.update');
    Route::delete('/ceo/categories/{id}/delete', [CEOController::class, 'categories_delete'])->name('ceo.categories.delete');
    Route::get('/ceo/search/categories', [CEOController::class, 'cate_search'])->name('ceo.search.categories');
    Route::get('ceo/products/category/{id}', [CEOController::class, 'byCategory'])->name('ceo.products.byCategory');

    // product
    Route::get('/ceo/products', [CEOController::class, 'products'])->name('ceo.products');
    Route::get('/ceo/product/show/{id}', [CEOController::class, 'product_show'])->name('ceo.product.show');
    Route::get('/ceo/product/add', [CEOController::class, 'product_add'])->name('ceo.product.add');
    Route::post('/ceo/product/store', [CEOController::class, 'product_store'])->name('ceo.product.store');
    Route::get('/ceo/product/edit/{id}', [CEOController::class, 'product_edit'])->name('ceo.product.edit');
    Route::put('/ceo/product/update', [CEOController::class, 'product_update'])->name('ceo.product.update');
    Route::delete('/ceo/product/{id}/delete', [CEOController::class, 'product_delete'])->name('ceo.product.delete');
    Route::get('/ceo/search/product', [CEOController::class, 'product_search'])->name('ceo.search.product');
    //order
    Route::get('/ceo/orders', [CEOController::class, 'orders'])->name('ceo.orders');
    Route::get('/ceo/order/{order_id}/details', [CEOController::class, 'order_details'])->name('ceo.order.details');
    Route::put('/ceo/order/update-status', [CEOController::class, 'update_order_status'])->name('ceo.order.status.update');
    Route::get('/ceo/search/order', [CEOController::class, 'search_order'])->name('ceo.search.order');
    //invoice admin
    Route::get('/ceo/orders/{id}/invoice', [CEOController::class, 'invoice'])->name('ceo.orders.invoice');
    //slide
    Route::get('/ceo/slides', [CEOController::class, 'slides'])->name('ceo.slides');
    Route::get('/ceo/slide/add', [CEOController::class, 'slide_add'])->name('ceo.slide.add');
    Route::post('/ceo/slide/store', [CEOController::class, 'slide_store'])->name('ceo.slide.store');
    Route::get('/ceo/slide/{id}/edit', [CEOController::class, 'slide_edit'])->name('ceo.slide.edit');
    Route::put('/ceo/slide/update', [CEOController::class, 'slide_update'])->name('ceo.slide.update');
    Route::delete('/ceo/slide/{id}/delete', [CEOController::class, 'slide_delete'])->name('ceo.slide.delete');
    Route::get('/ceo/search/slide', [CEOController::class, 'slide_search'])->name('ceo.search.slide');
    //contactshowinadminpage
    Route::get('/ceo/contact', [CEOController::class, 'contacts'])->name('ceo.contacts');
    Route::get('/ceo/contact/{id}', [CEOController::class, 'contact_show'])->name('ceo.contact.show');
    Route::delete('/ceo/contact/{id}/delete', [CEOController::class, 'contact_delete'])->name('ceo.contact.delete');
    Route::get('/ceo/search/contact', [CEOController::class, 'search_contact'])->name('ceo.search.contact');
    //searchinadmin
    Route::get('/ceo/search', [CEOController::class, 'search'])->name('ceo.search');
    //user
    Route::get('/ceo/account', [CEOController::class, 'user'])->name('ceo.user.account');
    Route::get('/ceo/account/create', [CEOController::class, 'user_create'])->name('ceo.account.create');
    Route::post('/ceo/account/store', [CEOController::class, 'user_store'])->name('ceo.account.store');
    Route::get('/ceo/account/edit/{id}', [CEOController::class, 'user_edit'])->name('ceo.account.edit');
    Route::put('/ceo/account/update/{id}', [CEOController::class, 'user_update'])->name('ceo.account.update');
    Route::delete('/ceo/account/{id}/delete', [CEOController::class, 'user_delete'])->name('ceo.account.delete');
    Route::get('/ceo/search/user', [CEOController::class, 'search_user'])->name('ceo.search.user');
    // Admin personal profile
    Route::get('/ceo/profile', [CEOController::class, 'profile'])->name('ceo.profile');
    Route::put('/ceo/profile/update', [CEOController::class, 'updateProfile'])->name('ceo.profile.update');

    //notification
    Route::get('/ceo/inbox', [CEOController::class, 'notification'])->name('ceo.inbox');
    Route::get('/ceo/inbox/count', [CEOController::class, 'notification_count'])->name('ceo.inbox.count');
    Route::get('/ceo/inbox/show/{id}', [CEOController::class, 'notification_show'])->name('ceo.inbox.show');
    Route::delete('/ceo/inbox/delete/{id}', [CEOController::class, 'notification_delete'])->name('ceo.inbox.delete');
    //setting
    Route::get('/ceo/settings', [CEOController::class, 'settings'])->name('ceo.settings');
    Route::get('/ceo/settings/add', [CEOController::class, 'setting_add'])->name('ceo.settings.add');
    Route::post('/ceo/settings/store', [CEOController::class, 'storeSettings'])->name('ceo.settings.store');
    Route::get('/ceo/settings/{id}/edit', [CEOController::class, 'setting_edit'])->name('ceo.settings.edit');
    Route::put('/ceo/settings/update', [CEOController::class, 'updateSettings'])->name('ceo.settings.update');
    Route::delete('/ceo/settings/{id}/delete', [CEOController::class, 'setting_delete'])->name('ceo.settings.delete');
    Route::get('/ceo/search/setting', [CEOController::class, 'search_setting'])->name('ceo.search.setting');

    //taskboard
    Route::get('/ceo/tasks', [CEOController::class, 'task'])->name('ceo.tasks');
    Route::post('/ceo/tasks', [CEOController::class, 'task_store'])->name('ceo.tasks.store');
    Route::put('/ceo/tasks/{task}/status', [CEOController::class, 'task_update'])->name('ceo.tasks.update');
    Route::delete('/ceo/tasks/{task}', [CEOController::class, 'task_delete'])->name('ceo.tasks.delete');
    Route::get('/ceo/tasks/{task}/show', [CEOController::class, 'task_show'])->name('ceo.tasks.show');

    //support
    Route::get('/ceo/support', [CEOController::class, 'support'])->name('ceo.support');
    Route::get('/ceo/support/users/{id}', [CEOController::class, 'show'])->name('ceo.users.show');

    //coupons
    Route::get('/ceo/coupons', [CEOController::class, 'coupons'])->name('ceo.coupons');
    Route::get('/ceo/coupon/add', [CEOController::class, 'coupon_add'])->name('ceo.coupon.add');
    Route::post('/ceo/coupon/store', [CEOController::class, 'coupon_store'])->name('ceo.coupon.store');
    Route::get('/ceo/coupon/edit/{id}', [CEOController::class, 'coupon_edit'])->name('ceo.coupon.edit');
    Route::put('/ceo/coupon/update', [CEOController::class, 'coupon_update'])->name('ceo.coupon.update');
    Route::delete('/ceo/coupon/{id}/delete', [CEOController::class, 'coupon_delete'])->name('ceo.coupon.delete');
    Route::get('/ceo/search/coupon', [CEOController::class, 'search_coupon'])->name('ceo.search.coupon');

    //banner
    Route::get('/ceo/banners', [CEOController::class, 'banners'])->name('ceo.banners');
    Route::get('/ceo/banner/add', [CEOController::class, 'banner_add'])->name('ceo.banner.add');
    Route::post('/ceo/banner/store', [CEOController::class, 'banner_store'])->name('ceo.banner.store');
    Route::get('/ceo/banner/{id}/edit', [CEOController::class, 'banner_edit'])->name('ceo.banner.edit');
    Route::put('/ceo/banner/update', [CEOController::class, 'banner_update'])->name('ceo.banner.update');
    Route::delete('/ceo/banner/{id}/delete', [CEOController::class, 'banner_delete'])->name('ceo.banner.delete');
    Route::get('/ceo/search/banner', [CEOController::class, 'banner_search'])->name('ceo.search.banner');

    //discount
    Route::get('/ceo/discounts', [CEOController::class, 'discounts'])->name('ceo.discounts');
    Route::get('/ceo/discount/add', [CEOController::class, 'discount_add'])->name('ceo.discount.add');
    Route::post('/ceo/discount/store', [CEOController::class, 'discount_store'])->name('ceo.discount.store');
    Route::get('/ceo/discount/edit/{id}', [CEOController::class, 'discount_edit'])->name('ceo.discount.edit');
    Route::put('/ceo/discount/update', [CEOController::class, 'discount_update'])->name('ceo.discount.update');
    Route::delete('/ceo/discount/{id}/delete', [CEOController::class, 'discount_delete'])->name('ceo.discount.delete');
    Route::get('/ceo/search/discount', [CEOController::class, 'discount_search'])->name('ceo.search.discount');

    //Review
    Route::get('/ceo/reviews', [CEOController::class, 'reviews'])->name('ceo.reviews');
    Route::delete('/ceo/review/{id}/delete', [CEOController::class, 'review_delete'])->name('ceo.review.delete');
    Route::get('/ceo/search/review', [CEOController::class, 'search_review'])->name('ceo.search.review');
    Route::get('/ceo/review/{id}/show', [CEOController::class, 'review_show'])->name('ceo.review.show');

    //stock alert
    Route::get('/ceo/stock-alert', [CEOController::class, 'stock_alert'])->name('ceo.stock.alert');

    // Stock In
    Route::get('/ceo/stock-in', [CEOStockTransactionController::class, 'stockInForm'])->name('ceo.stock-in');
    Route::post('/ceo/stock-in', [CEOStockTransactionController::class, 'stockInStore'])->name('ceo.stock-in.store');

    // Stock Out
    Route::get('/ceo/stock-out', [CEOStockTransactionController::class, 'stockOutForm'])->name('ceo.stock-out');
    Route::post('/ceo/stock-out', [CEOStockTransactionController::class, 'stockOutStore'])->name('ceo.stock-out.store');

    // Stock Adjustment
    Route::get('/ceo/stock-adjustment', [CEOStockTransactionController::class, 'stockAdjustmentForm'])->name('ceo.stock-adjustment');
    Route::post('/ceo/stock-adjustment', [CEOStockTransactionController::class, 'stockAdjustmentStore'])->name('ceo.stock-adjustment.store');

    // Stock History 
    Route::get('/ceo/stock-history', [CEOStockTransactionController::class, 'history'])->name('ceo.stock-history');

    //Stock report
    Route::get('/ceo/stock-report', [CEOStockTransactionController::class, 'stockReport'])->name('ceo.stock-report');

    //stock report
    Route::get('/ceo/purchase-requests', [PurchaseRequestController::class, 'ceoIndex'])->name('ceo.purchase-requests.index');
    Route::get('/ceo/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('ceo.purchase-requests.create');
    Route::post('/ceo/purchase-requests', [PurchaseRequestController::class, 'store'])->name('ceo.purchase-requests.store');
    Route::get('/ceo/purchase-requests/{id}', [PurchaseRequestController::class, 'ceoShow'])->name('ceo.purchase-requests.show');
    Route::post('/ceo/purchase-requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('ceo.purchase-requests.approve');
    Route::post('/ceo/purchase-requests/{id}/reject', [PurchaseRequestController::class, 'reject'])->name('ceo.purchase-requests.reject');
    Route::get('/ceo/purchase-requests/{id}/export-excel', [PurchaseRequestController::class, 'exportExcel'])->name('ceo.purchase-requests.export.excel');
    Route::get('/ceo/purchase-requests/{id}/export-pdf', [PurchaseRequestController::class, 'exportPdf'])->name('ceo.purchase-requests.export.pdf');

    //shipping_zones
    Route::get('/ceo/shipping-zones', [CEOController::class, 'shipping_zones'])->name('ceo.shipping.zones');
    Route::post('/ceo/shipping-zones/update', [CEOController::class, 'shipping_zone_update'])->name('ceo.shipping.zones.update');

    //flash sale 
    Route::get('/ceo/flash-sales', [CEOController::class, 'flash_sales'])->name('ceo.flash.sales');
    Route::post('/ceo/flash-sales/store', [CEOController::class, 'flash_sale_store'])->name('ceo.flash.sales.store');
    Route::get('/ceo/flash-sales/{id}/edit', [CEOController::class, 'flash_sale_edit'])->name('ceo.flash.sales.edit');
    Route::put('/ceo/flash-sales/{id}/update', [CEOController::class, 'flash_sale_update'])->name('ceo.flash.sales.update');
    Route::delete('/ceo/flash-sales/{id}/delete', [CEOController::class, 'flash_sale_delete'])->name('ceo.flash.sales.delete');
    Route::put('/ceo/flash-sales/{id}/toggle', [CEOController::class, 'flash_sale_toggle'])->name('ceo.flash.sales.toggle');

    Route::get('/ceo/pos', [CEOPosController::class, 'index'])->name('ceo.pos');
    Route::get('/ceo/pos/search', [CEOPosController::class, 'search'])->name('ceo.pos.search');
    Route::post('/ceo/pos/checkout', [CEOPosController::class, 'checkout'])->name('ceo.pos.checkout');
    Route::get('/ceo/pos/receipt/{id}', [CEOPosController::class, 'receipt'])->name('ceo.pos.receipt');
    Route::get('/ceo/pos/history', [CEOPosController::class, 'history'])->name('ceo.pos.history');
    Route::put('/ceo/pos/{id}/cancel', [CEOPosController::class, 'cancel'])->name('ceo.pos.cancel');
    Route::get('ceo/pos/discounts', [CEOPosController::class, 'getDiscounts'])->name('ceo.pos.discounts');
    Route::get('/ceo/pos/payment/{id}', [CEOPosController::class, 'showAddPaymentForm'])->name('ceo.pos.payment');
    Route::post('/ceo/pos/payment/{id}', [CEOPosController::class, 'addPayment'])->name('ceo.pos.payment.submit');
    Route::get('/ceo/pos/order/{id}/edit',   [CEOPosController::class, 'edit_pos'])->name('ceo.pos.edit');
    Route::put('/ceo/pos/order/{id}/update', [CEOPosController::class, 'update_pos'])->name('ceo.pos.update');
    Route::get('/ceo/pos/payment-receipt/{id}', [CEOPosController::class, 'payment_receipt'])->name('ceo.pos.payment.receipts');

    Route::get('/ceo/reports', [CEOController::class, 'reports'])->name('ceo.reports');
    Route::get('/ceo/staff-reports', [CEOController::class, 'staff_reports'])->name('ceo.staff.reports');
    Route::get('/ceo/staff-reports/{id}/products', [CEOController::class, 'staff_report_products'])->name('ceo.staff.report.products');
    Route::get('/ceo/staff-reports/{id}/payments', [CEOController::class, 'staff_report_payments'])->name('ceo.staff.report.payments');


    //permission
    Route::get('/ceo/permissions', [PermissionController::class, 'index'])->name('ceo.permissions.index');
    Route::post('/ceo/permissions', [PermissionController::class, 'update'])->name('ceo.permissions.update');

    //staff report
    Route::get('/ceo/staff-reports', [CEOController::class, 'staff_reports'])->name('ceo.staff.reports');
    Route::get('/ceo/staff-reports/{id}/products', [CEOController::class, 'staff_report_products'])->name('ceo.staff.report.products');
    Route::get('/ceo/staff-reports/{id}/payments', [CEOController::class, 'staff_report_payments'])->name('ceo.staff.report.payments');

    //product report
    Route::get('/ceo/reports/products', [CEOController::class, 'product_reports'])->name('ceo.reports.products');
    Route::get('/reports/invoices', [CEOController::class, 'customer_invoices'])->name('ceo.reports.invoices');

    //summary report
    Route::get('/ceo/reports/summary', [CEOController::class, 'summary_report'])->name('ceo.reports.summary');
    //summary detail report
    Route::get('/ceo/reports/summary/detail', [CEOController::class, 'summary_detail'])->name('ceo.reports.summary.detail');

    //permission
    Route::get('/ceo/permissions', [PermissionController::class, 'index'])->name('ceo.permissions.index');
    Route::post('/ceo/permissions', [PermissionController::class, 'update'])->name('ceo.permissions.update');
});
