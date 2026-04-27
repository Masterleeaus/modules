# 18. Signal Envelope and Event Backfeed Contract

## Purpose

This document defines the contract that allows Worksuite modules, PWAs, Titan Zero, Studio, Omni, and automation engines to coordinate safely.

It separates four things that are often mixed together:

- **domain events** — facts that something happened inside a bounded domain
- **signals** — normalized cross-system envelopes used for routing, governance, and automation
- **commands/actions** — requests to perform work
- **backfeed** — asynchronous mirror data sent to other surfaces for intelligence, personalization, and analytics

This separation is essential if the platform is going to become an AI-controlled, interconnected system instead of a set of tightly coupled Laravel modules.

---

## Why this contract exists

The source set already implies the need for a signal-native architecture:

- modules are encouraged to expose `signals_manifest.json`
- the future system includes dedicated `Signals`, `Automation`, `Workflows`, and `Ai` platform layers
- the constitutional model says Studio must not mutate Worksuite operational state directly
- AI proposals must pass through governance and approval paths instead of becoming silent side effects

Without a formal contract, these ideas collapse into:

- duplicated integration code
- hidden side effects in controllers
- brittle webhook logic
- impossible replay/recovery
- weak auditability
- cross-system confusion about who owns truth

---

## Definitions

### Domain event
A fact emitted by a domain module.

Examples:

- `booking.confirmed`
- `invoice.sent`
- `site.access_code_changed`
- `lead.qualified`
- `message.inbound_received`

A domain event belongs first to the module that produced it.

### Signal
A normalized envelope that makes an event or proposed action understandable to shared platform services.

Examples:

- an automation engine trigger
- an AEGIS governance review input
- a PWA sync mutation
- a Studio intelligence notification
- an Omni routing input

A signal is not the domain record itself. It is the **platform-facing envelope**.

### Command or action request
A request to do something.

Examples:

- create booking
- reschedule visit
- send reminder
- assign reviewer
- escalate overdue invoice

Commands are requests. Events are facts. Signals are normalized envelopes that let the platform coordinate either.

### Backfeed
An asynchronous mirror projection sent to another system or surface after the source-of-truth system has already accepted the event.

Examples:

- Worksuite sends a new lead outcome back to Studio
- CustomerConnect conversation activity is mirrored for advisory intelligence
- invoice recovery status is mirrored to reporting and AI memory surfaces

Backfeed must never become secret write access.

---

## Constitutional rule

### Worksuite owns operational truth

Worksuite is the system of record for:

- jobs
- sites
- clients
- quotes
- invoices
- schedules
- payments
- operational conversations
- voice session outcomes where they impact operations

### Studio owns advisory and marketing truth

Studio may own:

- audience segmentation
- campaign recommendations
- content plans
- creative assets
- advisory summaries
- growth insights

### Titan Zero owns AI reasoning and proposal generation

Titan Zero may:

- analyze
- synthesize
- recommend
- score
- propose actions
- request approvals
- compose context packs

Titan Zero must not become a silent bypass around domain authority.

### Titan Core executes approved AI requests

Titan Core routes and executes approved requests through the correct subsystem. It does not invent authority.

---

## Signal lifecycle

### Step 1 — Event occurs
A domain module emits a local event.

Example:
`BookingCreated`

### Step 2 — Signal normalization
A listener, emitter, or domain adapter converts the event into a normalized signal envelope.

### Step 3 — Validation
The platform validates:

- schema
- actor
- company boundary
- module authority
- replay/idempotency state
- risk class

### Step 4 — Governance
AEGIS or policy rules decide whether the signal is:

- directly routable
- approval-gated
- blocked
- downgraded
- deferred
- enriched first

### Step 5 — Routing
The signal is routed to one or more destinations:

- workflow engine
- automation engine
- communications engine
- AI context builder
- backfeed projection bus
- audit log
- notification layer

### Step 6 — Execution or projection
A routed subsystem performs a bounded action or materializes a read model.

### Step 7 — Audit and replay record
The signal and all outcomes are retained for traceability.

---

## Canonical signal envelope

Every serious cross-system signal should share a normalized structure.

```json
{
  "signal_id": "uuid",
  "idempotency_key": "string",
  "kind": "event|proposal|mutation|approval|projection",
  "name": "booking.confirmed",
  "module": "BookingManagement",
  "company_id": 123,
  "user_id": 456,
  "device_uuid": "optional-device-id",
  "source_system": "worksuite",
  "source_surface": "api|filament|pwa|omni|studio|ai",
  "causation_id": "optional-parent-signal-id",
  "correlation_id": "optional-flow-id",
  "occurred_at": "ISO-8601",
  "risk_class": "low|medium|high|critical",
  "payload": {},
  "evidence": [],
  "policy_hints": {},
  "targets": []
}
```

### Required philosophy

The envelope must carry enough context to support:

- audit
- replay
- tenant safety
- device attribution
- approval routing
- correlation across systems

But it should not become a dumping ground for raw database snapshots.

---

## Signal kinds

### 1. Event signals
Used when a fact has already happened in the source-of-truth system.

Examples:

- booking created
- quote accepted
- worker clocked in
- invoice paid

### 2. Proposal signals
Used when AI or automation wants something to happen but does not yet have authority.

Examples:

- AI proposes a follow-up
- AI proposes changing priority
- AI proposes lead escalation

### 3. Mutation signals
Used by edge nodes and integrations to request bounded domain changes through a normalized pipeline.

Examples:

- complete checklist item
- upload proof
- submit field note

### 4. Approval signals
Used when human or policy approval changes the status of another signal or proposal.

### 5. Projection signals
Used to materialize downstream read models, analytics, caches, or Studio-facing backfeed.

---

## Idempotency and replay

Signals must be safe to replay.

### Minimum rules

- every signal gets a `signal_id`
- every externally generated mutation gets an `idempotency_key`
- every consumer stores handled state
- replay does not recreate destructive side effects

### Good examples

- sending a second identical “booking confirmed” signal should not create duplicate reminders
- replaying a projection should rebuild a view, not create a second invoice
- replaying a field evidence upload should relink the existing evidence record

This is what makes offline PWA nodes, outbox processing, and AI/automation resilience possible.

---

## Approval contract

Not every signal should flow straight to execution.

### Suggested decision classes

#### Direct
Low-risk and fully authorized.

#### Review
Needs human or policy review.

#### Deny
Blocked by policy.

#### Stage
Held until related signals or missing evidence arrive.

#### Split
Some targets may execute directly while others wait for review.

### What usually requires review

- money movement
- destructive edits
- privileged reassignment
- contract-impacting changes
- tenant-wide configuration changes
- AI-proposed actions with meaningful business impact

---

## Backfeed contract

Backfeed is one of the most important ideas in the source set.

It allows one system to stay informed without violating authority boundaries.

### Good backfeed

- Worksuite sends lead outcome summaries back to Studio
- Worksuite sends messaging or booking milestones to advisory surfaces
- CustomerConnect conversation facts are mirrored to AI memory builders
- operational outcomes feed model refinement and dashboard analytics

### Bad backfeed

- Studio directly edits Worksuite bookings because it “knows better”
- analytics surfaces mutate source-of-truth tables
- mirrored records become a second operational database

### Rule

Backfeed is **read-model and intelligence enrichment**, not hidden write access.

---

## Source and target mapping

### Typical sources

- module listeners
- API controllers
- PWA mutation adapters
- Omni channel adapters
- Studio advisory engines
- AI proposal engines
- system schedulers

### Typical targets

- automation engines
- workflow engines
- comms routing
- AI context packs
- dashboards
- Studio intelligence views
- observability and audit stores
- notification systems

---

## Module responsibilities

Each serious module should expose its signal-facing contract explicitly.

### Recommended files

- `signals_manifest.json`
- `lifecycle_manifest.json`
- `ai_tools.json`
- `api_manifest.json`
- optional `omni_manifest.json`
- optional `cms_manifest.json`

### `signals_manifest.json` should declare

- supported signal names
- whether the module emits them, consumes them, or both
- payload shape
- risk hints
- replay expectations
- approval requirements
- projection/backfeed targets

This is how modules become first-class ecosystem components instead of hidden local CRUD islands.

---

## Signal-to-workflow mapping

Signals should be the trigger language for workflows.

Examples:

- `lead.qualified` → lifecycle engine opens quote stage
- `booking.confirmed` → reminder engine schedules confirmations
- `invoice.overdue` → recovery engine starts chase workflow
- `proof.submitted` → review workflow opens QA task
- `message.inbound_received` → Omni routing decides owner or bot path

Workflows should not need to know every table in every module. They should react to normalized signal names and typed payloads.

---

## Signal-to-AI mapping

AI should consume and emit through the signal contract.

### AI consumes

- domain events
- backfeed summaries
- approved operational facts
- workflow state changes
- device or conversation context

### AI emits

- proposals
- critiques
- summaries
- prioritization hints
- recommended next actions
- anomaly flags

### AI should not emit

- silent direct data mutations into operational tables

This preserves the constitutional split between intelligence and authority.

---

## Signals versus webhooks

Signals are not just internal webhooks.

### Webhooks
Usually transport-specific notifications sent to external systems.

### Signals
Internal canonical envelopes with governance, replay, audit, and routing semantics.

A webhook adapter may translate a signal to an outbound webhook, or vice versa, but the platform should not confuse them.

---

## Signal storage and observability

The platform should log every meaningful signal state transition.

Suggested records:

- accepted
- rejected
- deferred
- approved
- executed
- projected
- replayed
- dead-lettered

Useful dimensions:

- signal name
- module
- company
- source surface
- target subsystem
- processing latency
- retry count
- approval time
- execution outcome

This is the foundation for auditability and operational trust.

---

## Example flow — Studio to Worksuite handoff

### Scenario
Studio detects that a marketing lead is now ops-ready.

### Correct flow

1. Studio emits advisory event
2. platform normalizes it into a signal
3. governance validates allowed handoff
4. Worksuite handoff adapter creates or updates lead/work item through approved domain action
5. event is logged in audit
6. backfeed summary returns to Studio for reporting/personalization

### Incorrect flow

- Studio writes directly into Worksuite ops tables with no signal, no approval, and no audit trail

---

## Example flow — PWA field proof

1. Go device captures proof locally
2. node emits mutation signal with evidence pointer
3. signal passes validation and tenant checks
4. proof upload is linked to job/site
5. projection updates command surfaces and QA queues
6. backfeed summary updates AI memory and reporting

---

## Example flow — AI proposal

1. Titan Zero observes overdue invoice and inbound conversation history
2. Titan Zero emits proposal signal: suggest recovery follow-up
3. AEGIS scores risk and policy requirements
4. if allowed, it routes directly to reminder/recovery engine; if not, it opens approval queue
5. outcome is logged
6. resulting communication and payment events become new signals

---

## Final rules

1. **Events state facts.**
2. **Signals normalize facts and requests for platform coordination.**
3. **Commands ask for work.**
4. **Backfeed informs other surfaces without stealing authority.**
5. **Replay, audit, and approval are mandatory, not optional.**

When these rules hold, Worksuite, Studio, PWAs, Omni, and Titan Zero can behave like one coordinated system without collapsing into one dangerous, tightly coupled codebase.
