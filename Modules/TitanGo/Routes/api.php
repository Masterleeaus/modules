<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanGo\Http\Controllers\TechnicianJobController;
use Modules\TitanGo\Http\Controllers\TechnicianLocationController;
use Modules\TitanGo\Http\Controllers\TechnicianMediaController;
use Modules\TitanGo\Http\Controllers\TechnicianSyncController;

Route::middleware(['auth', 'role:technician'])
    ->prefix('api/technician')
    ->name('technician.')
    ->group(function () {
        // ── Job read ──────────────────────────────────────────────────────────
        Route::get('/jobs/today', [TechnicianJobController::class, 'today'])
            ->name('jobs.today');
        Route::get('/jobs/{job}', [TechnicianJobController::class, 'apiShow'])
            ->name('jobs.show');

        // ── Job mutations ─────────────────────────────────────────────────────
        Route::patch('/jobs/{job}/status', [TechnicianJobController::class, 'updateStatus'])
            ->name('jobs.update_status');
        Route::patch('/jobs/{job}/notes', [TechnicianJobController::class, 'updateNotes'])
            ->name('jobs.update_notes');
        Route::patch('/jobs/{job}/customer-notes', [TechnicianJobController::class, 'updateCustomerNotes'])
            ->name('jobs.update_customer_notes');
        Route::patch('/jobs/{job}/checklist/{item}', [TechnicianJobController::class, 'toggleChecklistItem'])
            ->name('jobs.checklist_item.toggle');

        // ── Line items ────────────────────────────────────────────────────────
        Route::post('/jobs/{job}/line-items', [TechnicianJobController::class, 'addLineItem'])
            ->name('jobs.line_items.store');
        Route::patch('/jobs/{job}/line-items/{lineItem}', [TechnicianJobController::class, 'updateLineItem'])
            ->name('jobs.line_items.update');
        Route::delete('/jobs/{job}/line-items/{lineItem}', [TechnicianJobController::class, 'deleteLineItem'])
            ->name('jobs.line_items.destroy');

        // ── Photos ────────────────────────────────────────────────────────────
        Route::post('/jobs/{job}/photos', [TechnicianMediaController::class, 'uploadPhoto'])
            ->name('jobs.photos.store');
        Route::delete('/jobs/{job}/photos/{attachment}', [TechnicianMediaController::class, 'deletePhoto'])
            ->name('jobs.photos.destroy');

        // ── Signature ─────────────────────────────────────────────────────────
        Route::post('/jobs/{job}/signature', [TechnicianMediaController::class, 'storeSignature'])
            ->name('jobs.signature.store');

        // ── Catalog ───────────────────────────────────────────────────────────
        Route::get('/catalog', [TechnicianJobController::class, 'catalogItems'])
            ->name('catalog.index');

        // ── Location ──────────────────────────────────────────────────────────
        Route::post('/location', [TechnicianLocationController::class, 'store'])
            ->name('location.store');

        // ── Offline batch sync ────────────────────────────────────────────────
        Route::post('/sync', [TechnicianSyncController::class, 'batch'])
            ->name('sync.batch');
    });
