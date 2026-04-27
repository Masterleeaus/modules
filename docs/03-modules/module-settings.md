# Module Settings

## Purpose

Define how a Titan-ready module declares, seeds, reads, validates, and evolves its settings across super admin, package, tenant, and runtime layers.

## Scope

This document covers:

- module settings storage contracts
- default seeding rules
- super admin and tenant override flow
- package-aware enablement flags
- runtime feature toggle resolution
- upgrade-safe settings evolution

This document does not define:
- sidebar permissions
- route naming
- manifest schemas
- API endpoint design

Those are covered in the routing, permissions, installation, and manifests documents.

## Architecture Position

Module settings sit between installation-time registration and runtime execution.

They connect:

- module discovery and install flow
- package/module enablement
- tenant scoping
- CMS / Omni / PWA surface availability
- automation and AI feature exposure

## Responsibilities

A module settings layer must:

- seed defaults for new and existing companies
- expose stable keys for runtime use
- support package-aware enable/disable logic
- separate global defaults from tenant overrides
- remain safe on reinstall and upgrade
- avoid trapping business rules only in UI forms

## Required Storage Concepts

A module should support at least these setting scopes:

### Global defaults
Values applied as the base configuration for all tenants unless overridden.

### Package defaults
Values determined by package entitlement and module inclusion.

### Company overrides
Tenant-specific settings keyed by `company_id`.

### Optional user or surface overrides
Only where truly necessary for a user-facing experience or channel surface.

## Minimum Setting Categories

A module should be able to represent:

- activation state
- feature toggles
- defaults
- limits
- automation toggles
- presentation preferences
- channel/surface bindings

Examples:

- `status`
- `company_id`
- `module_name`
- `enable_api`
- `enable_pwa`
- `enable_omni`
- `enable_ai_tools`
- `default_status`
- `default_visibility`

## Data Contract

Settings should be stored in a structured and queryable form.

Minimum expectations:

- stable module key
- stable setting key
- typed value handling
- tenant boundary column
- timestamps where possible

## Seeding Rules

During installation, the module must seed settings for:

- existing companies
- future companies
- default package conditions

Seeders must be idempotent and safe to rerun.

If a setting already exists, the installer must not overwrite tenant changes unless an explicit upgrade rule allows it.

## Resolution Order

At runtime, effective settings should resolve in this order:

1. hard platform constraint
2. package entitlement
3. global module default
4. tenant override
5. optional user/surface override

This prevents local UI settings from bypassing package or policy restrictions.

## Super Admin Integration

A module may expose settings in a super admin surface for:

- defaults
- display rules
- pricing rules
- automation toggles
- integration toggles

These settings must still write through the module settings contract rather than living only in UI state.

## Package Integration

Module settings must support the Add Modules To Packages flow.

At minimum, package-aware logic must be able to answer:

- is the module enabled for this company?
- which features are allowed?
- which surfaces are exposed?
- which channels are allowed?

## PWA, CMS, Omni, and AI Surface Flags

Module settings should be usable by runtime layers to decide whether to expose the module through:

- PWA surfaces
- CMS renderer
- Omni channels
- Titan chat tools
- automation flows

This keeps the same module usable across web, API, PWA, and AI execution paths.

## Upgrade and Repair Rules

When a module version changes, settings upgrades must:

- preserve tenant-specific values
- seed only missing keys
- map renamed keys safely
- log destructive changes
- allow repair scripts to backfill missing defaults

## Failure Modes

Common failures include:

- setting exists for some tenants but not others
- package says enabled but module settings say disabled
- UI surface reads a stale key
- installer overwrites tenant changes
- PWA/API exposure ignores package gating

## Observability

Settings changes should be visible in:

- audit logs
- install/repair logs
- upgrade logs
- module health checks

## Security Model

All tenant-scoped settings must respect `company_id` as the tenant boundary.

Settings that affect execution or exposure should never be writable by an untrusted front-end surface without permission and policy checks.

## Example Flow

1. Module installs.
2. Default settings are seeded for every company.
3. Package enables the module for selected tenants.
4. Super admin adjusts defaults.
5. Tenant overrides allowed keys.
6. Runtime resolves effective settings for API, PWA, Omni, and AI surfaces.

## Future Expansion

This settings contract should later support:

- typed setting schemas
- validation metadata
- dependency-aware toggles
- channel-specific override groups
- AI-readable setting descriptors
