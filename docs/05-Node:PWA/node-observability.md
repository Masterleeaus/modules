# Titan PWA Node Observability

## Purpose

Defines the observability model for Titan PWA Nodes so runtime behavior can be inspected, diagnosed, audited, and improved without relying on guesswork.

Observability makes distributed node behavior measurable across offline execution, sync, governance, service worker runtime, and edge AI assistance.

---

## Objectives

The observability layer must make it possible to answer:

- what happened
- when it happened
- where it happened
- why it happened
- whether it succeeded
- whether recovery occurred
- what state the node is currently in

---

## Minimum Telemetry Set

Every node runtime should make these streams available:

- sync attempts and outcomes
- queue depth and retry counts
- approval holds and releases
- policy overlay application
- storage compaction and rollback events
- upgrade activation and rollback events
- operator recovery entry points
- edge AI availability and fallback state

## Audit Linking

Observability records should be linkable back to:

- node_id
- tenant_id
- signal_id where applicable
- workflow_id where applicable
- runtime version
- overlay version
