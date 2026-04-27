<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvoiceActionController extends Controller
{
    public function show(int $invoiceId)
    {
        $companyId = $this->currentCompanyId();
        $invoice = $this->getInvoice($invoiceId, $companyId);
        if (!$invoice) abort(404, 'Invoice not found');

        $contact = $this->resolveInvoiceContact($invoice);

        return view('accountings::cashflow.invoice_action', compact('invoice','contact'));
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
