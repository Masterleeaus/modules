# Module Dependency Resolution

## Purpose

Define how module dependencies are declared, resolved, validated, and enforced so boot order, install order, upgrades, and runtime expectations remain safe and predictable.

## Scope

This document covers:

- required vs optional dependencies
- install and boot ordering
- dependency failure handling
- circular dependency prevention
- capability extension patterns
- interaction with packages and runtime surfaces

## Architecture Position

Dependency resolution sits inside discovery, install, and lifecycle management.

It protects:

- provider boot order
- migration safety
- manifest interpretation
- package/surface visibility
- cross-module runtime assumptions

## Responsibilities

A dependency system must:

- determine which modules must load first
- identify optional enhancement relationships
- block invalid boot/install paths
- detect cycles
- expose missing dependency diagnostics
- keep ordering deterministic across environments

## Dependency Types

### Required dependencies
Modules that must exist and be valid before the current module can install, enable, or operate correctly.

### Optional dependencies
Modules that enrich or extend behavior when present, but are not required for base operation.

### Runtime integrations
Loose relationships where modules communicate through APIs, signals, or manifests without a hard boot dependency.

## Declaration Sources

Dependencies may be declared through:

- `module.json`
- package metadata
- manifest references
- platform-level compatibility maps

The preferred contract is a loader-readable manifest declaration, not an undocumented convention buried in provider code.

## Resolution Order

A safe default ordering model is:

- core/platform
- shared infrastructure
- domain modules
- UI/surface extensions
- optional add-ons

Where explicit priorities exist, they must still not violate required dependency order.

## Install-Time Behavior

If a required dependency is missing or invalid, installation should:

- fail clearly
- explain what is missing
- avoid partial activation
- offer repair/retry after the dependency is satisfied

## Boot-Time Behavior

At boot/runtime, the system should not assume optional dependencies exist.

Optional integrations should:

- feature-detect dependency presence
- degrade gracefully
- avoid fatal provider or route failures when the optional module is absent

## Circular Dependency Rules

Circular dependencies must be rejected.

Common unsafe patterns include:

- Module A requires B while B requires A
- UI extension module depends on domain module that also depends back on the UI extension
- two modules mutually binding providers without a stable base contract

Dependency analysis should surface cycles early.

## Package and Surface Interaction

Dependency satisfaction is separate from package entitlement.

A module may be installed and valid globally, but still not exposed to a tenant because package state disables it.

Similarly, optional dependencies may exist globally but still need tenant/package checks before cross-surface features appear.

## Failure Modes

Common dependency failures include:

- module installs before required base module
- optional integration is coded like a hard dependency
- environment-specific boot order differences
- hidden dependency on a route, policy, or manifest not declared anywhere
- cycle detected only after provider boot attempts

## Observability

Dependency resolution should surface:

- ordered module boot/install plan
- unmet dependency warnings
- cycle errors
- optional integration notes
- package/surface compatibility drift warnings

## Security Model

Dependency presence must not bypass permission, package, or tenant controls.

Resolving a dependency only means the technical prerequisite exists; it does not grant unrestricted access or exposure.

## Example Flow

1. Registry reads `module.json`.
2. Dependency graph is built.
3. Required modules are validated first.
4. Optional integrations are noted but not forced.
5. Install/boot order is computed deterministically.
6. Runtime surfaces only expose cross-module features if package and permission rules also allow them.

## Future Expansion

This contract should later support:

- capability-based dependency declarations
- semantic version constraints
- graph visualization
- automated install ordering
- richer optional integration negotiation
