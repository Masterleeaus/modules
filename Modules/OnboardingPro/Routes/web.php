<?php

use Illuminate\Support\Facades\Route;
use Modules\OnboardingPro\Http\Controllers\OnboardingProController;
use Modules\OnboardingPro\Http\Controllers\Admin\BannerController;
use Modules\OnboardingPro\Http\Controllers\Admin\SurveyController;
use Modules\OnboardingPro\Http\Controllers\Admin\IntroductionStyleController;

/*
|--------------------------------------------------------------------------
| User-facing onboarding routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'account'])
    ->prefix('account/onboarding')
    ->as('onboardingpro.')
    ->group(function () {
        Route::get('/status',            [OnboardingProController::class, 'status'])->name('status');
        Route::post('/next-step',        [OnboardingProController::class, 'nextStep'])->name('next-step');
        Route::post('/complete',         [OnboardingProController::class, 'complete'])->name('complete');
        Route::post('/submit',           [OnboardingProController::class, 'submit'])->name('submit');
        Route::post('/dismiss/{banner}', [OnboardingProController::class, 'dismiss'])->name('dismiss');
    });

/*
|--------------------------------------------------------------------------
| Admin management routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'account'])
    ->prefix('account/onboarding/admin')
    ->as('onboardingpro.admin.')
    ->group(function () {

        // Banners
        Route::resource('banners', BannerController::class)->names('banners');

        // Surveys
        Route::resource('surveys', SurveyController::class)->names('surveys');

        // Introduction Styles
        Route::get('styles',                   [IntroductionStyleController::class, 'index'])->name('styles.index');
        Route::post('styles/{style}/activate', [IntroductionStyleController::class, 'activate'])->name('styles.activate');
    });
