# Module Routing

## Purpose

Define the canonical routing contract for Titan-ready Worksuite modules so every module exposes predictable web, API, and optional admin entry points without drifting from platform navigation, tenancy, or automation expectations.

## Scope

This document covers:

- `Routes/web.php`
- `Routes/api.php`
- optional `Routes/admin.php`
- route naming rules
- audience separation
- menu-safe named routes
- route loading through module service providers
- compatibility with panel, PWA, Omni, and AI surfaces

This document does not define controller business logic, request validation rules, or API payload schemas.

## Architecture Position

Module routing sits between:

- the module provider boot process
- platform navigation and menus
- API consumers such as Titan Go, Titan Portal, and external integrations
- AI/tool adapters that require stable endpoint targets

It must align with the module checklist requirement that modules load routes through their service provider and expose API routes for PWA and portal use. fileciteturn4file0

## Responsibilities

Module routing owns:

- declaring module URLs and named routes
- separating user, superadmin, and API audiences
- mapping HTTP entry points to thin controllers
- preserving stable route names for menus and links
- keeping route files modular and provider-loaded
- supporting future Titan bridge route loading patterns

## Required Route Files

Every module should provide:

- `Routes/web.php`
- `Routes/api.php`

Optional:

- `Routes/admin.php` for non-Filament admin-only endpoints

The domain module blueprint places these route files under the module root and expects them to be loaded by the module service provider, not manually copied into core route files. fileciteturn4file6

## Route Loading Contract

Module service providers must load route files during boot:

- web routes from `Routes/web.php`
- API routes from `Routes/api.php`
- optional admin routes when present

This follows Laravel's provider-based boot model and keeps route discovery consistent and cacheable across environments. fileciteturn4file1turn4file3

## Audience Separation

### User panel routes

User-facing web routes should live under account-scoped prefixes such as:

- `account/promotions`
- `account/services`
- `account/cms`

This aligns with the Titan-ready checklist’s user panel rule. fileciteturn4file0

### Superadmin routes

Superadmin routes should use a distinct prefix such as:

- `super-admin/promotions`
- `super-admin/services`

This prevents tenant-facing and platform-management surfaces from collapsing into one route layer.

### API routes

API routes should be exposed from `Routes/api.php` for:

- Titan Go
- Titan Command
- Titan Portal
- external integrations
- future PWA/device node clients

The checklist explicitly marks API surface registration as required for Omni, Titan Go, and Portal. fileciteturn4file0

## Naming Rules

Named routes are mandatory for any route referenced by:

- menus
- redirects
- notifications
- Filament actions
- module settings links
- automation callbacks

Preferred pattern:

- `dashboard.user.<module>.index`
- `dashboard.user.<module>.show`
- `dashboard.user.<module>.create`
- `dashboard.superadmin.<module>.index`

This follows Laravel’s recommendation to prefer named routes for durable linking and future path changes. fileciteturn4file3

## Controller Mapping Rules

Routes should map to thin controllers that act as request coordinators, not business-logic containers.

Controllers should:

- accept validated requests
- delegate to actions/services
- return views/resources/responses
- avoid embedding workflow, queue, policy, or transition logic directly

This follows Laravel guidance on keeping controllers lean and moving business logic into requests, actions, and services. fileciteturn4file2turn4file3

## Route Grouping

Modules should use route groups for:

- middleware
- prefixes
- namespaces where needed
- route name prefixes

Typical grouping concerns:

- authenticated user access
- superadmin authorization
- tenant scoping middleware
- API auth/token middleware

Laravel route groups are the correct place to express shared middleware and URL structure without duplicating route metadata. fileciteturn4file3

## Menu Compatibility

Any route intended for sidebar/menu wiring must:

- be named
- resolve in the correct audience context
- remain stable across upgrades
- return a valid page without redirect loops

The Titan checklist ties sidebar visibility directly to named route targets inserted into the `menus` table. fileciteturn4file0

## PWA and Omni Compatibility

A route design is only Titan-ready if it supports more than browser navigation.

Routing must assume future use from:

- panel UI
- mobile/PWA clients
- chat-triggered actions
- automation callbacks
- omni-channel entry points

That is why core module behavior must not depend on Filament-only URLs or panel closures. The route layer is one consumer boundary; the business action layer remains below it. fileciteturn4file6turn4file7

## Failure Modes

Common routing failures:

- provider not loading route files
- route names drifting from menu seeds
- API routes missing for module features exposed in PWA
- web-only assumptions breaking automation or AI tools
- duplicate prefixes across modules
- controller business logic growing inside route handlers

## Observability

Routing-related diagnostics should include:

- route:list validation during QA
- install-time module health checks
- named route existence checks for menu targets
- API smoke tests for required module endpoints

## Security Model

Routing must respect:

- company-bound tenant access
- user/superadmin audience separation
- middleware-based authorization
- stateless API authentication where required

Routes open the door, but permissions and policies must still be enforced downstream.

## Example Flow

1. Module provider boots.
2. `Routes/web.php` and `Routes/api.php` load.
3. User sidebar points to `dashboard.user.services.index`.
4. Browser UI uses web routes.
5. Titan Portal uses API routes.
6. Controllers delegate to shared actions/services.
7. Same module behavior works across UI, API, and automation contexts.

## Future Expansion

Later additions may include:

- route manifests for auto-registration
- Titan bridge route export files under `routes/Titan/`
- device-node specific API partitions
- channel-triggered route adapters for omni workflows
