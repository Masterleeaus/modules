# Titan PWA Node Push Subscription Contract

## Purpose

This document defines how nodes should register and refresh push subscriptions.

---

## Server-side reference model

The project-source `TitanPWA` module stores push subscriptions with:

- `user_id`
- `endpoint`
- `p256dh`
- `auth_token`
- `user_agent`

This means each node should treat push registration as **user + device scoped**, not as a single account-global token.

---

## Client registration flow

1. confirm notification permission
2. obtain worker registration
3. create or refresh Web Push subscription
4. POST subscription to authenticated node API
5. store local registration timestamp and subscription fingerprint

---

## Minimum payload

```json
{
  "endpoint": "https://...",
  "p256dh": "...",
  "auth_token": "...",
  "user_agent": "browser-or-device-string"
}
```

---

## Refresh triggers

Refresh subscription on:

- first sign-in on device
- worker activation after subscription invalidation
- permission change
- server 410/expired endpoint response
- explicit logout/login cycle

---

## Security rules

- never expose push keys in logs
- delete subscription on logout from shared devices
- keep push subscription tenant-safe via authenticated API calls
- treat push as a notification channel, not as proof of authorization
