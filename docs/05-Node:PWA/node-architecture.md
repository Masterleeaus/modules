# Titan PWA Node Architecture

## Purpose

This document defines the runtime architecture for Titan PWA Nodes. Nodes provide the distributed execution layer enabling offline-first operation, signal routing, automation triggers, and AI-assisted edge decisions across tenants, sites, devices, and operators.

This file is Pass 01 of the cumulative Titan Docs Starter Kit output.

---

## Conceptual Model

A Titan Node is a lightweight execution environment capable of:

- Local data persistence
- Offline workflows
- Signal emission and intake
- Automation trigger evaluation
- Sync negotiation with the platform core
- Edge AI inference (optional)
- Service-worker-backed resilience

Nodes operate as **first-class runtime peers**, not passive clients.

---

## Node Layers

### 1. Interface Layer

Handles:

- PWA UI shell
- device adapters
- operator interaction surfaces
- offline forms
- cached navigation trees

Backed by:

- service worker caching
- IndexedDB state storage
- asset manifest pinning

---

### 2. Local Persistence Layer

Responsibilities:

- offline queue buffering
- optimistic mutation staging
- signal envelope storage
- replay-safe operation tracking

Storage targets:

- IndexedDB
- Cache Storage
- encrypted local vault (optional secure contexts)

---

### 3. Sync Engine

Coordinates:

- upstream reconciliation
- downstream state hydration
- conflict resolution policies
- idempotent replay protection
- tenant boundary enforcement

Sync supports:

- pull-first hydration
- push-on-availability commits
- delta streaming updates

---

### 4. Signal Runtime

Each node emits structured envelopes:

{
  signal_type,
  module_origin,
  tenant_scope,
  payload,
  timestamp,
  replay_token
}

Signals feed:

- automation engines
- workflow transitions
- governance approvals
- AI orchestration

---

### 5. Automation Trigger Layer

Evaluates:

- lifecycle events
- timer-based triggers
- user actions
- sync completions
- signal arrivals

Triggers dispatch pipelines locally when possible before escalation upstream.

---

### 6. Edge AI Adapter (Optional)

Provides:

- classification
- summarization
- anomaly hints
- intent detection
- local fallback reasoning

Edge inference reduces latency and preserves privacy.

---

### 7. Governance Gate

Ensures:

- tenant isolation
- permission compliance
- approval-required operations
- safe-mode fallback behavior

Prevents unsafe automation execution offline.

---

## Node Types

### Operator Node

Runs on:

- tablets
- laptops
- field terminals

Supports:

- dispatch workflows
- inspection capture
- booking updates
- approvals

---

### Site Node

Installed per facility/site.

Supports:

- device telemetry
- presence signals
- compliance checkpoints
- environmental triggers

---

### Mobile Node

Runs on handheld devices.

Supports:

- offline-first capture
- delayed sync
- push-triggered refresh
- lightweight AI inference

---

### Edge Relay Node

Acts as a sync concentrator.

Responsibilities:

- batching updates
- signal routing
- WAN minimization
- tenant-local caching

---

## Service Worker Responsibilities

Each node registers a worker handling:

- route prefetching
- mutation queue persistence
- retry scheduling
- stale asset eviction
- offline fallback routing

This enables resilient execution independent of network availability.

---

## Sync Conflict Strategy

Priority order:

1. explicit approval decisions
2. workflow transitions
3. latest authoritative server state
4. node-local staged mutations

Conflicts produce envelopes for arbitration instead of silent overwrite.

---

## Relationship to MVC Platform Core

Nodes interact with Laravel MVC backends through structured API contracts where:

- models represent synchronized state
- controllers coordinate signal intake
- views render operator surfaces

This layered separation improves scalability and maintainability across distributed execution surfaces.

---

## Future Documents Depending on This File

Upcoming passes:

- node-sync-engine.md
- node-signal-envelope.md
- node-service-worker-runtime.md
- node-edge-ai.md
- node-governance.md

These extend the contracts defined here.

---

## Runtime Interfaces

This architecture document should be treated as the parent map for the rest of the Agent5 node docs.

Primary runtime interfaces:

- Service Worker Runtime for queue durability and offline continuity
- Sync Engine for upstream and downstream reconciliation
- Signal Envelope for transport-safe event contracts
- Governance Runtime for approval gating and policy enforcement
- Runtime Storage and Arbitration for local persistence and conflict resolution
- Edge AI Runtime for bounded local assistance
- Upgrade Coordination for version safety
- Observability for auditability and diagnosis
- Policy Overlays for tenant and vertical constraints
- Operator Recovery Flows for human-visible failure handling

## Node State Domains

A node should be understood as carrying several parallel state domains:

- interface state
- workflow state
- sync state
- approval state
- storage state
- policy state
- runtime version state
- observability state

These domains should be kept logically separated so replay, rollback, and recovery remain deterministic.

## Documentation Role

Use this file first when onboarding a developer, then branch into the specialized runtime documents listed above.
