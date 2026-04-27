<?php

use Illuminate\Support\Facades\Route;
use Modules\OnboardingPro\Http\Controllers\OnboardingProController;
use Modules\OnboardingPro\Http\Controllers\OnboardingFlowController;
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

/*
|--------------------------------------------------------------------------
| Onboarding Flows — user-facing & admin
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'account'])
    ->prefix('account')
    ->group(function () {
        Route::get('/onboarding/flow', [OnboardingFlowController::class, 'index'])->name('onboarding.flow.index');
        Route::post('/onboarding/flow/steps/{step}/complete', [OnboardingFlowController::class, 'complete'])->name('onboarding.flow.complete');
        Route::get('/admin/onboarding/flows', [OnboardingFlowController::class, 'adminIndex'])->name('onboarding.admin.flows.index');
        Route::post('/admin/onboarding/flows', [OnboardingFlowController::class, 'adminStore'])->name('onboarding.admin.flows.store');
        Route::get('/admin/onboarding/flows/create', function () {
            return view('onboardingpro::admin.flows.create');
        })->name('onboardingpro.admin.flows.create');
    });
