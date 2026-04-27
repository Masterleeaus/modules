# 23. Automation, Workflow, and Reliability Engine

## Purpose

This document defines how the Worksuite + Titan platform should execute automations without turning the application into a brittle tangle of callbacks, cron hacks, and UI-bound side effects. The goal is to create a durable execution substrate that can safely run from web requests, queues, schedules, mobile/device signals, AI proposals, operator approvals, and external integrations.

The core rule is simple: **automation is a platform capability, not a behavior hidden inside controllers, Filament actions, or ad hoc module code**.

## Why this layer must exist

The full engine blueprint already points to dedicated platform areas for `Automation`, `Workflows`, `Scheduling`, `Signals`, and reliability concerns such as retries, idempotency, dead letters, inboxes, and outboxes. The module/plugin blueprint also makes it clear that core domain behavior must live in reusable actions, services, events, listeners, and jobs rather than being trapped in a single UI surface. Together, those sources imply a formal automation runtime rather than scattered implementation logic.

Without this layer, the same business event gets implemented four different ways:

- a controller runs immediate side effects
- a Filament table action does a different version
- a scheduled command repeats the same logic imperfectly
- an AI proposal bypasses existing rules entirely

That duplication is the fastest path to drift, hidden bugs, and policy violations.

## Constitutional rule

Automation engines may **propose, orchestrate, defer, sequence, retry, or escalate** work, but they should not redefine domain truth. Domain truth still lives inside the relevant modules and platform services.

That means:

- workflows coordinate state changes
- engines evaluate triggers and timing
- queues perform deferred execution
- approvals gate sensitive steps
- domain actions remain the source of truth for actual business mutations

A workflow is therefore not “the code that changes invoices or bookings.” It is the coordination layer that decides **when** and **under what conditions** the relevant domain action should run.

## Core runtime model

The platform should model automation as five linked layers.

### 1. Trigger layer

This is how execution begins.

Sources include:

- domain events from modules
- scheduled timers and recurrence rules
- inbound signal envelopes
- operator actions
- package/policy changes
- AI proposals
- communication outcomes such as bounce, reply, payment success, no-answer, delivery failure

A trigger should be normalized into a stable internal event object before anything downstream runs.

### 2. Evaluation layer

This determines whether any workflow or engine should react.

Responsibilities include:

- policy checks
- tenant/package capability checks
- condition matching
- deduplication
- cool-down windows
- dependency checks
- risk classification
- approval requirements

This layer decides whether an event should be:

- ignored
- queued
- scheduled
- escalated
- proposed for approval
- executed immediately through a safe action

### 3. Orchestration layer

This layer selects the workflow, pipeline, or engine path.

Examples:

- lifecycle progression
- quote follow-up
- reminder cadence
- dispatch escalation
- payment chase
- campaign response routing
- rebooking after failed attendance
- service recovery after issue detection

The orchestration layer should not be responsible for low-level business changes. It should compose and sequence existing actions.

### 4. Execution layer

This is where actual work happens.

Execution should use:

- action classes
- services
- queued jobs
- notification/messaging dispatchers
- signal emission
- API adapters
- import/export workers

Every execution step should be idempotent or protected by idempotency keys.

### 5. Reliability and audit layer

This layer ensures that automation is survivable and inspectable.

It owns:

- retries
- backoff
- dead-letter queues
- duplicate suppression
- outbox/inbox patterns
- replay
- approval history
- operator-visible logs
- failure reasons
- execution metrics

This is the difference between “a feature that sometimes runs” and “a system engine.”

## Recommended platform structure

The platform blueprint already suggests the right shape. The automation runtime should be treated as a first-class platform subsystem such as:

```text
app/Platform/Automation/
  Engines/
  Coordinators/
  Triggers/
  Rules/
  Conditions/
  Pipelines/
  Executors/
  RuntimeState/
  Retries/
  Idempotency/
  DeadLetters/
  Outbox/
  Inboxes/
  Approvals/
  Audit/
  Support/
```

A complementary workflow system should live beside it:

```text
app/Platform/Workflows/
  Definitions/
  StateMachines/
  StepHandlers/
  Guards/
  Conditions/
  Transitions/
  Approvals/
  Templates/
  Metrics/
  Support/
```

And timing itself deserves explicit ownership:

```text
app/Platform/Scheduling/
  Cron/
  Timers/
  Delays/
  Recurrence/
  Windows/
  OverlapControl/
  TaskHooks/
  TimePolicies/
  Support/
```

## Engine families

The engine layer should not be a single generic “automation service.” It should consist of named engine families with clear responsibilities.

### Lifecycle Engine

Owns progression through lead → quote → booking → job → invoice → follow-up style flows. This is where multi-step operational state progression becomes formal and observable.

### Reminder Engine

Owns timed reminders for visits, approvals, invoices, onboarding, tasks, site access follow-ups, and dormant-contact nudges.

### Escalation Engine

Owns SLA misses, no-response timeouts, failed communication attempts, unresolved issues, and high-risk AI proposals that need human review.

### Dispatch Engine

Owns work allocation follow-ups, arrival confirmations, route drift alerts, late-start handling, and dispatch exception handling.

### Follow-up Engine

Owns quote nudges, review requests, recovery outreach, post-completion contact, and re-engagement loops.

### Rebooking Engine

Owns missed appointments, skip/reschedule flows, route compression, and replacement-slot generation.

### Recovery Engine

Owns issue remediation, complaint recovery, credit workflows, service-failure handling, and operator escalation.

### Campaign Engine

Owns sequence-driven comms that feel operationally close to Omni, but still need event-driven coordination.

### Billing Chase Engine

Owns overdue reminders, staged chase policies, channel escalation, and safe customer-contact rules.

### Compliance Engine

Owns policy checks, consent expiry, missing documents, incomplete approvals, and regulated step gating.

## Workflow definitions versus actions

A clean system makes a strong distinction here.

### Actions

Actions are the smallest trustworthy business units:

- CreateBookingAction
- ApproveQuoteAction
- RescheduleVisitAction
- RecordPaymentAction
- SendReminderAction
- MarkJobArrivedAction

These should be reusable from:

- controllers
- Filament pages/resources
- API endpoints
- jobs
- signal handlers
- AI tools
- imports
- workflow steps

### Workflows

Workflows compose those actions across states and time:

- New lead intake workflow
- Quote accepted workflow
- Appointment day-of workflow
- Job issue recovery workflow
- Overdue invoice chase workflow

A workflow step should call an action or queue a job, not reimplement the action.

## Approval-aware execution

Because the Titan system is approval-centric, not everything should run immediately.

The workflow/automation runtime should support three execution modes:

### Automatic

Safe, low-risk steps that can run without approval.

Examples:

- cache refresh
- internal status sync
- non-sensitive notification dispatch
- schedule recalculation
- low-risk reminder dispatch

### Proposed

The engine prepares a step, but it enters an approval queue first.

Examples:

- customer-facing recovery message generated by AI
- credit or refund suggestion
- nonstandard schedule change
- cross-domain action with financial impact

### Restricted

Execution is blocked unless a privileged actor or policy explicitly permits it.

Examples:

- invoice voids
- mass communications without consent proof
- irreversible data merges
- tenant-wide configuration changes

The runtime must be able to store and resume pending steps after approval.

## Reliability contracts

### Idempotency

Every automation-triggered mutation should be safe against replay. This usually means storing:

- idempotency key
- event/signal source
- tenant id
- action name
- payload hash
- first processed at
- last attempted at
- current state

### Retries and backoff

Do not allow modules to invent retry behavior ad hoc. The platform should standardize:

- max attempts
- exponential or staged backoff
- non-retryable failure classes
- escalation threshold

### Dead letters

Failed execution should not disappear. It should move to a recoverable state with:

- full payload snapshot
- failure reason
- retries exhausted flag
- operator requeue option
- related tenant/module references

### Outbox/inbox

Cross-system and cross-module events should use an outbox/inbox pattern wherever possible so a successful business mutation does not depend on synchronous downstream delivery.

### Replay

The system should support replaying:

- signals
- workflow transitions
- automation steps
- failed jobs

Replay should always remain policy-aware and dedup-aware.

## Tenant and actor boundaries

Automation must never weaken tenancy.

Every runtime object should remain scoped with:

- `company_id` as the tenant boundary
- `user_id` where there is a responsible initiator, approver, assignee, or actor
- optional device/session/channel context when relevant

A workflow that changes tenant data without an explicit tenant context should be treated as invalid by design.

## How Filament fits

Filament is not the engine. It is the operator surface.

Filament should provide:

- approval queues
- workflow state visualizations
- failed-run inspection
- replay controls
- dead-letter triage
- engine dashboards
- timeline views
- metrics and bottleneck surfaces

What Filament should not do:

- contain core step logic in page callbacks
- implement workflow rules in widgets
- bypass actions/services
- create alternate mutation paths not used by API/PWA/AI

## How AI fits

Titan Zero and related cores should interact with the automation layer through proposals and tools, not by bypassing the runtime.

AI can:

- propose workflow starts
- classify situations into workflow templates
- generate suggested messages/content
- rank next-best actions
- summarize failure context
- recommend escalation

AI should not:

- directly mutate operational data without the approved action path
- bypass approval requirements
- redefine workflow state machines informally in prompts

The automation/workflow engine is where AI suggestions become durable platform behavior.

## Observability requirements

Every engine should emit inspectable signals and metrics such as:

- trigger received
- workflow started
- step queued
- step executed
- step failed
- approval requested
- approval granted/denied
- replay attempted
- replay succeeded/failed
- dead-letter created

Operator-facing observability should answer:

- What happened?
- Why did it happen?
- What did it try to do?
- What policy gated it?
- What failed?
- What can be replayed safely?

## Implementation doctrine

When building this layer in Worksuite/Titan:

1. Do not start from UI.
2. Define event and workflow contracts first.
3. Make actions the atomic business units.
4. Keep retries, approvals, replay, and audit in the platform layer.
5. Let modules expose triggers and capabilities through manifests, but never force them to own engine infrastructure.
6. Keep timing, automation, workflows, and signals separate but interoperable.
7. Treat automation as infrastructure for all surfaces: web, Filament, API, mobile, PWA, nodes, and AI.

## Final principle

The platform becomes “AI-controlled” in a safe and durable way only when it has a real automation/workflow runtime. Without that runtime, AI and module logic are just scattered behaviors. With it, the system gains memory, policy, replay, reliability, and coordination.
