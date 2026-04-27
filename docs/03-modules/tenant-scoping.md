# Tenant Scoping

## Purpose

Define how a Titan-ready module enforces tenant boundaries across database writes, reads, APIs, automation, AI tooling, queues, and UI surfaces.

## Scope

This document covers:

- required tenant boundary columns
- query and write scoping
- package-aware module access
- tenant-safe background work
- cross-surface consistency for web, API, PWA, Omni, and Titan Zero

This document does not define:
- permission naming
- manifest schemas
- installation steps
- API response formats

Those are covered in related module docs.

## Architecture Position

Tenant scoping is one of the core platform safety contracts. It sits between:

- identity and package entitlement
- module settings and activation
- data persistence
- workflow and automation execution
- PWA/node and API access
- AI tool invocation

A module is not Titan-ready unless its data and behavior stay correctly fenced per tenant.

## Responsibilities

A module tenant-scoping layer must:

- treat `company_id` as the primary tenant boundary
- ensure every tenant-owned write includes the tenant key
- ensure every read path respects tenant scope
- keep background jobs tenant-aware
- prevent AI, automation, or API surfaces from bypassing tenant fences
- remain consistent across admin, user, API, import, export, and queue paths

## Required Boundary Columns

### Mandatory

- `company_id`

### Common supporting columns

- `user_id`
- `site_id`
- `location_id`
- `worker_id`

These may help narrow records further, but they do not replace the primary tenant boundary.

## Canonical Rule

The core rule is:

**`company_id` is the tenant boundary.**

Every tenant-owned table, action, API, signal, and job should assume that records must be created, queried, updated, exported, and deleted within the correct `company_id` scope.

## Data Modeling Rules

Tenant-owned tables should:

- include `company_id`
- index `company_id` where practical
- avoid ambiguous ownership
- avoid shared mutable rows across tenants unless explicitly modeled as global data

Module records that do not belong to a tenant must be clearly marked as global or system-level, and must not be confused with tenant-owned data.

## Read Scoping Rules

All read paths must resolve tenant context before querying module data.

That includes:

- web controllers
- API controllers
- Filament resources/pages/widgets
- jobs
- imports/exports
- automation executors
- AI tool adapters

The module must not assume that a UI surface already applied the tenant filter.

## Write Scoping Rules

All write paths must ensure the correct `company_id` is attached at creation time and preserved during update flows.

Unsafe patterns include:

- accepting tenant ID directly from an untrusted client
- updating records by ID without scoping to tenant
- reusing admin-only queries inside tenant flows

## Package and Activation Interaction

Tenant scoping works together with package/module activation.

A module may exist globally in the codebase but still be unavailable to a tenant if:

- the package does not include it
- module settings disable it for that company
- policy constraints block access

So tenant-scoped access is not only about row filtering. It also includes whether the tenant is allowed to use the module at all.

## Background Jobs and Queue Safety

Queued jobs must carry enough context to restore tenant boundaries safely.

At minimum, jobs should be able to resolve:

- module record identity
- tenant identity
- execution intent

Jobs must not process a record in isolation without validating that the record belongs to the expected tenant.

## Signals, Automation, and Workflow Safety

Signals emitted by a module should preserve tenant context inside their envelopes or related execution metadata.

Automation and workflows must not consume module signals in a way that loses:

- `company_id`
- package availability
- policy restrictions
- record ownership context

## API, PWA, and Omni Surface Safety

Tenant scoping must remain consistent when the module is accessed through:

- REST APIs
- PWA/mobile runtime
- Omni channel adapters
- Titan Zero tools

No surface should be allowed to bypass tenant resolution just because it is headless or asynchronous.

## Import, Export, and Bulk Operations

Bulk operations are a common source of tenant leakage.

Imports and exports must:

- resolve the current tenant before processing
- reject mixed-tenant batches where not explicitly supported
- write audit trails for high-risk bulk actions

## Failure Modes

Common tenant-scoping failures include:

- record fetched by ID without tenant filter
- job runs after context loss
- API trusts inbound `company_id`
- export includes rows from another tenant
- AI tool resolves a module action globally
- admin query reused in tenant-facing code

## Observability

Tenant-scoped operations should be traceable via:

- audit logs
- signal logs
- approval logs
- job traces
- module repair/install reports

High-risk failures should be visible as explicit tenant-boundary violations, not generic exceptions.

## Security Model

Tenant scoping is both a data-isolation and execution-isolation requirement.

It must work with:

- authentication
- permissions
- policies
- package/module enablement
- governance and approval layers

A permission grant alone must never override tenant boundaries.

## Example Flow

1. Tenant user opens a PWA screen for a module.
2. API resolves authenticated user and tenant.
3. Query applies `company_id` scope.
4. User submits an update.
5. Action writes only within the tenant-owned row set.
6. Signal emitted from the action includes tenant context.
7. Workflow/job consumers continue with the same tenant boundary.

## Future Expansion

This contract should later support:

- global scopes or scoped query helpers
- tenant-aware repository patterns
- tenant-safe AI retrieval contracts
- multi-company administrative exception rules with explicit governance
