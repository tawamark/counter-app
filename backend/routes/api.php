<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryCountController;
use App\Http\Controllers\Api\MobileSummaryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:admin,stockist,counter')->group(function (): void {
        Route::get('/products/search', [ProductController::class, 'search']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
    });

    Route::middleware('role:admin,counter')->group(function (): void {
        Route::get('/mobile/summary', MobileSummaryController::class);
        Route::get('/inventory-counts', [InventoryCountController::class, 'index']);
        Route::get('/inventory-counts/{inventoryCount}', [InventoryCountController::class, 'show']);
        Route::get('/inventory-counts/{inventoryCount}/items', [InventoryCountController::class, 'items']);
        Route::post('/inventory-counts/{inventoryCount}/items', [InventoryCountController::class, 'updateItems']);
        Route::post('/inventory-counts/{inventoryCount}/sync', [InventoryCountController::class, 'updateItems']);
    });
});
