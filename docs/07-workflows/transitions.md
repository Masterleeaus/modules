# Titan Zero Documentation

Layer: Workflows
Scope: Transition rules, transition contracts, guard execution order, and workflow handoff behavior across domains.
Status: Draft v1
Depends On: Workflow definitions, State machines, Signals, Automation, Core Platform
Consumed By: TitanZero, Workflow services, Automation engines, Approval services, Reporting, Specialist agents
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan transitions move a workflow instance from one state to another, including guard order, approval splits, side-effect timing, and cross-domain handoff rules.

## 2. Why it exists

A state machine is only useful if transitions are explicit and enforceable. Titan needs transition contracts so that:

- state changes happen consistently
- side effects do not fire before legality is confirmed
- approvals can intercept movement cleanly
- automation and signals know when a handoff is valid
- reporting can audit what changed, why, and who caused it

## 3. Core responsibilities

- define transition identity and metadata
- validate legal `from → to` movement
- run guards in a deterministic order
- branch into approval states when required
- apply side effects only after successful transition
- emit workflow handoff outputs to automation/signals
- preserve audit and retry compatibility

## 4. Boundaries

### In scope

- transition contracts
- transition validation order
- guard execution sequence
- approval split behavior
- side-effect timing
- cross-domain handoff semantics

### Out of scope

- state-machine definitions themselves
- notification template content
- raw signal schema details
- UI button labeling
- low-level provider execution logic

## 5. Transition model

Every transition should be represented as a first-class contract, not just a string assignment.

### Minimum fields

- `transition_key`
- `workflow_key`
- `from_state`
- `to_state`
- `trigger_type`
- `guard_set`
- `approval_mode`
- `side_effect_set`
- `emits`
- `timeout_policy`
- `retry_policy`
- `actor_requirements`

### Example

```text
transition_key: quote.accept
from_state: sent
to_state: accepted
trigger_type: user_action
guard_set: [tenant_scope, quote_not_expired]
approval_mode: none
side_effect_set: [record_acceptance_at]
emits: [QuoteAccepted]
```

## 6. Transition categories

### User-triggered transitions

Caused by a user or operator action.

Examples:

- send quote
- approve output
- cancel service job
- reopen complaint

### System-triggered transitions

Caused by scheduler, automation, or deadline logic.

Examples:

- mark invoice overdue
- move reminder into escalation
- timeout approval request

### Signal-triggered transitions

Caused by validated inbound signals from another domain/layer.

Examples:

- payment cleared
- job completed
- customer replied

### AI-assisted transitions

AI may propose or prepare a transition, but workflow services still own the actual commit.

Examples:

- suggest triage severity
- propose next-best workflow path
- draft recovery branch recommendation

## 7. Required execution order

Transitions should execute in a fixed sequence.

### Canonical order

1. Resolve workflow definition.
2. Resolve current runtime state.
3. Confirm requested transition exists.
4. Confirm actor/trigger is permitted.
5. Run guard set.
6. If approval required, enter approval wait state instead of target state.
7. Persist transition audit intent.
8. Commit target runtime state.
9. Execute side effects.
10. Emit downstream events/signals.
11. Update metrics/timers.

This ordering prevents side effects from firing on illegal or unapproved movement.

## 8. Guard execution

Guards should run before any state commit.

### Recommended guard classes

- tenant scope guard
- permission/role guard
- data completeness guard
- prerequisite signal guard
- policy ceiling guard
- financial/compliance guard
- concurrency/lock guard
- duplicate-transition guard

### Guard results

Each guard should return structured output:

- pass/fail
- reason code
- human-readable explanation
- optional remediation hint

## 9. Approval-split transitions

When a transition requires approval, the requested `to_state` is not entered immediately.

### Pattern

Requested transition:

```text
prepared → approved_for_send
```

Approval-aware runtime path:

```text
prepared → awaiting_approval
awaiting_approval → approved_for_send
awaiting_approval → rejected
awaiting_approval → escalated
```

This keeps the transition auditable and makes approval wait time measurable.

## 10. Side effects

Side effects must never be the definition of the transition itself. They are downstream consequences of an already-valid movement.

### Good side effects

- write acceptance timestamp
- queue notification
- generate governed artefact
- emit domain signal
- schedule follow-up timer

### Bad side effects

- changing unrelated states in hidden ways
- sending irreversible messages before state commit
- mutating the workflow instance outside transition services

## 11. Cross-domain handoffs

Transitions often hand one domain into another.

### Example chain

```text
quote.accepted → service_job.scheduled
service_job.completed → invoice.draft
invoice.issued → payment.pending
```

The workflow layer should treat these as controlled outputs, not casual observer side effects.

### Handoff contract

A handoff should declare:

- source workflow/state/transition
- target workflow/state or creation intent
- payload contract
- idempotency key
- company scope
- audit link

## 12. Retry and recovery compatibility

Some transitions can be retried safely; others cannot.

### Safe retry candidates

- entering a waiting state
- recomputing metrics
- scheduling a reminder if idempotent

### Unsafe retry candidates

- duplicate invoicing
- duplicate acceptance events
- duplicate outbound irreversible messages

Each transition should declare whether retry is allowed and which idempotency key protects it.

## 13. Failure modes

### Illegal transition request

A caller requests a transition that is not declared.

**Response:** reject and audit.

### Guard-pass drift

A side effect assumes data stayed valid after guards but before commit.

**Response:** use locking/version checks where transition concurrency matters.

### Approval bypass

A direct state mutation skips the approval wait state.

**Response:** enforce all state changes through workflow transition services only.

### Duplicate handoff

A downstream workflow is created twice from the same source transition.

**Response:** require idempotency keys and audit-linked handoffs.

## 14. Dependencies

### Upstream

- workflow definitions
- state-machine definitions
- automation trigger framework
- signal validation/governance
- domain actions/services

### Downstream

- approval services
- communications layer
- metrics/stuck-state detection
- audit/reporting
- AI assist layers

## 15. Open questions

- Which transitions should be modeled as synchronous vs queued side effects?
- Which handoffs require a formal outbox pattern first?
- Should transition definitions live as code-only classes, config manifests, or both?

## 16. Implementation notes

- Put transition classes/contracts under `app/Platform/Workflows/Transitions/` and related support folders.
- Prefer named transition keys over free-form status changes.
- Use a dedicated workflow service to apply transitions; do not let controllers, jobs, or widgets mutate workflow state directly.
- Store transition audit records even when approval pauses intercept the intended target state.
- Keep cross-domain handoffs explicit so automation and signals can subscribe safely.
