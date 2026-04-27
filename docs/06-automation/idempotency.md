# Titan Zero Documentation

Layer: Automation
Scope: Duplicate suppression, exactly-once intent handling, replay safety, and repeatable runtime execution
Status: Draft v1
Depends On: Signals, automation engines, scheduling, queues, module actions, audit logs, PWA replay behavior
Consumed By: Lifecycle engine, reminder engine, escalation engine, recovery engine, Titan Zero, PWA nodes, module actions
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan prevents the same automated intent from producing duplicate side effects when work is retried, replayed, rescheduled, or received from multiple surfaces.

## 2. Why it exists

Titan is a multi-surface system. The same business action may arrive from:

- a web UI
- an API client
- a queue retry
- a scheduler tick
- a PWA offline replay
- a Titan Zero proposal
- a recovery re-drive

Without idempotency controls, the system can accidentally:

- send the same reminder many times
- create duplicate jobs or invoices
- apply the same status change repeatedly
- charge or chase payment more than once
- replay stale node work as fresh work

Idempotency is therefore a core runtime safety mechanism, not an optional optimization.

## 3. Core responsibilities

- generate stable idempotency keys for automation actions
- suppress duplicate execution when the intended work already completed
- distinguish true retries from new work
- make offline replay safe
- support no-op outcomes without losing auditability
- preserve recoverability while preventing double side effects
- coordinate with retry, recovery, and dead-letter patterns

## 4. Boundaries

### In scope

- idempotency key design
- duplicate suppression checks
- result caching or execution registry patterns
- replay handling for nodes and PWAs
- queue retry safety
- concurrency-safe verification before side effects
- audit of duplicate attempts and suppressed runs

### Out of scope

- business approval policy itself
- domain mutation rules hidden inside module logic
- long-term archival strategy for all audit records
- UI wording for duplicate or replay events

## 5. Architecture

## 5.1 Canonical principle

Titan should aim for **exactly-once intent handling** even when infrastructure delivers messages more than once.

The system may physically receive the same trigger multiple times. Idempotency ensures those triggers resolve to one authoritative outcome.

## 5.2 Idempotency key model

An idempotency key should usually be derived from a stable combination of:

- tenant boundary (`company_id`)
- action type
- target entity id
- lifecycle stage or transition key where relevant
- source event or correlation id
- normalized payload hash when payload matters

Examples:

- `company:7|action:send_quote_reminder|quote:412|window:1`
- `company:7|action:auto_close_job|job:891|stage:awaiting_review`
- `company:7|action:create_invoice|visit:223|source_signal:abc123`

## 5.3 Idempotency registry

The platform should maintain an execution registry or equivalent store containing:

- idempotency key
- action type
- entity reference
- first_seen_at
- status (`executing`, `completed`, `failed`, `suppressed`, `expired`)
- result reference or output hash where useful
- correlation id
- replay source details when relevant

This registry is the canonical place to decide whether a new trigger is fresh work or a duplicate attempt.

## 6. Contracts

## 6.1 Inputs

Idempotency checks should receive:

- candidate action name
- tenant/company id
- target entity reference
- correlation or signal id
- normalized payload
- runtime context such as lifecycle stage or reminder window

## 6.2 Outputs

An idempotency check should return one of:

- proceed
- suppressed_duplicate
- stale_context
- already_completed
- already_executing
- retry_allowed
- replay_requires_reconciliation

## 6.3 Cross-layer contract

Every automation-capable action should be able to answer:

- what makes two requests “the same”
- what output is safe to reuse or treat as completed
- what state change proves the work already happened
- what retry path is legitimate versus a duplicate

That contract should be shared by modules, AI tools, and runtime engines.

## 7. Runtime behavior

## 7.1 Standard pattern

Before any side effect:

1. build the idempotency key
2. look up existing execution state
3. if already completed, suppress duplicate side effect
4. if already executing, avoid launching parallel duplicate work
5. if failed but retryable, continue according to retry policy
6. if stale against current entity state, return no-op or reconciliation state
7. write final outcome back to the registry

## 7.2 Safe side-effect classes

Idempotency matters especially for:

- outbound reminders and follow-ups
- financial record creation
- payment-chase messages
- job dispatch or auto-assignment
- approval-triggered actions
- recovery re-drives
- PWA sync replays

## 7.3 Offline replay handling

PWA or node-originated work must include replay metadata so the server can decide whether the action:

- is fresh and should run
- already ran centrally and should be suppressed
- partially ran and needs reconciliation
- conflicts with newer server truth and should be rejected

Offline replay should never bypass idempotency checks just because it originated on a trusted node.

## 7.4 Concurrency control

Idempotency is not just about historical duplicates. It must also stop concurrent duplicates.

Typical protections:

- short-lived execution lock on the idempotency key
- transaction boundary around registry update + side effect trigger
- post-action verification against entity state
- queue/job uniqueness where appropriate

## 8. Failure modes

### Duplicate trigger storm

A scheduler, retry worker, or external integration submits the same work repeatedly.

Response:

- collapse repeated triggers onto one idempotency key
- suppress downstream side effects
- alert only if repetition indicates unhealthy upstream behavior

### Partial execution

A job updates entity state but crashes before marking the registry completed.

Response:

- verify entity end state during retry
- convert retry to complete/suppressed where possible
- avoid replaying side effects blindly

### Replay against newer truth

An offline client replays a stale action after the entity has advanced.

Response:

- compare replay intent with current server state
- mark as stale or reconciliation-needed
- preserve audit record of the rejected replay

### Key design too broad or too narrow

Keys that are too broad suppress legitimate work. Keys that are too narrow allow duplicates.

Response:

- document per-action key semantics
- keep keys explicit and testable
- include window/stage/correlation identifiers where needed

## 9. Dependencies

Upstream dependencies:

- signals and correlation ids
- module action contracts
- queue runtime
- lifecycle/runtime context
- tenant-safe entity state

Downstream consumers:

- retry strategy
- dead-letter and recovery flows
- audit and observability layers
- PWA sync reconciliation
- AI action execution

## 10. Open questions

- Which actions need long-lived idempotency records versus short-lived execution suppression?
- Should result caching be retained for read-heavy AI/tool actions as well as mutating actions?
- What is the standard retention period for idempotency keys by action class?
- How should cross-channel communications share idempotency when one business event can target multiple channels?

## 11. Implementation notes

- Treat idempotency as part of the action contract, not just queue middleware.
- Prefer stable semantic keys over random per-request tokens for automation.
- Verify entity state as well as registry state before deciding a retry must rerun.
- Record suppressed duplicates in audit logs so operators can debug hidden repetition.
- Pair idempotency with retry, approval, recovery, and dead-letter docs; it should never live as an isolated reliability note.
