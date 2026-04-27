# Titan Zero Documentation

Layer: Automation
Scope: Runtime state tracking for automation execution, retries, approvals, escalation, and recovery
Status: Draft v1
Depends On: Automation engines, queues, signals, governance, audit layer
Consumed By: Titan Zero, AEGIS, operations UI, recovery tools, telemetry, support tooling
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define the state store that records where automation work is, what happened to it, and what should happen next.

## 2. Why it exists

Automation cannot be reliable if runtime state is implicit.

Without a runtime state store:

- approvals lose context between proposal and execution
- retries become guesswork
- recovery cannot safely resume work
- dead-letter items have no clear history
- escalation cannot distinguish first failure from repeated failure
- operators cannot see whether work is queued, blocked, failed, or completed

The runtime state store makes execution visible, resumable, and auditable.

## 3. What it tracks

For each runtime item, the state store should capture:

- execution ID
- correlation ID
- originating signal or schedule trigger
- engine owner
- company or tenant scope
- current status
- retry count
- approval state
- escalation state
- last error code and message summary
- timestamps for received, started, completed, blocked, failed, retried, escalated, and recovered states
- idempotency key
- next planned action time if deferred

## 4. Core responsibilities

- provide a single current state for runtime work
- preserve history of major transitions
- support pause and resume
- support safe recovery and replay
- expose operator-readable status
- support observability and metrics rollups
- prevent duplicate active execution for the same idempotency key

## 5. Canonical model

The store should separate **current state** from **event history**.

### Current state record

Represents the latest known truth about a runtime item.

Useful fields:

- execution_id
- company_id
- engine_name
- status
- owner_reference
- idempotency_key
- current_attempt
- awaiting_approval boolean
- dead_lettered boolean
- next_run_at
- last_error_code
- updated_at

### Event history record

Represents important transitions over time.

Useful events:

- received
- approved
- rejected
- started
- succeeded
- failed
- retry_scheduled
- escalated
- dead_lettered
- recovered
- cancelled

## 6. State rules

### One active owner

A runtime item should have one active owner at a time.

Examples:

- lifecycle owns before reminder handoff
- reminder owns while reminder delivery is active
- recovery owns once replay begins

### Current state must be queryable fast

Operators and automations need fast status reads. Use the current-state table or cache-backed projection for active work.

### History must be append-only

Transition history should not be rewritten except for legal redaction or controlled repair tooling.

## 7. Relationship to queues

Queue jobs are not the source of truth for business runtime status.

Queue payloads are transport. The runtime state store is the source of truth for:

- whether work is still relevant
- whether work has already completed
- whether approval is missing
- whether retries are exhausted
- whether recovery is allowed

## 8. Relationship to idempotency

The runtime state store is one of the key idempotency enforcement surfaces.

At minimum it should support:

- checking whether the same idempotency key is already active
- checking whether a completed execution already exists
- suppressing duplicate execution or merging it into the existing runtime record

## 9. Relationship to approvals

When a runtime item pauses for approval, the state store must preserve:

- what action was proposed
- why approval was required
- who must approve it
- what context pack supports the decision
- what state should resume on approval

This avoids rebuilding execution context from scratch.

## 10. Relationship to escalation and recovery

### Escalation

Escalation uses runtime state to determine:

- how long work has been blocked or failing
- whether thresholds are breached
- who should be notified
- whether escalation has already happened

### Recovery

Recovery uses runtime state to determine:

- whether work is safe to replay
- what step failed last
- whether prior side effects completed
- whether compensation is required before resume

## 11. UI / operator value

A runtime state store enables surfaces such as:

- active automation queue
- awaiting approval queue
- retry queue
- dead-letter review queue
- escalated items dashboard
- recovery workbench
- per-company automation health view

## 12. Failure classification

The store should distinguish failure types rather than collapsing them into one status.

Recommended classification:

- transient_failure
- permanent_failure
- policy_blocked
- approval_blocked
- dependency_unavailable
- dead_lettered
- manually_cancelled

## 13. Recommended implementation shape

Suggested structure:

```text
app/Platform/Automation/
├─ RuntimeState/
│  ├─ RuntimeExecution.php
│  ├─ RuntimeTransition.php
│  ├─ RuntimeStateRepository.php
│  ├─ RuntimeStateProjector.php
│  └─ RuntimeStateService.php
```

Supporting tables or projections should be company-scoped and searchable by status, engine, next run time, and approval state.

## 14. Outcome

A runtime state store gives Titan durable memory for automation execution. It is the layer that lets approvals pause cleanly, retries stay controlled, escalations stay informed, and recovery resume safely.
