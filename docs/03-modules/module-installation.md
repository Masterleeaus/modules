# Module Installation

## Purpose

Define the canonical installation flow for a Titan-ready module so it can be discovered, validated, registered, migrated, seeded, permissioned, surfaced, and activated safely across tenant, package, API, PWA, Omni, and AI layers.

## Scope

This document covers:

- install prerequisites
- discovery and validation
- provider registration
- migration and seeding flow
- package and settings bootstrapping
- manifest validation
- activation checks
- rollback and repair expectations

This document does not replace lifecycle-upgrade-repair rules or health-check diagnostics, but it establishes the base install path those systems extend.

## Architecture Position

Installation is the transition point between a module existing on disk and becoming a trusted system component.

It connects:

- the module registry
- service providers
- database migrations and seeders
- menus and permissions
- package entitlements
- manifests and runtime surfaces
- health-check and repair tooling

## Responsibilities

A correct installation pipeline must:

- discover the module
- validate its structure and loader manifest
- boot providers safely
- run database changes idempotently
- seed permissions and settings
- register runtime visibility
- validate declared manifests
- confirm the module is safe to activate

## Prerequisites

Before install begins, the system should verify:

- module folder name is stable
- `module.json` exists and is readable
- provider namespaces resolve
- required route files exist
- migrations and seeders are present where required
- tenant boundary assumptions are understood

## Install Pipeline

### 1. Discover module
The registry or installer detects the module under `Modules/<ModuleName>/`.

### 2. Validate manifest and structure
Read `module.json`, confirm provider paths, and validate the minimum required file layout.

### 3. Register service provider
The module’s main service provider must be loadable and safe to boot.

At minimum it should support:
- routes
- views
- translations
- migrations
- config

### 4. Load routes
Both web and API route surfaces should register without collisions or namespace drift.

### 5. Execute migrations
Migrations must be idempotent, safe on rerun, and aligned with tenant-scope requirements.

### 6. Seed permissions
The module must seed or register permission families required for UI, API, and action-level access.

### 7. Seed module settings
Defaults should be created for existing and future tenants where needed.

### 8. Register package visibility
The module must become visible to package integration logic so entitlements can control exposure.

### 9. Validate optional manifests
If present, manifests for AI, signals, lifecycle, CMS, and Omni should be parsed and validated.

### 10. Activation smoke test
Run health checks to confirm routes, providers, settings, permissions, and package state are aligned.

## Database Rules

Installation-time database work must be:

- idempotent
- rerunnable
- safe for partial recovery
- tenant-aware
- explicit about destructive changes

The installer should prefer create-if-missing and backfill-safe patterns over fragile one-shot assumptions.

## Permission and Menu Wiring

Installation should ensure:

- permissions are seeded
- menu entries can resolve named routes
- sidebar visibility can later respect package and permission state
- admin/user route separation remains clear

## Settings and Package Bootstrapping

Install should establish enough state for the module to participate in:

- packages screen visibility
- tenant enablement/disablement
- feature toggles
- API/PWA/Omni/AI exposure flags where supported

## PWA, Omni, and AI Awareness

A module is not fully installed just because a web page loads.

A Titan-ready install should consider whether the module can safely participate in:

- API consumers
- PWA/mobile surfaces
- Omni channels
- Titan Zero tool use
- signal and lifecycle flows

## Failure Modes

Common install failures include:

- provider boots but routes fail
- migrations succeed but permissions are missing
- package screen cannot see the module
- API routes were never registered
- manifests parse but point to stale endpoints
- module installs in one tenant flow but not another
- installer says success while sidebar and runtime remain broken

## Rollback and Repair Expectations

If installation fails mid-stream, the system should:

- surface exactly which phase failed
- avoid silently leaving the module half-installed
- support repair mode or safe rerun
- preserve already-valid tenant data where possible

## Observability

Install flow should emit enough detail for debugging:

- phase-by-phase status
- provider and route load errors
- migration state
- seeder status
- manifest validation output
- package visibility registration
- health-check summary

## Security Model

Installation should not expose the module to users until:

- permission seeds exist
- package and tenant gating can be evaluated
- tenant fencing is valid
- API/UI surfaces are safe to access

## Example Flow

1. Module is uploaded to `Modules/<ModuleName>/`.
2. Registry reads `module.json`.
3. Provider boots and loads config, routes, views, translations, and migrations.
4. Migrations and seeders run.
5. Permissions and settings are created.
6. Package visibility and manifests are registered.
7. Health checks pass.
8. Module is marked ready for tenant activation.

## Future Expansion

This installation contract should later support:

- dry-run installs
- structured installer reports
- auto-repair recipes
- dependency-aware install ordering
- richer module doctor integrations
