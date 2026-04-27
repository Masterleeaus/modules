# Titan Zero Documentation

Layer: Workflows
Scope: Detection, classification, alerting, and recovery handling for workflow instances that stop progressing within expected operating windows.
Status: Draft v1
Depends On: Workflow definitions, State machines, Guards, Approvals, Metrics, Signals, Automation, Observability
Consumed By: TitanZero, Workflow engine, Automation engine, Super Admin diagnostics, Company operations dashboards, Specialist agents
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan detects workflow instances that are no longer progressing as expected, classifies why they are stuck, and routes them into the correct alert, approval, retry, or recovery path.

## 2. Why it exists

A workflow can be valid and still become operationally dead if it remains in the same state too long without making progress. This can happen because of:

- missing prerequisite signals
- unmet guard conditions
- human approvals never completed
- failed jobs or listeners
- external dependencies timing out
- data quality problems
- silent exceptions or side-effect failures
- logic loops that never transition to terminal states

Without explicit stuck-state detection, these cases hide inside status fields and only surface when a customer, worker, or admin notices something has gone wrong.

## 3. Core responsibilities

- detect workflow instances that exceed allowed dwell time in a state
- classify the type and probable cause of blockage
- separate expected waiting from abnormal stalling
- emit measurable alerts and audit events
- route the stuck instance into retry, escalation, approval, or recovery handling
- preserve runtime evidence for diagnosis and replay

## 4. Boundaries

### In scope

- dwell-time thresholds per workflow/state
- blocked-state classification
- escalation and alert rules
- stuck-state metrics
- recovery queue handoff
- admin and operator visibility requirements

### Out of scope

- low-level queue worker implementation details
- generic app uptime monitoring
- UI-only badge colors without underlying workflow semantics
- business-specific remediation scripts not attached to workflow recovery

## 5. Architecture placement

Stuck-state detection belongs primarily to the workflow layer, but it depends on automation, observability, and signal audit data.

### Recommended ownership

- **Workflows** define what counts as “too long” for a given state.
- **Automation** evaluates timers, retries, dead letters, and escalation handoff.
- **Signals** provide evidence of what was or was not received.
- **Observability** records alerts, trends, and diagnostics.
- **Titan Zero** can summarize, explain, and propose recovery actions, but should not replace the workflow truth model.

## 6. Detection model

Every workflow state should optionally define a maximum acceptable dwell window.

### Example fields

- `entered_at`
- `last_progress_at`
- `expected_max_duration`
- `warning_threshold`
- `critical_threshold`
- `last_transition_key`
- `last_signal_seen`
- `last_error_code`
- `approval_deadline`

### Core detection rule

A workflow instance is considered **stuck** when:

- it is in a non-terminal state, and
- no valid progress event has occurred within the configured threshold, and
- the state is not intentionally in a long wait mode covered by policy.

## 7. Progress events

Stuck-state detection must use explicit progress events, not just record existence.

### Valid progress signals may include

- state transition completed
- prerequisite signal received
- approval decision recorded
- retry attempt advanced
- dependent job completed successfully
- required data dependency attached or validated
- downstream side effect acknowledged

### Things that do **not** count as progress by default

- a record being viewed in UI
- the same state being rewritten with no semantic change
- repeated failed attempts without changed result
- duplicate signals that do not advance the workflow

## 8. Stuck-state classes

Titan should classify stuck states so recovery behavior is targeted.

### 1. Approval blocked

The workflow is waiting for a human decision beyond its expected approval window.

Examples:

- quote awaiting review
- refund awaiting manager approval
- AI proposal awaiting authorization

### 2. Dependency blocked

A required upstream signal, record, or external confirmation never arrived.

Examples:

- booking confirmation missing
- payment settlement missing
- file import never completed

### 3. Guard blocked

The workflow is repeatedly attempting a transition that guards reject.

Examples:

- invalid tenant mismatch
- missing required entity relation
- compliance precondition not satisfied

### 4. Retry exhausted / recovery blocked

The process failed and entered a retry or recovery state but never recovered.

Examples:

- notification retries exhausted
- downstream job permanently failing
- repeated provider timeouts

### 5. Silent logic stall

No explicit error exists, but the workflow stopped transitioning.

Examples:

- orphaned runtime state
- missing transition dispatch
- event/listener chain broken

## 9. Threshold design

Thresholds should be defined by workflow and state, not globally only.

### Threshold levels

- **warning**: unusual but not yet critical
- **critical**: operationally broken and must be surfaced
- **breach**: SLA or governance deadline exceeded

### Example

A complaint triage workflow might define:

- `open` warning at 15 minutes
- `open` critical at 30 minutes
- `pending_approval` warning at 2 hours
- `pending_approval` critical at 8 hours

A site onboarding workflow may have much longer windows.

## 10. Intentional waiting vs abnormal stalling

Not all inactivity is bad. Some states are intentionally long-lived.

### Expected waiting states may include

- customer awaiting reply
- scheduled future execution
- approval requested with defined window
- delayed follow-up timer

The workflow definition must distinguish these from unbounded waiting. Intentional waiting should still have:

- expiry windows
- reminder thresholds
- escalation policy

## 11. Detection execution pattern

The scheduler/automation layer should periodically evaluate active workflow instances.

### Recommended flow

1. load non-terminal workflow instances
2. compare current state dwell time against workflow thresholds
3. exclude instances in explicitly allowed wait windows
4. classify the stuck type using last signal/error/approval/runtime metadata
5. emit a stuck-state event/log
6. trigger alerts or route into recovery as configured

## 12. Recovery and escalation paths

A stuck workflow should not always be auto-fixed. Response depends on class and policy.

### Possible recovery actions

- send reminder to approver
- request missing dependency again
- enqueue safe retry
- move to `recovery_pending`
- move to `manual_intervention_required`
- escalate to supervisor/admin
- cancel safely if policy allows

### Recommended recovery states

- `warning_pending`
- `escalated`
- `recovery_pending`
- `manual_intervention_required`
- `abandoned`

## 13. Observability requirements

Every stuck-state event should be traceable.

### Required telemetry

- workflow key
- workflow instance id
- company id
- current state
- entered_at
- duration_in_state
- classification
- likely cause
- last transition key
- last signal id or event key if available
- recovery action taken
- alert recipients

This data should feed both diagnostics and metrics.

## 14. Metrics

Workflow metrics should expose stuck-state behavior, not just completion counts.

### Useful metrics

- stuck count by workflow
- stuck count by state
- mean dwell time per state
- approval wait breaches
- recovery success rate
- manual intervention rate
- repeat stuck incidents on same workflow type

These metrics support both engineering reliability work and business operations reporting.

## 15. Super Admin vs Company visibility

### Super Admin

Should see:

- cross-tenant trends
- workflow families with systemic stalls
- engine/runtime reliability issues
- repeated dead-letter/retry patterns

### Company Admin

Should see:

- tenant-scoped stuck workflows
- blocked approvals
- operational bottlenecks
- recommended remediation actions

### End Users / Staff

Should only see:

- role-relevant blocked tasks or approvals
- localized recovery prompts where they have authority

## 16. AI relationship

Titan Zero should use stuck-state data to explain and assist, not to redefine workflow truth.

### Good AI uses

- summarize why a workflow is stuck
- propose likely remediation path
- draft an escalation or reminder
- identify repeated operational patterns across sites or clients

### Bad AI uses

- mutate workflow state without going through legal transitions
- bypass approvals because the model “thinks” it is safe
- hide the distinction between inferred cause and verified cause

## 17. Implementation guidance

### Recommended platform areas

```text
app/Platform/Workflows/
├─ Metrics/
├─ Support/
└─ StateMachines/

app/Platform/Automation/
├─ Scheduling/
├─ Retries/
├─ DeadLetters/
└─ Audit/

app/Platform/Observability/
└─ Alerts/
```

### Suggested components

- `StuckStateDetector`
- `WorkflowDwellPolicyResolver`
- `StuckStateClassifier`
- `WorkflowEscalationService`
- `WorkflowRecoveryRouter`
- `WorkflowBreachLogger`

## 18. Anti-patterns

Avoid:

- global “older than X = stuck” logic for all workflows
- UI-only stuck badges with no workflow evidence
- direct state mutation to “unstick” a process
- deleting and recreating runtime records to hide stall history
- using retries as the only recovery strategy

## 19. Recommended first implementation steps

1. add dwell-time metadata to workflow definitions
2. store `entered_at` and `last_progress_at` on runtime instances
3. schedule evaluation of non-terminal workflows
4. classify and log stuck-state events
5. expose tenant-safe admin/operator views
6. route stuck workflows into recovery or escalation paths

## 20. Summary

Stuck-state detection is the reliability guardrail of the workflow system. It turns silent operational failure into visible, classifiable, and recoverable process signals. In Titan Zero, it should be definition-aware, policy-aware, tenant-safe, and tightly linked to approvals, automation, and observability.


## 18. Relationship to retries, idempotency, and dead letters

Stuck-state detection is not only about long waits. It must also understand runtime reliability structures so the platform does not misclassify healthy retries or invisible dead-end failures.

### Required integrations

- **Retries** — distinguish active retry backoff from genuine abandonment
- **Idempotency** — detect duplicate re-entry attempts without treating them as progress
- **Outbox/Inbox** — confirm whether outbound or inbound handoff traffic is queued, delivered, deduped, or stalled
- **Dead letters** — escalate workflows whose downstream work exhausted retry policy and can no longer self-recover

The scheduling/reliability blueprint already defines retries, idempotency keys, inbox/outbox, and dead letters as required controls, so stuck detection should consume those signals instead of running as an isolated timer checker fileciteturn0file124.

## 19. Detection cadence

Titan should support more than one detection cadence.

### Real-time or near-real-time checks

Used for:

- operator-facing urgent workflows
- after-hours dispatch and escalation
- payment or compliance-sensitive actions
- approval queues with short SLA windows

### Scheduled sweeps

Used for:

- background backlog reviews
- reminder ladders
- stale draft detection
- low-priority workflow hygiene

### Event-driven re-evaluation

Used when:

- approvals change state
- prerequisite signals arrive
- retries are exhausted
- downstream handoffs succeed or fail

This layered model keeps stuck-state detection efficient while still being responsive where operations need rapid intervention.

## 20. Recovery routing expectations

Once a workflow is classified as stuck, Titan should not stop at alerting. Each stuck class should define a default recovery route.

### Typical recovery routes

- **approval_wait** → remind approver, escalate role, or expire back to review state
- **guard_blocked** → remain paused but expose remediation checklist
- **job_failure** → retry or move to dead-letter review queue
- **missing_signal** → wait window extension, then escalation or manual reconciliation
- **logic_loop** → freeze instance and require operator intervention
- **handoff_failure** → audit duplicate protection, then replay through outbox/inbox safe path

Where possible, recovery routing should preserve the same workflow instance rather than forcing hidden shadow records or manual status edits.

## 21. Recommended evidence captured on a stuck incident

Every stuck incident record should capture at minimum:

- workflow key and instance id
- company scope
- current state and how long it has been held
- threshold that was breached
- likely blockage class
- recent transitions and approvals
- retry/dead-letter status if applicable
- recommended next action
- escalation owner or queue
- resolved_at / resolved_by / resolution_type

This evidence makes replay, audit, and operational learning possible.

## 22. Implementation notes — second pass

- Keep detection logic in workflow/automation/observability services, not in dashboard widgets.
- Make threshold classification template-aware so similar workflows remain comparable.
- Connect stuck incidents to metrics and approvals so the same workflow can be viewed as both a health event and a runtime-control event.
- Use cross-domain workflow chains as a diagnostic aid: if Quote → Job or Job → Invoice handoffs fail to complete, the stuck layer should preserve which boundary failed, not just which local state timed out fileciteturn0file6.
