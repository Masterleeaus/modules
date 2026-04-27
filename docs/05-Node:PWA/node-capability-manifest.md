# Titan PWA Node Capability Manifest

## Purpose

This document defines the manifest a node should expose so the platform and UI can negotiate behavior safely.

---

## Why this matters

A phone, desktop browser, and rugged field tablet should not pretend to have the same runtime powers. Capability-aware routing improves reliability and reduces broken flows.

---

## Suggested manifest

```json
{
  "node_id": "uuid",
  "device_class": "browser_pwa|mobile|desktop|field_tablet|edge_relay",
  "capabilities": {
    "offline_db": true,
    "background_sync": true,
    "push": true,
    "camera": true,
    "geolocation": true,
    "microphone": false,
    "local_ai": false,
    "file_access": true
  },
  "limits": {
    "max_attachment_mb": 20,
    "queue_soft_limit": 500,
    "supports_large_media": false
  }
}
```

---

## Usage

The manifest should influence:

- whether to queue or block an action
- whether voice/camera UI appears
- whether uploads use chunking
- whether local summarization is allowed
- whether certain shells are installable on the device

---

## Rule

Capability manifests are advisory for routing and UX, but never override policy, tenancy, or approval rules.
