# Titan Zero Documentation

Layer: Automation
Scope: Reminder engine for timed nudges, follow-ups, deadline warnings, and reminder dispatch orchestration
Status: Draft v1
Depends On: Automation engines, scheduling, lifecycle manifests, communications layer, module actions, signals, governance
Consumed By: Lifecycle engine, communications engine, Titan Zero, PWA nodes, customer portal flows, module actions
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define the engine that decides **when** reminders should be created, **who** they should target, **which template or payload** should be used, and **how** reminder work should be dispatched without duplicating messages or bypassing approval rules.

## 2. Why it exists

Reminder behavior appears simple until the platform is multi-surface and policy-aware.

Titan needs a dedicated reminder engine because reminder work may be triggered by:

- lifecycle stage age
- payment due dates
- booking confirmation gaps
- checklist completion delays
- staff non-response windows
- customer follow-up sequences
- recovery re-entry after an interrupted run

Without a central reminder engine:

- reminders get hard-coded into modules and controllers
- channels behave differently across email, SMS, WhatsApp, and push
- no one place owns suppression windows, retry timing, or duplicate prevention
- reminders are sent without checking approval or opt-out rules
- the same entity receives conflicting nudges from separate surfaces

## 3. Core responsibilities

- evaluate reminder schedules and due reminder work
- derive reminder targets from entity, role, and channel context
- choose reminder class, template, and urgency tier
- enforce suppression, cooldown, and idempotency before dispatch
- hand off delivery to communications channels and queues
- record reminder creation, send attempts, outcomes, and suppression reasons
- trigger escalation when reminder thresholds are exhausted

## 4. Boundaries

### In scope

- reminder due-time evaluation
- reminder cadence and windows
- reminder audience resolution
- channel preference lookup
- suppression rules and cooldowns
- reminder payload selection
- dispatch handoff to communications
- reminder outcome logging

### Out of scope

- direct implementation of email, SMS, voice, or push channels
- rich workflow state modeling
- domain-specific mutation rules hidden inside the engine
- UI-only reminder preferences that never affect runtime enforcement
- long-term campaign analytics beyond runtime delivery telemetry

## 5. Architecture

## 5.1 Position in the stack

The reminder engine sits inside the automation runtime layer.

It consumes:

- lifecycle state and timer signals
- scheduling windows
- entity context from modules
- communication preferences
- governance constraints

It produces:

- reminder dispatch requests
- suppression logs
- escalation handoff events
- retryable reminder jobs
- reminder audit records

## 5.2 Reminder pipeline

A canonical reminder run should follow this shape:

1. identify candidate reminders
2. verify entity state is still reminder-eligible
3. resolve audience and channel preferences
4. check suppression, cooldown, and idempotency
5. generate reminder payload and urgency metadata
6. hand off to communications dispatch
7. persist outcome and next-step timing
8. emit escalation handoff if reminder budget is exhausted

## 5.3 Reminder classes

Titan should support several reminder classes:

- **deadline reminders** for quotes, invoices, approvals, and bookings
- **follow-up reminders** for non-responsive customers or staff
- **pre-event reminders** before visits, appointments, or jobs
- **post-event reminders** for proof, reviews, payment, or checklists
- **exception reminders** when required work remains incomplete
- **recovery reminders** after interrupted or re-driven automation runs

## 5.4 Reminder budgets

Each reminder family should have an explicit budget:

- maximum send count
- minimum spacing between sends
- allowed channels
- quiet hours and local-time windows
- expiry condition
- escalation threshold

This prevents infinite nagging loops and makes escalation predictable.

## 6. Contracts

## 6.1 Inputs

The reminder engine should consume:

- entity identifier
- company_id tenant boundary
- lifecycle stage or runtime state
- reminder type
- due timestamp
- allowed channels
- target audience role
- opt-out and consent state
- escalation policy reference

## 6.2 Outputs

The reminder engine should produce:

- reminder dispatch envelope
- reminder audit record
- suppression outcome
- retry scheduling record
- escalation signal when thresholds are crossed

## 6.3 Required manifests and policy sources

Relevant sources include:

- `lifecycle_manifest.json` for stage-linked reminder timing
- `signals_manifest.json` for reminder-related signals
- module configuration for entity-specific reminders
- company settings for channel permissions and timing
- communications templates and channel availability rules

## 7. Runtime behavior

## 7.1 Candidate generation

Reminder runs should originate from timer ticks, lifecycle stage checks, or event-triggered reevaluation.

The engine should never assume that because a reminder was scheduled earlier it is still valid now. Before creating dispatch work, it should re-check:

- current entity state
- tenant ownership
- cancellation or completion status
- approval blocks
- channel consent
- reminder expiry windows

## 7.2 Suppression and cooldown

Before dispatch, the engine should check:

- whether an equivalent reminder already succeeded
- whether a cooldown period is still active
- whether the entity moved to a new state that invalidates the reminder
- whether a stronger reminder or escalation already occurred
- whether the target channel is temporarily unavailable

Suppressed reminders should still be logged as intentional no-op outcomes.

## 7.3 Channel selection

The reminder engine should not embed channel mechanics, but it must select the intended route based on:

- user or customer preferences
- company policy
- urgency tier
- available integrations
- time-of-day rules
- channel fallback order

Example:
- try email first for normal payment reminders
- use SMS or WhatsApp for same-day visit reminders
- route to internal push for worker checklist reminders

## 7.4 Reminder completion

A reminder run is complete when the dispatch request and its runtime outcome are both recorded. The engine should persist:

- created_at / attempted_at / sent_at
- target audience
- channel chosen
- payload/template version
- suppression reason or send result
- next reminder eligibility timestamp
- escalation counter

## 8. Failure modes

### Reminder created after entity is no longer eligible

The engine must re-check state at send time and no-op safely.

### Duplicate reminder dispatch

Idempotency and recent-send lookup must suppress duplicates.

### Channel unavailable

The engine should either:
- choose an allowed fallback channel, or
- mark the reminder as deferred/retryable, depending on policy.

### Reminder loop without escalation

Reminder budgets must cap repeated sends and emit escalation handoff.

### Reminder sent during invalid local time

Scheduling windows and tenant-local timezone checks must prevent this.

### Conflicting reminders from multiple modules

The engine should normalize reminder families by entity and intent so equivalent reminders collapse into one authoritative cadence.

## 9. Dependencies

### Upstream

- scheduling engine
- lifecycle engine
- module lifecycle and entity state
- tenant settings and consent data
- signals and governance checks

### Downstream

- communications engine
- queue runtime
- escalation engine
- audit logs and telemetry
- recovery engine when sends need re-drive

## 10. Open questions

- Should reminder cadence live only in manifests, or also in tenant override tables?
- Which reminder families require approval before customer-facing delivery?
- How should voice reminders fit into the same budget model as email and messaging?
- When a reminder is suppressed due to a stronger channel event, should the next reminder timer reset?

## 11. Implementation notes

- Keep reminder due-evaluation in automation services, not in controllers or Filament callbacks.
- Reuse a single reminder action path from UI, scheduler, recovery, and AI-triggered flows.
- Store reminder runtime records with `company_id` so tenant-safe analytics and audits remain possible.
- Pair reminder dispatch with idempotency keys that include entity, reminder family, channel, and cadence slot.
- Keep template rendering and transport delivery in the communications layer; the reminder engine owns timing and policy, not channel code.
