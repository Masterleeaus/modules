# Scheduling + Retry + Idempotency Blueprint

Status: Canonical draft  
Layer: Timed execution and reliability controls

## Role

This layer makes automations safe. It governs when work runs, how retries happen, how duplicates are prevented, and how failed work is surfaced.

## Tree

```text
app/Platform/Scheduling/
├─ Cron/
├─ Timers/
├─ Delays/
├─ Recurrence/
├─ Windows/
├─ OverlapControl/
├─ TaskHooks/
├─ TimePolicies/
├─ Retries/
├─ Idempotency/
├─ Outbox/
├─ Inboxes/
├─ DeadLetters/
└─ Support/
```

## Scheduling Responsibilities

### Cron / Timers / Delays
- recurring tasks
- delayed sends
- reminder ladders
- retry backoff windows

### Recurrence
- daily/weekly/monthly rules
- working-day policies
- blackout periods
- site-specific timing windows

### Overlap Control
- prevent duplicate runs
- prevent conflicting dispatch operations
- stop the same reminder set from firing twice

## Reliability Responsibilities

### Retries
- bounded retry counts
- exponential backoff
- classified retry rules by failure type

### Idempotency
Use idempotency keys for:
- payment intent creation
- signal processing
- invoice sends
- booking creation from webhooks
- repeated worker sync submissions

### Outbox Pattern
- persist outbound work before delivery
- dispatch from queue
- mark sent / failed / retrying

### Inbox Pattern
- dedupe inbound webhooks and device sync packets
- store source message IDs and hashes

### Dead Letters
- move exhausted failures to review queue
- attach failure reason, last payload, retry history

## Recommended Tables

- `scheduled_tasks`
- `task_runs`
- `retry_logs`
- `idempotency_keys`
- `outbox_messages`
- `inbox_messages`
- `dead_letter_items`

## Rule

Every important automation should be:
- schedulable
- retry-aware
- idempotent
- auditable
