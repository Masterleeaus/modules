# Titan Module Manifest and Extension Schema

## Purpose

Defines the canonical schema expectations for `module.json` and extension-style manifests
used by Titan-compatible modules.

---

## Primary Manifest

Every Titan-compatible module must include `module.json`

Recommended location:

`Modules/<ModuleName>/module.json`

---

## Required Fields

At minimum:

- name
- alias
- providers

Strongly recommended:

- description
- keywords
- priority
- version
- files

---

## Expanded Schema Definition

The module manifest is the loader contract that allows the platform to discover, boot, validate, and reason about a module. Extension metadata should be explicit enough for registry, doctor, AI, CMS, package, and route systems to consume it consistently.

### Core `module.json` fields
Required:
- `name`
- `alias`
- `providers`

Recommended:
- `version`
- `description`
- `author`
- `priority`
- `requires`
- `permissions`
- `ai_tools`
- `cms_surfaces`
- `api_routes`
- `lifecycle_support`
- `omni_support`

### Example
```json
{
  "name": "PromotionManagement",
  "alias": "promotionmanagement",
  "version": "1.0.0",
  "description": "Promotion and offer management module",
  "providers": [
    "Modules\PromotionManagement\Providers\PromotionManagementServiceProvider"
  ],
  "requires": ["Cms", "Packages"],
  "permissions": ["promotions.view", "promotions.create"]
}
```

### Extension metadata goals
A good manifest should let the platform answer:
- how do I boot this module?
- what routes/resources/manifests does it expose?
- what dependencies does it have?
- what permissions does it require?
- which packages, surfaces, and AI systems can interact with it?

### Validation rules
- aliases should be stable and lowercase-safe
- provider classes must resolve
- dependency declarations must point to known modules or platform features
- optional manifest references should resolve to real files when declared

### Relationship to extension systems
Where the stack also uses extension loading, keep naming and provider contracts aligned so doctor checks can validate both module and extension trees without special cases.

---
## Registry Expectations

The loader and registry layers should be able to answer from the manifest:

- what the module is called
- how it is booted
- whether it is compatible with the current stack
- which surfaces it exposes
- which permissions and manifests it expects
- which other modules or platform areas it depends on

## Alias and Naming Rules

- keep aliases lowercase and stable
- do not rename module aliases casually after production rollout
- keep folder names, provider namespaces, and alias references aligned
- avoid ambiguous generic names that collide with legacy packages

## Doctor Validation Targets

Doctor-style checks should validate at least:

- `module.json` exists and parses
- provider classes resolve
- alias is unique
- required route/provider/view paths exist
- declared manifest files exist where referenced
- dependency declarations are satisfiable

## Change Management

Manifest changes should be treated like API changes. If they affect boot behavior, dependencies, or exposed surfaces, they should be tracked in changelog and version metadata rather than changed silently.


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
