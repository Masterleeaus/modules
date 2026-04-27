<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;

class GstReportService
{
    private const SCALE = 4; // bcmath decimal scale for intermediate calculations

    /**
     * GST summary with accrual vs cash basis.
     *
     * Uses bcmath for all monetary arithmetic (precision required for tax).
     *
     * - accrual: bills by bill_date, invoices by issue_date/created_at
     * - cash: bills by payment date (acc_bill_payments), invoices by paid_at/payment_date when available
     */
    public function summary(?string $from, ?string $to, string $basis = 'accrual'): array
    {
        $basis = in_array($basis, ['accrual', 'cash']) ? $basis : 'accrual';

        $user = auth()->user();
        $companyId = $user->company_id ?? null;
        $userId = $user->id ?? null;

        $schema = DB::getSchemaBuilder();

        // ---------- GST PAID (INPUTS) ----------
        $gstPaidBills = '0.0000';

        if ($basis === 'cash' && $schema->hasTable('acc_bill_payments')) {
            // allocate bill tax proportionally to payments: (bill.tax_total / bill.total) * payment.amount
            $pay = DB::table('acc_bill_payments as p')
                ->join('acc_bills as b', 'b.id', '=', 'p.bill_id')
                ->when($companyId, fn($q) => $q->where('p.company_id', $companyId))
                ->when($userId, fn($q) => $q->where('p.user_id', $userId))
                ->whereNotNull('p.paid_at');

            if ($from) $pay->whereDate('p.paid_at', '>=', $from);
            if ($to) $pay->whereDate('p.paid_at', '<=', $to);

            $rows = $pay->select('p.amount', 'b.tax_total', 'b.total')->get();
            $calc = '0.0000';
            foreach ($rows as $r) {
                $total = (string) ($r->total ?? 0);
                $tax = (string) ($r->tax_total ?? 0);
                if (bccomp($total, '0', self::SCALE) <= 0 || bccomp($tax, '0', self::SCALE) <= 0) continue;
                $ratio = bcdiv($tax, $total, self::SCALE);
                $calc = bcadd($calc, bcmul((string)$r->amount, $ratio, self::SCALE), self::SCALE);
            }
            $gstPaidBills = $calc;
        } else {
            // accrual basis: use bill lines by bill_date or created_at
            $billLines = DB::table('acc_bill_lines as l')
                ->join('acc_bills as b', 'b.id', '=', 'l.bill_id')
                ->when($companyId, fn($q) => $q->where('l.company_id', $companyId))
                ->when($userId, fn($q) => $q->where('l.user_id', $userId));

            $dateCol = $schema->hasColumn('acc_bills', 'bill_date') ? 'b.bill_date' : 'l.created_at';
            if ($from) $billLines->whereDate($dateCol, '>=', $from);
            if ($to) $billLines->whereDate($dateCol, '<=', $to);

            $gstPaidBills = (string) ($billLines->sum('l.line_tax') ?? 0);
        }

        $expenses = DB::table('acc_expenses')
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($userId, fn($q) => $q->where('user_id', $userId));

        if ($from) $expenses->whereDate('expense_date', '>=', $from);
        if ($to) $expenses->whereDate('expense_date', '<=', $to);

        $gstPaidExpenses = (string) ($expenses->sum('tax_amount') ?? 0);
        $gstPaidTotal = bcadd($gstPaidBills, $gstPaidExpenses, self::SCALE);

        // ---------- GST COLLECTED (OUTPUTS) ----------
        $gstCollectedTotal = '0.0000';
        if ($schema->hasTable('invoices')) {
            $inv = DB::table('invoices')
                ->when($companyId, fn($q) => $schema->hasColumn('invoices','company_id') ? $q->where('company_id', $companyId) : $q)
                ->when($userId, fn($q) => $schema->hasColumn('invoices','user_id') ? $q->where('user_id', $userId) : $q);

            // date col for basis
            $dateCol = null;
            if ($basis === 'cash') {
                foreach (['paid_at','payment_date','paid_date','updated_at'] as $c) {
                    if ($schema->hasColumn('invoices', $c)) { $dateCol = $c; break; }
                }
            } else {
                foreach (['issue_date','invoice_date','created_at'] as $c) {
                    if ($schema->hasColumn('invoices', $c)) { $dateCol = $c; break; }
                }
            }
            if ($from && $dateCol) $inv->whereDate($dateCol, '>=', $from);
            if ($to && $dateCol) $inv->whereDate($dateCol, '<=', $to);

            if ($basis === 'cash') {
                // best-effort: require paid marker
                foreach (['paid_at','paid_date'] as $c) {
                    if ($schema->hasColumn('invoices', $c)) { $inv->whereNotNull($c); break; }
                }
                foreach (['status','payment_status'] as $c) {
                    if ($schema->hasColumn('invoices', $c)) { $inv->whereIn($c, ['paid','Paid','PAID']); break; }
                }
            }

            foreach (['tax', 'tax_total', 'total_tax', 'gst'] as $col) {
                if ($schema->hasColumn('invoices', $col)) {
                    $gstCollectedTotal = (string) ($inv->sum($col) ?? 0);
                    break;
                }
            }

            if (bccomp($gstCollectedTotal, '0', self::SCALE) === 0) {
                if ($schema->hasColumn('invoices', 'total') && $schema->hasColumn('invoices', 'sub_total')) {
                    $rows = $inv->select('total', 'sub_total')->get();
                    $calc = '0.0000';
                    foreach ($rows as $r) {
                        $diff = bcsub((string)$r->total, (string)$r->sub_total, self::SCALE);
                        if (bccomp($diff, '0', self::SCALE) > 0) {
                            $calc = bcadd($calc, $diff, self::SCALE);
                        }
                    }
                    $gstCollectedTotal = $calc;
                }
            }
        }

        $gstCollectedTotal = bcadd($gstCollectedTotal, '0', 2); // round to 2dp
        $gstPaidBillsFinal = bcadd($gstPaidBills, '0', 2);
        $gstPaidExpensesFinal = bcadd($gstPaidExpenses, '0', 2);
        $gstPaidTotalFinal = bcadd($gstPaidTotal, '0', 2);
        $net = bcsub($gstCollectedTotal, $gstPaidTotalFinal, 2);

        return [
            'from' => $from,
            'to' => $to,
            'basis' => $basis,
            'gst_collected' => (float) $gstCollectedTotal,
            'gst_paid' => (float) $gstPaidTotalFinal,
            'gst_paid_bills' => (float) $gstPaidBillsFinal,
            'gst_paid_expenses' => (float) $gstPaidExpensesFinal,
            'net_gst' => (float) $net,
        ];
    }

    /**
     * Calculate GST totals for a company/period without relying on auth().
     */
    public function calculateForPeriod(int $companyId, string $start, string $end): array
    {
        $collected = $this->getGstCollected($companyId, $start, $end);
        $paid      = $this->getGstPaid($companyId, $start, $end);

        return [
            'gst_collected' => $collected,
            'gst_paid'      => $paid,
            'net_gst'       => round($collected - $paid, 2),
        ];
    }

    /**
     * Build a BAS-ready data structure from a GstPeriod model.
     *
     * @param  \Modules\Accountings\Entities\GstPeriod  $period
     */
    public function exportBas(\Modules\Accountings\Entities\GstPeriod $period): array
    {
        return [
            'period_start'      => $period->period_start?->toDateString(),
            'period_end'        => $period->period_end?->toDateString(),
            'period_type'       => $period->period_type,
            'status'            => $period->status,
            // G1 — Total sales (BAS field)
            'G1_total_sales'    => $period->gst_collected,
            // G2 — Export sales (not tracked here)
            'G2_export_sales'   => 0,
            // G3 — Other GST-free sales
            'G3_gst_free'       => 0,
            // G10 — Capital acquisitions
            'G10_capital'       => 0,
            // G11 — Non-capital acquisitions
            'G11_non_capital'   => $period->gst_paid,
            // 1A — GST on sales
            '1A_gst_on_sales'   => $period->gst_collected,
            // 1B — GST credits
            '1B_gst_credits'    => $period->gst_paid,
            // Net GST payable / refundable
            'net_gst'           => $period->net_gst,
            'lodged_at'         => $period->lodged_at?->toDateTimeString(),
        ];
    }

    /**
     * Pull GST collected from the invoices table for a given company/period.
     */
    public function getGstCollected(int $companyId, string $start, string $end): float
    {
        $schema = DB::getSchemaBuilder();
        if (! $schema->hasTable('invoices')) {
            return 0.0;
        }

        $q = DB::table('invoices')
            ->where('company_id', $companyId);

        $dateCol = null;
        foreach (['issue_date', 'invoice_date', 'created_at'] as $c) {
            if ($schema->hasColumn('invoices', $c)) {
                $dateCol = $c;
                break;
            }
        }
        if ($dateCol) {
            $q->whereDate($dateCol, '>=', $start)->whereDate($dateCol, '<=', $end);
        }

        foreach (['tax', 'tax_total', 'total_tax', 'gst'] as $col) {
            if ($schema->hasColumn('invoices', $col)) {
                return (float) ($q->sum($col) ?? 0);
            }
        }

        // Fallback: total - sub_total
        if ($schema->hasColumn('invoices', 'total') && $schema->hasColumn('invoices', 'sub_total')) {
            return (float) ($q->selectRaw('SUM(total - sub_total) as gst_est')->value('gst_est') ?? 0);
        }

        return 0.0;
    }

    /**
     * Pull GST paid from acc_bill_lines + acc_expenses for a company/period.
     */
    public function getGstPaid(int $companyId, string $start, string $end): float
    {
        $schema = DB::getSchemaBuilder();
        $total  = 0.0;

        if ($schema->hasTable('acc_bill_lines') && $schema->hasTable('acc_bills')) {
            $dateCol = $schema->hasColumn('acc_bills', 'bill_date') ? 'b.bill_date' : 'b.created_at';
            $total  += (float) (DB::table('acc_bill_lines as l')
                ->join('acc_bills as b', 'b.id', '=', 'l.bill_id')
                ->where('l.company_id', $companyId)
                ->whereDate($dateCol, '>=', $start)
                ->whereDate($dateCol, '<=', $end)
                ->sum('l.line_tax') ?? 0);
        }

        if ($schema->hasTable('acc_expenses')) {
            $total += (float) (DB::table('acc_expenses')
                ->where('company_id', $companyId)
                ->whereDate('expense_date', '>=', $start)
                ->whereDate('expense_date', '<=', $end)
                ->sum('tax_amount') ?? 0);
        }

        return round($total, 2);
    }

    /**
     * Calculate GST amount from a price using bcmath.
     *
     * @param  string  $price      Monetary value as string (avoids float imprecision)
     * @param  string  $treatment  'inclusive'|'exclusive'
     * @param  string  $rate       GST rate as decimal string, e.g. '0.10' for 10%
     * @return string  GST amount rounded to 2 decimal places
     */
    public static function calculateGst(string $price, string $treatment = 'inclusive', string $rate = '0.10'): string
    {
        if ($treatment === 'inclusive') {
            // GST = price * rate / (1 + rate)
            $divisor = bcadd('1', $rate, self::SCALE);
            $gst = bcdiv(bcmul($price, $rate, self::SCALE), $divisor, self::SCALE);
        } else {
            // exclusive: GST = price * rate
            $gst = bcmul($price, $rate, self::SCALE);
        }

        return bcadd($gst, '0', 2); // round to 2dp
    }
}
