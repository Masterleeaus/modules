# Titan PWA Node Auth and Session Rotation

## Purpose

This document defines how a node should handle auth refresh, revocation, and shared-device safety.

---

## Node session requirements

A node session must track:

- current user
- tenant/company binding
- token issue time
- token expiry or refresh window
- permission snapshot version
- package visibility snapshot version

---

## Rotation triggers

Refresh or revalidate session on:

- app foreground restore after long idle period
- sync rejection with auth-related code
- worker activation after app upgrade
- role/package change detected by server delta
- explicit remote revocation notice

---

## Shared terminal rule

On shared or kiosk-style nodes:

- use shortened idle timeout
- purge local drafts on logout unless explicitly preserved
- remove push subscription on logout if bound to an individual user
- clear tenant-scoped caches before another tenant/user can sign in

---

## Hard failure states

The node must block write replay when it detects:

- tenant mismatch
- revoked token
- missing permission refresh
- corrupted session state

In these states, the node may continue limited read-only offline access if policy allows, but must not silently push queued writes.
