# Titan Zero Documentation

Layer: Workflows
Scope: Workflow definitions and the contracts that describe guarded, approval-aware, measurable multi-step processes.
Status: Draft v1
Depends On: Core Platform, Signals, Automation, AI, Modules, Communications
Consumed By: TitanZero, Automation engines, PWA/UI shells, specialist agents, admin tooling, reporting surfaces
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define the canonical shape of a workflow in Titan Zero so developers can model long-running business and system processes without trapping rules inside controllers, Filament callbacks, or channel delivery code.

## 2. Why it exists

Titan needs more than isolated actions and signals. It needs a formal way to represent multi-step processes that:

- move through explicit states
- apply guarded transitions
- pause for approvals
- recover from failures or retries
- emit metrics and stuck-state alerts
- coordinate cross-domain flows such as CRM → Work → Money

Without workflow definitions, logic drifts into UI layers, controllers, jobs, and ad hoc status columns. The workflow layer prevents that drift and gives Titan Zero a stable process model.

## 3. Core responsibilities

- define workflow identity, scope, and state model
- describe allowed transitions and their guards
- declare approval splits and resumption points
- define handoff points to signals, automation, communications, and AI
- expose measurable runtime states for reporting and stuck-state detection

## 4. Boundaries

### In scope

- workflow definitions
- workflow instance lifecycle
- state names and transition maps
- guard conditions
- approval checkpoints inside a workflow
- reusable workflow templates
- workflow-level metrics and timeout rules
- cross-domain workflow composition

### Out of scope

- signal envelope schema definitions
- low-level channel delivery rules
- provider/model adapter behavior
- UI-only interaction logic
- direct business CRUD logic that belongs in actions/services

## 5. Architecture

The workflow layer should live as a first-class platform area under `app/Platform/Workflows/`, separate from automation engines and communications transports. Source blueprints place workflow definitions beside state machines, step handlers, guards, transitions, approvals, templates, metrics, and support services.

A workflow definition is the canonical static description of a process. A workflow instance is the runtime record of one execution of that process for a specific tenant/entity/context.

### Recommended structure

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
```

### Relationship to adjacent layers

- **Signals** move meaning/events between domains.
- **Automation** decides trigger evaluation, retries, deduplication, and execution reliability.
- **Workflows** define the legal process path that automation and signals must respect.
- **Communications** handles delivery after workflow decisions are made.
- **AI/Titan Zero** reasons over workflow context, proposes actions, and may assist transition decisions, but does not replace workflow state truth.

## 6. Workflow definition model

Every workflow definition should declare at least the following:

### Identity

- `workflow_key`
- `name`
- `domain`
- `version`
- `status` (draft, active, deprecated)

### Scope

- tenant boundary (`company_id` required at runtime)
- primary entity types (client, site, service job, quote, invoice, complaint, etc.)
- optional related entity types

### States

A workflow must enumerate explicit states. These should be stable semantic states, not UI labels.

Examples:

- `draft`
- `pending_review`
- `approved`
- `scheduled`
- `in_progress`
- `awaiting_customer`
- `completed`
- `cancelled`
- `failed`
- `archived`

### Transitions

Each transition should declare:

- `from_state`
- `to_state`
- `trigger`
- `guard_set`
- `approval_required`
- `side_effects`
- `timeout_policy`
- `retry_policy` if applicable

### Runtime metadata

- priority
- SLA/timeout windows
- stuck-state thresholds
- observability tags
- metrics labels

## 7. State machine rules

Workflow definitions must compile into explicit state machines.

### Required rules

- no hidden transitions
- no direct mutation of state outside workflow handlers
- terminal states must be explicit
- invalid transitions must be rejected with a reason code
- side effects must occur after transition validation, not before
- approval branches must preserve resumability

### Example state machine pattern

```text
draft → pending_review → approved → executing → completed
                     └→ rejected
approved → cancelled
executing → failed → recovery_pending → executing
```

This allows both business and system processes to remain inspectable and replayable.

## 8. Guards

Guards determine whether a transition is legal at the moment it is attempted.

### Guard classes typically check

- tenant scope integrity
- role/permission requirements
- required data presence
- prerequisite signals received
- prior state correctness
- financial or compliance constraints
- policy ceilings from TitanCore/TitanZero governance
- approval completion state

### Guard principles

- guards should be deterministic
- guards should return structured failure reasons
- guards should not perform channel delivery or unrelated side effects
- reusable guards should be composable into guard sets

## 9. Approvals inside workflows

Approvals are not separate from workflows; they are explicit workflow split points.

### Approval checkpoint model

An approval-aware transition should support:

- proposal created
- pending approver state
- approved path
- rejected path
- timeout/escalation path
- resumed execution path

### Typical approval locations

- quote approval before issue or conversion
- high-risk job change approval
- governed AI output approval before send/share
- policy exception approval
- spend/payment exception approval

The workflow layer should model the checkpoint, while approval services/UI layers handle assignment, notification, and review surfaces.

## 10. Runtime behavior

At runtime the flow should behave like this:

1. A trigger, signal, user action, or scheduled event requests a transition.
2. The workflow engine loads the relevant definition and current instance state.
3. Guard sets are evaluated.
4. If approval is required, the instance moves into an approval-waiting state.
5. If guards pass and no approval block exists, the transition is committed.
6. Side effects are dispatched through the correct adjacent layers:
   - actions/services for business writes
   - signals for downstream events
   - automation for retries/recovery patterns
   - communications for outbound delivery
7. Metrics and audit records are written.
8. Stuck-state timers or SLA watchers are updated.

## 11. Cross-domain workflows

The workflow layer must support flows that span multiple domains. Existing cross-domain documentation already shows chains like:

- Customer → Site → Service Job → Checklist
- Quote → Service Job
- Quote → Invoice
- Invoice → Payments → Paid status

These should not be hardcoded as controller chains. They should be modeled as workflow definitions that reference domain actions and domain constraints while preserving tenant-safe transitions.

### High-value workflow families

- lead → quote → booking → job → invoice → follow-up
- complaint intake → triage → resolution → recovery outreach
- recurring service lifecycle → visit generation → execution → QA → invoicing
- document/request review → approval → publish/share
- onboarding/training → acknowledgement → competency check

## 12. Contracts

### Inputs

A workflow transition request should include:

- workflow key
- instance id or entity reference
- company id
- acting user/agent/system source
- requested transition
- contextual payload
- approval context if resuming

### Outputs

A workflow handler should return structured results:

- success/failure
- previous state
- new state
- blocking reason or guard failures
- approvals created/resolved
- side effects dispatched
- metrics/audit references

### Persistence expectations

Workflow persistence should capture:

- definition version used
- current state
- transition history
- approval waits and outcomes
- timeout markers
- retry count / recovery markers
- audit timestamps

## 13. Failure modes

### Invalid transition

The transition is rejected; state remains unchanged; structured reason returned.

### Guard failure

The transition is denied; guard failures are logged and surfaced.

### Approval timeout

The instance moves into an escalation, expiry, or fallback state according to definition.

### Side-effect failure after transition

The transition remains recorded, but downstream execution enters retry/recovery handling through automation/outbox patterns.

### Stuck state

A watcher detects that an instance has remained in a state beyond its allowed window and emits a metric/signal for surfacing and intervention.

## 14. Dependencies

### Upstream

- Core platform tenancy/auth/permissions
- Signals layer for event intake and downstream dispatch
- Automation layer for retries, idempotency, dead letters, and runtime reliability
- AI/Titan Zero governance for approval-aware proposals where AI participates

### Downstream

- PWA and dashboard shells
- specialist agents
- admin/operator review interfaces
- reporting/metrics surfaces
- communications dispatch layers

## 15. Implementation notes

- Keep workflow truth out of Filament resource callbacks.
- Keep transition rules out of controllers; controllers should delegate to workflow-aware actions/services.
- Persist workflow definitions/templates in a way that supports versioning.
- Use structured enums/constants for states and transitions where possible.
- Keep company scoping mandatory on runtime instances.
- Where a domain already has statuses, map them into explicit workflow states instead of allowing uncontrolled string drift.
- Use workflow metrics to power stuck-state detection and operational dashboards.
- Reuse signal and automation layers for handoffs instead of redefining those responsibilities here.

## 16. Open questions

- Which workflow families should be defined first for the MVP: lifecycle, complaint resolution, quoting, or dispatch?
- Should workflow definitions be code-first, manifest-first, or hybrid?
- Which current domain status fields in the repo should become canonical workflow-backed state fields?
- How should workflow version migration behave for long-running instances already in flight?
