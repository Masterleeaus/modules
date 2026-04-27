# Module Signal Registration

## Purpose

Define how a module declares, validates, and participates in the signal system so automation, workflows, governance, approvals, and Titan Zero can reason about module events safely.

## Scope

This document covers:

- signal declaration
- registration and validation
- emitter/consumer contracts
- governance and approval compatibility
- auditability and replay expectations
- relationship to automation and AI layers

## Architecture Position

Signal registration is the module’s contract with the broader event-and-governance backbone.

It connects modules to:

- the signal engine
- automation engine
- workflow engine
- AEGIS governance
- Sentinel approval readiness
- AI orchestration and audit trails

## Responsibilities

A module signal layer must make it possible to know:

- which signals the module emits
- which signals it listens to
- which actions or entities those signals represent
- whether approval/gating applies
- how the signals should be audited or replayed

## Primary Declaration Surface

Signal participation should be expressed through `signals_manifest.json` or an equivalent stable machine-readable contract.

This manifest should not be ornamental; it should correspond to real module behavior.

## Signal Categories

A useful signal contract may include:

- entity-created
- entity-updated
- entity-deleted
- status-changed
- lifecycle-transitioned
- notification-triggered
- approval-requested
- approval-resolved

The exact taxonomy can vary, but semantics must be clear.

## Registration Flow

During install or registry refresh:

1. Read the signal manifest.
2. Validate syntax and required keys.
3. Confirm module references are real.
4. Register signal metadata with the signal engine.
5. Make registered signals visible to automation, workflow, and AI readers.

## Emitter Rules

A module may emit signals from:

- actions
- services
- domain events
- workflow transitions

Signals should not be treated as random side effects hidden in UI callbacks. They should represent meaningful domain events.

## Consumer Rules

If a module consumes signals from elsewhere, it should do so through explicit handlers, listeners, or automation/workflow bindings rather than hidden assumptions.

Consumers should validate tenant context and package/permission constraints before acting.

## Governance and Approval Compatibility

Some signals may feed directly into approval or governance layers.

The signal contract should allow the broader platform to determine:

- whether a signal is informational only
- whether it requires governance review
- whether it can trigger automation immediately
- whether it should wait for approval or readiness checks

## Replay and Audit

Signal registration should support later replay and audit needs.

That means the system should be able to tie a module signal back to:

- the module
- the entity/action it represents
- the tenant context
- the governance/approval path
- downstream automation/workflow effects

## Failure Modes

Common signal-registration failures include:

- signal manifest exists but handlers do not
- module emits signals not declared anywhere
- signal names drift across versions
- automation expects signals the module never registers
- tenant context is lost between signal emission and handling
- approval-sensitive signals are treated as ordinary fire-and-forget events

## Observability

Signal registration and use should appear in:

- registry/install logs
- signal engine diagnostics
- audit logs
- replay tooling
- AI/automation validation traces

## Security Model

Signal registration must preserve:

- tenant fencing
- permission awareness
- package entitlement boundaries
- governance/approval compatibility

A registered signal is not permission to mutate data arbitrarily.

## Example Flow

1. Module install reads `signals_manifest.json`.
2. Signal metadata is validated and registered.
3. `CreateBookingAction` emits `booking.created`.
4. Signal engine logs the envelope.
5. Automation and workflow layers can react according to declared rules.
6. Governance and approval layers remain able to inspect or block downstream action.

## Future Expansion

This contract should later support:

- typed signal payload metadata
- severity or trust levels
- replay recipes
- lifecycle-step correlation
- richer AI-readable signal semantics
