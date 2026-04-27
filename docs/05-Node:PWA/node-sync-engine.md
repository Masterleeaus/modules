# Titan PWA Node Sync Engine

## Purpose

This document specifies the Sync Engine responsible for reconciling Titan Node state with the platform core. It ensures reliable bidirectional data flow, offline mutation durability, replay safety, and tenant-aware consistency.

This is Pass 02 of the cumulative Titan Docs Starter Kit output.

---

## Sync Engine Responsibilities

The Sync Engine coordinates:

- upstream mutation commits
- downstream state hydration
- envelope replay protection
- conflict arbitration
- tenant boundary enforcement
- delta transport optimization
- retry scheduling

It guarantees deterministic convergence between node-local state and server-authoritative state.

---

## Sync Modes

### 1. Hydration Mode

Executed when a node initializes or reconnects.

Responsibilities:

- fetch baseline state snapshot
- retrieve navigation manifests
- load permissions
- restore workflow positions
- hydrate cached resources

Hydration always runs before mutation replay.

---

### 2. Mutation Replay Mode

Processes locally staged updates.

Replay pipeline:

1. read local queue
2. validate tenant scope
3. attach replay token
4. submit envelope batch
5. await acknowledgment
6. mark committed

Replay is idempotent.

---

### 3. Delta Pull Mode

Retrieves incremental updates since last sync checkpoint.

Includes:

- workflow transitions
- signal dispatch updates
- permissions changes
- automation triggers
- CMS updates

Reduces bandwidth usage significantly.

---

### 4. Priority Sync Mode

Triggered for critical workflows:

- approvals
- escalations
- compliance actions
- dispatch transitions

Bypasses batching delays.

---

## Sync Queue Model

Each node maintains three queues:

### Mutation Queue

Stores:

- entity updates
- workflow transitions
- operator inputs

Processed first-in-safe-order.

---

### Signal Queue

Stores outgoing envelopes for:

- automation engines
- governance approval
- AI orchestration

Signals persist until acknowledged upstream.

---

### Retry Queue

Stores failed transmissions.

Retry strategy:

- exponential backoff
- network awareness
- priority escalation override

---

## Conflict Resolution Strategy

Resolution order:

1. approval-gated transitions
2. server-authoritative workflow state
3. timestamp precedence
4. node mutation fallback

Conflicts produce arbitration envelopes instead of overwriting data.

---

## Replay Protection

Replay protection uses:

- replay_token
- mutation_hash
- timestamp window validation

Duplicate commits are discarded safely without state corruption.

---

## Tenant Isolation Enforcement

Before transmission:

- tenant_id verified
- module scope verified
- permission context attached

Prevents cross-tenant leakage.

---

## Sync Checkpoints

Each node maintains:

last_sync_at
last_delta_token
last_signal_commit

These checkpoints guarantee incremental convergence.

---

## Offline Continuity Model

While offline:

- mutations accumulate locally
- signals remain buffered
- workflows continue progressing locally
- automation triggers execute when safe

On reconnect:

queued envelopes replay automatically.

---

## Transport Layer Expectations

Sync supports:

- REST batching
- streaming delta channels
- signal envelope transport
- compressed payload blocks

Transport adapters may switch dynamically depending on connectivity quality.

---

## Governance-Aware Sync

If an operation requires approval:

mutation enters pending state

until upstream authorization returns.

This prevents unsafe autonomous execution.

---

## Failure Recovery Strategy

Recovery pipeline:

1. detect failed batch
2. isolate failing envelope
3. retry remaining set
4. escalate persistent failures
5. emit recovery signal

Ensures queue durability.

---

## Future Documents Depending on This File

Upcoming passes:

- node-signal-envelope.md
- node-service-worker-runtime.md
- node-edge-ai.md
- node-governance.md

These extend transport guarantees defined here.

---

## Sync Safety Rules

The sync engine should never assume continuous connectivity.

Required safety rules:

- mutations must be durable before transport
- hydration must be version-aware
- replay detection must survive restart
- conflict handling must be observable
- tenant boundaries must be enforced on every batch

## Sync Result States

Useful normalized states:

- hydrated
- committed
- partially_committed
- deferred
- conflicted
- replay_blocked
- failed_retrying
- failed_needs_recovery
