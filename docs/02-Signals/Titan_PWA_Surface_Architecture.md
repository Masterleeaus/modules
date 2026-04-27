# Titan PWA Surface Architecture

## Purpose

Defines the canonical Progressive Web App surface model for Titan.
This layer explains how Titan should present distinct app surfaces while still sharing the same platform,
engine, module, signal, workflow, sync, and AI core beneath them.

---

## Canonical PWA Tree

```text
app/Platform/Pwa/
├─ Contracts/
├─ Shell/
├─ Navigation/
├─ Sync/
├─ Offline/
├─ Device/
├─ Surfaces/
├─ Guards/
└─ Support/
```

---

## What the PWA Layer Owns

- shell composition
- installability
- surface registration
- navigation rules
- device-aware layout behavior
- offline-aware interaction patterns
- sync-state visibility
- app-surface guards
- surface-specific entry points

---

## Canonical Surface Families

- Titan Portal
- Titan Command
- Titan Go
- Titan Money
- Titan Omni

Different surfaces expose different workflows and priorities, but all consume the same underlying contracts.

---

## Expanded Surface Model

PWAs should be treated as first-class operational shells, not as responsive leftovers of the web app. Each shell should use a shared substrate but have a role-specific composition and permission profile.

### Shared substrate
Every PWA should share:
- auth/session integration
- API client and response envelopes
- notification handling
- local cache
- pending mutation queue
- sync manager
- feature flag/package awareness
- offline and stale-state UI rules

### Main shells
- **Titan Omni** — communication and chat-first control shell
- **Titan Portal** — staff/admin access surface
- **Titan Command** — owner/manager command surface
- **Titan Go** — field worker execution surface
- **Titan Money** — invoicing, payment, and collections surface

### PWA contract with modules
Modules should expose stable APIs/read models so a PWA does not need direct coupling to Blade views or admin-only assumptions. Useful module contributions include:
- mobile-friendly endpoints
- manifest-declared surfaces
- compact read models
- sync policies
- permission-aware action routes

### UX doctrine
PWA design should favor:
- fast task completion
- role-specific navigation
- obvious online/offline state
- background sync and replay
- minimal data density where workers are in the field
- rich context where managers need approvals or decisions

### Acceptance checklist
- shell can authenticate safely
- module visibility respects package/company rules
- key user flows work on poor connectivity where promised
- pending writes survive reconnect
- notifications route into meaningful in-app destinations

---
## Surface Families

The PWA layer should support distinct surface families rather than one mixed shell:

- worker/operator shell
- owner/manager shell
- customer portal shell
- assistant/chat shell
- offline field-execution shell

Each surface may share platform runtime but should keep role-specific navigation, local caches, and action affordances.

## Runtime Contract

A PWA surface should be able to answer:

- what data is safe to cache locally
- what can be mutated offline
- what requires live confirmation
- which approval or freshness warnings apply
- which AI/tool surfaces are visible to that role

## Minimum Offline UX Rules

- drafts survive refresh or temporary offline loss
- stale data is visibly marked where it matters
- pending local mutations are inspectable
- sync conflicts are surfaced in operator language
- critical flows degrade safely rather than silently failing


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
