# Module Manifests

## Purpose

Define the machine-readable contract layer for a Titan-ready module so the platform, packages, CMS, PWA, Omni, automation, and Titan Zero can understand module capabilities without relying on guesses from UI code or raw schema inspection.

## Scope

This document covers:

- core loader manifest requirements
- optional Titan ecosystem manifests
- manifest ownership and placement
- validation expectations
- manifest-to-runtime mapping
- versioning and change safety

This document does not define the full install pipeline, route rules, package logic, or API controller behavior. It defines the metadata layer those systems consume.

## Architecture Position

Module manifests sit at the boundary between static module structure and runtime orchestration.

They are consumed by:

- module discovery and registry
- service-provider boot logic
- package enablement
- CMS surface rendering
- Omni channel eligibility
- signals and lifecycle engines
- Titan Zero tool/context readers

## Responsibilities

A manifest layer must let the system answer:

- what is this module?
- which provider boots it?
- which optional capabilities does it expose?
- which surfaces can consume it?
- which signals and lifecycle hooks exist?
- which tools may be invoked by AI?
- which channels may carry its data?

## Core Loader Manifest

Every module must expose a stable `module.json`.

At minimum it should identify:

- module name
- alias
- provider classes

Recommended fields include:

- priority
- version
- description
- author
- requires
- ai_tools
- cms_surfaces
- api_routes
- permissions

This manifest is the first contract the loader reads.

## Optional Titan Ecosystem Manifests

A Titan-ready module may also expose dedicated manifests for higher-order behavior.

### `signals_manifest.json`
Defines signal types the module emits or consumes.

### `lifecycle_manifest.json`
Declares lifecycle stages or participation in broader process flows.

### `cms_manifest.json`
Declares named CMS surfaces or render targets.

### `omni_manifest.json`
Declares supported channels such as WhatsApp, Messenger, Telegram, or email.

### `ai_tools.json`
Declares tool-like capabilities that Titan Zero or automation can invoke through explicit contracts.

## Manifest Placement

Manifests should live in a predictable module-local location.

Recommended path:

- `Modules/<ModuleName>/manifests/`

If the stack still supports root-level legacy placement for specific manifests, that support should be transitional rather than the long-term contract.

## Ownership Rules

A manifest must describe the module’s real behavior.

That means:

- endpoints listed in `ai_tools.json` must exist
- signals listed in `signals_manifest.json` must actually be emitted or handled
- CMS surfaces must correspond to real render targets
- Omni channels must match runtime routing capability

A manifest should never advertise capabilities the module does not really support.

## Validation Expectations

Manifests should be validated for:

- file presence where declared
- JSON readability
- required keys
- reference integrity
- route/endpoint existence where relevant
- version compatibility

Manifest validation should happen during install, upgrade, repair, and module doctor flows.

## Runtime Mapping

Manifests are not documentation only. They must map into runtime behavior.

Examples:

- loader reads `module.json` to boot providers
- package screen reads module metadata for visibility and entitlement wiring
- CMS reads `cms_manifest.json` for bindable surfaces
- signals engine reads `signals_manifest.json`
- lifecycle engine reads `lifecycle_manifest.json`
- Titan Zero reads `ai_tools.json` and related metadata

## Versioning and Change Safety

Manifest changes should be treated as compatibility-affecting changes.

Safe rules:

- additive changes are preferred
- renamed keys should include migration or fallback handling
- removed capabilities should be documented
- broken references should block or warn during install/repair

## Failure Modes

Common manifest failures include:

- `module.json` exists but provider namespace is wrong
- optional manifests exist but are malformed
- AI tools point to stale API endpoints
- lifecycle manifest declares stages unsupported by the module
- CMS surfaces are declared without renderer support
- Omni manifest channels exceed actual delivery capability

## Observability

Manifest handling should surface into:

- install logs
- upgrade logs
- module registry diagnostics
- doctor/repair output
- AI tool validation traces
- signal registration diagnostics

## Security Model

A manifest is not a permission bypass.

Even if a capability is declared in a manifest, it must still pass:

- package entitlement
- permission checks
- policy checks
- tenant fencing
- governance/approval rules where applicable

## Example Flow

1. Loader scans the module.
2. `module.json` identifies the provider.
3. Registry reads optional manifests.
4. Installation validates references and seeds visibility.
5. Runtime layers consume the relevant manifests for CMS, Omni, PWA, signals, lifecycle, and AI use.
6. Titan Zero reads the manifest envelope instead of exploring the full schema.

## Future Expansion

This contract should later support:

- manifest schemas
- semantic version compatibility checks
- typed tool inputs/outputs
- manifest-driven UI generation
- richer AI-readable entity metadata
