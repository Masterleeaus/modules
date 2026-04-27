# Module Structure

## Purpose

Define the canonical filesystem and class layout for a Titan-ready Worksuite module so discovery, loading, upgrades, APIs, AI tooling, PWA access, and panel integration all start from a stable base.

## Scope

This document covers:

- required top-level module folders and files
- recommended domain-layer directories
- manifest placement
- service provider and route placement
- testing and support structure
- structural rules that prevent drift

This document does not define the detailed behavior of installation, manifests, permissions, packages, settings, APIs, or tenant scoping. Those are handled in their dedicated module documents.

## Architecture Position

Module structure is the physical contract between:

- the loader and registry
- Laravel service providers
- route discovery
- migrations and seeders
- API/PWA/mobile execution
- Titan manifests
- Filament resources and operator surfaces

A stable structure makes modules installable, repairable, package-aware, tenant-safe, and future-proof.

## Responsibilities

A correct module structure must:

- be discoverable by the module loader
- expose stable provider entry points
- separate domain logic from panel UI
- support both web and API routes
- keep manifests machine-readable and predictable
- remain compatible with upgrades, tests, and future surface expansion

## Mandatory Minimum Structure

The minimum required Titan-ready module shape is:

- `Modules/<ModuleName>/`
- `module.json`
- `Config/config.php`
- `Providers/<ModuleName>ServiceProvider.php`
- `Providers/RouteServiceProvider.php`
- `Routes/web.php`
- `Routes/api.php`
- `Database/Migrations/`
- `Database/Seeders/`
- `Http/Controllers/`
- `Resources/views/`
- `Resources/lang/en/`
- `Entities/`

This minimum structure is required for installability and runtime visibility. The module checklist treats exact casing and path stability as mandatory.

## Recommended Gold-Standard Structure

A full Titan-grade domain module should expand beyond the minimum into a richer engine layout:

- `Config/`
- `Providers/`
- `Routes/`
- `Database/`
- `Http/`
- `Entities/`
- `Policies/`
- `Actions/`
- `Services/`
- `Data/`
- `Events/`
- `Listeners/`
- `Observers/`
- `Jobs/`
- `Notifications/`
- `Mail/`
- `Exports/`
- `Imports/`
- `Support/`
- `Traits/`
- `Scopes/`
- `Console/`
- `Tests/`
- `manifests/`
- `Filament/`

This follows the domain module blueprint and keeps the module viable beyond a single UI surface.

## Required Entry Files

### `module.json`
This is the core loader contract. It must identify the module and its providers.

### `Providers/<ModuleName>ServiceProvider.php`
The primary boot/register entry point. It should load routes, views, migrations, translations, and config.

### `Providers/RouteServiceProvider.php`
Used when route registration needs clearer separation or route-specific boot logic.

### `Routes/web.php`
User/admin web routes.

### `Routes/api.php`
API routes required for PWA, Omni, Titan Go, Titan Command, Titan Portal, and external integrations.

## Domain vs UI Split

Structure must preserve this rule:

### Domain module owns
- entities/models
- requests
- actions
- services
- events/listeners/observers
- jobs/notifications/mail
- imports/exports
- policies
- manifests
- API resources
- tenant-safe logic

### Filament/UI owns
- resources
- pages
- widgets
- tables
- forms
- infolists
- clusters
- operator dashboards

The structure should make that split obvious at a glance.

## Manifest Placement

Machine-readable manifests should live in a dedicated `manifests/` folder:

- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`

A predictable manifest location simplifies AI, automation, and registry scans.

## Support and Reuse Areas

A mature module should also support reusable infrastructure through:

- `Support/Enums/`
- `Support/DTOs/`
- `Support/Helpers/`
- `Support/Transformers/`

This keeps transport and formatting concerns out of controllers and UI closures.

## Tests Structure

Modules should carry their own tests:

- `Tests/Feature/`
- `Tests/Unit/`
- `Tests/Integration/`

That keeps module regression coverage close to the domain it protects.

## Naming and Case Rules

Folder names, namespaces, provider class names, manifest names, and route references must align exactly.

Avoid:
- mixed casing
- alias drift
- alternate folder wrappers
- duplicate naming schemes for the same module

Stable naming is required for loader reliability and future automation.

## Failure Modes

Common structural failures include:

- missing `Routes/api.php`
- provider path mismatch
- module folder name differs from `module.json`
- manifests scattered in multiple folders
- business logic trapped in `Filament/` instead of `Actions/` or `Services/`
- module only works while Filament is present

## Observability

Structure issues should be detectable through:

- module discovery logs
- install/repair diagnostics
- provider boot failures
- route list inspection
- health checks

## Security Model

A clean structure supports security by ensuring that:

- permissions and policies live in predictable places
- API and web entry points are inspectable
- tenant-fenced domain logic is not hidden in widgets or page callbacks
- manifests can be validated before execution

## Example Flow

1. Loader scans `Modules/`.
2. It finds `module.json`.
3. Providers register routes, views, migrations, and config.
4. Registry reads manifests from `manifests/`.
5. Web, API, PWA, Omni, and AI surfaces all consume the same domain logic from the module.

## Future Expansion

This structure should later support:

- extension fragments
- optional surface packs
- typed manifest schemas
- per-channel adapters
- richer code generation and doctor tooling
