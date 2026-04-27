<?php

use Illuminate\Support\Facades\Route;
use Modules\InstantAds\Http\Controllers\AdminInstantAdsController;
use Modules\InstantAds\Http\Controllers\InstantAdsController;

Route::middleware(['web', 'auth'])
    ->prefix('account/instant-ads')
    ->name('instant-ads.')
    ->group(function () {
        Route::get('/', [InstantAdsController::class, 'index'])->name('index');
        Route::post('/generate', [InstantAdsController::class, 'generate'])->name('generate');
        Route::get('/images', [InstantAdsController::class, 'getImages'])->name('images');
        Route::get('/completed-images', [InstantAdsController::class, 'getCompletedImages'])->name('completed-images');
        Route::get('/community-images', [InstantAdsController::class, 'getCommunityImages'])->name('community.images');
        Route::post('/community-images/like', [InstantAdsController::class, 'toggleLike'])->name('community.images.like');
        Route::post('/community-images/publish', [InstantAdsController::class, 'togglePublish'])->name('community.images.publish');
        Route::post('/share/generate', [InstantAdsController::class, 'generateShareLink'])->name('share.generate');
        Route::get('/realtime', [InstantAdsController::class, 'realtimeIndex'])->name('realtime');
        Route::post('/realtime/generate', [InstantAdsController::class, 'generateRealtimeImage'])->name('realtime.generate');
        Route::get('/realtime/images', [InstantAdsController::class, 'getRealtimeImages'])->name('realtime.images');
    });

// Public share link (no auth required)
Route::middleware('web')
    ->prefix('account/instant-ads')
    ->name('instant-ads.')
    ->group(function () {
        Route::get('/share/{token}', [InstantAdsController::class, 'viewSharedImage'])->name('share.view');
    });

// Admin routes
Route::middleware(['web', 'auth'])
    ->prefix('account/instant-ads/admin')
    ->name('instant-ads.admin.')
    ->group(function () {
        Route::get('/settings', [AdminInstantAdsController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminInstantAdsController::class, 'updateSettings'])->name('settings.update');
        Route::get('/community', [AdminInstantAdsController::class, 'communityImages'])->name('community');
        Route::get('/publish-requests', [AdminInstantAdsController::class, 'publishRequests'])->name('publish-requests');
        Route::post('/publish-requests/{id}/approve', [AdminInstantAdsController::class, 'approveRequest'])->name('publish-requests.approve');
        Route::post('/publish-requests/{id}/reject', [AdminInstantAdsController::class, 'rejectRequest'])->name('publish-requests.reject');
    });
