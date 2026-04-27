# Signals Engine Blueprint

Status: Canonical draft  
Layer: Signal intake, governance, approval, replay

## Role

Signals are the normalized event envelopes that move intent and state between modules, automation, AI, approval flows, device nodes, and outward channels.

## Tree

```text
app/Platform/Signals/
├─ Intake/
├─ Contracts/
├─ DTOs/
├─ Envelopes/
├─ Emitters/
├─ Mappers/
├─ Validators/
├─ Governance/
├─ Approval/
├─ Dispatch/
├─ Replay/
├─ Correlation/
├─ Idempotency/
├─ Audit/
├─ Logs/
└─ Support/
```

## Core Responsibilities

### Intake
- receive signal payloads from modules, APIs, PWA nodes, cron, queues, and AI proposals
- normalize transport-specific input into canonical envelopes

### Contracts
- signal names
- schema contracts
- versioned payload rules
- required metadata

### Envelopes
Canonical envelope fields should include:
- `signal_id`
- `signal_name`
- `schema_version`
- `company_id`
- `actor_id`
- `source_type`
- `source_id`
- `correlation_id`
- `causation_id`
- `occurred_at`
- `payload`
- `risk_level`
- `approval_mode`

### Validators
- schema validation
- tenant boundary validation
- required field checks
- duplicate / replay protection
- timestamp sanity checks

### Governance
- policy checks
- package entitlement checks
- role / permission checks
- AI safe-mode checks
- cross-domain rule checks

### Approval
- suggest only
- queue for review
- auto-approve when policy allows
- escalation when confidence is low or risk is high

### Dispatch
- route approved signals to engines, modules, jobs, notifications, channels, or node sync

### Replay
- rebuild state
- recover failed flows
- backfill timelines
- re-drive automation after fixes

## Signal Lifecycle

1. Module or AI emits intent.
2. Intake builds canonical envelope.
3. Validators verify schema and tenancy.
4. Governance checks policy and entitlement.
5. Approval determines suggest/queue/auto path.
6. Dispatch routes the approved envelope.
7. Audit logs every step.
8. Replay can re-run from any valid checkpoint.

## Storage Pattern

Recommended records:
- `signals`
- `signal_attempts`
- `signal_approvals`
- `signal_dispatches`
- `signal_replays`
- `signal_audit_logs`

## Module Contract

Modules should optionally expose `signals_manifest.json` with:
- signal names
- producer actions
- consumer handlers
- payload schema
- risk class
- approval hints
