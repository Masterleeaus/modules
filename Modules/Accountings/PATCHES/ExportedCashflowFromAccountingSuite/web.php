<?php

use Illuminate\Support\Facades\Route;
use Modules\AccountingCore\app\Http\Controllers\AccountingCoreController;
use Modules\AccountingCore\app\Http\Controllers\CategoryController;
use Modules\AccountingCore\app\Http\Controllers\DashboardController;
use Modules\AccountingCore\app\Http\Controllers\ReportsController;
use Modules\AccountingCore\app\Http\Controllers\TaxRateController;
use Modules\AccountingCore\app\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('accountingcore')->name('accountingcore.')->middleware(['auth', 'web'])->group(function () {
    // Main redirect
    Route::get('/', [AccountingCoreController::class, 'index'])->name('index');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/datatable', [TransactionController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
        Route::delete('/{id}/attachment', [TransactionController::class, 'deleteAttachment'])->name('delete-attachment');
        Route::get('/{id}/attachment/download/{fileId?}', [TransactionController::class, 'downloadAttachment'])->name('download-attachment');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/datatable', [CategoryController::class, 'indexAjax'])->name('datatable');
        Route::get('/search', [CategoryController::class, 'search'])->name('search');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
        Route::get('/export', [ReportsController::class, 'export'])->name('export');
        Route::get('/summary', [ReportsController::class, 'summary'])->name('summary');
        Route::get('/summary/export-pdf', [ReportsController::class, 'exportSummaryPdf'])->name('summary.export-pdf');
        Route::get('/cashflow', [ReportsController::class, 'cashflow'])->name('cashflow');
        Route::get('/cashflow/export-pdf', [ReportsController::class, 'exportCashflowPdf'])->name('cashflow.export-pdf');
        Route::get('/category-performance', [ReportsController::class, 'categoryPerformance'])->name('category-performance');
    });

    // Tax Rates
    Route::prefix('tax-rates')->name('tax-rates.')->group(function () {
        Route::get('/', [TaxRateController::class, 'index'])->name('index');
        Route::get('/datatable', [TaxRateController::class, 'getDataAjax'])->name('datatable');
        Route::get('/active', [TaxRateController::class, 'getActiveTaxRates'])->name('active');
        Route::get('/{taxRate}', [TaxRateController::class, 'getTaxRateAjax'])->name('show');
        Route::post('/', [TaxRateController::class, 'store'])->name('store');
        Route::put('/{taxRate}', [TaxRateController::class, 'update'])->name('update');
        Route::delete('/{taxRate}', [TaxRateController::class, 'destroy'])->name('destroy');
    });
});
