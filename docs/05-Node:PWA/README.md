# Agent5 Docs — Node/PWA Focused Set (v5)

This package is the **Agent 5 node/PWA documentation set** refocused around device nodes, offline execution, sync, service workers, push, and server handoff.

It uses the original `Agent5Docs11_FULL` bundle as the base, then expands it using the project-source PWA/node docs and the `TitanPWA` module implementation files.

## Scope

This set is intentionally limited to:

- PWA shell behavior
- node runtime responsibilities
- local storage and offline queues
- sync contracts and payloads
- push subscription handling
- service worker boundaries
- node identity and session refresh
- server handoff and reconciliation

It avoids broad backend/module doctrine unless directly needed for node execution.

## Source basis used for v5

- `Agent5.zip` → `Agent5Docs11_FULL`
- `worksuite_ai_pwa_docs.zip`
- `TitanPWA.zip`
- selected architecture references from project-source blueprints

## Added in v5

- `node-indexeddb-schema.md`
- `node-sync-payload-spec.md`
- `node-service-worker-contract.md`
- `node-push-subscription-contract.md`
- `node-api-envelope-contract.md`
- `node-auth-session-rotation.md`
- `node-capability-manifest.md`

## Edit approach

These docs are edited cumulatively:

- original Agent 5 files retained
- missing build-ready sections added
- node/PWA implementation details added from project-source PWA docs and `TitanPWA` module code


## Expansion pass v6
This pass adds deployment, resilience, role-shell mapping, media capture, runtime health checks, testing, and release/migration playbooks grounded in the dedicated PWA/node docs and the TitanPWA module patterns for manifest/service-worker/push/sync handling.
