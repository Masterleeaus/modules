# Titan Zero Documentation

Layer: Automation
Scope: Recovery engine for failed, interrupted, or partially completed runtime work
Status: Draft
Depends On: automation-engines.md, lifecycle-engine.md, idempotency.md, signal governance, queue infrastructure
Consumed By: reminder engine, escalation engine, future workflow layer, approvals runtime, operator support tooling
Owner: Agent 06
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan safely recovers automated work after interruption, timeout, rejection, transient dependency failure, or partial execution.

## 2. Why it exists

Titan is not a single request-response app. It runs timed, reactive, and deferred automation across queues, jobs, approvals, and external channels. That means failures are normal, not exceptional. The recovery engine exists so the platform can resume, replay, compensate, or quarantine work without duplicating side effects.

## 3. Core responsibilities

- detect interrupted or incomplete automation runs
- classify failures as retryable, recoverable, compensatable, or terminal
- restore runtime state from durable records rather than request memory
- coordinate replay, compensation, quarantine, or operator escalation
- preserve auditability for every recovery decision

## 4. Boundaries

### In scope

- failed jobs and interrupted engine runs
- replay and resume rules
- compensation handoff for partially completed actions
- recovery checkpoints and state restoration
- recovery-aware interaction with approvals and dead letters

### Out of scope

- business workflow modeling
- UI-only retry buttons as primary recovery logic
- hidden compensating logic inside Filament actions or controllers
- human support SOPs except where the engine hands work to operators

## 5. Architecture

The recovery engine sits inside the automation runtime layer beside lifecycle, reminder, and escalation engines. It listens for runtime failures, timeout markers, stale in-progress executions, approval expiries, and downstream delivery failures.

A normal pattern is:

1. Engine run starts with an idempotency key and persisted runtime state.
2. A step updates checkpoints before and after any side effect.
3. If a failure occurs, the run is classified.
4. Recovery policy chooses one of four paths:
   - resume from last safe checkpoint
   - replay full run behind idempotency guard
   - compensate and mark recovered
   - quarantine into dead-letter or operator review

This keeps recovery in the engine layer instead of burying it inside one module or UI surface.

## 6. Contracts

Recovery should operate on durable contracts, not implicit controller state.

Expected inputs:

- engine run identifier
- tenant/company identifier
- idempotency key
- runtime checkpoint state
- failure classification
- attempt count
- originating signal or trigger reference
- approval state if execution was interrupted by governance

Expected outputs:

- resumed run
- replayed run
- compensated outcome
- dead-letter item
- operator review task
- audit record of recovery decision

## 7. Runtime behavior

The recovery engine should prefer the least destructive path.

- If no side effect occurred, replay is cheap.
- If side effects may have occurred, recovery should resume from the last verified checkpoint.
- If the system cannot prove safety, it should quarantine instead of guessing.
- If approval expired mid-run, recovery should pause and re-enter approval-aware runtime rather than auto-continue.

Recovery must be company-scoped and must preserve the original execution context, because tenant data boundaries are mandatory in this stack.

## 8. Failure modes

Common failure classes:

- transient infrastructure failure
- downstream API timeout
- duplicate delivery attempt risk
- approval timeout or denial
- stale lock or overlap conflict
- corrupted or missing runtime checkpoint
- non-idempotent side effect without confirmation state

Responses:

- retry with backoff when safe
- resume from checkpoint when possible
- compensate if partial completion is confirmed
- quarantine to dead-letter when safety is unknown
- escalate to operators when business judgment is required

## 9. Dependencies

Upstream:

- triggers and originating signals
- lifecycle engine state
- queue runtime
- approval/governance layer
- idempotency records

Downstream:

- retry strategy
- dead-letter queues
- approval runtime doc
- operator/admin tooling
- audit and observability layers

## 10. Open questions

- What exact checkpoint schema should be standardized across all engines?
- Which module actions need explicit compensation contracts rather than replay?
- Should recovery policy be global, engine-specific, or module-specific with shared defaults?

## 11. Implementation notes

Recovery should be implemented as a first-class engine service, not scattered inside jobs. Laravel’s queue, events, and service-container patterns support this split, while the broader engine blueprint places recovery beside retries, idempotency, dead letters, and approvals rather than inside generic workflow docs. Module manifests and signal governance should be able to point to recovery-capable actions, especially where automated runtime work can be replayed or quarantined safely.
