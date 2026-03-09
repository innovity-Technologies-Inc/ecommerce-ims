<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return view('dashboard');
});

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('admin.products.show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/{product}/update', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/{product}/destroy', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    });

    Route::resource('categories', CategoryController::class)->names('admin.categories');
    Route::resource('brands', BrandController::class)->names('admin.brands');
    Route::resource('shipping-methods', ShippingMethodController::class)->names('admin.shipping_methods');

    Route::controller(SettingsController::class)->prefix('settings')->group(function () {
        Route::get('/general', 'generalSettings')->name('admin.settings.general');
        Route::post('/general/update', 'updateGeneralSettings')->name('admin.settings.general.update');
        Route::get('/mail', 'mailSettings')->name('admin.settings.mail');
        Route::post('/mail/update', 'updateMailSettings')->name('admin.settings.mail.update');
    });

    Route::resource('sliders', \App\Http\Controllers\SliderController::class)->names('admin.sliders');

    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{order}/destroy', [OrderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('sections')->group(function () {
        Route::get('/bestsellers', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'bestsellers'])->name('admin.sections.bestsellers');
        Route::get('/{sectionName}', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'editSection'])->name('admin.sections.edit');
        Route::post('/{sectionName}/update', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'updateSection'])->name('admin.sections.update');
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
});

Route::middleware(['auth:web'])->prefix('user')->controller(CustomerController::class)->group(function () {
    Route::get('my-account', 'accountInformation')->name('user.account');
    Route::put('profile-update', 'profileUpdate')->name('user.profile.update');
    Route::put('password-update', 'changePassword')->name('user.password.update');
    Route::put('address-update', 'addressUpdate')->name('user.address.update');

    Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
        Route::get('/', 'index')->name('user.wishlist.index');
        Route::post('/store', 'store')->name('user.wishlist.store');
        Route::delete('/{id}/destroy', 'destroy')->name('user.wishlist.destroy');
    });
});

require __DIR__.'/auth.php';
