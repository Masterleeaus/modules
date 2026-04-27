@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Invoice Follow-up</h4>
            <small class="text-muted">Comms are handled by CustomerConnect. Accountings only provides the cashflow context.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cashflow.index') }}" class="btn btn-sm btn-outline-secondary">Back to Cashflow</a>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">Return</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Invoice</div>
                <div class="card-body">
                    <div class="mb-1"><strong>ID:</strong> {{ $invoice->id }}</div>
                    @if(isset($invoice->total))
                        <div class="mb-1"><strong>Total:</strong> {{ number_format((float)$invoice->total,2) }}</div>
                    @endif
                    @php
                        $due = null;
                        foreach (['due_date','invoice_date','issue_date','date'] as $c) { if (isset($invoice->$c) && $invoice->$c) { $due = substr((string)$invoice->$c,0,10); break; } }
                    @endphp
                    @if($due)
                        <div class="mb-1"><strong>Due:</strong> {{ $due }}</div>
                    @endif

                    <hr>

                    <div class="mb-2"><strong>Customer</strong></div>
                    <div class="text-muted small">Name</div>
                    <div class="mb-2">{{ $contact['name'] ?? '—' }}</div>

                    <div class="text-muted small">Phone</div>
                    <div class="mb-2">{{ $contact['phone'] ?? '—' }}</div>

                    <div class="text-muted small">Email</div>
                    <div class="mb-2">{{ $contact['email'] ?? '—' }}</div>

                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @if(\Illuminate\Support\Facades\Route::has('cashflow.invoice_customerconnect'))
                            <a class="btn btn-sm btn-primary" href="{{ route('cashflow.invoice_customerconnect', $invoice->id) }}">Send via CustomerConnect</a>
                        @endif
                        @if(\Illuminate\Support\Facades\Route::has('customerconnect.inbox.index'))
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customerconnect.inbox.index') }}">Open CustomerConnect Inbox</a>
                        @endif
                    </div>

                    <hr>

                    @if (function_exists('user') && user() && user()->can('titanzero.use') && \Illuminate\Support\Facades\Route::has('titan.zero.intent.run'))
                        <div class="mb-2"><strong>Ask Titan Zero (Collections Hero)</strong></div>

                        <form method="POST" action="{{ route('titan.zero.intent.run') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="payload" value='@json([
                                "intent" => "draft_followup_task",
                                "return_url" => url()->current(),
                                "page" => ["route_name" => request()->route()?->getName(), "url" => url()->current()],
                                "record" => ["record_type" => "invoice", "record_id" => $invoice->id],
                                "fields" => [
                                    "customer" => $contact,
                                    "invoice" => ["id" => $invoice->id, "total" => (float)($invoice->total ?? 0), "due_date" => $due],
                                    "goal" => "draft a polite overdue follow-up message for CustomerConnect",
                                    "channel" => "sms_or_email"
                                ],
                                "hero_key" => "collections",
                                "user_id" => user()->id ?? null,
                                "company_id" => user()->company_id ?? null
                            ]'>
                            <button class="btn btn-sm btn-warning">Draft follow-up message</button>
                        </form>

                        <div class="small text-muted mt-2">Titan Zero shows results then returns here.</div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Follow-up history</div>
                <div class="card-body">
                    <p class="mb-2">CustomerConnect threads keep the message history, deliveries and replies.</p>
                    <div class="d-flex gap-2">
                        @if(\Illuminate\Support\Facades\Route::has('customerconnect.inbox.index'))
                            <a class="btn btn-outline-primary" href="{{ route('customerconnect.inbox.index') }}">Open Inbox</a>
                        @endif
                        @if(\Illuminate\Support\Facades\Route::has('cashflow.invoice_customerconnect'))
                            <a class="btn btn-outline-secondary" href="{{ route('cashflow.invoice_customerconnect', $invoice->id) }}">New follow-up</a>
                        @endif
                    </div>
                    <div class="small text-muted mt-2">No duplicated comms tables inside Accountings.</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
