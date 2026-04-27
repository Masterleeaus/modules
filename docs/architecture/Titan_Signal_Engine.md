# Titan Signal Engine

## Purpose

The Signal Engine is the backbone coordination layer connecting:

- modules
- automation engines
- AI orchestration
- approval governance
- workflow transitions
- communications routing
- PWA node synchronization

Signals represent validated intent, not execution.

Execution occurs only after approval.

## Signal Pipeline

All signals pass through four layers:

- Scout
- SignalAI
- AEGIS
- Sentinel

State transitions:

`process → processing → processed → approved`

Only approved signals reach domain services.

## Signal Envelope Structure

Example:

```json
{
  "signal": "job.completed",
  "tenant_id": 14,
  "entity_id": 882,
  "actor_id": 22,
  "timestamp": "2026-05-01T10:22:00Z",
  "payload": {}
}
```

Envelope rules:

- immutable
- tenant-scoped
- idempotent
- auditable
- replayable

## Signal Intake

Located in:

`app/Platform/Signals/Intake/`

Responsibilities:

- validate schema
- attach metadata
- enforce tenant boundary
- assign correlation id
- store intake record

## Signal Validation

SignalAI verifies:

- schema validity
- entity existence
- permission alignment
- duplicate suppression
- dependency completeness

Invalid signals are rejected with reason codes.

## Governance Layer

AEGIS evaluates:

- policy restrictions
- financial conflicts
- cross-domain consistency
- automation safety
- risk thresholds

Outputs:

- approved
- denied
- escalated

## Sentinel Approval

Domain readiness checks:

- resource availability
- schedule conflicts
- workflow legality
- state compatibility

Sentinel ensures execution readiness.

## Dispatch Phase

Approved signals routed to:

- automation engines
- workflow transitions
- communications engine
- module listeners
- AI orchestration layer

Signals never execute logic directly.

They trigger handlers.

## Signal Replay

Replay supports:

- crash recovery
- audit reconstruction
- simulation testing
- AI evaluation loops

Replay source:

`app/Platform/Signals/Replay/`

## Signal Logging

Tables:

- `signal_log`
- `aegis_log`
- `sentinel_log`

Logs include:

- timestamp
- decision
- reason code
- policy match
- actor reference

Ensures deterministic auditability.

## Signal Contracts

Defined in:

`signals_manifest.json`

Modules declare:

- emits
- accepts

Signal Engine validates compatibility automatically.

## Signal Engine Responsibilities

Owns:

- schema validation
- approval routing
- policy enforcement
- deduplication
- replay handling
- audit persistence
- dispatch orchestration

This converts Laravel events into governed system intent.
