# Titan Signals Engine

## Purpose

Defines the canonical signal runtime for Titan.
Signals are the transport and governance backbone between modules, automation engines,
AI orchestration, approvals, and external surfaces.

The signals engine turns raw events into controlled, replayable, auditable system behavior.

---

## Canonical Signals Tree

```text
app/Platform/Signals/
├─ Intake/
├─ Contracts/
├─ DTOs/
├─ Envelopes/
├─ Emitters/
├─ Validators/
├─ Governance/
├─ Approval/
├─ Dispatch/
├─ Replay/
├─ Audit/
├─ Logs/
└─ Support/
```

---

## What a Signal Is

A signal is a structured envelope that describes:

- what happened
- where it came from
- which tenant it belongs to
- which module or runtime emitted it
- the current governance state
- what downstream systems may do with it

Signals are system-grade events intended for orchestration and controlled execution.

---

## Core Responsibilities

The signals engine owns:

- signal intake
- normalization into canonical envelopes
- schema validation
- tenant and policy validation
- governance checks
- approval routing
- dispatch to engines or handlers
- replay and recovery
- audit trace creation
- long-term operational logs

---

## Signal Lifecycle

1. Intake
2. Validation
3. Governance
4. Approval
5. Dispatch
6. Audit
7. Replay

---

## Module Contract

Modules should expose their signal participation through `signals_manifest.json` describing:

- emitted signals
- accepted signals
- handlers or target actions
- governance requirements
- approval requirements
- replay behavior

---

## Governance Rule

No signal should move directly from emission to execution without validation.

At minimum, check:

- tenant boundary
- schema validity
- source integrity
- target availability
- policy constraints
- idempotency / duplication risk

---

## Expanded Engine Design

### Purpose
The signals engine is the governed interchange layer between modules, automation, AI, approvals, and runtime execution. Signals should carry structured intent and state changes through validation and approval paths rather than allowing hidden side effects.

### Recommended structure
```text
app/Platform/Signals/
├─ Intake/
├─ Contracts/
├─ DTOs/
├─ Envelopes/
├─ Emitters/
├─ Validators/
├─ Governance/
├─ Approval/
├─ Dispatch/
├─ Replay/
├─ Audit/
├─ Logs/
└─ Support/
```

### Typical signal lifecycle
1. a module, node, channel, or AI proposal emits a signal envelope
2. intake validates schema and tenant scope
3. governance evaluates permissions, policy, and risk
4. approval may hold the signal when necessary
5. approved signals are dispatched to target handlers or workflows
6. audit and replay records are stored

### Signal envelope principles
A signal should be:
- explicit about tenant and actor context
- typed and versioned
- small enough to audit and replay
- connected to source references and target intents
- suitable for idempotent handling when retried

### Why signals matter
Signals make it easier to:
- coordinate AI proposals safely
- replay or recover failed orchestration
- audit what happened and why
- decouple domain modules from orchestration layers

### Design rules
- do not use signals as an excuse for vague payloads
- keep business truth in modules; signals move intent and events between layers
- integrate signal manifests with doctor and observability tooling


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
