<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified', 'role:administrator,customer'])->group(function () { // Added 'role:administrator,customer'
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Admin-specific routes
Route::middleware(['auth', 'role:administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        // For this example, we'll return a simple text response.
        // In a real application, you would render an admin dashboard view.
        // return Inertia::render('Admin/Dashboard'); // Example for Inertia
        return response('Admin Dashboard');
    })->name('dashboard');
    // Add other admin-specific routes here, for example:
    // Route::get('/users', function () { return response('Admin Users Page'); })->name('users');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
