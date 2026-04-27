<?php



use Illuminate\Support\Facades\Route;
use Modules\Accountings\Http\Controllers\AccountingDashboardController;
use Modules\Accountings\Http\Controllers\CashflowController;
use Modules\Accountings\Http\Controllers\CashflowForecastController;
use Modules\Accountings\Http\Controllers\CashRunwayController;
use Modules\Accountings\Http\Controllers\CashRunwayWeeklyController;
use Modules\Accountings\Http\Controllers\WeeklyPlannerController;
use Modules\Accountings\Http\Controllers\ReceivablesController;
use Modules\Accountings\Http\Controllers\PayablesController;
use Modules\Accountings\Http\Controllers\CollectionsController;
use Modules\Accountings\Http\Controllers\InvoiceActionController;
use Modules\Accountings\Http\Controllers\CustomerConnectBridgeController;
use Modules\Accountings\Http\Controllers\AccountingController;
use Modules\Accountings\Http\Controllers\BalanceSheetController;
use Modules\Accountings\Http\Controllers\PnlController;
use Modules\Accountings\Http\Controllers\JournalTypeController;
use Modules\Accountings\Http\Controllers\JournalController;
use Modules\Accountings\Http\Controllers\AccSettingController;

Route::middleware(['web','auth'])->prefix('account')->group(function () {
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'account'], function () {

    Route::group(['prefix' => 'accountings'], function () {

        // Dashboard
        Route::get('dashboard', [AccountingDashboardController::class, 'index'])->name('accountings.dashboard');

        // Cashflow
        Route::get('cashflow', [CashflowController::class, 'index'])->name('cashflow.index');
        Route::get('cashflow/forecast', [CashflowForecastController::class, 'index'])->name('cashflow.forecast');
        Route::get('cashflow/runway', [CashRunwayController::class, 'index'])->name('cashflow.runway');
        Route::get('cashflow/runway-weekly', [CashRunwayWeeklyController::class, 'index'])->name('cashflow.runway_weekly');
        Route::get('cashflow/weekly-planner', [WeeklyPlannerController::class, 'index'])->name('cashflow.weekly_planner');
        Route::get('cashflow/receivables', [ReceivablesController::class, 'index'])->name('cashflow.receivables');
        Route::get('cashflow/payables', [PayablesController::class, 'index'])->name('cashflow.payables');
        Route::get('cashflow/collections', [CollectionsController::class, 'index'])->name('cashflow.collections');

        // Invoice follow-up action page (tradie workflow)
        Route::get('cashflow/invoice/{invoiceId}', [InvoiceActionController::class, 'show'])->name('cashflow.invoice_action');

        // CustomerConnect bridge (no comms in Accountings)
        Route::get('cashflow/invoice/{invoiceId}/customerconnect', [CustomerConnectBridgeController::class, 'invoiceFollowup'])->name('cashflow.invoice_customerconnect');
// Cashflow setup (budget + recurring expenses)
        Route::get('settings/cashflow', [AccSettingController::class, 'cashflowSetup'])->name('acc-settings.cashflow');
        Route::post('settings/cashflow', [AccSettingController::class, 'saveCashflowSetup'])->name('acc-settings.cashflow.save');
        Route::post('settings/cashflow/recurring', [AccSettingController::class, 'addRecurringExpense'])->name('acc-settings.cashflow.recurring.add');
        Route::get('settings/cashflow/recurring/{id}/toggle', [AccSettingController::class, 'toggleRecurringExpense'])->name('acc-settings.cashflow.recurring.toggle');

        // Core accounting
        Route::resource('chart', AccountingController::class)->names('accountings');
        Route::resource('balance-sheet', BalanceSheetController::class);
        Route::resource('pnl', PnlController::class);
        Route::resource('journal-type', JournalTypeController::class);

        Route::get('journal/download/{id}', [JournalController::class, 'download'])->name('journal.download');
        Route::post('journal/apply-quick-action', [JournalController::class, 'applyQuickAction'])->name('journal.apply_quick_action');
        Route::resource('journal', JournalController::class);

        // Module settings (existing)
        Route::resource('settings', AccSettingController::class)->names('acc-settings');
    });
});


// --- AUTO-ADDED SAFE ROUTES ---
Route::get('cashflow/ar-aging', [\Modules\Accountings\Http\Controllers\AccountingDashboardController::class, 'index'])->name('cashflow.ar_aging');
Route::get('cashflow/planner', [\Modules\Accountings\Http\Controllers\AccountingDashboardController::class, 'index'])->name('cashflow.planner');
Route::get('cashflow/top-overdue', [\Modules\Accountings\Http\Controllers\AccountingDashboardController::class, 'index'])->name('cashflow.top_overdue');
});

// --- PASS 3: Revenue + Job Linkage + GST/BAS ---
Route::middleware(['web', 'auth'])->prefix('account/accountings')->group(function () {
    Route::get('reports/gst', [\Modules\Accountings\Http\Controllers\GstController::class, 'index'])->name('accountings.reports.gst');
    Route::post('reports/gst/export', [\Modules\Accountings\Http\Controllers\GstController::class, 'export'])->name('accountings.reports.gst.export');
    Route::get('reports/job-profitability', [\Modules\Accountings\Http\Controllers\JobProfitabilityController::class, 'index'])->name('accountings.reports.job_profitability');
});

// --- PASS 4: Banking Hardening ---
Route::middleware(['web', 'auth'])->prefix('account/accountings')->group(function () {
    Route::get('banking/import', [\Modules\Accountings\Http\Controllers\BankImportController::class, 'index'])->name('accountings.banking.import');
    Route::post('banking/import', [\Modules\Accountings\Http\Controllers\BankImportController::class, 'upload'])->name('accountings.banking.upload');
    Route::post('banking/match', [\Modules\Accountings\Http\Controllers\BankImportController::class, 'match'])->name('accountings.banking.match');
    Route::get('period-locks', [\Modules\Accountings\Http\Controllers\PeriodLockController::class, 'index'])->name('accountings.period-locks.index');
    Route::post('period-locks', [\Modules\Accountings\Http\Controllers\PeriodLockController::class, 'store'])->name('accountings.period-locks.store');
    Route::delete('period-locks/{id}', [\Modules\Accountings\Http\Controllers\PeriodLockController::class, 'destroy'])->name('accountings.period-locks.destroy');
});

// --- PASS 5: Xero/MYOB/QuickBooks Sync ---
Route::middleware(['web', 'auth'])->prefix('account/accountings')->group(function () {
    Route::get('settings/sync', [\Modules\Accountings\Http\Controllers\AccountingsSyncController::class, 'index'])->name('accountings.settings.sync');
    Route::post('settings/sync', [\Modules\Accountings\Http\Controllers\AccountingsSyncController::class, 'sync'])->name('accountings.settings.sync.run');
});
