# Node Network Resilience and Recovery

## Purpose
Defines how nodes behave under unstable connectivity so the PWA remains useful in real field conditions.

## Connectivity states
- online
- intermittent
- offline
- captive/blocked
- degraded API but reachable shell

## Runtime strategy
### Online
- sync immediately when safe
- refresh policy/cache in background

### Intermittent
- prefer local confirmation + queued replay
- reduce chatter and batch where possible

### Offline
- operate from cached shell and local DB
- queue all nonlocal-safe actions for server handoff

## Recovery algorithm
1. detect connectivity return
2. refresh auth if needed
3. replay high-priority queue first
4. process media uploads
5. reconcile server-authoritative fields
6. surface any failed/conflicted items

## Anti-corruption rules
- do not silently drop failed queue items
- do not mark remote commit complete until acknowledged
- do not merge across tenant boundary changes
- do not assume clock sync; use reconciliation timestamps
