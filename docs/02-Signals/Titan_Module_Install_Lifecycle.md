# Titan Module Install Lifecycle

## Purpose

Defines the canonical install, enablement, upgrade, and repair lifecycle for Titan-compatible modules.

---

## Install Lifecycle Stages

1. Discovery
2. Structure validation
3. Manifest validation
4. Provider and route registration check
5. Database migration readiness
6. Permission seeding readiness
7. Package compatibility registration
8. Tenant settings registration
9. UI / API / manifest surface registration
10. Activation
11. Verification
12. Upgrade / repair / rollback discipline

Each stage should be explicit and inspectable.

---

## Failure Classes to Detect

- missing module.json
- broken provider reference
- missing route file
- route collision
- duplicate alias
- unsafe migration
- missing permission seeder
- package registration drift
- sidebar/menu registration drift
- missing API/controller surface
- broken manifest
- wrong tenant column strategy
- broken Filament/plugin registration

---

## Expanded Install Lifecycle

A module install path should be treated as an auditable lifecycle, not as a one-click guess. The install flow must work for fresh installs, upgrades, re-runs, and partial recovery.

### Recommended lifecycle stages
1. **preflight scan**
   - validate folder name and nwidart structure
   - validate `module.json`
   - validate service provider references
   - confirm route files, views, migrations, translations exist where expected
2. **registry registration**
   - register module in the modules table or registry service
   - set initial installed/enabled/version state
3. **database setup**
   - run idempotent migrations
   - seed permissions
   - seed module settings for existing tenants/companies where required
4. **surface registration**
   - sidebar/menu wiring
   - package visibility wiring
   - route cache clear and config/view refresh
5. **manifest validation**
   - AI tools
   - CMS surfaces
   - signals/lifecycle/Omni manifests if present
6. **post-install checks**
   - routes resolve
   - provider boots cleanly
   - permissions exist
   - tenant-scoped data access works
7. **upgrade path**
   - version bump
   - schema/data migrations
   - new permission/module-setting backfills

### Why idempotency matters
Install code must be safe on re-run. A repeated install or repair pass should not duplicate tables, duplicate menus, or corrupt settings. Prefer `Schema::hasTable`, guarded column adds, and repeat-safe seeders.

### Recovery behavior
If install fails mid-way, Doctor/repair tooling should be able to tell the operator what failed:
- invalid manifest
- migration failure
- permission mismatch
- route/provider mismatch
- package wiring failure

### Acceptance checklist
- appears in module list
- installs without tenant logout/theme breakage
- routes resolve
- permissions seeded
- package visibility works
- menu/sidebar entry appears where intended
- manifests validate
- upgrade/reinstall path is safe

---
## Install / Repair Invariants

The install pipeline should preserve these invariants:

- repeated runs do not duplicate structures
- install state is derivable from registry + schema + provider health
- each failed stage reports a specific recovery target
- tenant backfills are safe for existing companies
- route and permission refresh happens after structural changes

## Recommended Repair Modes

### Soft repair
Use when manifests, menu wiring, package visibility, or provider references drift but schema is intact.

### Schema repair
Use when migrations or required columns/tables are missing.

### Registry repair
Use when the module exists on disk but registry tables or enablement metadata drifted.

### Surface repair
Use when the module boots but does not appear correctly in sidebar, package surfaces, API discovery, or manifests.

## Upgrade Discipline

A module upgrade should include:

- version bump
- migration/backfill plan
- permission delta plan
- module settings delta plan
- route/provider impact check
- manifest compatibility review

Silent upgrades that only change code without a tracked install delta are likely to create drift later.


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
