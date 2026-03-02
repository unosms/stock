<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/image/{path}', [ItemController::class, 'image'])->where('path', '.*')->name('items.image');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/purchasing', [PurchasingController::class, 'index'])->name('purchasing.index');
    Route::post('/purchasing', [PurchasingController::class, 'store'])->name('purchasing.store');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');

    Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/purchasing', [ReportsController::class, 'purchasing'])->name('reports.purchasing');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/timezone', [SettingsController::class, 'updateTimezone'])->name('settings.timezone');
    Route::post('/settings/currencies', [SettingsController::class, 'storeCurrency'])->name('settings.currencies.store');
    Route::post('/settings/currencies/default', [SettingsController::class, 'setDefaultCurrency'])->name('settings.currencies.default');

    Route::post('/sources', [SourceController::class, 'store'])->name('sources.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
