# Titan Platform Engine Layer

## Purpose

Defines the shared runtime layer that sits above domain modules and below user-facing surfaces.
This is the layer that turns the system from a Laravel app with modules into a coordinated Titan platform.

---

## Role of the Platform Engine

The platform engine owns the system-wide contracts and runtime services that every module, AI surface,
Filament panel, PWA app, and Omni channel depends on.

It is responsible for:

- tenancy resolution
- identity and auth context
- package and module enablement
- permission and navigation registration
- API contract standardization
- signal flow and governance
- automation engine access
- PWA shell contracts
- CMS surface contracts
- Omni channel contracts
- audit and observability hooks
- AI orchestration entry points

It does not own domain-specific booking, quote, job, invoice, or campaign logic.

---

## Canonical Platform Tree

```text
app/Platform/
├─ Core/
├─ Identity/
├─ Tenancy/
├─ Permissions/
├─ Navigation/
├─ Modules/
├─ Packages/
├─ Api/
├─ Pwa/
├─ Cms/
├─ Omni/
├─ Sync/
├─ Audit/
├─ Observability/
├─ Communications/
├─ Automation/
├─ Workflows/
├─ Signals/
├─ Ai/
└─ Support/
```

---

## Layer Responsibilities

### Core
Owns registry loading, boot order, platform manifest loading, and shared runtime initialization.

### Identity
Owns user, actor, assistant, and device identity resolution.

### Tenancy
Owns company boundary enforcement and tenant-aware context building.

### Permissions
Owns shared authorization contracts and capability registration.

### Navigation
Owns sidebar, panel, and app navigation registries.

### Modules
Owns discovery, installability, enablement, compatibility checks, and manifest reading.

### Packages
Owns package/module availability and tier restrictions.

### Api
Owns response shape standards, versioning rules, and API surface registration.

### Pwa
Owns PWA shell contracts, app-surface registration, installability, and offline shell rules.

### Cms
Owns content surfaces that modules can render into.

### Omni
Owns channel compatibility and outbound/inbound message routing contracts.

### Sync
Owns offline envelopes, reconciliation, conflict handling, and device queue state.

### Audit
Owns approval records, execution traces, and review evidence.

### Observability
Owns health checks, diagnostics, metrics, and doctor-style system inspection.

### Communications
Owns channel abstractions such as email, notifications, SMS, messaging, and voice.

### Automation
Owns engine coordination, retry policies, approval queues, and orchestration runtime.

### Workflows
Owns state-machine and multi-step lifecycle definitions.

### Signals
Owns signal intake, validation, governance, approval, and replay.

### Ai
Owns Titan Zero, AEGIS, routing, memory, tool invocation, and evaluation.

---

## Provider Layer

```text
app/Providers/
├─ PlatformServiceProvider.php
├─ ModuleRegistryServiceProvider.php
├─ NavigationServiceProvider.php
├─ PermissionServiceProvider.php
├─ AutomationServiceProvider.php
├─ WorkflowServiceProvider.php
├─ SignalServiceProvider.php
├─ AiServiceProvider.php
├─ OmniServiceProvider.php
├─ PwaServiceProvider.php
├─ CmsServiceProvider.php
└─ CommunicationsServiceProvider.php
```

Providers should bind interfaces to implementations, register discovery, and expose platform contracts to modules.
Providers must not hold business logic.

---

## Platform vs Module Rule

### Platform owns
- contracts
- registries
- discovery
- orchestration
- runtime services
- shell and channel standards
- governance and audit hooks

### Module owns
- domain models
- domain actions
- domain services
- domain events/jobs/notifications
- domain API endpoints
- domain manifests

### Filament owns
- operator/admin UI only

This prevents duplication and keeps modules reusable across API, PWA, automation, and AI execution.

---

## Expanded Platform Layer

### Why the engine layer exists
A large operational system should not remain a loose Laravel app where logic is spread unpredictably across controllers, models, and views. The platform engine layer gives the stack explicit homes for shared runtime concerns.

### Recommended platform areas
Under `app/Platform/` the major engine and support areas should include:
- Core
- Identity
- Tenancy
- Permissions
- Navigation
- Modules
- Packages
- Api
- Pwa
- Cms
- Omni
- Sync
- Audit
- Observability
- Communications
- Automation
- Workflows
- Signals
- Ai
- Support

### Engine responsibilities at a glance
- **Automation** — triggers, rules, retries, dead letters, approvals
- **Workflows** — states, transitions, guards, lifecycle templates
- **Signals** — intake, validation, approval, replay, audit
- **Scheduling** — recurrence, windows, overlap control, timers
- **Communications** — channels, routing, templates, dispatch
- **Sync** — node state, deltas, acknowledgements, replay
- **AI** — reasoning, routing, governance, tools, memory
- **Observability** — health, metrics, diagnostics, doctor

### Boundary rule
The platform layer owns cross-cutting runtime infrastructure. Domain modules still own business truth and business workflows for their specific verticals. The platform should enable modules, not swallow them.

### Provider model
The engine layer should be surfaced through dedicated providers and registry services so features can be bootstrapped predictably and tested in isolation.


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
