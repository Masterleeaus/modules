# Titan Sync and Offline Runtime

## Purpose

Defines the canonical sync and offline runtime for Titan's device-first architecture.
This layer allows private/client nodes, server nodes, and external services to coordinate safely
without assuming constant connectivity.

---

## Canonical Sync Tree

```text
app/Platform/Sync/
├─ Contracts/
├─ Envelopes/
├─ ConflictResolution/
├─ DeviceState/
├─ Queues/
├─ Replay/
├─ Reconciliation/
├─ Policies/
└─ Support/
```

---

## What the Sync Runtime Owns

- offline action envelopes
- queued local mutations
- inbound and outbound sync queues
- reconciliation rules
- conflict resolution
- replay support
- device state awareness
- retry policy for sync attempts
- partial-sync safety
- eventual consistency discipline

---

## Node Model

- Private / Client Node
- Server Node
- External Frontier Model / Service Node

The sync runtime governs how state and signals move between them.

---

## Expanded Runtime Design

The sync and offline runtime should treat devices and browser sessions as nodes with identity, capability, cache state, local queues, and replay behavior.

### Node model
A node should carry:
- tenant/user identity context
- device/app capabilities
- sync cursor/checkpoint
- local cache/store
- pending mutation queue
- attachment upload queue
- optional local intelligence/runtime features

### Local storage doctrine
Nodes should not mirror the whole database. They should store only what the role-specific shell needs, such as:
- active assignments
- recent jobs/sites/customers
- current conversation/task state
- drafts and pending changes
- local settings/preferences

### Sync directions
**Pull** should fetch:
- deltas
- assignment changes
- manifest/package changes
- updated messages and approvals

**Push** should send:
- pending mutations
- status updates
- attachments
- local drafts
- telemetry/audit signals where enabled

### Conflict handling
Server responses should include:
- accepted mutations
- rejected mutations with reasons
- conflict markers
- refreshed records
- next sync cursor

### Design rules
- use idempotency keys for queued mutations
- preserve offline-created records until acknowledged
- show stale-state warnings where freshness matters
- keep field-critical flows resilient under poor connectivity

---
## Acknowledgement Model

Every pushed mutation should eventually end in one of these states:

- acknowledged and applied
- rejected with reason
- conflicted and awaiting operator decision
- still pending retry

Anything outside these states becomes invisible drift.

## Sync Envelope Expectations

A good sync envelope should include:

- idempotency key
- actor and tenant scope
- mutation type
- payload
- client timestamp
- local record reference
- dependency references when relevant
- retry metadata

## Operational Rules

- do not discard local pending changes before acknowledgement
- prefer append-only outbound queues over hidden mutation rewrites
- separate attachment transfer state from business record mutation state
- keep replay possible for bounded periods so failed sync windows can recover


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
