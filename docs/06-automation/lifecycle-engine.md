# Titan Zero Documentation

Layer: Automation
Scope: Lifecycle engine runtime progression across lead, quote, booking, job, invoice, follow-up, and recovery stages
Status: Draft v1
Depends On: Signals, governance, workflow definitions, scheduling, module lifecycle manifests, automation engines
Consumed By: Titan Zero, AEGIS, Sentinels, module actions, reminders, escalations, recovery engine
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define the runtime engine that advances business entities through time-aware, policy-aware lifecycle steps. The lifecycle engine does not invent the lifecycle model itself; it executes the approved next work around that model.

## 2. Why it exists

Titan needs a dedicated engine for lifecycle progression because operational work does not move only when a user clicks a button. It also moves when:

- deadlines are reached
- customer responses do not arrive
- approvals are granted or withheld
- site visits complete
- payment windows expire
- follow-up timers mature
- recovery conditions trigger a safe re-entry path

Without a lifecycle engine, each module would implement its own stage progression logic, reminder rules, timeout checks, and exception handling. That produces drift, duplicate automations, and inconsistent recovery behavior.

## 3. Core responsibilities

- execute time-aware progression across defined lifecycle stages
- interpret lifecycle manifests without owning domain semantics
- trigger reminders, escalations, pauses, and recovery when stage rules demand it
- enforce approval interruptions before sensitive state movement
- record entry, exit, timeout, and exception events for every stage
- coordinate with workflow state machines without replacing them
- hand off actual domain mutations to module actions and services

## 4. Boundaries

### In scope

- lifecycle stage timers
- entry and exit checks for stages
- stage timeout handling
- auto-advance conditions
- approval holds within a lifecycle stage
- reminder and escalation hooks attached to stage age
- recovery handoff for broken lifecycle runs
- entity-level runtime context for lifecycle progression

### Out of scope

- defining the canonical lifecycle vocabulary for every module
- rich workflow modeling of all transition diagrams
- direct ownership of communications channel implementations
- direct ownership of UI forms or admin surfaces
- domain mutation logic hidden inside the engine itself

## 5. Architecture

## 5.1 Position in the stack

The lifecycle engine sits between workflow/state definitions and executable module actions.

Typical shape:

1. a signal or timer indicates lifecycle work is due
2. governance confirms execution authority
3. workflow/state definitions describe allowable stage progression
4. lifecycle engine evaluates timing, holds, approvals, and retry state
5. engine calls a module action, service, or job to perform the stage mutation
6. downstream reminder/escalation/recovery engines react based on result state

## 5.2 Canonical lifecycle domains

The engine must support shared lifecycle families such as:

- lead
- quote
- booking
- job
- invoice
- payment chase
- follow-up
- recovery

Modules can expose their own lifecycle entries through `lifecycle_manifest.json`, but the engine should keep a common execution grammar across all of them.

## 5.3 Stage execution model

Each lifecycle stage should carry runtime metadata such as:

- stage key
- stage label
- entered_at
- due_at
- reminder windows
- escalation thresholds
- requires_approval
- auto_advance conditions
- retry policy
- recovery policy
- terminal or non-terminal state

This allows the same engine to run many module-defined lifecycles while keeping reliability behavior consistent.

## 6. Contracts

## 6.1 Inputs

Lifecycle engine inputs should include:

- approved signal envelopes
- company-scoped settings
- lifecycle manifest definitions
- current entity stage and timestamps
- workflow/state-machine allowance
- approval state
- retry and idempotency state
- offline replay markers when work originates from a node/PWA surface

## 6.2 Outputs

Lifecycle engine outputs should include:

- stage-entered events
- stage-completed events
- auto-advance dispatch requests
- reminder schedule requests
- escalation schedule requests
- blocked-for-approval state
- retry schedule requests
- recovery handoff requests
- audit and telemetry records

## 6.3 Minimal manifest expectations

A lifecycle manifest should be able to express at least:

- lifecycle name
- participating stages
- allowed next stages
- timeout windows
- reminder offsets
- escalation offsets
- approval-sensitive transitions
- terminal states
- recovery entrypoints

The manifest describes what the lifecycle is. The engine describes how it runs reliably.

## 7. Runtime behavior

## 7.1 Standard execution cycle

A standard lifecycle evaluation run should:

1. identify entities with due lifecycle work
2. hydrate runtime context for each entity
3. compute an idempotency key for the intended transition
4. confirm current stage is still valid
5. evaluate approval gates
6. evaluate timeout/reminder/escalation thresholds
7. either dispatch the next mutation or record a blocked state
8. emit lifecycle events and schedule downstream work
9. persist audit and runtime metrics

## 7.2 Auto-advance rules

Auto-advance should only occur when:

- the lifecycle manifest permits it
- governance approves it
- approval is not still pending
- the prior step completed successfully
- no conflicting retry or recovery lock exists
- idempotency check confirms the transition has not already run

## 7.3 Lifecycle pauses

The engine must support pause states such as:

- awaiting_customer
- awaiting_internal_review
- awaiting_approval
- awaiting_payment
- awaiting_recovery
- awaiting_retry_window

These are runtime holds, not execution failures.

## 7.4 Relationship to reminders and escalations

Lifecycle age should be the source input for reminder and escalation scheduling.

Examples:

- quote sent but no response after 24 hours → reminder
- booking awaiting confirmation after 3 reminders → escalation
- invoice overdue after configured grace window → payment-chase escalation
- job marked completed but proof missing after threshold → compliance escalation

The lifecycle engine owns the timing context. Reminder and escalation engines own the specialized follow-on behavior.

## 8. Failure modes

### Stale-stage execution

The engine attempts to advance an entity that already moved by another path.

Response:

- re-read entity state
- suppress duplicate mutation
- record no-op or stale-run outcome

### Approval race

Approval arrives while retry/recovery scheduling is also occurring.

Response:

- lock stage mutation path
- re-check approval state before dispatch
- emit a single canonical transition result

### Timeout drift

A timer fires after the entity was paused or exempted.

Response:

- re-evaluate stage eligibility
- suppress expired timer side effects
- record timer as invalidated

### Offline replay collision

A PWA/node replay resubmits a lifecycle action already completed online.

Response:

- use idempotency key + entity state verification
- convert to no-op where appropriate
- preserve audit trace of replay attempt

## 9. Dependencies

Upstream dependencies:

- signals
- governance / AEGIS checks
- workflow/state definitions
- scheduling engine
- module lifecycle manifests

Downstream consumers:

- reminder engine
- escalation engine
- recovery engine
- module actions/services/jobs
- communications layer
- audit/telemetry systems

## 10. Open questions

- Should lifecycle manifests support reusable stage templates shared across modules?
- Which transitions should remain permanently manual even when policy allows automation?
- How should lifecycle aging behave during blackout windows, holidays, or paused agreements?
- Should entity lifecycle snapshots be stored separately for replay/debugging?

## 11. Implementation notes

- Keep stage mutation logic in module actions, not in the engine.
- Use queue-safe jobs for lifecycle sweeps and due work dispatch.
- Treat lifecycle engine outputs as auditable runtime events.
- Reuse company-scoped settings and manifest contracts instead of hardcoding stage thresholds.
- Keep workflow documentation separate: workflow defines valid transition structure; lifecycle engine defines timed/runtime execution around that structure.
