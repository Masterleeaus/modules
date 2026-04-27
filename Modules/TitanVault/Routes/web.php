<?php

use Illuminate\Support\Facades\Route;
use Modules\TitanVault\Http\Controllers\VaultDocumentController;
use Modules\TitanVault\Http\Controllers\VaultShareController;
use Modules\TitanVault\Http\Controllers\VaultApprovalController;
use Modules\TitanVault\Http\Controllers\VaultCommentController;
use Modules\TitanVault\Http\Controllers\ContentManagerSettingsController;
use Modules\TitanVault\Http\Controllers\VaultProofController;
use Modules\TitanVault\Http\Controllers\VaultComplianceController;

/*
|--------------------------------------------------------------------------
| Web Routes – TitanVault
|--------------------------------------------------------------------------
*/

// Authenticated vault routes
Route::group(['middleware' => 'auth', 'prefix' => 'account', 'as' => 'titan-vault.'], function () {

    // Document vault (CRUD)
    Route::resource('vault/documents', VaultDocumentController::class)->names([
        'index'   => 'documents.index',
        'create'  => 'documents.create',
        'store'   => 'documents.store',
        'show'    => 'documents.show',
        'edit'    => 'documents.edit',
        'update'  => 'documents.update',
        'destroy' => 'documents.destroy',
    ]);

    Route::post('vault/documents/{id}/archive', [VaultDocumentController::class, 'archive'])
        ->name('documents.archive');

    Route::post('vault/documents/{id}/send-for-review', [VaultDocumentController::class, 'sendForReview'])
        ->name('documents.send-for-review');

    Route::get('vault/documents/{id}/versions', [VaultDocumentController::class, 'versions'])
        ->name('documents.versions');

    Route::post('vault/documents/{id}/versions/{versionId}/restore', [VaultDocumentController::class, 'restoreVersion'])
        ->name('documents.versions.restore');

    // Share links
    Route::get('vault/documents/{id}/links', [VaultShareController::class, 'index'])
        ->name('links.index');

    Route::post('vault/documents/{id}/links', [VaultShareController::class, 'generate'])
        ->name('links.generate');

    Route::delete('vault/documents/{id}/links/{linkId}', [VaultShareController::class, 'revoke'])
        ->name('links.revoke');

    // Approvals
    Route::get('vault/documents/{id}/approvals', [VaultApprovalController::class, 'index'])
        ->name('approvals.index');

    Route::delete('vault/documents/{id}/approvals/{approvalId}', [VaultApprovalController::class, 'destroy'])
        ->name('approvals.destroy');

    // Comments
    Route::post('vault/documents/{id}/comments', [VaultCommentController::class, 'store'])
        ->name('comments.store');

    Route::post('vault/documents/{id}/comments/{commentId}/resolve', [VaultCommentController::class, 'resolve'])
        ->name('comments.resolve');

    Route::delete('vault/documents/{id}/comments/{commentId}', [VaultCommentController::class, 'destroy'])
        ->name('comments.destroy');

    // Settings
    Route::get('admin/titan-vault/settings', [ContentManagerSettingsController::class, 'index'])
        ->name('settings.index');

    Route::post('admin/titan-vault/settings', [ContentManagerSettingsController::class, 'update'])
        ->name('settings.update');

    // Compliance management
    Route::get('vault/compliance', [VaultComplianceController::class, 'index'])
        ->name('compliance.index');

    Route::post('vault/compliance', [VaultComplianceController::class, 'store'])
        ->name('compliance.store');

    Route::get('vault/compliance/dashboard', [VaultComplianceController::class, 'dashboard'])
        ->name('compliance.dashboard');

    // Proof packs
    Route::post('vault/proof-packs', [VaultProofController::class, 'createPack'])
        ->name('proof_packs.create');

    Route::post('vault/proof-packs/{pack}/send', [VaultProofController::class, 'sendPack'])
        ->name('proof_packs.send');
});

// Public proof review routes (no auth required)
Route::group(['prefix' => 'vault/proof', 'as' => 'titan-vault.proof.'], function () {
    Route::get('{token}', [VaultProofController::class, 'show'])->name('show');
    Route::post('{token}/password', [VaultProofController::class, 'password'])->name('password');
    Route::post('{token}/approve', [VaultProofController::class, 'approve'])->name('approve');
    Route::post('{token}/revision', [VaultProofController::class, 'requestRevision'])->name('revision');
});

// Public client proof-pack approval routes (no auth required)
Route::get('vault/approve/{token}', [VaultProofController::class, 'approvePack'])
    ->name('titan-vault.client.approve');

Route::post('vault/approve/{token}', [VaultProofController::class, 'clientApprove'])
    ->name('titan-vault.client.approve.submit');
