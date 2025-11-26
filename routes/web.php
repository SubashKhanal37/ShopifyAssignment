<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureShopifyInstalled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dev login route
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    session()->forget('shopify_domain');
    Cookie::queue(Cookie::forget('shopify_domain'));
    session()->invalidate();
    session()->regenerateToken();

    return response()->json(['success' => true, 'redirect' => route('login')]);
})->name('logout');

Route::middleware([EnsureShopifyInstalled::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/sync', [ProductController::class, 'sync'])->name('products.sync');

    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections/sync', [CollectionController::class, 'sync'])->name('collections.sync');

    Route::post('/orders/sync', [OrderController::class, 'sync'])->name('orders.sync');
});
