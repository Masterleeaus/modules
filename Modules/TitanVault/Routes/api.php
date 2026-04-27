<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanVault\Http\Controllers\VaultDocumentController;

/*
|--------------------------------------------------------------------------
| API Routes – TitanVault
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    // List all vault documents for the authenticated user's company.
    Route::get('titan-vault/documents', [VaultDocumentController::class, 'index'])
        ->name('api.titan-vault.documents.index');

    // Get a single vault document.
    Route::get('titan-vault/documents/{id}', [VaultDocumentController::class, 'show'])
        ->name('api.titan-vault.documents.show');
});
