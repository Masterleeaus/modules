# 29. Reference Implementation Sequence and First Working Slice

## Purpose

This document converts the wider handbook into a concrete build order for the first real system slice. The goal is not to build every subsystem at once. The goal is to deliver one end-to-end path that proves the architecture works across Worksuite, Filament, Titan Zero, module manifests, package visibility, tenant scoping, PWA/device sync, communications, signals, and approvals.

The first working slice should be small enough to complete, but broad enough to validate the platform.

## The first slice to build

Build a single service-business journey:

**Lead -> Quote -> Booking -> Visit/Job -> Invoice -> Payment -> Follow-up**

That slice is the right foundation because it exercises nearly every part of the system:

- CRM/contact/site records
- package and module visibility
- company/user scoping
- Filament operator UI
- tenant user/account UI
- PWA worker flow
- outbound communications
- signals and approvals
- Titan Zero action proposals
- money rail integration
- audit and observability

Do not start with dozens of partially wired modules. Start with one life cycle that crosses the whole stack.

## What must already exist before feature work

Before building the first slice, the platform needs a minimum substrate.

### Core platform minimum

- tenant/company resolution
- authenticated user resolution
- package assignment
- module registry and sync
- module settings generation
- route and sidebar visibility rules
- policy/permission checks
- signal envelope contract
- queue and scheduler configured
- audit logging baseline

### UI minimum

- Admin Filament panel
- User Filament or standard operator panel
- PWA shell with auth/bootstrap/sync handshake
- one shared design token and navigation contract

### AI minimum

- Titan Zero proposal interface
- AEGIS approval/denial surface
- Titan Core executor boundary
- tool registry for module actions
- basic memory/context pack builder

## The first business modules in scope

The first release does not need every module family. It needs the modules that make the journey real.

### Required

- Customer/Contact module
- Site/Location module
- Quote module
- Booking or ServiceJob module
- Visit/Checklist/Proof module
- Invoice module
- Payment/ZeroPay session module
- CustomerConnect or conversation timeline module
- Notification/communications adapters

### Optional for the first slice

- route optimization
- advanced dispatch board
- campaign engine
- deep analytics
- marketplace/plugin ecosystem

## Build order

### Phase 0 — platform hardening

1. Normalize module discovery and DB sync.
2. Make package/module visibility deterministic.
3. Guarantee `company_id` tenant boundary everywhere.
4. Add `user_id` / actor fields where action ownership matters.
5. Finish idempotent migrations and install safety.
6. Ensure queues, schedule, cache, and logs are healthy.

### Phase 1 — module contract hardening

For each first-slice module:

- valid `module.json`
- service provider and route provider
- `Routes/web.php` and `Routes/api.php`
- seeded `modules` row and `module_settings`
- package visibility
- named routes
- menu/sidebar hooks
- company-safe policies
- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- optional `cms_manifest.json` where customer surfaces matter

### Phase 2 — domain actions and DTOs

For each module, define actions before UI work.

Examples:

- `CreateLeadAction`
- `CreateQuoteAction`
- `ApproveQuoteAction`
- `CreateBookingAction`
- `CheckInVisitAction`
- `CompleteVisitAction`
- `CreateInvoiceAction`
- `CreatePaymentSessionAction`
- `RecordPaymentAction`
- `TriggerFollowUpAction`

Every controller, Filament page, API endpoint, import, job, and AI tool must call these actions instead of duplicating behavior.

### Phase 3 — operator surfaces

Build the operator journey in Filament:

- lead inbox
n- quote review page
- booking board or schedule page
- visit/job detail page
- invoice screen
- payment reconciliation screen
- approval queue for AI proposals
- system health widgets

Filament is the control plane, not the source of domain truth.

### Phase 4 — PWA worker slice

Build one worker-facing PWA for the visit/job stage:

- device registration
- bootstrap payload
- assigned jobs list
- arrival/check-in
- checklist/proof capture
- offline queue
- sync replay
- issue/escalation submit
- complete job

This validates the edge-node model.

### Phase 5 — communications slice

Wire the life cycle to outbound comms:

- lead acknowledgement
- quote sent
- booking confirmed
- en route / arrived
- invoice sent
- payment reminder
- follow-up/review request

Do not hardwire channel logic into modules. Use the communications engine and templates.

### Phase 6 — AI proposal layer

Allow Titan Zero to propose actions, but not execute them freely.

First proposals:

- draft quote from intake
- suggest booking slot
- detect likely no-response follow-up
- recommend invoice reminder
- summarize job issues
- suggest review request timing

Every proposal must produce:

- context pack
- proposed action
- risk/authority mode
- approval requirement
- audit record

### Phase 7 — money rail validation

For the first money slice:

- generate one payment session/link or QR
- expose multiple rails where possible
- track session status
- write payment result back to invoice/customer timeline
- trigger receipt/follow-up events

The first financial goal is operational simplicity, not a giant accounting rebuild.

## The required data contracts

### Core records

- Company
- User
- Contact/Customer
- Site
- Lead or Inquiry
- Quote
- Booking / ServiceJob
- Visit
- Checklist / ProofOfService
- Invoice
- PaymentSession
- Payment
- Conversation / TimelineEvent
- SignalEnvelope
- ApprovalDecision
- Device

### Required cross-cutting fields

- `company_id`
- `user_id` where actor/owner matters
- status/state
- source channel
- timestamps
- audit references
- idempotency key where replay is possible

## The signal path for the first slice

Use a standard promotion flow:

1. Module emits signal.
2. Signal engine validates schema and identity.
3. Governance layer scores risk.
4. AEGIS decides auto-approve, queue, or deny.
5. Titan Core executes only approved actions.
6. Result is written back to the originating records.
7. Communications and analytics backfeed are triggered by events, not inline spaghetti.

### Example

`quote.accepted` -> booking proposal -> operator approval -> booking created -> confirmation sent -> visit scheduled -> invoice later emitted.

## The acceptance test for the first working slice

The system is not “working” because pages render. It is working when this scenario succeeds end to end:

1. A new lead enters from a web surface or manual operator entry.
2. Titan Zero can propose a quote draft.
3. Operator reviews and sends the quote.
4. Customer accepts.
5. Booking is created through the booking action.
6. Assigned worker sees the job in the PWA.
7. Worker completes checklist and proof, even if temporarily offline.
8. Invoice is created.
9. Customer receives payment link/QR.
10. Payment status returns to the system.
11. Follow-up is scheduled or suggested.
12. Timeline, audit, and dashboards all reflect the same truth.

If that full loop works, the architecture is proven.

## What not to do

### Do not start with giant horizontal rewrites

Avoid rewriting everything into a new framework tree before proving one real slice.

### Do not let Filament own domain logic

Filament should display, approve, summarize, and invoke actions. It should not become the only place where bookings, invoices, or communications can happen.

### Do not let AI bypass approvals

Titan Zero can propose. Titan Core can execute. AEGIS governs. Keep those boundaries hard.

### Do not let packages and module visibility drift

If the module exists on disk but not in package/module settings, the platform will feel broken. Keep registry, settings, packages, and sidebar in sync.

### Do not build mobile/PWA as a separate universe

PWAs are just another execution surface over the same domain actions, APIs, signals, and policies.

## The recommended team split

### Agent / stream 1 — platform

- module registry
- package sync
- tenancy
- policies
- providers
- queues/scheduler
- audit/observability

### Agent / stream 2 — domain modules

- customer/site/quote/booking/visit/invoice/payment session modules
- actions/DTOs/events
- manifests

### Agent / stream 3 — operator panels

- Filament resources/pages/widgets
- approval surfaces
- dashboards
- admin navigation

### Agent / stream 4 — PWA/edge

- bootstrap/auth/sync/device trust
- worker app flows
- offline queues

### Agent / stream 5 — AI/governance/comms

- Titan Zero proposal flows
- AEGIS approval logic
- Titan Core execution adapters
- communications templates/channels

## The roadmap after the first slice proves out

After the first slice works, expand in this order:

1. richer dispatch and routing
2. deeper field-worker tooling
3. advanced follow-up and campaign automations
4. finance expansion and reconciliation depth
5. CMS/portal personalization
6. vertical kits and industry overlays
7. broader node federation and local AI execution

## Final rule

A revolutionary system is not created by making every part futuristic at once. It is created by proving one coherent loop where:

- modules are real domain engines
- Filament is the operator brain
- PWAs are trusted edge nodes
- signals connect everything cleanly
- AI proposes and governs responsibly
- communications and money rails are native
- tenant safety and auditability never break

That first loop is the seed crystal for the entire platform.
