# Titan Zero Documentation

Layer: Workflows
Scope: Workflow guards, guard contracts, evaluation order, reusable guard classes, and denial behavior across Titan processes.
Status: Draft v1
Depends On: Workflow definitions, State machines, Transitions, Core Platform, Signals, Automation, Policies
Consumed By: TitanZero, Workflow services, Automation engines, Approval services, Specialist agents, Reporting
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define the guard layer that decides whether a workflow transition, step entry, or auto-execution path is legally allowed to proceed.

## 2. Why it exists

Titan cannot rely on controller checks, table actions, or ad hoc `if` statements to decide whether a workflow can move. Guards exist so that:

- legal movement is enforced consistently
- tenant and permission boundaries are checked before side effects fire
- approval routing can split from direct execution cleanly
- state transitions remain auditable and replay-safe
- automation engines and specialist agents use the same safety logic as UI actions

Without guards, workflow integrity fragments across UI layers, jobs, and channel-specific handlers.

## 3. What a guard is

A guard is a deterministic validation contract attached to one of three points:

- **transition guard** — runs before `from -> to` movement
- **step-entry guard** — runs before a state handler begins work
- **execution-mode guard** — decides whether an action can auto-run, must queue for approval, or must deny

A guard never performs the business side effect itself. It only returns an allow, deny, or reroute decision with structured reasons.

## 4. Core responsibilities

- validate tenant safety and company boundary integrity
- validate role and permission requirements
- validate dependency readiness before state changes
- validate policy ceilings from TitanCore
- validate timing, schedule, and recurrence constraints
- validate evidence or proof prerequisites
- validate financial, compliance, or package requirements
- emit machine-readable denial reasons for audit and UI surfacing

## 5. Guard categories

### 5.1 Identity and tenancy guards

Check:

- `company_id` alignment
- entity ownership
- user or worker identity validity
- cross-tenant leakage prevention

### 5.2 Permission guards

Check:

- role permission
- package feature availability
- module entitlement
- admin-only vs user-allowed transitions

### 5.3 Dependency guards

Check:

- required linked entities exist
- prerequisite steps already completed
- mandatory documents or proofs attached
- upstream workflow state satisfied

### 5.4 Scheduling guards

Check:

- time window validity
- overlap conflicts
- blackout windows
- recurrence constraints
- pause or hold windows

### 5.5 Compliance and policy guards

Check:

- policy ceilings from TitanCore
- AI confidence threshold rules
- approval requirements
- restricted actions in suggest-only mode
- governance constraints from AEGIS-style layers if present

### 5.6 Financial guards

Check:

- quote approval before booking conversion
- invoice prerequisites before dispatch follow-through
- payment state before downstream transitions
- spend or budget limits

### 5.7 Evidence guards

Check:

- proof-of-service present
- signature requirements
- completion photos or notes
- training or checklist completion evidence

## 6. Guard outcome contract

Every guard should return a structured result, not only a boolean.

Minimum result fields:

- `status` — allow, deny, reroute_for_approval, reroute_for_recovery
- `guard_key`
- `reason_code`
- `message`
- `severity`
- `blocking`
- `context_fragment`
- `next_action_hint`

This allows the same result to be consumed by:

- workflow runtime
- audit logs
- admin UI
- company UI
- reporting
- specialist agent explanation output

## 7. Evaluation order

Guard order must be deterministic so every surface behaves the same.

Recommended order:

1. tenant boundary
2. package / module entitlement
3. permission / role validity
4. dependency readiness
5. schedule or recurrence validity
6. financial or compliance prerequisites
7. evidence prerequisites
8. execution-mode decision (auto, approval, deny)

Fail fast where possible, but log the primary denial reason cleanly.

## 8. Guard interaction with transitions

Transitions should not mutate workflow state until all blocking guards pass.

Transition sequence:

1. resolve transition definition
2. load guard set
3. evaluate deterministic guard order
4. collect result
5. deny or reroute if needed
6. only then apply state change and side effects

This prevents partial transitions and premature event emission.

## 9. Guard interaction with approvals

Approvals are not separate from guards; they are one possible guard outcome.

A guard may decide:

- allow direct transition
- deny transition completely
- reroute to approval state
- reroute to recovery or exception state

Examples:

- low confidence AI quote -> approval queue
- dispatch after hours -> supervisor approval
- completion without proof -> deny
- risky rebooking after complaint -> manager approval

## 10. Guard placement in the code tree

Canonical home:

```text
app/Platform/Workflows/Guards/
```

Recommended internal grouping:

```text
app/Platform/Workflows/Guards/
├─ Tenancy/
├─ Permissions/
├─ Dependencies/
├─ Scheduling/
├─ Compliance/
├─ Financial/
├─ Evidence/
└─ Support/
```

Domain modules may ship reusable domain guards, but orchestration should still resolve through the shared workflow layer.

## 11. Example guards

### 11.1 Quote to booking conversion

Required guards:

- quote exists and belongs to company
- quote state is approved
- client and site still active
- package permits booking automation
- actor has permission or approval route exists

### 11.2 Scheduled to on-site

Required guards:

- assignment exists
- current time within allowed window
- worker not in conflicting shift
- required access instructions present if mandatory
- no unresolved safety lock or hold

### 11.3 Complete to invoice sent

Required guards:

- proof-of-service recorded
- completion state valid
- invoice module enabled
- billing policy permits auto-issue or sends to review

## 12. Denial handling

Guard denials must not vanish into logs only.

Titan should surface denials to:

- workflow audit trail
- admin/company run history
- stuck-state diagnostics where relevant
- assistant explanation layer
- retry or manual-fix queues if recoverable

Every denial should be classed as one of:

- permanent denial
- recoverable denial
- approval reroute
- transient runtime denial

## 13. Replay and recovery safety

On replay or recovery, guards must re-evaluate against current truth, not stale assumptions.

That means:

- no hidden UI-only checks
- no side-effect-only assumptions
- no reliance on ephemeral client state
- use canonical DB entities and policy registries

## 14. Relationship to other docs

This doc defines **whether movement is allowed**.

Related workflow docs define:

- `workflow-definitions.md` — process contract
- `state-machines.md` — legal state topology
- `transitions.md` — movement contract
- `approvals.md` — review and authorization split layer
- `metrics.md` — measurable outcomes
- `stuck-state-detection.md` — blocked/stalled flow surfacing

## 15. Anti-patterns to avoid

- permission checks only in Blade or Filament actions
- schedule conflict checks only in controllers
- AI confidence checks embedded only in prompt logic
- silent denials without reason codes
- duplicate guard logic per channel or per module surface

## 16. Implementation checklist

A workflow guard system is ready when:

- guards are first-class contracts, not anonymous closures spread through code
- every important transition has a declared guard set
- approvals are emitted as explicit reroute outcomes
- denials are auditable and user-visible where appropriate
- replay and recovery paths reuse the same guards
- modules and specialist agents cannot bypass the guard layer

## 17. Final rule

If a workflow move matters, it must be protected by a declared guard and return a structured decision. No important workflow transition should depend on UI-only validation or hidden controller logic.
