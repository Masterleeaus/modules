# Accountings (Pass 2)

This is the **Accountings** module upgraded to **Pass 2 (cleaning accounting workflows + UI)** for Titan / Worksuite.

## What Pass 0 changes (baseline + safety)

### 1) Route hygiene
All module routes now live inside the authenticated `account/accountings/*` route group.

### 2) Tenant scoping (MVP)
This Pass adds a defensive global scope trait:

- `Modules/Accountings/Traits/HasUserScope.php`

If a table has a `user_id` column, models automatically:
- scope reads to the authenticated user
- auto-fill `user_id` on create

Models that already used `HasCompany` now also use `HasUserScope`.

### 3) Service-layer safety
Cashflow/AR/AP services now apply `company_id` and (if present) `user_id` filters when reading from `invoices`, `expenses`, and common bank tables.

## What Pass 1 adds (data model + scaffolding)

### 1) MVP tenant enforcement (`company_id` + `user_id`)
- Adds `user_id` to core `acc_*` tables (COA, journals, mappings, journal types)
- Adds `company_id` and `user_id` to cashflow planning tables:
  - `acc_recurring_expenses`
  - `acc_cashflow_budgets`

### 2) Cleaning accounting support tables (minimal)
These are scaffolding tables to enable Pass 2 workflows & UI:
- `acc_vendors` (suppliers / subcontractors)
- `acc_tax_codes` (AU-ready GST codes)
- `acc_service_lines` (Residential, Bond, Carpet, Pressure, Pool, Commercial)
- `acc_bills`, `acc_bill_lines` (accounts payable)
- `acc_receipts` (evidence attachments)
- `acc_job_costs` (ties costs to a job reference)

### 3) New Eloquent entities
Added entity models under `Modules/Accountings/Entities` for the tables above.

## What Pass 2 adds (workflows + UI)

#### 1) Bills workflow (Accounts Payable)
- Routes: `account/accountings/bills/*`
- Create bills with multiple lines (qty x unit), apply tax code, classify by service line
- Optional `job_ref` per line creates an `acc_job_costs` record for profitability tracking

#### 2) Quick expenses workflow
- Routes: `account/accountings/expenses/*`
- Fast capture for day-to-day spend (fuel, chemicals, parking, small tools)
- Optional `job_ref` creates an `acc_job_costs` row

#### 3) Receipts / evidence capture
- Routes: `account/accountings/receipts/*`
- Stores metadata + file path/URL (file storage is handled by the host app)

#### 4) Reports
- GST Summary: `account/accountings/reports/gst`
  - GST paid is derived from bill lines + expense tax
  - GST collected is **best effort** from the host `invoices` table (if present)
- Job Profitability: `account/accountings/reports/job-profitability`
  - Pass 2 shows **costs by job_ref** (revenue linkage comes in Pass 3)

#### 5) Defaults seeder
- Adds template tax codes and service lines (company_id/user_id NULL template rows)

## Notes
- Pass 2 keeps the UI deliberately simple to ship functionality fast.
- Pass 3 will integrate revenue/jobs from your ops module, and add BAS-ready exports.


## Pass 4 (Hardening)
- Banking import (CSV) + bank transactions
- Bank reconciliation (draft/close) with difference calc
- Period lock date (prevents edits on/before date)
- Audit log for key actions
