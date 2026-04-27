<?php

use Illuminate\Support\Facades\Route;
use Modules\SupplyChain\Http\Controllers\Api\InventoryApiController;

Route::get('/ping', [InventoryApiController::class, 'ping'])->name('ping');
Route::get('/warehouses', [InventoryApiController::class, 'warehouses'])->name('warehouses');
Route::get('/items', [InventoryApiController::class, 'items'])->name('items');
Route::get('/stock', [InventoryApiController::class, 'stock'])->name('stock');
Route::get('/movements', [InventoryApiController::class, 'movements'])->name('movements');
Route::post('/movements', [InventoryApiController::class, 'storeMovement'])->name('movements.store');
Route::get('/transfers', [InventoryApiController::class, 'transfers'])->name('transfers');
