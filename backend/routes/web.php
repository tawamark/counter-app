<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivergenceController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::get('/divergences', [DivergenceController::class, 'index'])->name('divergences.index');
        Route::get('/reports/divergences.csv', [ReportController::class, 'divergences'])->name('reports.divergences');
        Route::post('/inventory-counts/{inventoryCount}/approve', [InventoryCountController::class, 'approve'])->name('inventory-counts.approve');
        Route::post('/inventory-counts/{inventoryCount}/finish', [InventoryCountController::class, 'finish'])->name('inventory-counts.finish');
        Route::resource('inventory-counts', InventoryCountController::class)->only(['create', 'store']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('role:admin,counter')->group(function (): void {
        Route::post('/inventory-counts/{inventoryCount}/items', [InventoryCountController::class, 'updateItems'])->name('inventory-counts.items.update');
        Route::resource('inventory-counts', InventoryCountController::class)->only(['index', 'show']);
    });

    Route::middleware('role:admin,stockist')->group(function (): void {
        Route::resource('products', ProductController::class)->only(['index']);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/stock.csv', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/movements.csv', [ReportController::class, 'movements'])->name('reports.movements');
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
