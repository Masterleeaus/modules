<?php

namespace Modules\Accountings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the accounts table with cleaning industry defaults.
 *
 * Account code ranges:
 *  1xxx — Assets
 *  2xxx — Liabilities
 *  3xxx — Equity
 *  4xxx — Revenue
 *  5xxx — Cost of Sales
 *  6xxx — Expenses
 */
class ChartOfAccountsSeeder extends Seeder
{
    public function run(int $organizationId): void
    {
        if (! DB::getSchemaBuilder()->hasTable('accounts')) {
            return;
        }

        $accounts = [
            // ── Assets ──────────────────────────────────────────────────────
            ['code' => '1000', 'name' => 'Cash and Bank',           'type' => 'asset',     'is_system' => true],
            ['code' => '1100', 'name' => 'Accounts Receivable',     'type' => 'asset',     'is_system' => true],
            ['code' => '1200', 'name' => 'Cleaning Supplies Stock', 'type' => 'asset',     'is_system' => false],
            ['code' => '1300', 'name' => 'Equipment',               'type' => 'asset',     'is_system' => false],
            ['code' => '1400', 'name' => 'GST Receivable',          'type' => 'asset',     'is_system' => true],

            // ── Liabilities ─────────────────────────────────────────────────
            ['code' => '2000', 'name' => 'Accounts Payable',        'type' => 'liability', 'is_system' => true],
            ['code' => '2100', 'name' => 'GST Payable',             'type' => 'liability', 'is_system' => true],
            ['code' => '2200', 'name' => 'Wages Payable',           'type' => 'liability', 'is_system' => false],
            ['code' => '2300', 'name' => 'PAYG Withholding',        'type' => 'liability', 'is_system' => false],

            // ── Equity ───────────────────────────────────────────────────────
            ['code' => '3000', 'name' => 'Owner Equity',            'type' => 'equity',    'is_system' => true],
            ['code' => '3100', 'name' => 'Retained Earnings',       'type' => 'equity',    'is_system' => true],

            // ── Revenue ──────────────────────────────────────────────────────
            ['code' => '4000', 'name' => 'Cleaning Revenue',                  'type' => 'revenue', 'is_system' => true],
            ['code' => '4001', 'name' => 'Residential Cleaning Revenue',      'type' => 'revenue', 'is_system' => false],
            ['code' => '4002', 'name' => 'Commercial Cleaning Revenue',       'type' => 'revenue', 'is_system' => false],
            ['code' => '4003', 'name' => 'Bond / End of Lease Revenue',       'type' => 'revenue', 'is_system' => false],
            ['code' => '4004', 'name' => 'Carpet Cleaning Revenue',           'type' => 'revenue', 'is_system' => false],
            ['code' => '4005', 'name' => 'Window Cleaning Revenue',           'type' => 'revenue', 'is_system' => false],
            ['code' => '4006', 'name' => 'Pressure Washing Revenue',          'type' => 'revenue', 'is_system' => false],
            ['code' => '4099', 'name' => 'Other Revenue',                     'type' => 'revenue', 'is_system' => false],

            // ── Cost of Sales ────────────────────────────────────────────────
            ['code' => '5000', 'name' => 'Labour Cost — Cleaning Staff',      'type' => 'expense', 'is_system' => false],
            ['code' => '5100', 'name' => 'Subcontractor Costs',               'type' => 'expense', 'is_system' => false],
            ['code' => '5200', 'name' => 'Cleaning Supplies Expense',         'type' => 'expense', 'is_system' => false],
            ['code' => '5300', 'name' => 'Equipment Hire',                    'type' => 'expense', 'is_system' => false],

            // ── Operating Expenses ───────────────────────────────────────────
            ['code' => '6000', 'name' => 'Vehicle Expenses',                  'type' => 'expense', 'is_system' => false],
            ['code' => '6100', 'name' => 'Fuel',                              'type' => 'expense', 'is_system' => false],
            ['code' => '6200', 'name' => 'Motor Vehicle Repairs',             'type' => 'expense', 'is_system' => false],
            ['code' => '6300', 'name' => 'Insurance',                         'type' => 'expense', 'is_system' => false],
            ['code' => '6400', 'name' => 'Advertising and Marketing',         'type' => 'expense', 'is_system' => false],
            ['code' => '6500', 'name' => 'Office Supplies',                   'type' => 'expense', 'is_system' => false],
            ['code' => '6600', 'name' => 'Software Subscriptions',            'type' => 'expense', 'is_system' => false],
            ['code' => '6700', 'name' => 'Phone and Internet',                'type' => 'expense', 'is_system' => false],
            ['code' => '6800', 'name' => 'Accounting and Legal Fees',         'type' => 'expense', 'is_system' => false],
            ['code' => '6900', 'name' => 'Bank Fees',                         'type' => 'expense', 'is_system' => false],
            ['code' => '6999', 'name' => 'Miscellaneous Expenses',            'type' => 'expense', 'is_system' => false],
        ];

        $now = now();

        foreach ($accounts as $account) {
            $exists = DB::table('accounts')
                ->where('organization_id', $organizationId)
                ->where('code', $account['code'])
                ->exists();

            if (! $exists) {
                DB::table('accounts')->insert(array_merge($account, [
                    'organization_id' => $organizationId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]));
            }
        }
    }
}
