# Titan Zero Documentation

Layer: Workflows
Scope: Reusable workflow templates, template inheritance, parameterized variants, and domain-safe reuse across Titan processes.
Status: Draft v1
Depends On: Workflow definitions, State machines, Transitions, Guards, Approvals, Core Platform
Consumed By: TitanZero, Workflow services, Automation engines, Module builders, Admin tooling, Documentation
Owner: Agent 07 — Workflows
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan reuses workflow logic through templates so common process shapes can be applied across modules, tenants, and vertical variants without copying transition logic into each domain.

## 2. Why it exists

Titan will have many process families that are structurally similar:

- review and approval flows
- lifecycle progressions from intake to completion
- escalation ladders
- reminder and follow-up sequences
- exception and recovery paths

If every module defines these from scratch, workflow logic drifts, metrics become incomparable, and fixes have to be repeated everywhere. Templates exist to provide reusable process skeletons with controlled variation.

## 3. Core responsibilities

- define reusable workflow patterns
- separate stable process structure from domain-specific labels and handlers
- support parameterized variants without copying the full machine
- preserve tenant-safe and module-safe reuse
- keep approvals, guards, and metric hooks aligned across similar workflows
- expose a stable template catalog developers can build from

## 4. What a workflow template is

A workflow template is a reusable definition blueprint that can generate or seed a concrete workflow definition.

A template should contain:

- a base state-machine shape
- transition categories
- guard slots
- approval slots
- timeout/retry placeholders
- metric hooks
- extension points for module-specific handlers

A template is not a runtime instance. It is a design-time contract used to create concrete workflows.

## 5. Template model

Recommended fields:

- `template_key`
- `name`
- `description`
- `domain_family`
- `version`
- `base_states`
- `base_transitions`
- `required_guards`
- `optional_guards`
- `approval_slots`
- `metric_hooks`
- `handler_slots`
- `parameter_schema`
- `compatible_entity_types`
- `status` — draft, active, deprecated

## 6. Template categories

### 6.1 Intake-to-execution templates

For workflows that begin with intake and move toward execution.

Examples:

- lead → quote → booking
- complaint intake → triage → action
- document import → classify → publish

### 6.2 Review-and-approval templates

For flows where movement must pause for human or policy review.

Examples:

- AI draft → review → approve → send
- quote review → supervisor approval → release
- governed artefact approval → publish

### 6.3 Reminder-and-escalation templates

For repeated timed follow-ups that escalate if unresolved.

Examples:

- invoice chase ladder
- complaint unresolved escalation
- overdue task follow-up sequence

### 6.4 Exception-and-recovery templates

For workflows that recover after failures, denials, or missing dependencies.

Examples:

- retry payment reminder path
- document import recovery path
- stalled dispatch reassignment path

## 7. Parameterization model

Templates should be parameterized instead of copied.

Parameter examples:

- allowed states to expose
- escalation timers
- approval threshold mode
- entity labels and terminology
- module action bindings
- timeout windows
- retry counts

This allows one template family to support multiple verticals while preserving comparable behavior.

## 8. Inheritance and override rules

Template reuse should be controlled.

### Allowed overrides

- state labels or display labels
- handler bindings
- timing parameters
- optional guard selection
- approval mode where policy permits
- module-specific emitted events/signals

### Not allowed to override casually

- tenant safety requirements
- audit requirements
- critical legal transition order
- mandatory approval checkpoints where required by policy
- metric hook minimums

These hard constraints protect the integrity of the template family.

## 9. Relationship to modules

Templates should not trap logic inside one module. Instead:

- modules consume templates
- modules bind handlers/actions to template slots
- module manifests may reference compatible template keys
- module-specific views may render the workflow differently, but they should not redefine the process structure ad hoc

This is especially important for Titan-ready modules, which are meant to become installable, package-aware, AI-executable, API-exposed, tenant-safe, and automation-ready components fileciteturn0file0.

## 10. Template examples

### Review template

Base states:

- draft
- under_review
- approved
- denied

Extension points:

- reviewer role
- approval threshold
- publish/send handler

### Escalation template

Base states:

- open
- reminder_sent
- escalated_l1
- escalated_l2
- resolved
- closed

Extension points:

- escalation timing
- escalation recipients
- auto-close conditions

### Service lifecycle template

Base states:

- intake
- qualified
- scheduled
- in_progress
- review_required
- completed
- invoiced
- closed

Extension points:

- module bindings for quote, booking, dispatch, invoice
- role-based approvals
- exception branches

## 11. Storage and source placement

Recommended source placement:

```text
app/Platform/Workflows/Templates/
```

Recommended support areas:

- `Definitions/`
- `Support/`
- module manifests referencing template compatibility

If database-backed template editing is added later, the code-defined template contract should still remain the canonical baseline.

## 12. Runtime behavior

At runtime, a workflow instance should always point to a resolved workflow definition, not directly to a mutable template. The template is applied at definition build/publish time.

This avoids a dangerous class of bugs where changing one template unexpectedly mutates active runtime instances.

## 13. Failure modes

### Template drift

Different modules fork the same workflow pattern informally.

**Response:** require template registration and compatibility mapping.

### Unsafe overrides

A tenant or module tries to remove mandatory approvals or guards.

**Response:** enforce non-overridable policy ceilings.

### Template sprawl

Too many near-duplicate templates emerge.

**Response:** maintain a small curated catalog with versioning and deprecation rules.

### Runtime-template coupling

Live instances depend on mutable templates.

**Response:** resolve templates into versioned workflow definitions before execution.

## 14. Dependencies

- workflow definitions
- state machines
- transitions
- guards
- approvals
- module manifests and package rules
- automation timing/retry systems
- metrics/stuck-state detection

## 15. Open questions

- which templates should be globally available versus module-scoped?
- should templates be code-only at first or also editable in admin later?
- how should version migration work when a template evolves but active workflows still exist?

## 16. Implementation notes

- Keep templates declarative and compact.
- Use parameter schemas rather than many hard forks.
- Do not trap workflow logic in Filament form/table callbacks; keep UI layers as consumers of workflow contracts, not owners of them fileciteturn0file6.
- Use Laravel service/container patterns so handlers bound into templates remain testable and reusable across controllers, jobs, listeners, and commands fileciteturn0file2.
