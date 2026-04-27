@php
    $user = auth()->user();
    $isFinanceAdmin = false;
    try {
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isFinanceAdmin = $user->hasRole('admin') || $user->hasRole('accountant') || $user->hasRole('owner');
            } elseif (method_exists($user, 'isAdmin')) {
                $isFinanceAdmin = (bool) $user->isAdmin();
            } elseif (property_exists($user, 'is_admin')) {
                $isFinanceAdmin = (bool) $user->is_admin;
            }
        }
    } catch (\Throwable $e) { $isFinanceAdmin = false; }
@endphp

@if (in_array('accountings', user_modules()))
    <x-menu-item icon="calculator" :text="__('accountings::app.menu.accounting')" :addon="false">
        <x-slot name="iconPath">
            <path d="M6 2.5h8A2.5 2.5 0 0 1 16.5 5v14A2.5 2.5 0 0 1 14 21.5H6A2.5 2.5 0 0 1 3.5 19V5A2.5 2.5 0 0 1 6 2.5Zm0 1.5A1 1 0 0 0 5 5v14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H6Z"/>
        </x-slot>

        <div class="accordionItemContent">
            @if (\Route::has('accountings.dashboard')) <x-sub-menu-item :link="route('accountings.dashboard')" text="Dashboard"/> @endif
            @if (\Route::has('cashflow.index')) <x-sub-menu-item :link="route('cashflow.index')" text="Overview"/> @endif
            @if (\Route::has('cashflow.runway_weekly')) <x-sub-menu-item :link="route('cashflow.runway_weekly')" text="Weekly Runway"/> @endif
            @if (\Route::has('cashflow.receivables')) <x-sub-menu-item :link="route('cashflow.receivables')" text="Money Owed To You"/> @endif
            @if (\Route::has('cashflow.payables')) <x-sub-menu-item :link="route('cashflow.payables')" text="Bills To Pay"/> @endif
            @if (\Route::has('cashflow.collections')) <x-sub-menu-item :link="route('cashflow.collections')" text="Collections Helper"/> @endif
            @if (\Route::has('cashflow.ar_aging')) <x-sub-menu-item :link="route('cashflow.ar_aging')" text="A/R Aging"/> @endif
            @if (\Route::has('cashflow.planner')) <x-sub-menu-item :link="route('cashflow.planner')" text="Weekly Planner"/> @endif
            @if (\Route::has('cashflow.top_overdue')) <x-sub-menu-item :link="route('cashflow.top_overdue')" text="Top 20 Overdue"/> @endif
            @if (\Route::has('acc-settings.cashflow')) <x-sub-menu-item :link="route('acc-settings.cashflow')" text="Cashflow Settings"/> @endif

            @if($isFinanceAdmin)
                @if (\Route::has('pnl.index')) <x-sub-menu-item :link="route('pnl.index')" :text="__('accountings::modules.pnl.pnl')"/> @endif
                @if (\Route::has('balance-sheet.index')) <x-sub-menu-item :link="route('balance-sheet.index')" :text="__('accountings::modules.bs.balanceSheet')"/> @endif
                @if (\Route::has('accountings.index')) <x-sub-menu-item :link="route('accountings.index')" :text="__('accountings::app.menu.accountings')"/> @endif
                @if (\Route::has('journal-type.index')) <x-sub-menu-item :link="route('journal-type.index')" :text="__('accountings::app.menu.journalType')"/> @endif
                @if (\Route::has('journal.index')) <x-sub-menu-item :link="route('journal.index')" :text="__('accountings::app.menu.journal')"/> @endif
            @endif
        </div>
    </x-menu-item>
@endif
