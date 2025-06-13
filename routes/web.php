<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController; // Add this import

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware(['auth']); // Assuming only authenticated users can post

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
// Optional: Route::get('/cart', [CartController::class, 'view'])->name('cart.view');

Route::post('/wishlist/toggle', [WishlistController::class, 'addOrRemove'])
    ->name('wishlist.toggle')
    ->middleware('auth');
// Optional: Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index')->middleware('auth');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
