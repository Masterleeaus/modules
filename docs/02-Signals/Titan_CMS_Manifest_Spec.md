# Titan CMS Manifest Specification

## Purpose

Defines template registration rules for CMS templates and landing bundles.

---

## Manifest File

`cms_manifest.json`

Example fields:

- slug
- name
- category
- tenant_scope
- thumbnail
- layouts
- supports_builder

---

## Registration Flow

CMS scans `resources/cms/templates/`

Each template folder must include:

- cms_manifest.json
- thumbnail.png
- index.blade.php

---

## Expanded Surface Contract

The CMS manifest declares how a module exposes content or functional surfaces to website builders, tenant sites, portals, and PWAs. It allows modules to contribute structured render targets without hard-coding knowledge into the CMS core.

### Purpose
A CMS manifest should answer four questions:
- what surfaces does this module expose?
- where can those surfaces be placed?
- what data shape do they require?
- who is allowed to render or configure them?

### Suggested file location
`Modules/<ModuleName>/manifests/cms_manifest.json`

### Suggested fields
- `module`
- `version`
- `surfaces`
  - `key`
  - `label`
  - `type` (widget, listing, banner, form, booking_widget, card_grid, hero_block, etc.)
  - `placements` (homepage, services page, account page, portal surface, PWA shell)
  - `data_source`
  - `config_schema`
  - `cache_policy`
  - `permission`
  - `tenant_scoped`
  - `api_backing`

### Example
```json
{
  "module": "PromotionManagement",
  "version": "1.0",
  "surfaces": [
    {
      "key": "homepage_promotions",
      "label": "Homepage promotions",
      "type": "card_grid",
      "placements": ["homepage", "portal_home"],
      "data_source": "promotions.active",
      "tenant_scoped": true,
      "cache_policy": "short_ttl"
    }
  ]
}
```

### Runtime expectations
A CMS surface should be renderable from a stable contract, ideally through read models or APIs rather than direct view coupling. This keeps surfaces usable by:
- web CMS pages
- customer portal pages
- PWA components
- AI-selected UI cards

### Rules
- Keep configuration schema separate from content data.
- Make tenant scope explicit.
- Prefer named placements and component types over ad hoc strings.
- Add cache policy and invalidation hints for high-traffic surfaces.
- Validate manifests during install/doctor checks.

---
## Acceptance Checklist

A CMS surface is ready when:

- it has a stable key and label
- the placement targets are explicit
- the data source is named and tenant-safe
- configuration is typed and bounded
- rendering does not depend on hidden controller state
- cache and invalidation expectations are declared
- missing or empty data states are handled gracefully

## Surface Types to Prefer

Common surface families that should be standardized across modules:

- hero blocks
- promo cards
- listing grids
- booking widgets
- quote forms
- testimonial/review blocks
- schedule or availability panels
- account summary cards

## Boundary Rule

The CMS manifest should describe what can be rendered. It should not contain the business rules for how records are created, approved, priced, or scheduled. Keep those rules in module actions, services, workflows, and policies.


---

## Extended Integration Layer (Pass‑5 Augmentation)

### Role in Titan Stack
Defines interaction surface with Titan Zero orchestration, Nexus automation triggers, CMS manifests, Omni channel routing, and Worksuite module registry.

### Signal Contracts
Origin: Scout / Module / User Action  
Validation: SignalAI schema + tenant fence  
Governance: AEGIS policy + permission matrix  
Approval: Sentinel domain readiness

### Database Touchpoints
company_id tenant scope enforced  
manifest registry linkage required  
approved_logs audit persistence expected

### Provider Wiring Expectations
ServiceProvider must:
- register routes
- bind services
- expose config
- publish manifests
- seed permissions

### Failure States
schema drift  
manifest mismatch  
route namespace collision  
tenant boundary violation  
missing provider registration

### Agent Editing Rules
Agents MAY extend lifecycle sections  
Agents MUST NOT rename canonical tables  
Agents MUST preserve manifest schema compatibility


---

## Execution Envelope Contract (Pass‑6 Augmentation)

### Envelope Structure
manifest_id
module_slug
company_id
signal_origin
lifecycle_stage
dependency_vector
policy_scope

### Governance Enforcement Path
SignalAI → schema validation
AEGIS → permission + quota enforcement
Sentinel → readiness + dependency verification
Domain Engine → execution eligibility only

### Manifest Compatibility Rules
Extensions must declare:
- slug
- version
- providers
- routes
- permissions
- menu bindings
- registry visibility flag

### Route Surface Expectations
Routes must follow:
titan.<domain>.<action>
dashboard.user.<module>.index

No URL paths allowed inside menu registry entries.

### Tenant Boundary Guarantees
company_id enforced at:
queries
events
signals
snapshots
approved_logs

Cross‑tenant reads prohibited unless governance‑approved.

### Offline / Sync Expectations
Mesh snapshots queued locally
conflict resolution via envelope timestamps
idempotent replay required
sync retries exponential backoff

### Observability Extensions
Doctor scans must verify:
provider load state
route registration
migration presence
permission seeding
menu wiring
manifest integrity

### Automation Engine Hooks
Automation triggers attach at:
signal processed
sentinel approved
workflow advanced
state transition committed

### CMS Surface Contracts
CMS manifests must expose:
template slug
surface type
tenant visibility
package compatibility
builder support flag


---

## Canonical Signals Architecture Alignment (Pass‑7 Refocus)

### Signals Are the Primary System Transport Layer
All modules communicate through signals, not direct service execution.

Core lifecycle:
process → processing → processed → approved

Mapped to:
Scout → SignalAI → AEGIS → Sentinel

### Signal Envelope Schema (Authoritative Fields)
signal_id
company_id
origin_module
origin_action
payload_schema_version
dependency_signals[]
policy_scope
lifecycle_stage
timestamp_vector

### Signal Validation Responsibilities

Scout:
creates envelope only

SignalAI:
schema validation
tenant fence enforcement
idempotency checks

AEGIS:
permissions
financial consistency
cross‑domain policy validation

Sentinel:
domain readiness
resource availability
execution eligibility

### Domain Engines NEVER Execute Without Approved Signals
Execution engines must consume only:
approved signals

Rejected signals must persist reason codes.

### Required Signal Log Tables

signal_log
aegis_log
sentinel_log
approved_logs

Each stage appends structured validation output.

### Cross‑Module Communication Rule

Modules MUST NOT:
call each other directly

Modules MUST:
emit signals instead

### Automation Trigger Points

Allowed attach states:

processing
processed
approved

Never attach triggers to raw process state.

### Offline Replay Contract

Signals replayable deterministically
timestamps monotonic
conflicts resolved by envelope precedence
duplicate execution prevented via signal_id

### CMS / Omni / Workflow Relationship to Signals

CMS:
publishes surface signals

Omni:
routes communication signals

Workflow:
chains dependency signals

Scheduling:
generates temporal signals

Automation:
subscribes to lifecycle signals


---

## Signals Governance Matrix (Pass-8 Deepening)

### Lifecycle State Authority Table

process
Created by Scout only

processing
Validated by SignalAI
Schema integrity enforced
Tenant boundary verified
Idempotency lock applied

processed
Governed by AEGIS
Permission matrix evaluated
Quota enforcement applied
Financial-domain consistency checked
Cross-module dependencies confirmed

approved
Authorized by Sentinel
Execution readiness confirmed
Resource availability verified
Scheduling conflicts resolved
Domain invariants satisfied

### Rejection Reason Code Categories

SCHEMA_INVALID
TENANT_MISMATCH
PERMISSION_DENIED
DEPENDENCY_MISSING
RESOURCE_UNAVAILABLE
POLICY_SCOPE_VIOLATION
QUOTA_EXCEEDED
STATE_CONFLICT
DUPLICATE_SIGNAL

All rejection reasons must persist to signal_log.

### Deterministic Replay Rules

Signals must support replay when:

offline queue flush occurs
device reconnect event detected
mesh snapshot restored
conflict merge required

Replay ordering priority:

timestamp_vector
dependency depth
signal_id monotonic order

### Dependency Resolution Contract

Signals declaring dependency_signals[]:

must not advance lifecycle
until dependencies reach approved

Partial dependency approval prohibited

### Automation Subscription Matrix

Automation engines may subscribe to:

processing → validation automations
processed → governance automations
approved → execution automations

Automation must never mutate envelope schema

### Sentinel Domain Approval Checklist

Sentinel verifies:

resource locks
schedule windows
domain invariants
policy overlays
security scope alignment
duplicate prevention markers

Only then may signal transition to approved

### Observability Requirements

Each lifecycle transition writes:

stage timestamp
validator identity
policy snapshot reference
dependency state vector
decision outcome hash

Ensures full audit-chain reproducibility

### Cross-Engine Signal Responsibilities

AI Core:
may emit reasoning signals

Workflow Engine:
may chain dependency signals

Scheduling Engine:
may emit temporal signals

CMS:
may emit surface-render signals

Omni Channel:
may emit communication signals

Modules:
must emit action signals only
