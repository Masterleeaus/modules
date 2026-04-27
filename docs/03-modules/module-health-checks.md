# Module Health Checks

## Purpose

Define the minimum health-check and repair expectations for a Titan-ready module so installs, upgrades, repairs, and runtime activation can be validated before the module is trusted across web, API, PWA, Omni, package, and AI surfaces.

## Scope

This document covers:

- module-level health diagnostics
- install and upgrade validation checks
- provider, route, migration, and permission verification
- package and tenant readiness checks
- API / PWA / AI surface presence checks
- repair-mode support expectations

This document does not replace the installation, lifecycle, manifests, settings, or permissions documents. It validates that those contracts are actually present and functioning.

## Architecture Position

Health checks sit between module registration and safe activation.

They connect to:

- discovery and registry
- installation and upgrade flows
- repair tooling
- package enablement
- tenant-scoped runtime activation
- AI and automation trust decisions

A module should not be treated as healthy just because its folder exists.

## Responsibilities

A health-check layer must be able to answer:

- can the module be discovered?
- can its provider boot safely?
- are required routes present?
- are migrations safe and complete?
- are permissions and settings seeded?
- is package/module visibility aligned?
- are tenant boundaries being respected?
- are declared manifests actually readable?
- are exposed APIs and surfaces consistent with the module contract?

## Required Health Domains

### Structure health
Verify that required files and folders exist in expected locations.

Examples:
- `module.json`
- provider classes
- `Routes/web.php`
- `Routes/api.php`
- migrations and seeders
- manifests folder when applicable

### Provider health
Verify that service providers load and register without fatal errors.

Checks should confirm:
- config merge works
- routes load
- views/translations load if declared
- migrations load
- bindings do not fail at boot

### Route health
Verify required named routes and API routes are actually registered.

This should include:
- user routes
- optional super admin routes
- API endpoints required by PWA/mobile/Omni
- route-name alignment with sidebar/menu expectations

### Migration health
Verify database migrations are:

- present
- idempotent
- safe to rerun
- aligned with tenant scoping rules

### Seeder and settings health
Verify that:

- permissions are seeded
- module settings are created
- package-related settings exist
- defaults exist for existing and future tenants where required

### Manifest health
Where manifests exist, verify they are:

- present
- readable
- syntactically valid
- aligned with the module’s actual routes and surfaces

Examples:
- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`

### Surface health
Verify that the module can safely expose its declared surfaces:

- sidebar
- dashboard widgets
- CMS renderer
- API endpoints
- PWA/mobile use
- Omni channels
- AI tools

## Required Checks

At minimum, a production-grade module health pass should validate:

- manifest readable
- provider loads
- routes registered
- migrations idempotent
- permissions seeded
- module settings present
- package visibility respected
- tenant scoping enforced

## Optional Advanced Checks

A stronger Titan-grade health pass may also validate:

- API exposure flags
- AI tool registry presence
- CMS surface binding
- Omni channel eligibility
- signal manifest consistency
- lifecycle step availability
- policy presence for critical mutations
- action/service bindings for core CRUD flows

## Repair Mode Expectations

Health diagnostics should feed a repair flow rather than only returning failure.

Repair mode should be able to:

- rebuild module registry cache
- backfill missing settings
- reseed missing permissions
- re-read manifests
- verify provider registration
- identify drift between package state and module state

Repair should be idempotent and safe on rerun.

## Failure Modes

Common module health failures include:

- provider boots locally but not in production
- module appears in list but routes are missing
- package says enabled while settings say disabled
- API route exists but tenant fencing fails
- manifests exist but point to non-existent endpoints
- migrations ran partially
- permissions were never seeded
- sidebar entry exists but route name drifted

## Observability

Health checks should emit enough information for operators and future agents to diagnose problems quickly.

Useful outputs include:

- module doctor results
- install/upgrade logs
- route inspection results
- migration status
- missing manifest warnings
- package/module mismatch warnings
- tenant-scope validation failures

## Security Model

Health checks are also a security gate.

A module must not be considered healthy if it bypasses:

- `company_id` tenant fencing
- permission seeding
- policy enforcement
- package entitlement gating
- safe API validation paths

## Example Flow

1. Module is discovered.
2. Health pass verifies file structure and provider boot.
3. Routes, migrations, permissions, and settings are checked.
4. Manifests are parsed and matched to declared surfaces.
5. Package and tenant gating are verified.
6. Module is marked ready or routed into repair mode.

## Future Expansion

This health contract should later support:

- structured doctor output
- severity levels
- auto-repair recipes
- manifest-schema validation
- panel-specific health probes
- AI trust scoring for module capability exposure
