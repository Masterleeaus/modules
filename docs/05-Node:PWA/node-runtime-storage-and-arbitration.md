# Titan PWA Node Runtime Storage and Arbitration

## Purpose

Defines the node-local storage model, retention behavior, recovery guarantees, and conflict arbitration rules used by Titan PWA Nodes.

This document closes the core runtime backbone by specifying how node state is preserved, compacted, inspected, replayed, and resolved when competing changes or partial failures occur.

---

## Responsibilities

This layer owns:

- local runtime persistence
- queue durability
- checkpoint storage
- signal retention
- compaction policy
- conflict arbitration inputs
- recovery snapshots
- replay inspection support

---

## Storage Buckets

The runtime should separate storage concerns into distinct buckets:

- durable outbound queue
- inbound hydration cache
- workflow checkpoints
- approval holds
- replay journal
- observability buffers
- AI assistance cache if enabled
- upgrade checkpoints

## Arbitration Inputs

Conflict arbitration should consider:

- server authority
- local timestamp ordering
- workflow state legality
- approval status
- actor role
- overlay constraints
- replay duplication markers
