# Observability, Health, and Doctor Blueprint

## Goal
Make the platform inspectable before it becomes clever.

## Core observability pillars
- logs
- metrics
- health checks
- queue visibility
- failed job visibility
- module health diagnostics
- route/provider/config verification

## Health surfaces
### Platform health
Checks:
- app boot
- DB connectivity
- cache connectivity
- queue connectivity
- mail/channel connectivity where configured
- storage write access

### Module health
Checks:
- manifest valid
- provider resolves
- routes load
- views load
- migrations present
- required tables/columns present
- permissions seeded
- module settings present

### Runtime health
Checks:
- queues backed up?
- schedules firing?
- dead letters growing?
- signal approvals stalled?
- integrations erroring?

## Doctor pattern
A “Doctor” surface should expose:
- failing checks
- warning checks
- fix suggestions
- non-destructive repair actions where safe

## Logging strategy
Recommended channels:
- application
- audit
- AI actions
- signals
- communications
- payments/finance
- deployment/install

## Correlation IDs
Prefer request/job/signal correlation IDs so one action can be traced across:
- HTTP request
- emitted signal
- queued job
- outbound message
- approval event

## Minimum dashboards
- queue failures
- channel delivery failures
- module install failures
- automation retries/dead letters
- latency hotspots
- top recurring exceptions
