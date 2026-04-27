# Platform and Module Checklist Master

## Platform checklist
- [ ] platform manifest exists and is versioned
- [ ] providers boot core registries
- [ ] tenant resolver returns `company_id`
- [ ] signal emitter contract is available
- [ ] AI tool registry loads module manifests
- [ ] PWA surface registry is defined
- [ ] communications engine has channel abstractions
- [ ] automation engine has retries/idempotency/outbox

## Module checklist
- [ ] module.json valid
- [ ] config + providers + routes present
- [ ] migrations are idempotent
- [ ] requests validate input
- [ ] actions own business mutations
- [ ] services own broader orchestration
- [ ] events/listeners/jobs handle side effects
- [ ] notifications/mail/export/import present where needed
- [ ] manifests exist for AI/signals/API/lifecycle
- [ ] Filament layer consumes module logic rather than duplicating it
- [ ] tests cover install, auth, tenant boundary, and key flows

## Delivery checklist
- [ ] route names match system conventions
- [ ] table names are explicit and tenant-safe
- [ ] event/job/notification names follow canonical rules
- [ ] docs updated when structure changes
