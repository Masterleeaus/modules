# Module UI Integration

## Purpose

Define how a Titan-ready module connects to user-facing and operator-facing surfaces without allowing UI frameworks to become the only place core business logic lives.

## Scope

This document covers:

- module integration with sidebar, dashboard, CMS, PWA, and panel surfaces
- the boundary between domain logic and Filament/UI code
- menu and navigation expectations
- UI-safe consumption of module actions/services
- cross-surface consistency rules

This document does not define API schemas, package entitlement rules, or manifest internals in depth, although it references them.

## Architecture Position

UI integration sits above the module domain layer and below user interaction surfaces.

It connects modules to:

- sidebar navigation
- dashboard widgets
- Filament resources/pages/widgets
- CMS surfaces
- PWA and mobile views
- Omni and AI-facing experiences

A module should remain useful even if one UI layer disappears.

## Responsibilities

A module UI integration layer must:

- expose the module through approved surfaces
- keep UI flows consistent with package and permission rules
- consume module-owned actions/services
- avoid duplicating domain rules in form closures or widget callbacks
- remain compatible with web, API, PWA, and future surfaces

## Required UI Targets

A Titan-ready module should support these UI targets where relevant:

- dashboard widgets
- sidebar menu
- CMS renderer
- API endpoints
- package screen presence
- optional super admin settings
- optional Titan chat tools

The module checklist treats several of these as required.

## Sidebar and Navigation Integration

The module must be able to appear in the appropriate menu system with a stable named route.

Typical expectations include:

- user panel route patterns such as `account/<module>`
- optional super admin route patterns such as `super-admin/<module>`
- stable route names suitable for menu registration
- permission-aware visibility

Navigation should reflect package status and tenant enablement.

## Dashboard Surface Integration

Dashboard widgets and summaries should be consumers of module data, not the sole owners of workflow or mutation logic.

Good pattern:
- widget calls a query/service/action
- action mutates state
- widget renders output

Bad pattern:
- widget embeds the only copy of status transition or notification logic

## CMS Surface Integration

If a module exposes `cms_manifest.json`, the UI layer should be able to render module data into named CMS surfaces such as:

- homepage blocks
- services pages
- booking widgets
- portal fragments
- synced public-facing content areas

The UI should render through declared surfaces, not ad hoc one-off integrations.

## PWA and Mobile Surface Integration

Modules should be consumable by PWA and mobile shells through the same domain/API contract.

That means UI integration must support:

- offline-friendly display assumptions where needed
- stable data keys for device sync/rendering
- no dependence on Filament-only callbacks for core operations
- safe decomposition into forms, cards, queues, and view models

## Filament Boundary Rules

Filament is a panel consumer, not the module itself.

### Filament owns
- resources
- pages
- widgets
- tables
- forms
- infolists
- clusters
- operator dashboards
- approval screens
- admin-facing filters and summaries

### Module owns
- entities/models
- policies
- requests
- actions
- services
- events/listeners/observers
- jobs/notifications/mail
- imports/exports
- API resources
- manifests
- tenant logic

This separation keeps the module future-proof and removable from any one panel technology.

## No-Double-Up Rules

UI integration must avoid duplicated CRUD paths.

Bad:
- web controller creates booking one way
- Filament form creates booking a different way
- API controller creates booking a third way

Good:
- all surfaces call `CreateBookingAction`
- shared validation lives in requests/services
- notifications and events remain in the module domain

## Settings and Visibility Coupling

UI availability must respect:

- package entitlement
- module activation state
- tenant settings
- permissions/policies
- channel/surface flags

A menu item or widget should not appear just because a route exists.

## AI and Omni Surface Integration

Optional Titan chat tools and Omni surfaces should still consume module-defined contracts.

A module may expose:
- AI tools through `ai_tools.json`
- omni-compatible channels through `omni_manifest.json`

But UI integration must still route through permissions, settings, and tenant boundaries.

## Failure Modes

Common failures include:

- module appears in sidebar but is disabled by package
- Filament path works but API/PWA path does not
- business rules live only in table actions
- CMS surface bypasses module settings
- widget visibility ignores permissions
- module breaks if Filament is removed

## Observability

UI integration should be visible through:

- menu registration diagnostics
- route inspection
- panel boot/provider logs
- package visibility checks
- surface manifest validation
- audit traces for UI-triggered mutations

## Security Model

UI layers must not bypass domain security.

They must respect:
- permission seeds
- policies
- package/module activation
- tenant fencing via `company_id`
- safe API/domain action boundaries

## Example Flow

1. Module is enabled for a tenant package.
2. Sidebar shows the module because the route, package state, and permissions align.
3. User opens a Filament resource or web page.
4. UI delegates create/update work to module actions.
5. API/PWA/AI paths use the same domain logic and return consistent behavior.

## Future Expansion

This contract should later support:

- richer surface manifests
- panel-agnostic admin adapters
- CMS block registries
- typed widget/view-model descriptors
- dynamic navigation composition from module metadata
