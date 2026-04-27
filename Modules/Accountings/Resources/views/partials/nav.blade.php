@php
    $current = request()->route()?->getName();
@endphp
<div class="mb-3">
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'accountings.dashboard') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('accountings.dashboard') }}">Dashboard</a>
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'bills.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('bills.index') }}">Bills</a>
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'expenses.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('expenses.index') }}">Expenses</a>
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'receipts.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('receipts.index') }}">Receipts</a>

        <a class="btn btn-sm {{ str_starts_with((string)$current, 'cashflow.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('cashflow.index') }}">Cashflow</a>

        <a class="btn btn-sm {{ str_starts_with((string)$current, 'banking.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('banking.import.index') }}">Banking</a>

        <a class="btn btn-sm {{ str_starts_with((string)$current, 'accountings.reports') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('accountings.reports.gst') }}">Reports</a>
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'journal.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('journal.index') }}">Journals</a>

        <a class="btn btn-sm {{ str_starts_with((string)$current, 'acc-settings.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('acc-settings.index') }}">Settings</a>
        <a class="btn btn-sm {{ str_starts_with((string)$current, 'accountings.audit.') ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('accountings.audit.index') }}">Audit</a>
    </div>
</div>
