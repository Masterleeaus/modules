# Node Runtime Health Checks

## Purpose
This document turns node/PWA runtime verification into a repeatable health protocol for Agent 5 and operator-facing doctor tools.

## Health domains
- shell reachability
- service worker health
- cache health
- push health
- sync queue health
- auth/session health
- API envelope health
- tenant identity health

## Required checks
### Shell
- manifest reachable
- install prompt conditions satisfied
- icons and theme metadata resolve

### Service worker
- current worker registered
- active worker version matches expected release
- offline fallback cache present
- stale workers cleaned up after upgrade

### Push
- VAPID public key endpoint reachable
- subscription stored for current user/device
- unsubscribe path removes stale endpoint

### Sync
- queue can accept offline mutations
- pending and failed counts are queryable
- processing endpoint updates states correctly
- duplicate replay is rejected or merged safely

### Identity
- node session belongs to the active tenant
- company boundary is preserved in queued envelopes
- device identity rotation does not orphan subscriptions

## Suggested health states
- green: runtime safe
- amber: degraded but usable
- red: installation/sync/push broken

## Red-state triggers
- service worker not registered
- offline fallback absent
- repeated failed sync items
- VAPID keys unavailable
- auth/session mismatch causing replay failure
- manifest or icons unreachable from shell
