# Titan PWA Node Sync Payload Spec

## Purpose

This document defines the preferred request and response payload structure for node sync operations.

---

## Request envelope

Every sync request should carry:

```json
{
  "node": {
    "node_id": "uuid",
    "tenant_id": 42,
    "user_id": 77,
    "app_version": "1.0.0",
    "capabilities": ["camera", "push", "offline_db"]
  },
  "checkpoint": {
    "last_sync_at": "2026-04-20T08:00:00Z",
    "last_delta_token": "abc123"
  },
  "mutations": [],
  "signals": [],
  "attachments": []
}
```

---

## Mutation item

```json
{
  "queue_id": "uuid",
  "idempotency_key": "uuid",
  "entity_type": "visit",
  "entity_id": 991,
  "action": "complete",
  "base_revision": 12,
  "payload": {},
  "created_at": "2026-04-20T08:01:00Z",
  "priority": "high"
}
```

## Signal item

```json
{
  "envelope_id": "uuid",
  "signal_type": "visit.completed",
  "origin": "titan_go_node",
  "tenant_id": 42,
  "payload": {},
  "replay_token": "uuid",
  "created_at": "2026-04-20T08:01:10Z"
}
```

## Attachment item

```json
{
  "attachment_id": "uuid",
  "entity_type": "visit",
  "entity_id": 991,
  "mime_type": "image/jpeg",
  "checksum": "sha256",
  "upload_ref": "presigned-or-multipart-ref"
}
```

---

## Response envelope

The server should return one compact envelope:

```json
{
  "accepted": [],
  "rejected": [],
  "conflicts": [],
  "deltas": [],
  "approval_updates": [],
  "checkpoint": {
    "synced_at": "2026-04-20T08:04:00Z",
    "next_delta_token": "abc124"
  }
}
```

### `accepted`
Acknowledged mutations/signals that may be removed locally.

### `rejected`
Items that failed validation, permission, or policy checks.

### `conflicts`
Items needing merge or operator review.

### `deltas`
Fresh read-model updates to hydrate the node.

### `approval_updates`
State changes for queued actions that required governance.

---

## Rejection format

```json
{
  "ref": "queue-or-envelope-id",
  "code": "tenant_mismatch|stale_revision|permission_denied|policy_blocked",
  "message": "Human-readable reason",
  "retryable": false
}
```

---

## Rules

- server must be authoritative for approval state and protected workflow transitions
- node must keep local copies until accepted or explicitly discarded
- conflicts must be visible, not silently flattened
- payloads must remain compact and role-specific
