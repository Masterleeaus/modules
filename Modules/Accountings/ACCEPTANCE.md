# Accountings - Cleaning Accounting Module - Acceptance Checklist (Pass 5)

## Install / Upgrade
- [ ] Module ZIP uploaded + unzipped to `Modules/Accountings`
- [ ] `php artisan module:migrate Accountings` runs without errors
- [ ] `php artisan optimize:clear` runs without errors

## Navigation
- [ ] Any Accountings page shows the top navigation buttons (Dashboard, Bills, Expenses, Receipts, Cashflow, Banking, Reports, Journals, Settings, Audit)

## Banking
- [ ] Create a bank account
- [ ] Import a CSV (date/description/amount) and see transactions
- [ ] Create a reconciliation for a date range and close it

## Matching Hook
- [ ] On reconciliation detail page, match a bank transaction to:
  - [ ] a Bill ID
  - [ ] an Expense ID
  - [ ] an Invoice ID
- [ ] Remove a match

## Audit
- [ ] Audit page loads and shows entries for imports/reconciliations/settings where available
- [ ] Filters (action/from/to) work

## Tenant Safety
- [ ] All new/updated data is scoped by `company_id` and `user_id`
