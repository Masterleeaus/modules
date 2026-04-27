# Module Discovery Registry

## Purpose

Define how the system discovers modules, builds a reliable registry, resolves dependencies and priorities, and exposes module metadata to the rest of the platform.

## Scope

This document covers:

- filesystem discovery
- loader manifest reading
- provider and capability indexing
- dependency and priority ordering
- registry cache behavior
- activation-state exposure
- diagnostics and invalidation

This document does not define install or upgrade logic in full, but discovery is the first stage those flows depend on.

## Architecture Position

The module registry is the control map between disk-level module presence and runtime-level module awareness.

It is consumed by:

- installers
- package screens
- menus/navigation
- health checks
- CMS/PWA/Omni surface builders
- AI and automation capability readers

## Responsibilities

A registry must:

- scan the `Modules/` tree
- identify valid modules
- read `module.json`
- resolve provider class references
- index optional manifests
- order modules by dependency and priority
- expose enabled/disabled state
- invalidate and rebuild predictably

## Discovery Process

### 1. Scan module roots
The registry scans the known module directory for candidate folders.

### 2. Detect manifest presence
A valid candidate must contain a readable loader manifest.

### 3. Validate structure hints
Basic structure checks confirm this is a real module and not a partial or malformed folder.

### 4. Read registry metadata
The registry extracts:
- name
- alias
- providers
- version
- priority
- optional declared capabilities

### 5. Index optional manifests
Where available, the registry should also note:
- AI tools
- signals
- lifecycle hooks
- CMS surfaces
- Omni channels

## Priority Resolution

Modules may need deterministic ordering.

Priority resolution should consider:

- hard dependencies first
- platform/core modules before domain modules
- explicit priority values where supported
- deterministic tie-breaking to avoid non-repeatable boot order

## Dependency Resolution

The registry should record both required and optional relationships where declared.

It must be able to:

- identify missing required dependencies
- allow optional feature extension where safe
- reject circular dependency chains
- expose unmet dependency warnings to install/doctor flows

## Registry State Model

A useful registry should expose more than “present or not present.”

Helpful states include:

- discovered
- valid
- invalid
- installed
- enabled
- disabled
- repair-needed

These states help the rest of the platform make safer decisions.

## Cache and Invalidation

Registry rebuilding can be expensive and should usually be cached.

The cache must invalidate when:

- a module is added or removed
- `module.json` changes
- provider references change
- manifests affecting capabilities change
- repair or upgrade flows request a rebuild

## Activation-State Exposure

The registry should be able to answer questions such as:

- is this module known to the system?
- is it bootable?
- is it installed?
- is it enabled for the current tenant/package context?
- what optional capabilities does it expose?

This makes the registry useful to runtime surfaces, not just installers.

## Failure Modes

Common discovery/registry failures include:

- folder exists but no readable manifest
- manifest exists but provider namespace is wrong
- registry cache is stale after module changes
- dependency ordering differs between environments
- optional capability manifests are ignored
- invalid modules still appear as activatable

## Observability

Registry operations should surface in:

- discovery logs
- cache rebuild logs
- install diagnostics
- doctor output
- admin/operator debug tooling

## Security Model

The registry should not be treated as an execution authority.

It reports module metadata, but all actual use must still pass through:

- permissions
- package entitlements
- tenant fencing
- policy/governance layers

## Example Flow

1. Registry scans `Modules/`.
2. It identifies valid manifests.
3. Provider and capability metadata are indexed.
4. Priorities and dependencies are resolved.
5. Registry cache is built.
6. Installers, package screens, menus, AI, and runtime surfaces read from the registry.

## Future Expansion

The registry should later support:

- structured dependency graphs
- capability querying
- health-state overlays
- manifest schema version checks
- cross-module compatibility warnings
