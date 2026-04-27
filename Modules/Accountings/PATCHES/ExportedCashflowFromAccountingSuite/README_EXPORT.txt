Exported Cashflow references

Copied from Master-AccountingSuite-AI-V1.1:
- cashflow.blade.php (reports cashflow UI)
- web.php (routes)
- ReportsController.php (data build)

These are NOT auto-wired into Accountings module yet (to avoid breaking app), but are provided as reference code to implement:
- cashflow report with cash/bank accounts classification
- transaction model based inflow/outflow
- optional cashflow forecast (budget suite)

Next steps to fully integrate:
1) Add bank/cash account tagging to acc_coa (e.g. is_cash_account boolean).
2) Implement CashflowService using Journald + bank tagging rather than description heuristic.
3) Add forecast: join budgets + recurring expenses to predict net cash.
