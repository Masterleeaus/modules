# Titan PWA Node Signal Envelope

## Purpose

This document defines the canonical signal envelope exchanged by Titan PWA Nodes, the platform core, automation engines, workflow engines, and AI orchestration layers.

The signal envelope is the transport-safe contract for runtime events. It allows nodes to emit actionable state changes without coupling the node UI or local runtime to any one backend implementation.

---

## Why Signal Envelopes Exist

Signal envelopes provide:

- a stable runtime contract
- replay-safe transport
- tenant-scoped event propagation
- workflow transition triggers
- automation entry points
- AI context packaging
- governance and approval checkpoints

This pattern aligns with event-driven decoupling, where side effects and downstream actions should be triggered through structured events rather than UI-local branching alone.

---

## Canonical Envelope Shape

```json
{
  "signal_id": "uuid",
  "signal_type": "booking.created",
  "module_origin": "BookingManagement",
  "tenant_id": 123,
  "node_id": "node_abc",
  "actor_id": 456,
  "workflow_id": "optional",
  "entity_type": "booking",
  "entity_id": 789,
  "payload": {},
  "meta": {},
  "replay_token": "hash-or-uuid",
  "priority": "normal",
  "requires_approval": false,
  "occurred_at": "2026-04-15T18:00:00Z"
}
```

---

## Required Fields

### signal_id

Unique identifier for this envelope instance.

Used for:

- traceability
- deduplication
- replay analysis
- audit linkage

### signal_type

Canonical event name.

Examples:

- booking.created
- booking.rescheduled
- invoice.sent
- crew.arrived
- inspection.failed

### module_origin

Declares which module or subsystem emitted the signal.

Examples:

- BookingManagement
- ServiceManagement
- Cms
- Omni
- TitanZero

### tenant_id

Primary tenant boundary field.

All envelopes must be tenant-scoped.

### node_id

Identifies the originating device or runtime node.

### actor_id

Identifies the user, worker, system agent, or automation actor responsible for the origin action.

### payload

Contains domain-specific event data.

### replay_token

Used by the sync engine and signal intake layer to prevent duplicate processing.

### occurred_at

The authoritative event timestamp.

---

## Optional Fields

### workflow_id

Links the signal to a workflow instance when present.

### entity_type and entity_id

Let downstream systems resolve the affected record or resource efficiently.

### meta

Supports auxiliary context, such as:

- local app version
- offline mode flag
- geolocation presence flag
- battery state
- channel source
- UI surface

### priority

Allowed example values:

- low
- normal
- high
- urgent

### requires_approval

Marks the signal as needing governance or operator approval before downstream actioning.

---

## Envelope Classes

### 1. Domain Signals

Represent business events.

Examples:

- quote.approved
- job.completed
- payment.failed

### 2. Runtime Signals

Represent node/runtime state changes.

Examples:

- node.online
- node.offline
- sync.failed
- cache.evicted

### 3. Governance Signals

Represent approval or policy checkpoints.

Examples:

- action.pending_approval
- action.denied
- permission.mismatch

### 4. AI Signals

Represent AI requests, summaries, classifications, or confidence flags.

Examples:

- ai.intent.detected
- ai.summary.generated
- ai.anomaly.flagged

---

## Payload Design Rules

Payloads should be:

- minimal
- explicit
- serializable
- replay-safe
- versionable

Payloads should not assume direct model hydration on every consumer.

Good payload example:

```json
{
  "status_from": "scheduled",
  "status_to": "in_progress",
  "scheduled_start": "2026-04-15T19:00:00Z",
  "worker_id": 88
}
```

Bad payload example:

- full UI state dump
- opaque HTML
- framework-bound objects
- unserializable closures

---

## Versioning Strategy

Signal contracts should support change over time.

Recommended fields inside `meta`:

```json
{
  "schema_version": 1,
  "app_build": "pwa-0.1.0"
}
```

Versioning allows node and server runtimes to evolve without silent contract drift.

---

## Approval and Governance Integration

If `requires_approval = true`, the envelope must not be treated as execution-ready.

Instead it should flow through:

1. intake validation
2. governance policy checks
3. approval queueing
4. downstream authorization response

This preserves the Titan pattern where AI and automation propose or route actions, while governed layers approve them before domain execution.

---

## AI Context Use

Signal envelopes are also lightweight AI context packs.

They can be used to:

- summarize live activity
- classify incident types
- detect follow-up intent
- route signals to specialist cores
- enrich operator prompts

This matches the broader architecture where backends provide structured state and controller coordination, while higher reasoning layers operate on normalized context packs instead of raw UI state.

---

## Storage Expectations

Node-local storage should preserve envelopes until:

- acknowledged upstream
- expired by policy
- compacted after durable audit persistence

Server-side storage should support:

- filtering by tenant
- filtering by signal_type
- replay inspection
- approval tracing
- workflow linkage

---

## Validation Rules

Each envelope should be checked for:

- valid tenant scope
- allowed signal_type
- required payload keys
- acceptable timestamp window
- valid replay token
- actor permission context when applicable

This follows the general emphasis on validation and clean request/application boundaries rather than trusting raw inbound data.

---

## Relationship to Future Docs

This file is the contract base for:

- node-service-worker-runtime.md
- node-edge-ai.md
- node-governance.md
- node-runtime-storage.md
- node-conflict-arbitration.md

These documents will build on the envelope rules defined here.

---

## Required Envelope Guarantees

The signal envelope contract should preserve these guarantees:

- transport safety
- replay detectability
- tenant scoping
- actor traceability
- workflow linkage where relevant
- policy evaluation compatibility
- audit compatibility

## Envelope Lifecycle

A typical envelope path is:

create -> validate -> persist locally -> sync upstream -> govern -> approve or reject -> dispatch -> audit
