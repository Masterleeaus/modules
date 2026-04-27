# 14. Observability, Performance, and Deployment

## Purpose
A distributed SaaS/PWA/node system needs a stronger operational discipline than a normal CRUD app. Performance, cache behavior, queue health, route loading, and deploy safety all become product features.

## Laravel Baseline Principles
The uploaded Laravel material emphasizes:
- selecting only needed fields in queries
- eager loading to avoid N+1
- using caching deliberately
- queueing slow work
- keeping controllers small and testable fileciteturn1file2.

These ideas should become platform rules.

## Observability Layers

### App Health
- uptime
- queue backlog
- failed jobs
- route cache status
- config cache status

### Data Health
- slow queries
- N+1 warnings in non-production
- migration drift
- package/module visibility drift

### Node / Device Health
- last sync
- heartbeat lag
- offline count
- push failures

### AI Health
- proposal volume
- approval rate
- rejection reasons
- tool failure rate

## Performance Doctrine

### Routes
Use controller/resource routes rather than closures for deployable, cacheable route sets, consistent with Laravel route caching guidance fileciteturn1file3.

### Queries
- eager load known relationships
- avoid select * in heavy lists
- use pagination/chunking for large sets
- cache stable lookups

### Queues
Push non-blocking work to queues:
- notifications
- report generation
- AI summarization
- media processing
- synchronization retries

### Frontend
- cache app shell aggressively for PWAs
- lazy load noncritical screens
- avoid huge one-shot dashboards on mobile

## Suggested Platform Metrics
- request latency p50/p95
- queue age
- failed jobs per module
- sync failure rate by device type
- proposal-to-approval ratio
- package/module drift count

## Deployment Rules
- clear caches intentionally
- rebuild caches intentionally
- run migrations safely/idempotently
- smoke test module discovery after deploy
- smoke test package visibility after deploy
- smoke test auth + tenant switching

## Feature Flags
Use feature flags for:
- new PWAs
- new AI tools
- new channel connectors
- experimental sync modes
- Filament panel sections

## Environment Split
Maintain clear splits for:
- local/dev
- staging
- production
- tenant-specific hotfix or canary paths if needed

## Logging Doctrine
Logs should be structured enough to answer:
- what failed
- where
- for which company
- for which module
- on which device/node
- whether AI or human initiated it

## Recovery Mindset
Design for:
- repeatable migrations
- job retries
- idempotent syncs
- rollback plans
- queue drain/recovery
- temporary module disablement

## Outcome
The product feels advanced not just when it can do clever things, but when it can do them repeatedly, safely, and observably under real production stress.
