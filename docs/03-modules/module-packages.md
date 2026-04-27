# Module Packages

## Purpose

Define how a module participates in package entitlements, package-driven activation, and tenant-specific feature availability inside the Titan platform.

## Scope

This document covers:

- package-aware module enablement
- Add Modules To Packages integration
- tenant entitlement checks
- package defaults versus tenant overrides
- runtime feature exposure based on package state

This document does not define:
- low-level billing logic
- pricing strategy
- sidebar permission names
- route design

## Architecture Position

Package integration sits between platform-level availability and tenant runtime use.

It connects:

- module discovery and installation
- module settings
- tenant scoping
- UI/API/PWA exposure
- automation and AI tool availability

A module can exist in code and be installed globally, but still remain unavailable to a tenant until a package entitles that company to use it.

## Responsibilities

A module package layer must:

- integrate with package assignment flows
- expose package-readable metadata
- support module enable/disable checks per company
- allow package defaults to seed or constrain settings
- prevent surfaces from exposing unentitled features
- remain consistent across web, API, PWA, Omni, and AI channels

## Core Principle

A package is the entitlement layer.

The package decides whether a tenant can access a module, while module settings refine how the module behaves once entitled.

## Required Integration Points

A Titan-ready module should support:

- appearance in the Add Modules To Packages screen
- package-linked `module_settings`
- package-aware activation logic
- runtime checks for entitled features
- safe handling when the package is downgraded or removed

## Minimum Data Concepts

The module/package relationship should be able to answer:

- is this module included in the tenant's package?
- is it enabled or disabled for this company?
- are only certain features exposed?
- should PWA/API/Omni/AI surfaces be visible?

Common fields and concepts include:

- `module_name`
- `company_id`
- `status`
- package/module mapping rows
- optional feature toggles

## Activation Flow

A typical package-driven module flow looks like this:

1. Module is installed in the system.
2. Package configuration includes the module.
3. Tenant receives or changes package.
4. Module settings are seeded or updated for that company.
5. Runtime surfaces check package entitlement before exposing features.

## Package Defaults vs Tenant Overrides

Package defaults should define the baseline access level.

Tenant overrides may refine behavior, but they must not exceed package entitlement.

For example:

- package may allow API but not Omni
- tenant may disable API even if allowed
- tenant may not enable Omni if the package does not include it

## Runtime Exposure Rules

Every runtime surface should be able to test package entitlement before exposing the module.

That includes:

- sidebar/menu visibility
- dashboard widgets
- API routes or responses
- PWA app sections
- CMS surfaces
- Omni channels
- Titan Zero tools and actions

Package gating is therefore a runtime concern, not just an installation concern.

## Upgrade, Downgrade, and Removal

When a tenant's package changes, the module must support safe transitions.

### Upgrade
May unlock additional surfaces, limits, or features.

### Downgrade
May hide or disable features while preserving tenant data.

### Removal
Should prevent new activity while defining a safe policy for existing data retention and access.

These transitions should be auditable and reversible where practical.

## Interaction with Permissions

Permissions and package entitlements are different layers.

A user may have a permission like `view_module`, but if the package does not entitle the company to the module, the module should still remain unavailable.

## Interaction with Tenant Scoping

Package checks do not replace tenant scoping.

The correct order is typically:

1. resolve tenant
2. verify package entitlement
3. verify module settings
4. verify user permission/policy
5. execute module behavior

## AI, Automation, and Workflow Compatibility

Package gating must also apply to non-UI execution.

Automation, workflows, and AI tools must not invoke module actions for a tenant that is not entitled to the module or feature.

This is especially important for:

- scheduled automations
- Titan Zero proposals
- Omni-triggered actions
- background jobs resumed after package changes

## Failure Modes

Common package-integration failures include:

- module visible in sidebar but not entitled
- tenant package changed but settings not updated
- API endpoint active for an unentitled tenant
- AI tool still callable after downgrade
- automation fires for a module removed from package
- package check done in UI but not in background execution

## Observability

Package/module state changes should be visible through:

- package assignment logs
- module installation/repair logs
- tenant settings audits
- signal/governance logs for blocked actions
- admin diagnostics

## Security Model

Package integration is an entitlement and commercial-boundary layer. It must be enforced consistently and server-side.

Clients, panels, PWAs, and AI tools must never be treated as the source of truth for whether a tenant can use a module.

## Example Flow

1. Super admin adds a module to a package.
2. A company is assigned that package.
3. Module settings seed enabled defaults for that tenant.
4. Sidebar, API, and PWA surfaces resolve package state before exposing module features.
5. Tenant downgrade hides advanced features but preserves existing data.
6. AI and automation checks continue to respect updated entitlement state.

## Future Expansion

This contract should later support:

- per-feature package matrices
- usage limits and quotas
- package-driven AI tool scopes
- package-aware channel entitlement
- staged rollout or beta flags by package tier
