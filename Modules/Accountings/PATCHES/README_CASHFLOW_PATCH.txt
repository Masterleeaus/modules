Accountings module patch:
- Restored missing Routes files (Routes/web.php, web-settings.php, api.php)
- Added Cashflow report page (route cashflow.index) and sidebar label "Cashflow"
- Sidebar now shows all primary links: Cashflow, Chart of Accounts, Journals, Journal Types, Profit & Loss, Balance Sheet, Settings
- Cashflow feature inspired by Master-AccountingSuite-AI (reports/cashflow) but simplified to work with acc_journalh/acc_journald movements.

Next optional exports from other suites:
- Bank accounts + transactions ingestion
- Reconciliation workflow
- Forecasting (budget vs actual)
