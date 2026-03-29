<?php

use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponApplyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

// Route to trigger the flash sale expiry check (useful for Web-based Cron Jobs)
Route::get('/check-flash-sale-expiry', function (\App\Services\FlashSaleService $service) {
    $flashSale = $service->getFlashSale();
    $service->syncAllDiscounts($flashSale);

    return 'Flash sale expiry check executed successfully at '.now()->toDateTimeString();
});

Route::get('/test', function () {
    return view('dashboard');
});

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:dashboard.view');

    Route::prefix('products')->middleware('permission:products.view')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/low-stock', [DashboardController::class, 'lowStockProducts'])->name('admin.products.low-stock');
        Route::get('/best-selling', [DashboardController::class, 'bestSellingProducts'])->name('admin.products.best-selling');
        Route::get('/create', [ProductController::class, 'create'])->name('admin.products.create')->middleware('permission:products.create');
        Route::get('/import', [ProductController::class, 'importForm'])->name('admin.products.import')->middleware('permission:products.create');
        Route::post('/import', [ProductController::class, 'import'])->name('admin.products.import.store')->middleware('permission:products.create');
        Route::get('/import-template', [ProductController::class, 'downloadTemplate'])->name('admin.products.import.template')->middleware('permission:products.create');
        Route::post('/store', [ProductController::class, 'store'])->name('admin.products.store')->middleware('permission:products.create');
        Route::get('/{product}', [ProductController::class, 'show'])->name('admin.products.show');
        Route::post('/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status')->middleware('permission:products.edit');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit')->middleware('permission:products.edit');
        Route::put('/{product}/update', [ProductController::class, 'update'])->name('admin.products.update')->middleware('permission:products.edit');
        Route::delete('/{product}/destroy', [ProductController::class, 'destroy'])->name('admin.products.destroy')->middleware('permission:products.delete');
    });

    Route::prefix('categories')->middleware('permission:category.view')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/create', [CategoryController::class, 'create'])->name('admin.categories.create')->middleware('permission:category.create');
        Route::post('/', [CategoryController::class, 'store'])->name('admin.categories.store')->middleware('permission:category.create');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('admin.categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit')->middleware('permission:category.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('admin.categories.update')->middleware('permission:category.edit');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy')->middleware('permission:category.delete');
        Route::post('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status')->middleware('permission:category.edit');
    });

    Route::prefix('brands')->middleware('permission:brand.view')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('admin.brands.index');
        Route::get('/create', [BrandController::class, 'create'])->name('admin.brands.create')->middleware('permission:brand.create');
        Route::post('/', [BrandController::class, 'store'])->name('admin.brands.store')->middleware('permission:brand.create');
        Route::get('/{brand}', [BrandController::class, 'show'])->name('admin.brands.show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('admin.brands.edit')->middleware('permission:brand.edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('admin.brands.update')->middleware('permission:brand.edit');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy')->middleware('permission:brand.delete');
        Route::post('/{id}/toggle-status', [BrandController::class, 'toggleStatus'])->name('admin.brands.toggle-status')->middleware('permission:brand.edit');
    });

    Route::prefix('shipping-methods')->middleware('permission:shipping_methods.view')->group(function () {
        Route::get('/', [ShippingMethodController::class, 'index'])->name('admin.shipping_methods.index');
        Route::get('/create', [ShippingMethodController::class, 'create'])->name('admin.shipping_methods.create')->middleware('permission:shipping_methods.create');
        Route::post('/', [ShippingMethodController::class, 'store'])->name('admin.shipping_methods.store')->middleware('permission:shipping_methods.create');
        Route::get('/{shippingMethod}', [ShippingMethodController::class, 'show'])->name('admin.shipping_methods.show');
        Route::get('/{shippingMethod}/edit', [ShippingMethodController::class, 'edit'])->name('admin.shipping_methods.edit')->middleware('permission:shipping_methods.edit');
        Route::put('/{shippingMethod}', [ShippingMethodController::class, 'update'])->name('admin.shipping_methods.update')->middleware('permission:shipping_methods.edit');
        Route::delete('/{shippingMethod}', [ShippingMethodController::class, 'destroy'])->name('admin.shipping_methods.destroy')->middleware('permission:shipping_methods.delete');
        Route::post('/{shippingMethod}/toggle-status', [ShippingMethodController::class, 'toggleStatus'])->name('admin.shipping_methods.toggle-status')->middleware('permission:shipping_methods.edit');
    });

    Route::controller(SettingsController::class)->prefix('settings')->middleware('permission:settings.view')->group(function () {
        Route::get('/general', 'generalSettings')->name('admin.settings.general');
        Route::post('/general/update', 'updateGeneralSettings')->name('admin.settings.general.update')->middleware('permission:settings.edit');
        Route::get('/contact', 'contactSettings')->name('admin.settings.contact');
        Route::post('/contact/update', 'updateContactSettings')->name('admin.settings.contact.update')->middleware('permission:settings.edit');
    });

    Route::prefix('sliders')->middleware('permission:sliders.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\SliderController::class, 'index'])->name('admin.sliders.index');
        Route::get('/create', [\App\Http\Controllers\SliderController::class, 'create'])->name('admin.sliders.create')->middleware('permission:sliders.create');
        Route::post('/', [\App\Http\Controllers\SliderController::class, 'store'])->name('admin.sliders.store')->middleware('permission:sliders.create');
        Route::get('/{slider}', [\App\Http\Controllers\SliderController::class, 'show'])->name('admin.sliders.show');
        Route::get('/{slider}/edit', [\App\Http\Controllers\SliderController::class, 'edit'])->name('admin.sliders.edit')->middleware('permission:sliders.edit');
        Route::put('/{slider}', [\App\Http\Controllers\SliderController::class, 'update'])->name('admin.sliders.update')->middleware('permission:sliders.edit');
        Route::delete('/{slider}', [\App\Http\Controllers\SliderController::class, 'destroy'])->name('admin.sliders.destroy')->middleware('permission:sliders.delete');
    });

    // Customer Management
    Route::prefix('customers')->middleware('permission:customers.view')->name('admin.customers.')->controller(AdminCustomerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:customers.edit');
    });

    Route::prefix('contact-messages')->middleware('permission:contact_messages.view')->name('admin.contact_messages.')->controller(AdminContactMessageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/read', 'markAsRead')->name('read');
        Route::post('/{id}/toggle-read', 'toggleReadStatus')->name('toggle-read');
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:contact_messages.delete');
    });

    Route::prefix('roles')->middleware('permission:roles.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('admin.roles.create')->middleware('permission:roles.create');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store')->middleware('permission:roles.create');
        Route::get('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('admin.roles.show');
        Route::get('/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin.roles.edit')->middleware('permission:roles.edit');
        Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update')->middleware('permission:roles.edit');
        Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('permission:roles.delete');
    });

    Route::prefix('promotions')->group(function () {
        Route::prefix('coupons')->middleware('permission:coupons.view')->group(function () {
            Route::get('/', [\App\Http\Controllers\CouponController::class, 'index'])->name('admin.coupons.index');
            Route::get('/create', [\App\Http\Controllers\CouponController::class, 'create'])->name('admin.coupons.create')->middleware('permission:coupons.create');
            Route::post('/', [\App\Http\Controllers\CouponController::class, 'store'])->name('admin.coupons.store')->middleware('permission:coupons.create');
            Route::get('/{coupon}', [\App\Http\Controllers\CouponController::class, 'show'])->name('admin.coupons.show');
            Route::get('/{coupon}/edit', [\App\Http\Controllers\CouponController::class, 'edit'])->name('admin.coupons.edit')->middleware('permission:coupons.edit');
            Route::put('/{coupon}', [\App\Http\Controllers\CouponController::class, 'update'])->name('admin.coupons.update')->middleware('permission:coupons.edit');
            Route::delete('/{coupon}', [\App\Http\Controllers\CouponController::class, 'destroy'])->name('admin.coupons.destroy')->middleware('permission:coupons.delete');
            Route::post('/{coupon}/toggle-status', [\App\Http\Controllers\CouponController::class, 'toggleStatus'])->name('admin.coupons.toggle-status')->middleware('permission:coupons.edit');
            Route::get('/{coupon}/history', [\App\Http\Controllers\CouponController::class, 'usageHistory'])->name('admin.coupons.history');
        });

        Route::prefix('flash-sale')->middleware('permission:flash_sale.view')->name('admin.flash_sale.')->controller(\App\Http\Controllers\Admin\FlashSaleController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::post('/update', 'update')->name('update')->middleware('permission:flash_sale.edit');
            Route::get('/search-products', 'searchProducts')->name('search_products');
        });
    });

    Route::prefix('orders')->middleware('permission:orders.view')->name('admin.orders.')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::put('/{order}/update-status', 'updateStatus')->name('update-status')->middleware('permission:orders.edit');
        Route::post('/{order}/generate-invoice', 'generateInvoice')->name('generate-invoice');
        Route::post('/{order}/regenerate-invoice', 'regenerateInvoice')->name('regenerate-invoice');
        Route::get('/{order}/view-invoice', 'viewInvoice')->name('view-invoice');
    });

    Route::prefix('returns')->middleware('permission:returns.view')->name('admin.returns.')->controller(\App\Http\Controllers\Admin\ReturnController::class)->group(function () {
        Route::get('/requests', 'requests')->name('requests');
        Route::get('/requests/{id}', 'showRequest')->name('show_request');
        Route::put('/requests/{id}/update-status', 'updateStatus')->name('update_status')->middleware('permission:returns.edit');
        Route::post('/requests/{id}/receive', 'receive')->name('receive')->middleware('permission:returns.edit');
        Route::get('/returned-products', 'returnedProducts')->name('returned_products');
        Route::get('/wastages', 'wastages')->name('wastages');
    });

    Route::prefix('sections')->middleware('permission:homepage_sections.view')->group(function () {
        Route::get('/bestsellers', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'bestsellers'])->name('admin.sections.bestsellers');
        Route::get('/{sectionName}', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'editSection'])->name('admin.sections.edit');
        Route::post('/{sectionName}/update', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'updateSection'])->name('admin.sections.update')->middleware('permission:homepage_sections.edit');
    });

    Route::prefix('inventory')->group(function () {
        Route::prefix('warehouses')->middleware('permission:warehouse.view')->controller(\App\Http\Controllers\Admin\WarehouseController::class)->group(function () {
            Route::get('/', 'index')->name('admin.warehouses.index');
            Route::get('/create', 'create')->name('admin.warehouses.create')->middleware('permission:warehouse.create');
            Route::post('/', 'store')->name('admin.warehouses.store')->middleware('permission:warehouse.create');
            Route::get('/{warehouse}/edit', 'edit')->name('admin.warehouses.edit')->middleware('permission:warehouse.edit');
            Route::put('/{warehouse}', 'update')->name('admin.warehouses.update')->middleware('permission:warehouse.edit');
            Route::delete('/{warehouse}', 'destroy')->name('admin.warehouses.destroy')->middleware('permission:warehouse.delete');
        });

        Route::prefix('suppliers')->middleware('permission:supplier.view')->controller(\App\Http\Controllers\Admin\SupplierController::class)->group(function () {
            Route::get('/', 'index')->name('admin.suppliers.index');
            Route::get('/create', 'create')->name('admin.suppliers.create')->middleware('permission:supplier.create');
            Route::post('/', 'store')->name('admin.suppliers.store')->middleware('permission:supplier.create');
            Route::get('/{supplier}/edit', 'edit')->name('admin.suppliers.edit')->middleware('permission:supplier.edit');
            Route::put('/{supplier}', 'update')->name('admin.suppliers.update')->middleware('permission:supplier.edit');
            Route::delete('/{supplier}', 'destroy')->name('admin.suppliers.destroy')->middleware('permission:supplier.delete');
        });

        Route::prefix('purchase-orders')->middleware('permission:po.view')->controller(\App\Http\Controllers\Admin\PurchaseOrderController::class)->group(function () {
            Route::get('/', 'index')->name('admin.inventory.po.index');
            Route::get('/create', 'create')->name('admin.inventory.po.create')->middleware('permission:po.create');
            Route::post('/', 'store')->name('admin.inventory.po.store')->middleware('permission:po.create');
            Route::get('/{po}', 'show')->name('admin.inventory.po.show');
            Route::get('/{po}/receive', 'receiveForm')->name('admin.inventory.po.receive')->middleware('permission:po.edit');
            Route::post('/{po}/receive', 'processReceive')->name('admin.inventory.po.process-receive')->middleware('permission:po.edit');
            Route::get('/{po}/edit', 'edit')->name('admin.inventory.po.edit')->middleware('permission:po.edit');
            Route::put('/{po}', 'update')->name('admin.inventory.po.update')->middleware('permission:po.edit');
            Route::put('/{po}/status', 'updateStatus')->name('admin.inventory.po.update-status')->middleware('permission:po.edit');
            Route::delete('/{po}', 'destroy')->name('admin.inventory.po.destroy')->middleware('permission:po.delete');
        });

        // Inventory Reports (Stock, Damaged & Batches)
        Route::prefix('inventory-reports')->middleware('permission:inventory.view')->controller(\App\Http\Controllers\Admin\InventoryReportController::class)->group(function () {
            Route::get('/stock', 'stock')->name('admin.inventory.stock.index');
            Route::get('/stock/{id}', 'productStockDetails')->name('admin.inventory.stock.show');
            Route::get('/damaged-products', 'damaged')->name('admin.inventory.damaged.index');
            Route::get('/batches', 'batches')->name('admin.inventory.batches.index');
            Route::get('/batches/{batch}', 'showBatch')->name('admin.inventory.batches.show');
        });

        Route::prefix('allocation')->middleware('permission:inventory.allocate')->controller(\App\Http\Controllers\Admin\InventoryAllocationController::class)->group(function () {
            Route::get('/', 'index')->name('admin.inventory.allocation.index');
            Route::get('/create', 'create')->name('admin.inventory.allocation.create');
            Route::post('/', 'store')->name('admin.inventory.allocation.store');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/products', 'products')->name('client.products.index');
    Route::get('/product/{slug}', 'productDetails')->name('client.products.details');
    Route::match(['get', 'post'], '/track-order', 'trackOrder')->name('client.track_order');
    Route::get('/order/{order_id}/invoice', 'publicInvoice')->name('client.public_invoice');
    Route::get('/contact', 'contact')->name('client.contact');
    Route::post('/contact/send', 'storeContactMessage')->name('client.contact.send');
    Route::prefix('returns')->name('client.returns.')->controller(\App\Http\Controllers\Client\ReturnController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/order-details', 'getOrderDetails')->name('order_details');
        Route::post('/store', 'store')->name('store');
        Route::get('/track', 'track')->name('track');
    });
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::post('/update', [CartController::class, 'updateQuantity'])->name('update');
    Route::post('/remove', [CartController::class, 'removeItem'])->name('remove');
    Route::post('/update-shipping', [CartController::class, 'updateShippingMethod'])->name('update_shipping');
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/store', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('success');
    Route::post('/apply-coupon', [CouponApplyController::class, 'apply'])->name('apply_coupon');
    Route::post('/remove-coupon', [CouponApplyController::class, 'remove'])->name('remove_coupon');
});

Route::middleware(['auth:web', 'verified'])->prefix('user')->controller(CustomerController::class)->group(function () {
    Route::get('my-account', 'accountInformation')->name('user.account');
    Route::put('profile-update', 'profileUpdate')->name('user.profile.update');
    Route::put('password-update', 'changePassword')->name('user.password.update');
    Route::put('address-update', 'addressUpdate')->name('user.address.update');
    Route::get('orders', 'orderHistory')->name('user.orders');
    Route::get('orders/{orderId}', 'orderDetails')->name('user.order_details');
    Route::get('orders/{orderId}/invoice', 'viewInvoice')->name('user.view_invoice');

    Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
        Route::get('/', 'index')->name('user.wishlist.index');
        Route::post('/store', 'store')->name('user.wishlist.store');
        Route::delete('/{id}/destroy', 'destroy')->name('user.wishlist.destroy');
    });
});

require __DIR__.'/auth.php';
