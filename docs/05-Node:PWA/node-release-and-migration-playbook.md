# Node Release and Migration Playbook

## Purpose
Explains how Agent 5 should roll forward shell/runtime changes without breaking installed nodes, cached workers, or queued local data.

## Release surfaces
- manifest metadata
- service worker script
- shell JS/CSS
- local DB schema
- queue payload schema
- push subscription model

## Safe release order
1. deploy backward-compatible API support
2. deploy shell/runtime assets
3. allow new worker to install
4. migrate local schema on activate/open
5. reconcile legacy queue items
6. retire old worker/cache generations

## Local schema migration rules
- version IndexedDB explicitly
- preserve pending queue items during migration
- provide transforms for payload shape changes
- keep rollback path for one prior schema when practical

## Agent checks before release
- old queue payloads still parse
- activate event does not orphan caches
- offline fallback remains reachable
- update banner/prompt shown when refresh required
- push subscriptions remain valid or are re-requested cleanly
