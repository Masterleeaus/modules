# Titan Zero Documentation

Layer: Automation
Scope: Worked examples showing how engine families cooperate across lifecycle, reminders, escalation, recovery, and approvals
Status: Draft v1
Depends On: automation-engines.md, lifecycle-engine.md, reminder-engine.md, escalation-engine.md, recovery-engine.md, approval-runtime.md, trigger-evaluation.md
Consumed By: Module developers, AI/tooling engineers, ops teams, support teams, implementation agents
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Show practical end-to-end examples of how Titan automation behaves in real business situations.

## 2. Why it exists

The engine docs define responsibilities, but developers and operators still need to see the runtime chain in motion.

Without worked examples:

- lifecycle docs feel abstract
- approval pauses are hard to visualize
- escalation rules are implemented inconsistently
- recovery is treated as a vague fallback
- support teams struggle to explain runtime outcomes

This document turns the engine set into concrete scenarios.

## 3. Example format

Each example shows:

- incoming signal or request
- governance outcome
- engine sequence
- state transitions
- final module action
- side effects and audit records

## 4. Example A: booking confirmation to scheduled reminder

### Scenario

A customer confirms a booking for next Tuesday at 9:00 AM.

### Flow

1. Module emits `booking.confirmed`
2. AEGIS validates company permissions and policy
3. Sentinel confirms booking is schedulable
4. Lifecycle Engine advances `booking -> scheduled`
5. ProcessRecord is opened or updated
6. Reminder Engine creates reminder windows:
   - 24 hours before
   - 2 hours before
7. Automation runtime stores run records and idempotency key
8. At the right time, communications layer sends reminder
9. Audit log records delivery result

### Tables touched

- process_records
- automation_runs
- automation_outbox
- automation_trigger_logs

### Why it matters

This shows the normal happy-path split between lifecycle and reminder engines.

## 5. Example B: operator approval before reschedule

### Scenario

A worker requests a reschedule because site access is blocked.

### Flow

1. Module emits `booking.reschedule_requested`
2. Titan Zero proposes a new time based on availability
3. AEGIS marks the proposal as approval-sensitive
4. Sentinel verifies there is no domain conflict in the proposed slot
5. Approval Runtime pauses execution
6. An approval request is written to `automation_approvals`
7. Operator approves from panel or chat surface
8. Lifecycle Engine resumes
9. Module action `RescheduleBookingAction` executes
10. Reminder Engine regenerates reminder windows for the new slot

### Why it matters

This shows that approval is not a side note. It is a durable runtime pause with explicit resume.

## 6. Example C: missed reminder escalates to manager

### Scenario

A worker does not acknowledge a critical visit reminder.

### Flow

1. Reminder Engine publishes reminder message
2. No acknowledgement arrives within the required window
3. Trigger Evaluation marks reminder breach
4. Escalation Engine opens escalation run
5. Escalation policy routes message to manager and operations inbox
6. ProcessRecord status updates to `attention_required`
7. Communications layer sends escalation notice
8. Audit trail records breach and route target

### Why it matters

This separates reminder delivery from escalation reaction.

## 7. Example D: repeated downstream failure enters dead letter

### Scenario

A billing chase action fails three times because the downstream invoicing API is unavailable.

### Flow

1. Automation runtime executes billing chase run
2. Module service throws recoverable error
3. Retry Strategy schedules retry 1
4. Retry 1 fails
5. Retry Strategy schedules retry 2
6. Retry 2 fails
7. Retry exhaustion rule triggers quarantine
8. Dead-letter entry is created with payload, error code, and causation metadata
9. Escalation Engine optionally notifies support/operator
10. Recovery Engine waits for manual re-drive or automated health recovery

### Why it matters

This shows dead-letter quarantine as a controlled outcome, not a silent queue loss.

## 8. Example E: replay after service recovery

### Scenario

A failed downstream service is restored and quarantined runs must be re-driven.

### Flow

1. Support marks dead-letter entry eligible for re-drive
2. Recovery Engine checks idempotency and replay safety
3. Decision envelope is reconstructed from stored payload + process context
4. New automation run is created with a new attempt chain but linked causation
5. Module action executes successfully
6. Dead-letter row is marked resolved
7. ProcessRecord closes or returns to normal state

### Why it matters

Replay must reconstruct context, not just shove the old payload back into the queue.

## 9. Example F: recurring follow-up campaign after completion

### Scenario

A completed job should trigger a follow-up message after 3 days unless a complaint is opened first.

### Flow

1. Module emits `job.completed`
2. Lifecycle Engine moves process to `completed`
3. Automation runtime schedules follow-up window at +3 days
4. Trigger Evaluation watches for suppression event `complaint.opened`
5. If no suppression event arrives, Campaign or Reminder Engine dispatches follow-up
6. If complaint opens first, scheduled follow-up is cancelled
7. Audit records whether the follow-up was sent or suppressed

### Why it matters

This shows timed automation plus suppression logic.

## 10. Example G: overlap control on route dispatch

### Scenario

Dispatch recalculation is triggered twice by near-simultaneous updates.

### Flow

1. Two signals arrive close together
2. Trigger Evaluation resolves both as valid
3. Runtime lock key `dispatch-recalc:{company_id}:{route_date}` is requested
4. First run acquires lock and proceeds
5. Second run is deferred, coalesced, or cancelled by policy
6. Dispatch Engine completes and releases lock
7. Second run either exits as duplicate or recomputes with newer state snapshot

### Why it matters

This prevents duplicated route recomputation and keeps scheduling stable.

## 11. Example H: AI proposal that never executes directly

### Scenario

Titan Zero suggests sending a recovery offer after a missed job.

### Flow

1. Titan Zero produces proposal
2. AEGIS checks authority, pricing, and policy
3. Sentinel checks domain readiness, such as complaint state and active agreement
4. Decision envelope is emitted
5. Automation runtime schedules or pauses the proposed action
6. Module action executes only after runtime acceptance and any needed approval

### Why it matters

AI does not directly mutate the system. It proposes into governed runtime.

## 12. Common patterns shown across all examples

- ProcessRecord is the continuity spine
- idempotency prevents duplicate execution
- approvals are durable state, not transient UI prompts
- retries and dead letters are first-class runtime outcomes
- engine families cooperate but do not own each other’s business truth
- communications are side effects, not the source of execution state

## 13. Recommended usage

Use these examples when:

- defining module manifests
- designing runtime tables
- building admin/operator tooling
- explaining automation to support teams
- testing pause/resume, retry, and recovery flows

## 14. Result

Worked examples turn the automation stack into an implementation guide that developers, operators, and support teams can all use consistently.
