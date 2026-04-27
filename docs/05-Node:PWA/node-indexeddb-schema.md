# Titan PWA Node IndexedDB Schema

## Purpose

This document defines the recommended local persistence schema for a Titan node running as a PWA. The goal is to keep the node small, resilient, and replay-safe rather than mirroring the full server database.

---

## Principles

The local database should:

- store only role-relevant working state
- separate read models from pending mutations
- support replay with idempotency keys
- keep tenant and user scope on every mutable record
- retain enough metadata for conflict diagnosis

---

## Recommended stores

### 1. `node_meta`

Purpose:
- node bootstrap state
- versioning
- last successful sync
- capability registration

Fields:

```json
{
  "node_id": "uuid",
  "tenant_id": 42,
  "user_id": 77,
  "app_version": "1.0.0",
  "schema_version": 3,
  "last_sync_at": "2026-04-20T08:00:00Z",
  "last_delta_token": "abc123",
  "last_permission_refresh_at": "2026-04-20T07:50:00Z"
}
```

### 2. `read_models`

Purpose:
- compact UI records for cards, lists, and active work surfaces

Fields:

```json
{
  "key": "job:551",
  "entity_type": "job",
  "entity_id": 551,
  "tenant_id": 42,
  "status": "in_progress",
  "payload": {},
  "server_revision": 17,
  "cached_at": "2026-04-20T08:00:00Z",
  "expires_at": null
}
```

### 3. `mutation_queue`

Purpose:
- writes created while offline or before server acknowledgment

Fields:

```json
{
  "queue_id": "uuid",
  "tenant_id": 42,
  "user_id": 77,
  "entity_type": "visit",
  "entity_id": 991,
  "action": "complete",
  "payload": {},
  "idempotency_key": "uuid",
  "mutation_hash": "sha256",
  "created_at": "2026-04-20T08:01:00Z",
  "priority": "high",
  "status": "pending"
}
```

### 4. `signal_outbox`

Purpose:
- outbound signal envelopes waiting for transmission or approval response

Fields:

```json
{
  "envelope_id": "uuid",
  "signal_type": "visit.completed",
  "origin": "titan_go_node",
  "tenant_id": 42,
  "payload": {},
  "approval_state": "awaiting_dispatch",
  "created_at": "2026-04-20T08:01:10Z",
  "status": "queued"
}
```

### 5. `attachments`

Purpose:
- local references for photos, signatures, audio notes, and uploads awaiting commit

Fields:

```json
{
  "attachment_id": "uuid",
  "tenant_id": 42,
  "entity_type": "visit",
  "entity_id": 991,
  "local_uri": "blob:...",
  "mime_type": "image/jpeg",
  "checksum": "sha256",
  "upload_status": "pending",
  "created_at": "2026-04-20T08:02:00Z"
}
```

### 6. `ui_state`

Purpose:
- transient but useful interface state

Examples:
- current tab
- open draft
- filters
- selected route day
- draft command text

### 7. `memory_cache`

Purpose:
- lightweight working memory for assistant context and site/job memory

Fields:

```json
{
  "memory_key": "site:88:entry_notes",
  "tenant_id": 42,
  "scope": "site",
  "payload": {},
  "source": "server|local|merged",
  "updated_at": "2026-04-20T08:03:00Z"
}
```

---

## Required indexes

Minimum indexes:

- tenant_id
- entity_type + entity_id
- status
- created_at
- idempotency_key
- envelope_id
- upload_status

---

## Purge rules

Safe purge targets:

- acknowledged queue items older than retention threshold
- expired read models
- uploaded attachment temp blobs
- stale UI state

Do not purge automatically:

- unacknowledged mutations
- failed transmissions without operator review
- unresolved conflicts

---

## Boundary rule

IndexedDB is a **node working database**, not a tenant database mirror. Store what the device needs to act, recover, and reconcile—nothing more.
