# Titan PWA Node API Envelope Contract

## Purpose

This document defines a stable API shape for node-facing endpoints.

---

## Principles

Node APIs should be:

- compact
- versioned
- explicitly tenant-bound
- safe for optimistic UI
- predictable in error formatting

---

## Read response envelope

```json
{
  "ok": true,
  "data": {},
  "meta": {
    "tenant_id": 42,
    "revision": 17,
    "generated_at": "2026-04-20T08:00:00Z"
  }
}
```

## Write response envelope

```json
{
  "ok": true,
  "result": {
    "entity_type": "visit",
    "entity_id": 991,
    "status": "accepted"
  },
  "reconciliation": {
    "server_revision": 18,
    "refresh_keys": ["visit:991", "job:551"]
  }
}
```

## Error envelope

```json
{
  "ok": false,
  "error": {
    "code": "permission_denied",
    "message": "Action not allowed for current session",
    "retryable": false
  }
}
```

---

## Node-specific API families

Recommended families:

- `/api/v1/node/bootstrap`
- `/api/v1/node/sync`
- `/api/v1/node/capabilities`
- `/api/v1/node/push/subscribe`
- `/api/v1/node/push/unsubscribe`
- `/api/v1/node/attachments/*`

The project-source `TitanPWA` module already demonstrates a practical subset for:

- push subscribe/unsubscribe
- VAPID key fetch
- sync queue store/index/process/destroy
