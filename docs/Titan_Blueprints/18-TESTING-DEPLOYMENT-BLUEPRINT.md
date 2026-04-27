# Testing + Deployment Blueprint

Status: Canonical draft  
Layer: Verification, install safety, release discipline

## Role

A system this modular needs structured tests and environment-safe deployment. The goal is to verify installability, tenant safety, runtime reliability, and UI wiring without relying on production discovery.

## Testing Tree

```text
tests/
├─ Unit/
├─ Feature/
├─ Integration/
├─ Architecture/
├─ Contract/
├─ Snapshot/
└─ Support/
```

## Recommended Test Types

### Unit
- actions
- services
- guards
- value objects
- DTO mapping

### Feature
- controllers
- API endpoints
- approval flows
- signal emission
- Filament actions wrapping module actions

### Integration
- module install
- migrations idempotency
- provider boot order
- queue/listener wiring
- channel delivery mocks

### Architecture
- forbidden dependency direction
- Filament not owning business rules
- module not depending on UI internals

### Contract
- manifest schema validity
- API resource shape
- signal envelope shape
- PWA contract shape

## Deployment Blueprint

### Environment Domains
- local
- staging
- production
- optional device/node test environment

### Deployment Steps
1. pull code
2. install PHP dependencies
3. install frontend dependencies if used
4. run build
5. run idempotent migrations
6. warm config/routes/views caches
7. verify queue workers / schedulers
8. health check panels, APIs, channels, and critical modules

### Post-Deploy Checks
- route cache health
- queue health
- module discovery
- Filament panel boot
- signal registry loaded
- PWA manifest available
- Omni channel credentials present where enabled

## Required Release Tests for a Module
- installs cleanly
- appears in module list
- respects company boundary
- exposes expected routes
- boot provider does not break panel or API
- migrations are safe on rerun
- manifests validate
