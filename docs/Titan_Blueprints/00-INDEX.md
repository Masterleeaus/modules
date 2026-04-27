# Titan Blueprints Index

Status: Canonical draft
Scope: Full platform, engine, AI, module, Filament, runtime-contract, PWA, Omni, sync, and deployment blueprint set

## Included Documents

1. `01-FULL-SYSTEM-DIRECTORY-TREE.md`
2. `02-PLATFORM-BLUEPRINT.md`
3. `03-FULL-ENGINE-BLUEPRINT.md`
4. `04-AI-CORE-BLUEPRINT.md`
5. `05-MODULE-BLUEPRINT.md`
6. `06-FILAMENT-PLUGIN-BLUEPRINT.md`
7. `07-MODULE-PLUS-PLUGIN-SPLIT-RULES.md`
8. `08-BOILERPLATE-SYSTEM.md`
9. `09-SIGNALS-ENGINE-BLUEPRINT.md`
10. `10-WORKFLOW-STATE-MACHINE-BLUEPRINT.md`
11. `11-COMMUNICATIONS-ENGINE-BLUEPRINT.md`
12. `12-SCHEDULING-RETRY-IDEMPOTENCY-BLUEPRINT.md`
13. `13-SYNC-OFFLINE-NODE-BLUEPRINT.md`
14. `14-PWA-SURFACE-BLUEPRINT.md`
15. `15-OMNI-CHANNEL-BLUEPRINT.md`
16. `16-PROVIDER-REGISTRY-BOOTSTRAP-BLUEPRINT.md`
17. `17-MANIFESTS-CONTRACTS-BLUEPRINT.md`
18. `18-TESTING-DEPLOYMENT-BLUEPRINT.md`

## Design Rules

- Platform owns shared runtime, contracts, registries, governance, signals, AI orchestration, sync, audit, and observability.
- Modules own domain data, domain actions, services, policies, jobs, events, notifications, exports/imports, manifests, and APIs.
- Filament owns admin/operator presentation only.
- AI is a first-class system layer, not a helper.
- Engines turn the system from CRUD into runtime automation.
- PWA, Omni, Portal, Go, Command, CMS, and API all consume the same core.
- Device-first and privacy-first patterns are reflected in sync, node runtime, and offline envelope flows.

## Pass 02 Additions

This pass adds the missing runtime contract set:
- signals and signal governance
- workflow/state machine structure
- communications and channel dispatch
- scheduling, retries, outbox, idempotency, and dead letters
- sync/offline/device-node runtime
- PWA surface model
- Omni channel model
- provider/bootstrap registry layout
- manifests/contracts reference
- testing and deployment blueprint

## Suggested Build Order

1. Core platform providers and registry
2. Signal contracts and governance
3. Workflow and scheduling runtime
4. Communications engine and Omni
5. Sync/node runtime
6. PWA surfaces
7. AI orchestration integration
8. Module-by-module adoption

## Pass 03 additions
- [19-TENANCY-IDENTITY-BOUNDARY-BLUEPRINT](19-TENANCY-IDENTITY-BOUNDARY-BLUEPRINT.md)
- [20-MODULE-INSTALL-UPGRADE-LIFECYCLE](20-MODULE-INSTALL-UPGRADE-LIFECYCLE.md)
- [21-FILAMENT-PANEL-INTEGRATION-BLUEPRINT](21-FILAMENT-PANEL-INTEGRATION-BLUEPRINT.md)
- [22-SECURITY-PERMISSIONS-AUDIT-BLUEPRINT](22-SECURITY-PERMISSIONS-AUDIT-BLUEPRINT.md)
- [23-OBSERVABILITY-HEALTH-DOCTOR-BLUEPRINT](23-OBSERVABILITY-HEALTH-DOCTOR-BLUEPRINT.md)
- [24-API-TOOLS-AND-EXTERNAL-SURFACES](24-API-TOOLS-AND-EXTERNAL-SURFACES.md)
- [25-CMS-PWA-OMNI-SURFACE-MAP](25-CMS-PWA-OMNI-SURFACE-MAP.md)
- [26-MULTI-AGENT-DOCS-WORKSPLIT](26-MULTI-AGENT-DOCS-WORKSPLIT.md)


## Pass 04 additions
- 27-CANONICAL-PLATFORM-STARTER-PACK.md
- 28-CANONICAL-MODULE-STARTER-PACK.md
- 29-CANONICAL-FILAMENT-STARTER-PACK.md
- 30-ROUTE-NAMING-AND-SURFACE-MATRIX.md
- 31-DATABASE-TABLE-MATRIX-AND-NAMING.md
- 32-EVENT-JOB-NOTIFICATION-NAMING-CONVENTIONS.md
- 33-GOLDEN-WORKED-EXAMPLE-BOOKING-MODULE.md
- 34-PLATFORM-AND-MODULE-CHECKLIST-MASTER.md
