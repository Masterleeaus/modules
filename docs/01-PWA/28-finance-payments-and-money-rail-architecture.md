# 28. Finance, Payments, and Money-Rail Architecture

## Purpose

This document defines how the system should model finance, invoices, payments, payment recovery, and future money-rail capabilities without collapsing accounting truth, operational truth, and AI convenience into one unclear layer.

The goal is to make money a first-class governed subsystem.

## Core doctrine

The constitutional system docs are explicit that Worksuite is the system of record for operational and financial entities including quotes, invoices, payments, and compliance records. Titan Studio may influence customer journeys and marketing, but it must never become the ledger of operational money events. Titan Zero may infer and propose financial actions, but must not silently mutate financial records.

So the architecture must preserve a strict split:

- **Worksuite owns invoices, payment records, finance workflows, and customer money state.**
- **Titan Studio may generate demand, reminders, and handoffs, but not own settlement truth.**
- **Titan Zero classifies risk and approves action paths.**
- **Titan Core executes approved AI work, never financial intent on its own.**

## Financial subsystem layers

The money architecture should be split into the following layers.

### 1. Financial domain layer

Owns:
- quotes
- invoices
- invoice lines
- taxes
- credits
- payment intents
- payment sessions
- payments received
- payment failures
- refunds
- reconciliation states
- dunning and recovery state
- customer account balance context

This belongs in Worksuite domain modules and finance platform services.

### 2. Rail abstraction layer

Owns:
- payment providers
- bank rails
- card processors
- PayID / QR / wallet / ACH-like abstractions
- provider webhooks
- settlement status normalization
- retry/fallback rules by rail

This prevents provider-specific logic from leaking into invoice controllers or portal pages.

### 3. Customer payment surfaces

Owns:
- pay links
- invoice payment pages
- portal payment history
- QR surfaces
- wallet/session UX
- fallback channel handoff

These are surface concerns, not ledger concerns.

### 4. Recovery and collections layer

Owns:
- reminder cadence
- polite reminders
- escalation rules
- broken promise follow-up
- failed payment retries
- overdue workflows
- human handoff thresholds

This belongs with automation/workflow/communications engines, but must write through finance actions.

## Recommended ownership map

### Quotes

Own:
- commercial proposal before obligation
- optional line items
- acceptance state
- conversion path to booking/invoice

### Invoices

Own:
- billable truth
- due date
- tax and totals
- legal/payment references
- payment status summary

### Payments

Own:
- actual money event records
- provider reference
- payment method/rail
- settled / pending / failed / refunded state
- timestamps and audit trail

### Accounting connectors

Own:
- external bookkeeping sync
- mapping to external chart/accounts and ledgers
- reconciliation exports/imports

### AI

Own:
- draft explanation
- recovery recommendations
- risk classification
- channel choice proposals
- anomaly detection
- cashflow coaching

AI must not own invoice truth.

## Payment rail abstraction

The system should treat “payment method” and “rail” as separate concepts.

### Payment method

Examples:
- card
- bank transfer
- pay-by-link
- QR payment
- wallet
- direct debit
- installment plan

### Rail

Examples:
- Stripe
- PayPal
- PayID-like local rail
- bank API provider
- in-person QR settlement rail
- future ZeroPay/Titan Money rails

This abstraction matters because the same customer-facing method may be backed by different technical rails in different countries or packages.

## Payment session model

The system should support a payment session abstraction.

A payment session should represent:
- who is paying
- what they are paying for
- amount and currency
- allowed methods
- active rails
- expiry time
- status
- provider redirect or hosted flow metadata
- completion callback/webhook linkage

This is safer and more composable than binding invoice pages directly to one provider.

## Module and platform structure

Suggested domains:

- `app/Models/Finance/`
- `app/Actions/Finance/`
- `app/Services/Finance/`
- `app/Platform/Payments/`
- `app/Platform/Communications/`
- `app/Platform/Automation/`

Suggested payment platform folders:

- `app/Platform/Payments/Rails/`
- `app/Platform/Payments/Sessions/`
- `app/Platform/Payments/Webhooks/`
- `app/Platform/Payments/Reconciliation/`
- `app/Platform/Payments/Risk/`
- `app/Platform/Payments/Support/`

And module-side contracts:

- finance APIs
- portal payment surfaces
- payment-related manifests where needed
- communications templates for invoice and recovery messaging

## Invoice-to-cash lifecycle

A strong lifecycle model should include:

1. quote drafted
2. quote accepted
3. billable event created
4. invoice issued
5. payment session created
6. customer chooses rail
7. provider processes payment
8. webhook or confirmation received
9. payment normalized and posted
10. invoice status updated
11. receipt/communication emitted
12. accounting/reconciliation sync executed

Each transition should produce events and audit entries.

## Recovery architecture

Recovery should be part of the platform, not improvised from ad hoc reminder emails.

Recovery engine responsibilities:
- classify non-payment reason
- choose channel and tone
- decide reminder timing
- propose payment alternatives
- re-open expired payment sessions
- escalate to human review when needed
- stop when consent/policy/risk rules require

A recovery flow should be reusable across invoices, deposits, subscriptions, and staged job payments.

## Payment safety rules

Financial operations are high risk and must follow stricter governance.

Rules:
- AI may draft or propose but not silently settle, void, refund, or reallocate funds
- rail webhooks must be idempotent
- duplicate payment posting must be blocked
- human-visible audit trail is mandatory
- invoice changes after issuance must be versioned or tightly controlled
- customer-facing payment links should be signed and expirable
- reconciliation mismatches must surface to operator queues

## Tenant boundary and actor tracking

Finance records must always be tenant-safe.

Minimum financial data discipline:
- `company_id` on every tenant-owned finance entity
- `user_id` or actor metadata where human responsibility matters
- provider references stored separately from user-facing references where useful
- immutable or append-only audit for critical money actions

Suggested audit fields:
- created_by
- approved_by
- captured_by
- reconciled_by
- source_channel
- source_system
- ai_run_id when AI influenced a proposal

## Portal and customer experience

Customer payment UX should be simple and modular.

Recommended surfaces:
- invoice view
- pay-now button
- QR payment tile
- alternative method selector
- installment/partial-plan offer where policy allows
- receipt page
- payment failure recovery page

These surfaces should consume payment sessions, not fabricate provider logic directly in templates.

## AI in finance

Titan Zero and finance-specialist reasoning can add real value when constrained correctly.

Allowed uses:
- draft reminder language
- suggest best follow-up channel
- explain invoice line items in plain language
- summarize outstanding balances
- detect unusual delay patterns
- rank likely recovery outcomes
- recommend escalation to human operator

Disallowed uses without explicit governed confirmation:
- issuing refunds automatically
- changing invoice amounts silently
- writing off debt silently
- moving funds between records
- selecting expensive rails without policy approval

## Accounting and external sync

The accounting connector should not be the operational source of truth.

Recommended doctrine:
- Worksuite remains operational finance truth
- external accounting platforms receive normalized syncs
- reconciliation state is tracked internally
- sync failures go to review queues
- connectors are adapters, not owners

This allows the system to support BYO accounting tools without giving away platform authority.

## Productization path

This architecture supports multiple product layers:

- basic invoice and pay-link flows
- advanced portal and QR flows
- AI recovery automation
- multi-rail orchestration
- Titan Money / ZeroPay style abstractions
- future treasury/cashflow and finance coaching surfaces

Because the rail layer is abstracted, the product can start simple and still evolve into something much more differentiated.

## Final rule

Money must be treated as a governed platform domain, not a widget.

Operational finance truth lives in Worksuite. Rails are adapters. Recovery is an engine. AI is advisory and governed. Customer surfaces are composed on top of that foundation.
