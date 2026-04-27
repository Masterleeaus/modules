# 19. Module Registry, Packages, and Capability Manifests

## Purpose

This document defines how Worksuite and Titan should treat modules as first-class platform capabilities instead of passive code folders. A module is not considered real merely because it exists under `Modules/`; it becomes active only when the platform can discover it, describe it, scope it, package it, expose it, govern it, and audit it.

This layer sits between the module blueprint and the runtime engines. It makes packages, sidebars, PWAs, APIs, Titan Zero, and Filament all agree on what a module is and what it is allowed to do.

---

## Core doctrine

The Titan-ready checklist requires modules to provide valid structure, package integration via `module_settings`, tenant-safe scoping, API routes, and optional manifests such as `ai_tools.json`, `cms_manifest.json`, `omni_manifest.json`, `lifecycle_manifest.json`, and `signals_manifest.json`. The system therefore needs a registry layer that can ingest these declarations and convert them into usable runtime capability records. The checklist also makes `company_id` the tenant boundary and expects modules to appear in packages, sidebars, and install flows consistently. fileciteturn15file4

The module/plugin blueprint sharpens this further: the module owns migrations, entities, policies, actions, services, events, jobs, notifications, API routes, tenant scoping, package settings, and Titan manifests, while Filament is only the operator surface that consumes those capabilities. This means the registry must be module-first, not panel-first. fileciteturn15file0turn15file1

The full system blueprint supports a dedicated platform layer for Modules, Packages, Navigation, Permissions, PWA, CMS, Omni, Signals, and AI, with providers such as `ModuleRegistryServiceProvider`, `NavigationServiceProvider`, and `PermissionServiceProvider`. This gives the right home for a central registry. fileciteturn15file5

---

## What the registry must solve

Without a registry layer, module systems drift in predictable ways:

- a module exists on disk but is invisible in super admin packages
- a module has routes but no sidebar presence
- a module is in a package but not visible to users because `module_settings` lag behind
- Titan Zero cannot safely invoke module actions because no manifest contract exists
- PWAs cannot know what surfaces or APIs a module exposes
- Studio and Worksuite cannot reason about ownership boundaries per module

The registry is therefore the source of truth for:

- discovery
- identity
- capability description
- package eligibility
- tenant enablement
- role visibility
- API and manifest exposure
- audit and compatibility state

---

## Registry layers

### 1. Filesystem discovery

The first layer discovers modules from `Modules/<ModuleName>/module.json`. The basic loader contract requires valid `name`, `alias`, and provider declarations. The checklist already frames `module.json` as the core loader contract, so the registry should treat it as the mandatory seed document, not the full truth. fileciteturn15file4

### 2. Database registry

The second layer persists normalized records in the platform database. This is the part that powers:

- super admin package screens
- navigation eligibility
- install health checks
- tenant assignment
- version/upgrade tracking
- module doctor and platform audits

### 3. Capability manifests

The third layer reads optional manifests to understand what a module can do:

- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`

These are not decorative. They are how Titan Zero, PWAs, API gateways, Omni channels, and CMS surfaces discover module capability without reading arbitrary code. fileciteturn15file4turn15file0

### 4. Tenant enablement state

The fourth layer tracks how a module is enabled per tenant, package, and role. This is where `module_settings`, `company_id`, `user_id`, permissions, and package inclusion converge. The checklist makes this mandatory if a module is to appear in package screens and user accounts. fileciteturn15file4

---

## Canonical registry data model

A mature registry record should capture at least:

- module name
- alias/slug
- display name
- provider class
- module path
- version installed
- version available
- discovery state
- enabled state
- package-visible state
- assignable-to-plan state
- migration state
- menu/sidebar state
- tenant scoping support
- API support
- PWA surface support
- Titan manifest support
- health status
- last scan time
- last doctor run time

This is consistent with the custom installer direction you scanned earlier, but it should remain a platform concern rather than being hard-coded into individual modules.

---

## Package model

Packages are not just commercial pricing bundles. In Titan/Worksuite they are also capability bundles.

A package should answer four questions:

1. What modules can this tenant activate?
2. Which surfaces are visible to this tenant?
3. Which capabilities are AI/PWA-available?
4. Which channels or lifecycle steps are unlocked?

That means package records should not only link to module names. They should also optionally link to:

- feature flags
- AI tool groups
- PWA app bundles
- channel permissions
- workflow templates
- automation limits
- device/node quotas

In other words, a package is the commercial projection of a registry-defined capability graph.

---

## Capability manifests

### AI tools manifest

This manifest advertises executable tools to Titan Zero. The checklist expects endpoints, tool names, and descriptions. The system should add more fields over time:

- required permissions
- tenant scope rules
- approval requirement
- risk class
- idempotency expectations
- expected result envelope

Titan Core and Titan Zero must validate every AI invocation against this manifest instead of letting modules improvise execution contracts. The constitutional docs also require capability manifest checks as part of enforcement, with out-of-scope calls rejected and logged. fileciteturn15file8turn15file9

### Signals manifest

This manifest tells the signal engine which events a module emits and consumes. The checklist already frames this as how modules participate in Titan Zero, AEGIS, Sentinel approval chains, and the automation engine. That means the registry should compile these signal declarations into a platform-wide event catalog. fileciteturn15file4

### Lifecycle manifest

This manifest explains which lifecycle stages the module participates in, such as lead, quote, booking, job, invoice, and follow-up. The registry should expose this to:

- workflow engines
- wizard builders
- dashboards
- AI planners
- package designers

### CMS manifest

This manifest tells the site/CMS/PWA shells where a module can render data. It should describe reusable surfaces like hero blocks, pricing cards, booking widgets, service sections, or customer portals.

### Omni manifest

This manifest tells the communications layer which channels a module can speak through and in what role. A scheduling module might support reminder SMS and email, while a campaign module supports WhatsApp, Messenger, Telegram, and email. Operational modules must not silently advertise marketing channels without constitutional approval.

---

## Registry provider pattern

The full system blueprint names `ModuleRegistryServiceProvider` explicitly. That provider should:

- scan module directories
- parse `module.json`
- parse optional manifests
- compare discovered metadata with persisted metadata
- update registry records idempotently
- flag drift, missing providers, broken routes, or invalid manifests
- publish a normalized registry object to the rest of the app

It should not activate modules by itself. Discovery and activation are separate concerns.

---

## Sidebar and panel visibility

Registry state should feed two different UI systems.

### Super admin

Super admin needs:

- package assignability
- install/health status
- migration status
- settings pages
- capability summaries
- audit state
- panel links

### User/tenant side

Tenant users need:

- whether the module is in their package
- whether their role is allowed
- whether the module has navigation/menu surfaces
- whether the module is relevant to the current app surface (web, PWA, field app, command center)

This is why the registry cannot simply reflect `Modules/` folders; it must mediate package, permission, and role state before any sidebar is shown.

---

## PWA and node implications

A PWA should not parse PHP to decide what a tenant can use. It should consume a bootstrap payload derived from the module registry. That payload should include:

- enabled modules for this tenant
- surface declarations
- API endpoints
- offline sync contracts
- AI tools available locally vs remotely
- permissions and role visibility
- signal subscriptions

This is how a device becomes a Titan node that understands capability scope before attempting sync or action proposals.

---

## Tenant boundary

The checklist is explicit that `company_id` is the tenant boundary and that `user_id` is also required where relevant. The registry should enforce this at declaration time:

- modules must declare whether they are tenant-scoped
- registry doctor should reject modules that expose operational APIs without tenant-safe models or policies
- manifest validation should confirm that AI tools and APIs require tenant context

A module that can be installed but not safely tenant-scoped should be treated as unhealthy.

---

## Compatibility law

A module is not platform-compatible just because it loads. Registry compatibility should require:

- valid structure and providers
- valid migrations and safe re-runs
- `modules` / registry presence
- package visibility support
- `module_settings` support
- named routes
- role permissions
- tenant-safe queries
- manifest validity
- API exposure where required
- navigation surface declarations

This converts your earlier checklists into a machine-checkable standard.

---

## Recommended implementation sequence

1. Build a normalized registry model and service provider.
2. Make package screens read from registry output, not ad hoc DB rows.
3. Compile manifest files into registry capability records.
4. Make user bootstrap and PWA bootstrap consume registry data.
5. Add a registry doctor that validates module health and compatibility.
6. Expose a read-only registry API for AI, PWA, and admin diagnostics.

---

## Final rule

The module folder is only the raw artifact.

The registry is what turns that artifact into a real Titan/Worksuite capability.
