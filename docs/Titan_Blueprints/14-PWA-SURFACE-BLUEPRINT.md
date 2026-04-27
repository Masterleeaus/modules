# PWA Surface Blueprint

Status: Canonical draft  
Layer: User-facing device shell

## Role

The PWA surface is the installable, device-capable shell for operators, owners, office staff, and field workers. It consumes the platform and module engines without duplicating them.

## Tree

```text
app/Platform/Pwa/
├─ Shell/
├─ Navigation/
├─ Install/
├─ Offline/
├─ Sync/
├─ Notifications/
├─ Voice/
├─ Widgets/
├─ Cards/
├─ Actions/
├─ Permissions/
├─ Themes/
└─ Support/
```

## Surface Rules

### PWA Owns
- install manifest wiring
- service worker strategy
- offline shell
- device-specific navigation
- push registration
- mobile card composition
- app-mode UX state

### PWA Does Not Own
- domain business rules
- payment logic
- scheduling rules
- tenant resolution logic
- AI governance logic

## Surface Variants

### Titan Portal
- staff/customer portal surfaces
- approvals, summaries, account tools

### Titan Go
- field worker flow
- today/jobs/checklists/proof/issues

### Titan Command
- owner / dispatcher command view
- live map, exceptions, approval queue, comms shortcuts

## Recommended PWA Contracts

Modules should expose `pwa_contract.json` or equivalent metadata for:
- mobile cards
- action buttons
- offline-required data
- minimal sync set
- push event types
- voice intents

## Build Principle

PWA is not a separate backend. It is a surface contract over the same module/platform runtime.
