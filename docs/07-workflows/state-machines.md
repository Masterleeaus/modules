# Titan Zero Documentation

Layer: Workflows
Scope: Canonical state-machine model for Titan workflows, including runtime instances, terminal states, resumability, and cross-domain lifecycle alignment.
Status: Draft v1
Depends On: Workflow definitions, Core Platform, Signals, Automation, Modules
Consumed By: TitanZero, Automation engines, Approval services, Reporting, PWA/UI shells, Specialist agents
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan represents workflow state machines so every long-running process uses explicit states, legal transitions, resumable approval pauses, and measurable terminal outcomes.

## 2. Why it exists

Titan workflows are not just status columns. They are executable state contracts. A state machine is needed so:

- processes can only move through legal states
- approval pauses do not lose runtime intent
- retries and recovery resume from known points
- signals and automations can hand off against stable workflow truth
- reporting can distinguish active, blocked, failed, cancelled, and complete work

Without this layer, lifecycle rules drift into controllers, jobs, table actions, and ad hoc enums.

## 3. Core responsibilities

- define the state-machine structure for each workflow family
- distinguish definition-time states from runtime instance state
- classify start, active, waiting, recovery, and terminal states
- support resumable approval and exception branches
- normalize cross-domain lifecycle states where domains touch each other
- provide state truth for metrics, stuck-state detection, and audit

## 4. Boundaries

### In scope

- state-machine definitions
- workflow runtime instance states
- terminal-state rules
- pause/resume behavior
- recovery and retry-compatible states
- cross-domain lifecycle alignment

### Out of scope

- signal envelope schemas
- transport/channel delivery
- UI color/status presentation details
- provider/model routing behavior
- low-level CRUD validation unrelated to workflow state

## 5. Architecture

A workflow state machine should exist in two layers:

### Definition layer

Static structure stored in `app/Platform/Workflows/StateMachines/` and related definition classes.

It declares:

- workflow key
- states
- start state
- terminal states
- legal transitions
- approval checkpoints
- timeout/recovery branches

### Runtime layer

Instance state stored against a workflow instance record and updated only by workflow services.

It carries:

- `current_state`
- `previous_state`
- `entered_at`
- `blocked_reason`
- `approval_state`
- `attempt_count`
- `last_transition_key`
- `is_terminal`

This separation prevents runtime drift from modifying the underlying process contract.

## 6. State taxonomy

Titan state machines should use a common taxonomy across domains.

### Start states

The first legal state for a workflow instance.

Examples:

- `draft`
- `pending_intake`
- `open`
- `queued`

### Active execution states

States where work is currently progressing.

Examples:

- `scheduled`
- `in_progress`
- `processing`
- `executing`

### Waiting states

States where progress is paused pending an external dependency.

Examples:

- `awaiting_approval`
- `awaiting_customer`
- `awaiting_signal`
- `awaiting_payment`
- `awaiting_capacity`

### Recovery states

States entered after an error, timeout, or interrupted run.

Examples:

- `retry_pending`
- `recovery_pending`
- `needs_review`
- `blocked`

### Terminal states

States that end the workflow instance with no further legal progression unless a new instance or re-open rule is explicitly defined.

Examples:

- `completed`
- `cancelled`
- `failed`
- `rejected`
- `archived`

## 7. Canonical machine shape

Every state machine should answer these questions:

- what state starts the process?
- what states mean active progress?
- what states represent a legal pause?
- what states require approval before continuation?
- what states mean recovery is possible?
- which states are terminal?
- can a terminal state be reopened, or must a new instance be created?

### Recommended canonical structure

```text
start
  ↓
pending_intake
  ↓
ready
  ├─→ awaiting_approval ─→ approved_resume ─→ executing
  ├─→ blocked
  └─→ cancelled

executing
  ├─→ awaiting_dependency
  ├─→ retry_pending
  ├─→ failed
  └─→ completed
```

## 8. Approval-aware states

Approvals should not be modeled as booleans attached to unrelated records. They should appear in the state machine as explicit waiting/split states.

### Pattern

```text
prepared → awaiting_approval
awaiting_approval → approved_resume
awaiting_approval → rejected
approved_resume → executing
```

This makes the pause visible, measurable, and resumable.

## 9. Cross-domain lifecycle alignment

Titan has at least one important cross-domain chain already implied by source materials:

```text
enquiry → quote → service_job → invoice → payment
```

The workflow layer should not force all domains into one giant machine. Instead, it should align the handoff states between neighboring domain machines.

### Example alignment points

- `enquiry.open` can hand off into `quote.draft`
- `quote.accepted` can hand off into `service_job.scheduled`
- `service_job.completed` can hand off into `invoice.draft`
- `invoice.issued` can hand off into `payment.pending`
- `payment.cleared` can complete the money-side workflow

That allows each domain to keep its own machine while preserving lifecycle continuity.

## 10. Runtime behavior

A workflow runtime should behave like this:

1. Load workflow definition.
2. Resolve current runtime state.
3. Validate requested transition against the machine.
4. Run guard set.
5. If approval is required, enter approval state instead of executing side effects.
6. On approval, resume through the approved transition path.
7. Persist new state and transition audit.
8. Emit allowed downstream events/signals.
9. Update metrics and stuck-state timers.

## 11. Failure modes

### Invalid transition

A caller requests a transition not allowed by the machine.

**Response:** reject transition, log reason, do not mutate state.

### Hidden state mutation

A controller/job updates `status` directly.

**Response:** treat as architecture violation; route all state mutation through workflow services.

### Approval orphaning

A workflow enters `awaiting_approval` but no resumable pointer exists.

**Response:** record resume transition key and target state before pausing.

### Terminal-state confusion

A process marked complete is later treated as editable/retryable without reopening rules.

**Response:** define explicit reopen or new-instance rules; never guess.

## 12. Dependencies

### Upstream

- workflow definitions
- automation triggers and retries
- signal intake/approval layers
- domain modules and entities

### Downstream

- approvals
- metrics/stuck-state detection
- reporting surfaces
- AI reasoning over process context
- PWA/operator shells

## 13. Open questions

- Which workflow families need formal state-machine classes first: lifecycle, approvals, dispatch, complaints, or training?
- Should runtime instances live in one shared workflow table family or in per-domain runtime tables with shared contracts?
- Which terminal states support reopen vs new-instance-only behavior?

## 14. Implementation notes

- Prefer explicit state enums/value objects over loose strings where possible.
- Keep domain status labels mapped to workflow states rather than letting UI terms become state truth.
- Store transition audit with `from_state`, `to_state`, `transition_key`, `actor_type`, `actor_id`, `reason`, and timestamps.
- Approval pauses should store enough resume metadata to continue deterministically.
- Do not hide workflow state changes inside Filament table actions, controller helpers, or ad hoc observers.
