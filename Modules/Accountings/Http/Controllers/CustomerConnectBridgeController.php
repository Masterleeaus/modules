<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerConnectBridgeController extends Controller
{
    public function invoiceFollowup(Request $request, int $invoiceId)
    {
        if (!\Illuminate\Support\Facades\Route::has('customerconnect.inbox.create')) {
            return back()->with('error', 'CustomerConnect module is not available (route missing).');
        }

        $companyId = $this->currentCompanyId();
        $invoice = $this->getInvoice($invoiceId, $companyId);

        if (!$invoice) abort(404, 'Invoice not found');

        $contact = $this->resolveInvoiceContact($invoice);

        $channel = 'email';
        if (!empty($contact['phone'])) $channel = 'sms';

        $subject = 'Invoice #' . $invoiceId . ' follow-up';
        $due = $this->detectDueDate($invoice);
        $total = isset($invoice->total) ? (float)$invoice->total : 0.0;

        $message = $this->defaultMessage($contact['name'] ?? null, $invoiceId, $total, $due);

        return redirect()
            ->route('customerconnect.inbox.create')
            ->withInput([
                'channel' => $channel,
                'display_name' => $contact['name'] ?? null,
                'email' => $contact['email'] ?? null,
                'phone' => $contact['phone'] ?? null,
                'telegram_chat_id' => $contact['telegram_chat_id'] ?? null,
                'subject' => $subject,
                'message' => $message,
            ]);
    }

    private function defaultMessage(?string $name, int $invoiceId, float $total, ?string $due): string
    {
        $who = $name ? $name : 'there';
        $dueText = $due ? (" (due " . $due . ")") : "";
        $amt = $total > 0 ? (" $" . number_format($total, 2)) : "";
        return "Hi {$who},\n\nJust following up on Invoice #{$invoiceId}{$dueText}{$amt}.\nCould you please let me know when payment will be made?\n\nThanks,\n";
    }

    private function detectDueDate($invoice): ?string
    {
        foreach (['due_date','invoice_date','issue_date','date'] as $c) {
            if (isset($invoice->$c) && $invoice->$c) return substr((string)$invoice->$c,0,10);
        }
        return null;
    }

    private function getInvoice(int $invoiceId, ?int $companyId)
    {
        if (!Schema::hasTable('invoices')) return null;

        $q = DB::table('invoices')->where('id', $invoiceId);
        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('company_id', $companyId);

        return $q->first();
    }

    private function resolveInvoiceContact($invoice): array
    {
        $out = ['name'=>null,'email'=>null,'phone'=>null,'telegram_chat_id'=>null];

        foreach (['client_name','customer_name','company_name','name'] as $col) {
            if (isset($invoice->$col) && $invoice->$col) { $out['name'] = $invoice->$col; break; }
        }
        foreach (['client_email','customer_email','email'] as $col) {
            if (isset($invoice->$col) && $invoice->$col) { $out['email'] = $invoice->$col; break; }
        }
        foreach (['client_phone','customer_phone','phone','mobile'] as $col) {
            if (isset($invoice->$col) && $invoice->$col) { $out['phone'] = $invoice->$col; break; }
        }

        $fk = null;
        foreach (['client_id','customer_id'] as $col) {
            if (isset($invoice->$col) && $invoice->$col) { $fk = (int)$invoice->$col; break; }
        }

        if ($fk) {
            $tables = [
                ['clients', 'id', ['name','company_name'], ['email'], ['phone','mobile']],
                ['customers', 'id', ['name','company_name'], ['email'], ['phone','mobile']],
            ];

            foreach ($tables as [$t,$idcol,$nameCols,$emailCols,$phoneCols]) {
                if (!Schema::hasTable($t) || !Schema::hasColumn($t,$idcol)) continue;
                $row = DB::table($t)->where($idcol, $fk)->first();
                if (!$row) continue;

                if (!$out['name']) {
                    foreach ($nameCols as $c) if (isset($row->$c) && $row->$c) { $out['name']=$row->$c; break; }
                }
                if (!$out['email']) {
                    foreach ($emailCols as $c) if (Schema::hasColumn($t,$c) && isset($row->$c) && $row->$c) { $out['email']=$row->$c; break; }
                }
                if (!$out['phone']) {
                    foreach ($phoneCols as $c) if (Schema::hasColumn($t,$c) && isset($row->$c) && $row->$c) { $out['phone']=$row->$c; break; }
                }
                break;
            }
        }

        return $out;
    }

    private function currentCompanyId(): ?int
    {
        try { if (function_exists('company') && company()) return (int)company()->id; } catch (\Throwable $e) {}
        try { $u=auth()->user(); if ($u && isset($u->company_id)) return (int)$u->company_id; } catch (\Throwable $e) {}
        try { $sid=session('company_id'); if ($sid) return (int)$sid; } catch (\Throwable $e) {}
        return null;
    }
}
