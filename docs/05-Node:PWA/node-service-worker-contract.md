# Titan PWA Node Service Worker Contract

## Purpose

This document defines what the service worker may and may not own inside a Titan node.

---

## The service worker should own

- static asset caching
- offline route fallback
- background sync registration where supported
- deferred upload wake-ups
- push event intake
- cache invalidation on version change

## The service worker should not own

- business rules
- approval decisions
- tenant policy evaluation
- workflow transition authority
- hidden mutation rewriting

---

## Cache families

### `shell-cache`
For app shell HTML/CSS/JS needed to boot.

### `route-cache`
For selected read-only route payloads.

### `media-temp`
For temporary blobs awaiting upload.

### `offline-fallback`
For offline status page and recovery instructions.

---

## Lifecycle events

### install
- cache shell assets
- cache offline fallback
- record worker version

### activate
- remove stale caches
- publish new worker version to the app shell
- trigger capability refresh if needed

### fetch
- serve shell offline
- prefer network for mutable API calls
- optionally stale-while-revalidate safe read surfaces

### sync
- wake queue processor
- retry pending mutation and attachment uploads
- stop on auth or tenant failures

### push
- display local notification
- attach deep-link route and action metadata
- avoid executing business actions directly from push payload alone

---

## Version rule

A new worker version must not silently reinterpret queued mutation payloads. If the payload schema changes, the app must run a queue migration or block replay until upgraded.
