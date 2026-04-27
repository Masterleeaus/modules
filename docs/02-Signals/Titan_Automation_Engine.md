# Titan Automation Engine

## Purpose

Defines the automation runtime that coordinates triggers, workflows, queues, approvals,
retries, and deferred execution across the Titan platform.

This is the layer that turns modules into active engines rather than passive CRUD systems.

---

## Canonical Automation Tree

```text
app/Platform/Automation/
├─ Engines/
│  ├─ LifecycleEngine/
│  ├─ ReminderEngine/
│  ├─ EscalationEngine/
│  ├─ DispatchEngine/
│  ├─ FollowUpEngine/
│  ├─ RebookingEngine/
│  ├─ RecoveryEngine/
│  ├─ CampaignEngine/
│  ├─ BillingChaseEngine/
│  └─ ComplianceEngine/
├─ Coordinators/
├─ Triggers/
├─ Rules/
├─ Conditions/
├─ Pipelines/
├─ Executors/
├─ RuntimeState/
├─ Retries/
├─ Idempotency/
├─ DeadLetters/
├─ Outbox/
├─ Inboxes/
├─ Approvals/
├─ Audit/
└─ Support/
```

---

## What the Automation Engine Owns

- trigger evaluation
- rule execution
- condition checks
- workflow handoff
- approvals and safe modes
- retries and deduplication
- delayed execution
- timed reminders
- outbox dispatch
- dead-letter handling
- recovery and replay
- runtime metrics and audit logging

It coordinates domain actions rather than replacing them.

---

## Relationship to Domain Modules

Modules expose:

- actions
- services
- events
- jobs
- notifications
- manifests
- signal endpoints

The automation engine consumes those capabilities.

Example flow:

1. `booking.confirmed` signal emitted
2. LifecycleEngine maps signal to automation policy
3. Rules + Conditions validate eligibility
4. Approval mode checked
5. ReminderEngine schedules customer reminder job
6. FollowUpEngine schedules follow-up ladder
7. Audit layer records orchestration trace

---

## Approval Modes

### Suggest
Generates a proposed action only.

### Queue For Approval
Creates a pending action that must be approved.

### Auto Execute
Runs immediately if all guards pass.

This supports Titan Academy style review loops and safe automation growth.

---

## Reliability Rules

Every automation run should be:

- idempotent
- auditable
- replayable
- tenant-scoped
- safe on retries
- channel-aware
- module-compatible

---

## Expanded Engine Design

### Role of the automation engine
The automation engine is the runtime that evaluates triggers, applies rules, coordinates pipelines, handles retries, and moves approved work toward execution. It should exist under `app/Platform/Automation/` and cooperate with workflows, signals, scheduling, and communications rather than duplicating them.

### Recommended structure
```text
app/Platform/Automation/
├─ Engines/
├─ Coordinators/
├─ Triggers/
├─ Rules/
├─ Conditions/
├─ Pipelines/
├─ Executors/
├─ RuntimeState/
├─ Retries/
├─ Idempotency/
├─ DeadLetters/
├─ Outbox/
├─ Inboxes/
├─ Approvals/
├─ Audit/
└─ Support/
```

### Core responsibilities
The automation layer should own:
- trigger evaluation
- condition matching
- pipeline orchestration
- rule execution ordering
- retry policy
- dead-letter capture
- deduplication and idempotency
- approval queueing for sensitive actions
- timed reminders and campaign progression
- recovery and replay support

### Relationship to workflows
Workflows define business states and legal transitions. Automation decides when to progress or react based on rules and events. A useful split is:
- **workflow** = what the lifecycle is allowed to do
- **automation** = when the system should attempt to do it

### Trigger sources
Automation should accept triggers from:
- events and signals
- scheduled timers
- queue callbacks
- inbound channel activity
- API requests
- node sync updates
- manual operator triggers

### Reliability requirements
A production-grade automation engine needs:
- idempotency keys for mutation requests
- retry policy with jitter/backoff
- dead-letter storage for repeated failures
- audit records for every attempt
- pause/resume controls
- approval holding states for risky actions

### Example automation flow
1. event or schedule creates a trigger envelope
2. trigger is validated for tenant scope and eligibility
3. rules and conditions are evaluated
4. the engine selects the correct pipeline or action
5. if sensitive, the job is queued for approval
6. if approved, the executor invokes the target service/action
7. success or failure is written to audit and observability surfaces
8. optional follow-up signals, notifications, or next timers are emitted

### Design rules
- Never bury business logic only inside automation callbacks.
- Prefer actions and services as the source of truth, with automation acting as a caller.
- Queue all long-running or channel-delivery work.
- Make automation state inspectable from admin/operator surfaces.
- Store enough data to replay a failed path without guessing what happened.


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
