# Full Engine Blueprint

Status: Canonical draft
Layer: Automation runtime

## Goal

Turn the system from CRUD plus UI into a real operating engine.

## Engine Areas

```text
app/Platform/Automation/
├─ Engines/
│  ├─ LifecycleEngine/
│  ├─ ReminderEngine/
│  ├─ EscalationEngine/
│  ├─ DispatchEngine/
│  ├─ FollowUpEngine/
│  ├─ RebookingEngine/
│  ├─ RecoveryEngine/
│  ├─ CampaignEngine/
│  ├─ BillingChaseEngine/
│  └─ ComplianceEngine/
├─ Coordinators/
├─ Triggers/
├─ Rules/
├─ Conditions/
├─ Pipelines/
├─ Executors/
├─ RuntimeState/
├─ Retries/
├─ Idempotency/
├─ DeadLetters/
├─ Outbox/
├─ Inboxes/
├─ Approvals/
├─ Audit/
└─ Support/
```

## Engine Building Blocks

### Actions
Atomic write operations.

### Events
Announce important domain changes.

### Listeners
React immediately to domain events.

### Jobs
Defer work to queues.

### Notifications/Mail/Channels
Deliver system output to users, workers, customers, and admins.

### Workflows
Handle guarded multi-step lifecycle progression.

### Scheduler
Runs timed automations.

### Outbox / Inbox
Stabilize delivery and prevent silent failures.

### Retries / Idempotency / Dead Letters
Make automation safe on noisy systems.

### Approvals
Support suggest, queue-for-review, and auto-execute modes.

## Required Supporting Trees

```text
app/Platform/Workflows/
├─ Definitions/
├─ StateMachines/
├─ StepHandlers/
├─ Guards/
├─ Conditions/
├─ Transitions/
├─ Approvals/
├─ Templates/
├─ Metrics/
└─ Support/

app/Platform/Scheduling/
├─ Cron/
├─ Timers/
├─ Delays/
├─ Recurrence/
├─ Windows/
├─ OverlapControl/
├─ TaskHooks/
├─ TimePolicies/
└─ Support/

app/Platform/Communications/
├─ Mail/
├─ Notifications/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Email/
├─ Voice/
├─ Push/
├─ Templates/
├─ Routing/
└─ Support/
```

## Engine Modes

### Suggest Mode
AI or rule engine proposes action only.

### Review Queue Mode
System stages pending action for approval.

### Auto Mode
System executes automatically under policy.

## Required Runtime Guarantees

- idempotent action handling
- retryable jobs
- queue-safe notifications
- auditable transitions
- replay-safe signal/event IDs
- delayed and recurring scheduling
- per-tenant isolation
