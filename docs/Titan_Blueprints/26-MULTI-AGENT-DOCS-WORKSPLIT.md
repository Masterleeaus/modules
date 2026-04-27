# Multi-Agent Documentation Worksplit

## Goal
Split doc production cleanly across agents without overlap or contradictory doctrine.

## Recommended split
### Agent 1 — Module + Filament
Owns:
- module structure
- install/upgrade lifecycle
- Filament split rules
- package/settings/menu integration

### Agent 2 — Platform + Runtime
Owns:
- engine blueprints
- workflows
- signals
- scheduling
- sync/offline
- observability

### Agent 3 — AI + Tooling
Owns:
- AI core
- governance
- memory
- tool registry
- proposal/approval model
- evaluation and safety

### Agent 4 — Surface layer
Owns:
- PWA
- Omni
- CMS surface maps
- API surface conventions
- user/admin panel surface roles

### Agent 5 — Integration + delivery
Owns:
- deployment
- testing
- security/audit
- docs index cleanup
- naming normalization

## Shared rules
All agents must:
- preserve naming doctrine
- use `company_id` tenant boundary
- avoid duplicating logic across surfaces
- keep business rules outside UI-only code
- update index docs when adding docs

## Merge discipline
- one canonical index
- one glossary if needed
- explicit pass numbering
- avoid silently rewriting another agent’s domain without stating it
