# Titan Zero Documentation

Layer: Workflows
Scope: Workflow metrics, runtime measurements, bottleneck signals, KPI hooks, and process observability for Titan workflow systems.
Status: Draft v1
Depends On: Workflow definitions, State machines, Transitions, Approvals, Signals, Automation, Observability
Consumed By: TitanZero, Reporting, Admin dashboards, Automation health checks, Specialist agents, Stuck-state detection
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan measures workflows so teams can see throughput, bottlenecks, approval delays, stuck states, and process quality without relying on UI-only counters or ad hoc SQL.

## 2. Why it exists

A workflow system that cannot be measured cannot be governed or improved. Titan workflows need metrics so the platform can:

- understand process health
- compare template performance across tenants and modules
- detect bottlenecks and delay patterns
- tune automation safely
- surface operational risk early

Metrics are also required if Titan is going to move from suggest-only systems toward increasingly reliable guided and automated execution.

## 3. Core responsibilities

- define workflow measurement dimensions
- capture state timing and transition timing
- measure approval latency and denial rates
- expose throughput, failure, and recovery signals
- feed stuck-state detection and admin dashboards
- provide comparable metrics across workflow families
- support audit-friendly historical analysis

## 4. Metric layers

### 4.1 Definition-level metrics

Metrics tied to the workflow design itself.

Examples:

- number of states
- number of guarded transitions
- number of approval checkpoints
- expected SLA windows per state

### 4.2 Instance-level metrics

Metrics captured for each runtime workflow instance.

Examples:

- total duration
- time in current state
- number of retries
- number of denials
- approval wait duration
- completion outcome

### 4.3 Aggregate metrics

Metrics rolled up by company, module, workflow type, or time window.

Examples:

- average completion time
- approval queue backlog
- completion rate
- denial rate
- transition failure rate
- median time in triage state

## 5. Minimum metrics to capture

### Time-based metrics

- `started_at`
- `completed_at`
- `time_in_state_ms`
- `approval_wait_ms`
- `transition_duration_ms`
- `queue_delay_ms`
- `time_to_first_action_ms`

### Volume metrics

- workflow instances started
- workflow instances completed
- workflow instances cancelled
- workflow instances failed
- workflow instances reopened

### Quality metrics

- transition denial count
- guard-failure count
- approval rejection count
- recovery count
- retry count
- manual intervention count

### Output metrics

- terminal outcome type
- emitted-signal count
- downstream action count
- artefacts generated

## 6. State timing model

Every workflow instance should record when it enters and exits a state.

Recommended fields or derived facts:

- `state_entered_at`
- `state_exited_at`
- `duration_ms`
- `entered_by_transition_key`
- `exit_reason`

This is essential because many useful workflow metrics are really state-duration metrics in disguise.

## 7. Approval metrics

Because approvals are core to Titan workflows, approval metrics should be first-class.

Capture:

- count of approval-required instances
- average approval wait time
- approval rejection rate
- approval expiry rate
- approval-by-role distribution
- approval override frequency

These metrics help distinguish healthy governance from workflow friction.

## 8. Transition metrics

Transitions should emit measurable events or logs.

Recommended transition metrics:

- successful transitions by key
- denied transitions by key
- rerouted transitions by key
- average transition execution time
- transitions requiring manual resume

## 9. Tenant and scope dimensions

All aggregate metrics should support at least these dimensions:

- `company_id`
- `workflow_key`
- `template_key` if applicable
- `module_name`
- `entity_type`
- `approval_mode`
- `status/outcome`
- time window

This keeps reporting tenant-safe and operationally useful.

## 10. Metric consumers

### TitanZero

Uses metrics for orchestration tuning, assist suggestions, and process insight.

### Admin dashboards

Uses metrics for operational visibility and backlog monitoring.

### Automation systems

Uses metrics for escalation decisions, retry policies, and unhealthy-flow detection.

### Reporting and BI

Uses metrics for trend analysis and workflow quality review.

### Stuck-state detection

Uses metric thresholds to determine unhealthy dwell times.

## 11. Recommended storage strategy

Use layered storage:

### Canonical logs/events

Store detailed workflow events and transition logs as the source of truth.

### Derived summaries

Store rolled-up summaries for dashboards and fast queries.

### Snapshot or pulse summaries

Store periodic summaries for health views and alerts where needed.

This follows the same general principle Laravel encourages around queues, events, and deferred work: keep core execution and reporting concerns separable and manageable through structured runtime services rather than controller bloat or UI-specific counting fileciteturn0file2.

## 12. KPI examples by workflow family

### Quote workflow

- quote turnaround time
- approval delay before send
- acceptance rate
- stale quote rate

### Complaint workflow

- time to triage
- escalation rate
- reopen rate
- resolution SLA hit rate

### Service/job workflow

- time from intake to schedule
- time from schedule to completion
- review-required frequency
- completion proof delay

### Document governance workflow

- time to approve
- revision count per artefact
- publish delay
- rejection loop frequency

## 13. Visual surfacing recommendations

Metrics should surface as:

- queue and backlog cards
- throughput charts
- approval latency charts
- state-duration tables
- SLA breach indicators
- stuck-state alerts

Presentation belongs in dashboard/admin layers, but the metrics themselves must come from the workflow layer, not be invented by the UI.

## 14. Failure modes

### Metric gaps

Not enough events are captured to reconstruct workflow health.

**Response:** require transition and state-entry logging as core workflow behavior.

### Inconsistent definitions

Different modules measure the same lifecycle differently.

**Response:** define minimum workflow metrics globally and use template-based metric hooks.

### UI-only metrics

Dashboards compute ad hoc numbers that diverge from runtime truth.

**Response:** centralize metric derivation in workflow/observability services.

### No tenant scoping

Cross-company metrics leak or aggregate unsafely.

**Response:** all metric rollups must remain company-scoped by default.

## 15. Dependencies

- workflow definitions
- state machines
- transitions
- approvals
- logs/audit services
- automation and observability layers
- dashboard/reporting surfaces

## 16. Open questions

- should workflow summaries be recalculated on demand, event-driven, or both?
- which metrics need real-time surfacing versus scheduled summaries?
- where should SLA policy definitions live relative to workflow templates?

## 17. Implementation notes

- Keep measurement hooks inside workflow execution services, not scattered through controllers or Filament widgets.
- Prefer stable metric keys over display labels so dashboards can evolve without changing the underlying measurement contract.
- Use routeable, queue-friendly, service-container-based measurement services so metrics remain reusable across jobs, listeners, commands, and UI paths fileciteturn0file3.
- Workflow metrics should eventually align with stuck-state detection thresholds and alerting policy rather than existing as passive reporting only.


## 18. Alert thresholds and SLA windows

Metrics should not remain passive. Every workflow family should declare threshold values that can trigger warning, breach, escalation, or recovery behavior.

### Minimum threshold classes

- warning dwell threshold
- breach dwell threshold
- approval wait threshold
- retry exhaustion threshold
- queue delay threshold
- recovery timeout threshold

### Threshold ownership

- **workflow definitions** declare default operating windows
- **templates** provide reusable threshold baselines
- **tenant/company policy** may tighten thresholds without weakening platform safety ceilings
- **stuck-state detection** consumes these thresholds to classify state health

This keeps metrics, stuck detection, and approval escalation aligned rather than letting each system invent its own timers.

## 19. Cross-domain workflow measurements

Because Titan workflows frequently hand work from one domain to another, metrics should capture handoff quality, not just local state timing.

### Minimum handoff measurements

- source workflow/state/transition
- target workflow or creation intent
- handoff accepted vs rejected
- handoff delay
- duplicate handoff prevention hits
- downstream completion confirmation

### Example chains

- CRM → Work → Money
- Quote → Service Job → Invoice
- Complaint → Resolution → Follow-up
- Draft → Approval → Publish

Cross-domain workflow references in project docs already emphasize guarded movement between CRM, Work, and Money, including acceptance, issue, and payment transitions, so these chains should become first-class measurement paths rather than informal reporting joins fileciteturn0file6.

## 20. Recommended canonical tables

The workflow blueprint recommends a minimum model including:

- `workflow_definitions`
- `workflow_instances`
- `workflow_steps`
- `workflow_transitions`
- `workflow_approvals`
- `workflow_metrics`
- `workflow_recovery_logs`

The metrics layer should treat `workflow_metrics` as a summary/derivation surface, not the only source of truth. Detailed events still belong in step, transition, approval, and recovery logs so dashboards can be rebuilt without trusting a single counter table fileciteturn0file123.

## 21. Implementation notes — second pass

- Derive aggregate KPI summaries from canonical workflow logs using scheduled summaries and event-driven updates where both are justified.
- Keep breach windows and warning windows configurable per workflow family, but require stable global metric keys so comparison remains possible.
- Align metrics with scheduling, retry, idempotency, inbox/outbox, and dead-letter layers so workflow health reflects real runtime reliability, not just happy-path completion counts fileciteturn0file124.
- Where teams expose dashboard charts, ensure they read derived workflow summaries or observability services rather than recomputing semantics separately in controllers or widgets.
