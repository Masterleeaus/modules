# Titan Zero Documentation

Layer: Automation
Scope: Dead-letter queues and quarantine handling for failed automation work
Status: Draft
Depends On: automation-engines.md, retry-strategy.md, idempotency.md, recovery-engine.md, signal governance
Consumed By: operator tooling, recovery engine, escalation engine, approvals runtime, support workflows
Owner: Agent 06
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan quarantines automation work that cannot be safely retried, resumed, or completed automatically.

## 2. Why it exists

Retries are useful for transient faults, but some failures are no longer safe to repeat. A downstream action may already have partially executed, a payload may be invalid, a policy decision may have blocked progression, or a dependency may be permanently misconfigured. Dead-letter queues exist so Titan does not loop forever, duplicate side effects, or silently drop failed work.

## 3. Core responsibilities

- quarantine terminal or unsafe-to-retry automation runs
- preserve enough context for replay, diagnosis, or operator intervention
- separate transient runtime failures from true dead-letter cases
- provide routing rules for different dead-letter classes
- keep an audit trail linking the dead-letter item to the original trigger, signal, and attempts

## 4. Boundaries

### In scope

- dead-letter classification
- dead-letter payload structure
- quarantine routing and storage
- replay eligibility rules
- operator review handoff
- approval-linked dead-letter scenarios

### Out of scope

- general queue infrastructure tuning
- workflow modeling of business states
- UI styling for operator dashboards
- hiding failures inside controller or panel callbacks

## 5. Architecture

Dead-letter queues sit after retry and recovery policies. A work item should only enter dead-letter when the engine can no longer prove that retry or resume is safe.

A typical path is:

1. Trigger produces a runtime job or engine run.
2. Retry strategy classifies the failure and exhausts allowed attempts where appropriate.
3. Recovery engine checks whether resume, replay, or compensation is safe.
4. If no safe automatic path remains, the item is moved to dead-letter.
5. The dead-letter item stores execution context, failure history, policy outcome, and replay eligibility.
6. Operator tooling or support workflows decide whether to fix, replay, compensate, or permanently close the item.

This keeps quarantine explicit and reviewable rather than scattering one-off fallback logic across modules.

## 6. Contracts

A dead-letter item should preserve the minimum durable contract needed for safe later action.

Expected fields:

- dead-letter identifier
- original engine run identifier
- tenant/company identifier
- originating trigger or signal reference
- idempotency key
- engine type
- attempt count and retry history
- failure class and terminal reason
- last safe checkpoint
- replay eligibility flag
- approval state if policy blocked or interrupted execution
- timestamps for created, last attempted, quarantined, and reviewed

Expected outputs:

- replay request
- compensation request
- support escalation task
- permanent closure record
- audit entry for all operator actions

## 7. Runtime behavior

Dead-letter behavior should be conservative.

- A transient timeout with no side effect should stay in retry/recovery, not dead-letter.
- A suspected duplicate external action should dead-letter quickly if the system cannot verify outcome.
- A policy denial should dead-letter only when the denial is terminal for that run, otherwise it should return to approval runtime.
- A malformed payload should dead-letter immediately with validation context attached.

Dead-letter queues should also be segmented by class where useful, such as:

- delivery dead letters
- policy dead letters
- data integrity dead letters
- integration dead letters
- manual review dead letters

That segmentation helps operators understand what kind of intervention is needed.

## 8. Failure modes

What can go wrong in the dead-letter layer itself:

- insufficient context stored to replay or diagnose
- items incorrectly dead-lettered when a safe retry path existed
- items never reviewed and effectively become silent graveyards
- duplicate replays launched without idempotency checks
- dead-letter routing crosses tenant boundaries

Responses:

- enforce a durable dead-letter schema
- require recovery policy classification before quarantine
- add review SLAs or escalation timers for unhandled items
- require replay through the same idempotent engine path, not ad hoc code
- scope every dead-letter item by company_id and original execution context

## 9. Dependencies

Upstream:

- triggers and originating signals
- retry strategy
- recovery engine
- idempotency contracts
- governance and approval outcomes

Downstream:

- operator review tooling
- support workflows
- compensation handlers
- audit and observability
- future dead-letter dashboards and metrics

## 10. Open questions

- Should dead-letter storage be queue-native, DB-native, or dual-written for observability?
- Which dead-letter classes should auto-escalate versus wait for operator review?
- How long should replay eligibility remain open before closure becomes mandatory?
- Which dead-letter reasons should create customer-facing communication automatically?

## 11. Implementation notes

Keep dead-letter logic in the automation/runtime layer, not inside individual Filament actions, controllers, or module UIs. Replays should go back through the same action/service/event path so business rules stay consistent. The module and engine blueprints both point toward manifest-driven execution, signal handling, queue-backed retries, and explicit reliability patterns rather than hidden callback recovery. fileciteturn0file0 fileciteturn0file6 fileciteturn0file7
