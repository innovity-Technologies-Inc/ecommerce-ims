<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return view('dashboard');
});

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');




});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');

});

Route::middleware(['auth:web'])->prefix('user')->controller(CustomerController::class)->group(function () {
    Route::get('my-account', 'accountInformation')->name('user.account');
    Route::put('profile-update', 'profileUpdate')->name('user.profile.update');
    Route::put('password-update', 'changePassword')->name('user.password.update');
    Route::put('address-update', 'addressUpdate')->name('user.address.update');

});


require __DIR__.'/auth.php';
