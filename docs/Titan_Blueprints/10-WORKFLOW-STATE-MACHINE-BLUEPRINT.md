# Workflow + State Machine Blueprint

Status: Canonical draft  
Layer: Lifecycle control and guarded progression

## Role

Workflows formalize multi-step processes. State machines enforce legal transitions. Together they stop UI callbacks and ad hoc controller logic from becoming the source of truth.

## Tree

```text
app/Platform/Workflows/
├─ Definitions/
├─ Templates/
├─ StateMachines/
├─ StepHandlers/
├─ Guards/
├─ Conditions/
├─ Transitions/
├─ Approvals/
├─ Escalations/
├─ Metrics/
├─ Recovery/
└─ Support/
```

## Concepts

### Definitions
Describe the workflow:
- states
- entry criteria
- exit criteria
- actions per step
- timeout rules
- approval requirements

### State Machines
Enforce:
- legal transitions only
- terminal states
- reversible states
- recovery states
- split paths

### Guards
Guard functions should validate:
- tenant safety
- dependency readiness
- permissions
- schedule conflicts
- financial or compliance prerequisites

### Step Handlers
Handlers perform the work for a state step:
- create quote
- assign worker
- request approval
- issue invoice
- send reminder
- collect signature

## Example Lifecycle

```text
Lead -> Qualified -> Quote Drafted -> Quote Sent -> Approved -> Booking Drafted -> Scheduled -> Dispatched -> On Site -> Complete -> Invoice Sent -> Paid -> Follow-up Closed
```

## Example FSM Rules

- `Scheduled -> On Site` requires active assignment and valid time window
- `Complete -> Invoice Sent` requires proof-of-service or equivalent completion evidence
- `Approved -> Auto-Execute` depends on AI confidence, package level, and governance rule pass

## Recommended Data Model

- `workflow_definitions`
- `workflow_instances`
- `workflow_steps`
- `workflow_transitions`
- `workflow_approvals`
- `workflow_metrics`
- `workflow_recovery_logs`

## Module Pattern

Each serious domain module may include:

```text
Modules/<ModuleName>/Workflows/
├─ Definitions/
├─ StepHandlers/
├─ Guards/
└─ Support/
```

Shared orchestration still lives in `app/Platform/Workflows/`.
