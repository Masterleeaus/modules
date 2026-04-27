# 15. Module and Plugin Blueprint

## Purpose

This document defines the preferred build pattern for Worksuite modules and Filament plugins in the Titan system.

The goal is to make every serious feature:

- installable in Worksuite
- package-aware and tenant-safe
- usable from web, API, PWA, mobile, jobs, and AI
- visible in the right admin surfaces
- reusable across multiple UI layers without duplicating business logic

This is the architectural line between **domain modules** and **panel plugins**.

---

## Core rule

A module is the engine.

A Filament plugin is the operator surface.

The module owns the business capability. The plugin exposes an admin or operator interface for that capability.

That means the module must remain valid even if Filament is removed, replaced, or joined later by:

- a mobile app
- a PWA
- an API client
- Titan Zero chat tools
- automation runners
- import/export jobs
- voice flows

Filament should consume module actions, not become the only place where the feature works.

---

## Golden split

### The module owns

- migrations and seeders
- Eloquent entities/models
- policies and permission contracts
- request validation rules that matter everywhere
- actions and services
- DTOs and value objects
- events, listeners, observers
- jobs, notifications, mail
- imports and exports
- web and API controllers
- tenant scoping and company boundary rules
- module settings and package integration
- manifests for AI, CMS, lifecycle, signals, and Omni
- tests for domain behavior

### Filament owns

- resources
- pages
- widgets
- forms
- tables
- infolists
- relation managers
- clusters and navigation grouping
- operator dashboards
- approval/review screens
- admin-facing summaries and filters

This keeps the panel thin and the domain durable.

---

## Recommended module tree

```text
Modules/<ModuleName>/
├─ module.json
├─ version.txt
├─ README.md
├─ CHANGELOG.md
├─ Config/
│  ├─ config.php
│  └─ features.php
├─ Providers/
│  ├─ <ModuleName>ServiceProvider.php
│  └─ RouteServiceProvider.php
├─ Routes/
│  ├─ web.php
│  ├─ api.php
│  └─ admin.php
├─ Database/
│  ├─ Migrations/
│  ├─ Seeders/
│  └─ factories/
├─ Http/
│  ├─ Controllers/
│  ├─ Controllers/Api/
│  ├─ Requests/
│  ├─ Middleware/
│  └─ Resources/
├─ Entities/
├─ Policies/
├─ Actions/
├─ Services/
├─ Data/
├─ Events/
├─ Listeners/
├─ Observers/
├─ Jobs/
├─ Notifications/
├─ Mail/
├─ Exports/
├─ Imports/
├─ Support/
│  ├─ Enums/
│  ├─ DTOs/
│  ├─ Helpers/
│  └─ Transformers/
├─ Traits/
├─ Scopes/
├─ Console/
├─ Tests/
│  ├─ Feature/
│  ├─ Unit/
│  └─ Integration/
├─ manifests/
│  ├─ ai_tools.json
│  ├─ signals_manifest.json
│  ├─ lifecycle_manifest.json
│  ├─ cms_manifest.json
│  └─ omni_manifest.json
└─ Filament/
   ├─ Plugin/
   ├─ Resources/
   ├─ Pages/
   ├─ Widgets/
   ├─ Tables/
   ├─ Forms/
   ├─ Actions/
   ├─ Infolists/
   ├─ Clusters/
   └─ Support/
```

Not every module needs every folder on day one, but the structure should allow growth without rewrites.

---

## Loader contract

Every module needs a valid loader contract.

### `module.json`

Minimum:

```json
{
  "name": "BookingManagement",
  "alias": "bookingmanagement",
  "providers": [
    "Modules\\BookingManagement\\Providers\\BookingManagementServiceProvider"
  ]
}
```

Recommended additional metadata:

- version
- description
- keywords
- priority
- dependencies
- permissions
- AI/CMS/API hints

### Service provider duties

The provider should be responsible for:

- merging config
- loading routes
- loading migrations
- loading views
- loading translations
- binding services/actions when appropriate

The provider is where the module formally becomes part of the application.

---

## Route doctrine

### User panel routes

Default user path pattern:

- `account/<module>`

Examples:

- `account/bookings`
- `account/services`
- `account/promotions`

### Super admin routes

Default super admin path pattern:

- `super-admin/<module>`

### API routes

Every serious module should have an API surface:

- `/api/<module>`

This is required for:

- PWA access
- mobile clients
- Titan Zero tools
- automation entry points
- partner integrations

### Named routes

Use stable named routes, not raw URL assumptions.

Examples:

- `dashboard.user.bookings.index`
- `dashboard.user.bookings.show`
- `superadmin.bookings.settings`

The UI should navigate by route names, not fragile string paths.

---

## Package and visibility contract

To behave like a first-class Worksuite module, a feature must do more than boot.

It must be visible and assignable.

### Required visibility integrations

- appear in module list
- attach to packages
- seed `module_settings`
- show in sidebar when enabled
- expose correct menu entries
- respect role/permission checks

### Package integration

The module should seed package-aware settings through `module_settings` using:

- `module_name`
- `company_id`
- `status`

Without this, the module may install correctly and still never appear for tenant users.

---

## Tenant boundary doctrine

The tenant boundary is `company_id`.

That is the minimum rule.

### Required fields

Use `company_id` whenever the record belongs to a tenant.

Use `user_id` whenever the record has a creator, owner, assignee, or actor.

### Often useful optional fields

- `location_id`
- `worker_id`
- `site_id`
- `team_id` only when maintaining legacy compatibility

### Good default

Most business records in this system should be scoped by:

- `company_id` for tenancy
- `user_id` for authorship or ownership when relevant

A module that only works for one user and ignores company boundary is not ready for Worksuite SaaS.

---

## Action-first doctrine

Business logic should converge into actions or services.

### Bad pattern

- web controller creates booking one way
- API controller creates booking another way
- Filament resource creates booking a third way

### Good pattern

- `CreateBookingAction` is the source of truth
- web controller calls it
- API controller calls it
- Filament action calls it
- import job calls it
- automation calls it

This is the only stable way to support:

- multiple interfaces
- AI execution
- replayability
- strong testing
- future UI changes

---

## Filament plugin doctrine

A Filament plugin should register only panel-facing artifacts.

### Plugin owns

- panel resources
- panel pages
- widgets
- tables/forms/infolists
- navigation clusters
- operator review flows

### Plugin should not own

- domain rules
- notifications
- policy law
- business transitions
- queue behavior
- external API invocation logic

If a rule matters outside the panel, it belongs in the module.

---

## No-double-up rules

### Never duplicate CRUD behavior

There must be one source of truth for domain operations.

### Never trap business law in UI callbacks

Do not bury business rules inside:

- Filament form closures
- table actions
- widget buttons
- Blade conditionals

### Never make Filament the only admin path

A serious feature should still work through:

- controllers
- jobs
- CLI
- API
- AI tool execution

Filament is a consumer, not the sovereign owner.

---

## Manifest doctrine

Modules should be manifest-rich so they can participate in the larger system.

### `ai_tools.json`

Defines callable operations for Titan Zero.

### `signals_manifest.json`

Defines emitted and consumed events/signals.

### `lifecycle_manifest.json`

Defines where the module participates in lead → quote → booking → job → invoice → follow-up flows.

### `cms_manifest.json`

Defines renderable surfaces for websites, portals, and PWA pages.

### `omni_manifest.json`

Defines which communication channels the module can speak through.

Manifests turn modules from isolated Laravel add-ons into system-native components.

---

## Migration doctrine

Every migration must be:

- idempotent
- safe on rerun
- tenant-aware where relevant
- defensive about dependent tables and columns

That means:

- guard table creation
- guard alter statements
- guard foreign keys when prerequisites may not exist yet
- avoid brittle assumptions about migration order

The module pack history has already shown why this matters.

---

## Super admin settings doctrine

If a module needs configuration above tenant level, provide a super admin settings surface.

Typical placement:

- `super-admin/settings/modules/<module>`

Examples of what belongs there:

- defaults
- pricing rules
- display rules
- automation toggles
- provider credentials or global integrations

Do not force system-level configuration into tenant-only screens.

---

## Testing doctrine

Every serious module should have at least three layers of tests:

### Unit tests

- actions
- services
- validators
- policy helpers

### Integration tests

- events/listeners
- job dispatch
- route/controller/action flow
- database writes

### Installation/health tests

- module files present
- providers load
- routes register
- permissions exist
- migrations work
- schema valid
- module visible in package and sidebar paths

Installation health testing is especially important for Worksuite because modules are often uploaded, merged, enabled, and repaired after deployment.

---

## Recommended lifecycle for a new module

1. Define domain boundary.
2. Create module skeleton.
3. Add migrations and entities.
4. Add actions and services.
5. Add requests/DTOs.
6. Add events, listeners, jobs.
7. Add web and API controllers.
8. Seed permissions.
9. Seed `module_settings` and package support.
10. Add manifests.
11. Add Filament plugin/resources.
12. Add tests.
13. Verify sidebar/package/user visibility.

That order protects the domain first and lets the panel arrive later without taking over the feature.

---

## Final standard

A Worksuite/Titan module is considered complete when it is:

- structurally valid
- bootable by provider
- route-correct
- package-aware
- tenant-safe
- API-exposed
- AI-manifested
- PWA-ready
- visible in the correct sidebars
- configurable at the right scope
- testable and rerunnable

A Filament plugin is considered correct when it provides operator surfaces for that module without becoming the only place where the domain works.
