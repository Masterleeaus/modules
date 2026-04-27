# Titan Zero Documentation

Layer: Automation
Scope: Runtime automation engines for recurring, timed, reactive, and recovery work
Status: Draft v1
Depends On: Signals, governance, scheduling, module manifests, Laravel queue/runtime infrastructure
Consumed By: Titan Zero, AEGIS, Sentinels, workflow layer, PWA nodes, communications layer, module actions
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define the engine layer that performs automated runtime work safely, repeatedly, and with auditability. This layer is where Titan turns approved intent into controlled execution patterns such as reminders, escalations, retries, recovery runs, timed actions, and dead-letter quarantine.

## 2. Why it exists

Titan needs a distinct layer between **workflow definition** and **runtime execution**.

Without an automation layer:

- retries get hidden inside controllers or ad hoc jobs
- reminder logic gets duplicated across modules
- failure handling becomes inconsistent
- approvals are bypassed or bolted on too late
- signals cannot be replayed safely
- the same business action behaves differently from UI, API, queue, and AI paths

The automation layer solves this by making runtime behavior explicit, reusable, and governed.

## 3. Core responsibilities

- execute recurring, delayed, and reactive automation
- coordinate lifecycle, reminder, escalation, and recovery engines
- enforce retries, idempotency, and failure quarantine
- pause execution where approval is required
- dispatch approved work into queues, channels, and downstream modules
- preserve audit trails for all runtime decisions
- support replay and rollback-safe reprocessing patterns

## 4. Boundaries

### In scope

- lifecycle engine execution rules
- reminder timing and reminder dispatch
- escalation thresholds and escalation handoff
- retry policies and retry exhaustion handling
- idempotency enforcement and duplicate suppression
- dead-letter capture and re-drive patterns
- approval interruption points in runtime execution
- recovery and replay coordination
- execution telemetry, status, and runtime logs

### Out of scope

- domain-specific business rules owned by modules
- workflow state-machine modeling itself
- UI presentation of automation settings
- raw signal validation and governance law definition
- direct ownership of messaging channel implementations

## 5. Architecture

## 5.1 Engine position in the stack

The automation layer sits below AI reasoning and workflow planning, but above module actions and channel delivery.

Typical order:

1. **Signals** capture an event or request.
2. **Governance** validates policy, authority, and readiness.
3. **Workflow** decides what stage or transition is relevant.
4. **Automation engines** decide when, how often, and under what reliability rules work runs.
5. **Module actions / services / jobs** perform the actual domain operation.
6. **Communications** deliver outbound channel effects.
7. **Audit and telemetry** record what happened.

## 5.2 Engine families

### Lifecycle Engine
Owns timed and condition-based runtime progress across business stages such as lead, quote, booking, job, invoice, follow-up, and recovery.

### Reminder Engine
Owns reminder schedules, recurrence windows, suppression rules, and delivery handoff.

### Escalation Engine
Owns timeout detection, SLA breaches, missed approvals, repeated failures, and upward routing to users, managers, or governance layers.

### Recovery Engine
Owns replay, resume, compensation, retry exhaustion follow-up, and post-failure re-entry logic.

### Reliability Subsystems
Shared support services across engines:

- retry strategy
- idempotency registry
- dead-letter queues
- approval runtime gates
- execution audit log
- runtime state store

## 5.3 Canonical structure

Recommended structure:

```text
app/Platform/Automation/
├─ Engines/
│  ├─ LifecycleEngine/
│  ├─ ReminderEngine/
│  ├─ EscalationEngine/
│  ├─ RecoveryEngine/
│  ├─ DispatchEngine/
│  └─ BillingChaseEngine/
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
├─ Approvals/
├─ Audit/
└─ Support/
```

This keeps runtime behavior visible instead of scattering it across controllers, Filament callbacks, cron commands, and module jobs.

## 6. Contracts

## 6.1 Inputs

Automation engines should consume structured, validated input rather than raw UI state.

Typical inputs:

- approved signal envelopes
- lifecycle manifest entries
- module feature flags
- company-scoped settings
- scheduling policies
- AI-produced proposals that already passed governance
- queued runtime context packs

## 6.2 Outputs

Automation engines should emit structured outputs such as:

- execution started / succeeded / blocked / failed events
- approved jobs for downstream processing
- reminder dispatch requests
- escalation records
- dead-letter entries
- recovery tasks
- audit records
- runtime metrics

## 6.3 Manifest dependencies

Modules should be able to declare automation-relevant surfaces using manifests such as:

- `lifecycle_manifest.json`
- `signals_manifest.json`
- `ai_tools.json`
- `omni_manifest.json`

Automation reads these as contracts, not as source-of-truth business logic. Modules still own the domain action; automation owns the runtime policy around it.

## 6.4 Approval interruption contract

Any engine step that can change business state, send regulated communications, trigger money movement, or cross a risk boundary must be able to pause and wait for approval.

States should support at minimum:

- proposed
- queued
- awaiting_approval
- approved
- rejected
- executing
- completed
- failed
- dead_lettered
- recovered

## 7. Runtime behavior

## 7.1 Execution pattern

A standard engine run should behave like this:

1. receive a valid runtime trigger
2. build execution context
3. compute idempotency key
4. check duplicate or previously-completed work
5. check approval requirements
6. either pause for approval or continue
7. dispatch the correct action/job/service
8. capture result state
9. schedule retry, escalation, or recovery if needed
10. write audit and metrics

## 7.2 Trigger types

Supported trigger classes:

- **time-based**: cron, delayed, recurrence window
- **event-based**: signal emitted, status changed, record created
- **condition-based**: threshold crossed, no response within SLA
- **manual**: user or admin re-drive
- **AI-originated**: Titan Zero proposal after approval

## 7.3 Approval gates

Approval gates should interrupt execution before:

- sending sensitive outbound messages
- creating or changing financial records
- rescheduling field work
- applying policy-sensitive automation
- performing cross-tenant or cross-channel actions

Approval is a runtime state, not a hidden UI checkbox.

## 7.4 Multi-surface consistency

The same runtime action must behave identically whether triggered from:

- web controller
- API controller
- Filament page/action
- queue job
- scheduler
- Titan Zero tool call
- PWA offline sync replay

That means business work must be delegated to shared actions/services, while automation wraps those shared actions in reliability and governance behavior.

## 8. Failure modes

## 8.1 Duplicate execution

Causes:

- repeated webhook delivery
- queue reprocessing
- manual re-drive during in-flight execution
- offline replay collisions

Response:

- compute deterministic idempotency key
- record first successful execution
- short-circuit duplicate runs
- log duplicate suppression clearly

## 8.2 Retry storms

Causes:

- broken third-party service
- bad payload repeatedly retried
- missing approval never resolved

Response:

- capped retries with backoff
- distinguish transient vs terminal failure
- move exhausted items to dead letter
- emit escalation event

## 8.3 Hidden business logic drift

Cause:

- module embeds critical runtime rules in controller or UI callback

Response:

- move reusable domain work into actions/services
- have automation call that shared layer only
- treat direct UI-only behavior as architectural drift

## 8.4 Stuck approval queues

Cause:

- runtime item remains awaiting approval without timeout rules

Response:

- apply approval SLA
- escalate to responsible authority
- support cancel, supersede, or re-queue actions

## 8.5 Replay corruption

Cause:

- recovery run reuses stale state or missing snapshot references

Response:

- record execution checkpoints
- require snapshot or envelope versioning where necessary
- only replay from explicit safe boundaries

## 9. Dependencies

### Upstream

- signal intake and validation
- governance / AEGIS policy checks
- workflow definitions
- scheduling/time policy layer
- module manifests and settings
- tenant scoping and permissions

### Downstream

- module actions and services
- jobs / queues
- notifications / mail / messaging channels
- audit logging
- metrics and observability
- recovery and support tooling

## 10. Relationship to workflows

Automation and workflow are related but not identical.

### Workflow owns

- state models
- transitions
- stage meaning
- business progression logic

### Automation owns

- when runtime work fires
- how it retries
- how it pauses for approval
- how it escalates
- how it quarantines failure
- how it replays or recovers

A workflow may say *“invoice follow-up after 3 days if unpaid.”*
Automation decides:

- the actual timer
- whether duplicate follow-ups are blocked
- whether approval is needed
- what happens if email fails
- when escalation starts
- where the failed item is quarantined

## 11. Implementation notes

- Keep controllers thin; they should hand off to requests, DTO/data objects, actions, and services.
- Keep events and queued jobs as first-class runtime tools for decoupled side effects.
- Keep module manifests explicit so automation can discover lifecycle and signal surfaces safely.
- Keep company-scoped settings outside global assumptions; automation must respect tenant boundaries.
- Keep caches, retry policy, and dead-letter handling visible in code, not implied.
- Keep approval runtime records auditable and queryable.

## 12. Recommended first implementation set

For the first concrete implementation wave, prioritize:

1. lifecycle engine
2. reminder engine
3. escalation engine
4. retry strategy
5. idempotency registry
6. dead-letter queue handling
7. approval runtime gates
8. recovery engine

This gives Titan a minimal but real execution spine.

## 13. Open questions

- What is the canonical storage model for runtime state and dead-letter records?
- Which approval actions can auto-expire versus requiring manual review?
- Should retry policy be global, per engine, or per module capability?
- How should offline PWA replays mint and reconcile idempotency keys?
- What subset of automation can run fully on-device versus server-coordinated only?
