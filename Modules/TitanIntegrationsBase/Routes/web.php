<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanIntegrations\Http\Controllers\ApiTokenController;
use Modules\TitanIntegrations\Http\Controllers\ConnectionController;
use Modules\TitanIntegrations\Http\Controllers\IntegrationsController;
use Modules\TitanIntegrations\Http\Controllers\WebhookEndpointController;

Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {

    // Integration dashboard
    Route::get('titan-integrations', [IntegrationsController::class, 'index'])
        ->name('titan-integrations.index');

    // Connect / disconnect
    Route::get('titan-integrations/{provider}/connect', [IntegrationsController::class, 'showConnect'])
        ->name('titan-integrations.connect.show');
    Route::post('titan-integrations/{provider}/connect', [IntegrationsController::class, 'connect'])
        ->name('titan-integrations.connect');
    Route::post('titan-integrations/{provider}/disconnect', [IntegrationsController::class, 'disconnect'])
        ->name('titan-integrations.disconnect');

    // OAuth flow
    Route::get('titan-integrations/{provider}/oauth/redirect', [ConnectionController::class, 'redirect'])
        ->name('titan-integrations.oauth.redirect');
    Route::get('titan-integrations/{provider}/oauth/callback', [ConnectionController::class, 'callback'])
        ->name('titan-integrations.oauth.callback');

    // Activity log
    Route::get('titan-integrations/logs', [IntegrationsController::class, 'logs'])
        ->name('titan-integrations.logs');

    // API Tokens
    Route::get('titan-integrations/api-tokens', [ApiTokenController::class, 'index'])
        ->name('titan-integrations.api-tokens.index');
    Route::post('titan-integrations/api-tokens', [ApiTokenController::class, 'store'])
        ->name('titan-integrations.api-tokens.store');
    Route::delete('titan-integrations/api-tokens/{id}', [ApiTokenController::class, 'destroy'])
        ->name('titan-integrations.api-tokens.destroy');

    // Outbound Webhook Endpoints
    Route::get('titan-integrations/webhooks', [WebhookEndpointController::class, 'index'])
        ->name('titan-integrations.webhooks.index');
    Route::post('titan-integrations/webhooks', [WebhookEndpointController::class, 'store'])
        ->name('titan-integrations.webhooks.store');
    Route::delete('titan-integrations/webhooks/{id}', [WebhookEndpointController::class, 'destroy'])
        ->name('titan-integrations.webhooks.destroy');
    Route::post('titan-integrations/webhooks/{id}/test', [WebhookEndpointController::class, 'test'])
        ->name('titan-integrations.webhooks.test');
    Route::get('titan-integrations/webhooks/{id}/logs', [WebhookEndpointController::class, 'logs'])
        ->name('titan-integrations.webhooks.logs');
});

// iCal feed (public, token-signed)
Route::get('titan-integrations/ical/{company}/{token}', [ConnectionController::class, 'icalFeed'])
    ->name('titan-integrations.ical');
