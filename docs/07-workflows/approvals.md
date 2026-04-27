# Titan Zero Documentation

Layer: Workflows
Scope: Approval-aware workflow splitting, review queues, approval states, authority checks, and auto-vs-review execution policy inside Titan processes.
Status: Draft v1
Depends On: Workflow definitions, State machines, Transitions, Guards, Automation, Policies, Signals
Consumed By: TitanZero, Workflow runtime, Automation engines, Specialist agents, TitanVault-style review surfaces, Reporting
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how approvals operate inside Titan workflows so important actions can pause, queue for review, resume safely, and remain auditable across company and platform layers.

## 2. Why it exists

Titan is designed to support several operating modes:

- suggest only
- queue for review
- auto-execute under policy

Workflows need a formal approval layer so they can move between those modes without duplicating logic in UI screens, bot builders, or channel handlers.

## 3. Approval role in the workflow layer

Approvals are not generic comments or notifications. They are workflow control points that:

- intercept risky or policy-limited transitions
- route work to an approval queue
- preserve runtime intent while paused
- resume the workflow from a known state after decision
- produce a durable audit record of who approved, denied, or edited the proposed action

## 4. What can require approval

Typical approval triggers include:

- low-confidence AI proposals
- high-risk or high-cost automations
- quote acceptance thresholds
- dispatch overrides or after-hours assignments
- financial actions like invoice issue or credit flows
- complaint resolution actions with compensation impact
- workflow transitions that bypass normal lifecycle order
- execution requiring elevated permission

## 5. Approval model

Approvals should be modeled as explicit workflow-aware records, not just flags.

Minimum approval fields:

- `approval_key`
- `workflow_instance_id`
- `transition_key` or `action_key`
- `company_id`
- `scope_type`
- `scope_id`
- `requested_by`
- `required_role` or `approval_policy_key`
- `status` — pending, approved, denied, expired, superseded, cancelled
- `decision_by`
- `decision_at`
- `decision_reason`
- `proposed_payload`
- `resolved_payload`
- `risk_level`
- `confidence_score` if AI-originated

## 6. Approval states inside workflows

Recommended workflow-aware approval states:

- `awaiting_approval`
- `approved_pending_resume`
- `denied`
- `expired`
- `cancelled`
- `superseded`

These states can be represented either as dedicated workflow states or as approval overlays depending on the workflow design, but they must remain explicit.

## 7. Approval creation sequence

Recommended sequence:

1. transition or action requested
2. guards evaluate
3. execution-mode guard decides approval is required
4. workflow instance is paused or split into approval state
5. approval record created
6. notification or queue surfacing dispatched
7. no irreversible side effect fires until approval outcome is resolved

## 8. Approval decision sequence

When a reviewer acts:

1. reviewer authority is checked
2. workflow instance and approval status are revalidated
3. decision recorded with actor, time, and rationale
4. workflow resumes, denies, or reroutes accordingly
5. downstream side effects only fire after legal resume

## 9. Approval authorities

Approval rights should be resolved through policy, not guessed in UI code.

Possible authority sources:

- role-based permission
- company admin authority
- supervisor hierarchy
- financial threshold policy
- package-level automation mode
- platform-level hard ceilings from TitanCore

Company rules may be stricter than platform defaults, but not more permissive than platform ceilings.

## 10. Approval modes

Titan workflows should support at least three runtime modes.

### 10.1 Suggest mode

AI or automation proposes only.

- no action executes
- approval always required
- useful during training or calibration periods

### 10.2 Review queue mode

System can assemble a ready action, but execution pauses for approval.

- workflow stores proposed action
- reviewer can approve, deny, or edit before resume

### 10.3 Auto mode

System executes without review only if:

- policy permits it
- confidence or risk thresholds pass
- no guard escalates to approval

## 11. Approval outcomes

Every approval request should resolve to one of a limited set of outcomes:

- approve as proposed
- approve with edits
- deny with reason
- request manual follow-up
- expire without decision
- supersede with newer proposal

These outcomes must drive workflow behavior directly.

## 12. Editing during approval

Some approvals should allow payload editing before resume.

Examples:

- quote amount adjusted before send
- dispatch assignment swapped to a different worker
- customer response draft edited before publish
- follow-up campaign text revised before schedule

The approval system must distinguish:

- proposed payload
- approved payload
- final executed payload

## 13. Relationship to specialist agents and AI

Specialist agents may generate proposals, but they do not own approval truth.

Approval rules must remain in workflow and policy layers so that:

- the same action behaves consistently from chat, admin UI, API, or automation
- AI confidence affects routing but does not replace governance
- approvals remain auditable even when origin is a bot or assistant

## 14. Approval queue surfaces

Approval queues may appear in different UIs, but all consume the same underlying approval records.

Potential surfaces:

- super admin global queue for platform-level exceptions
- company admin queue for tenant operations
- domain-specific queues such as finance, dispatch, or complaints
- artifact review surfaces in TitanVault-style modules
- assistant-generated review cards in chat-first shells

The queue view is a surface, not the source of truth.

## 15. Expiry and stale approvals

Approvals should not stay pending forever.

Required behaviors:

- expiry windows per approval type
- stale approval detection
- invalidation when workflow context changes materially
- superseding of old proposals by newer ones
- blocked workflow surfacing when approval has become stale

## 16. Approval audit requirements

Every approval decision must record:

- who requested it
- why it was required
- what payload was proposed
- who decided
- what changed
- when it resumed or terminated workflow progression

This is essential for:

- operational accountability
- AI training feedback
- dispute handling
- governance review

## 17. Approval placement in the code tree

Canonical home:

```text
app/Platform/Workflows/Approvals/
```

Recommended structure:

```text
app/Platform/Workflows/Approvals/
├─ Policies/
├─ Resolvers/
├─ Queueing/
├─ Decisions/
├─ Expiry/
├─ Audit/
└─ Support/
```

## 18. Example approval splits

### 18.1 AI-generated quote send

Flow:

- quote drafted
- confidence below threshold
- approval created for company admin
- admin edits price and approves
- workflow resumes to `quote_sent`

### 18.2 Dispatch override

Flow:

- workflow wants to assign after-hours worker
- schedule/policy guard requires supervisor review
- approval queue created
- supervisor approves or reroutes to manual dispatch

### 18.3 Complaint compensation

Flow:

- complaint triage recommends refund or redo
- compensation policy requires manager approval
- decision outcome determines whether workflow resumes to recovery action or denial path

## 19. Relationship to other docs

This doc defines **how approval pauses and decisions work inside workflows**.

Related docs define:

- `workflow-definitions.md` — process contract
- `state-machines.md` — legal state topology
- `transitions.md` — movement semantics
- `guards.md` — why approval may be required
- `metrics.md` — approval latency and throughput measurement
- `stuck-state-detection.md` — handling workflows blocked in review

## 20. Anti-patterns to avoid

- approvals implemented only as UI modals without durable records
- execution happening before approval write succeeds
- reviewer edits not preserved separately from original proposal
- company-level approvals bypassing platform policy ceilings
- AI confidence being treated as approval itself

## 21. Implementation checklist

An approval-aware workflow layer is ready when:

- approval requirements are policy-driven and explicit
- paused workflows retain full runtime intent
- reviewers can approve, deny, or edit where allowed
- resume logic is deterministic and auditable
- stale approvals are surfaced and expired cleanly
- specialist agents and automations cannot bypass approval routing

## 22. Final rule

Approvals are workflow control points, not UI conveniences. If a process matters enough to review, the approval must be represented as a durable workflow-aware record that can pause, resume, deny, expire, and explain itself cleanly.
