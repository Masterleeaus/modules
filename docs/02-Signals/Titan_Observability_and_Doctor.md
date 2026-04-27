# Titan Observability and Doctor

## Purpose

Defines how modules expose health signals and how Doctor inspects, reports, and repairs system state.

---

## Observability Surfaces

Modules must expose:

- install status
- migration status
- permission status
- package membership status
- menu registration status
- route availability
- provider load confirmation
- config readiness
- tenant scoping verification

Doctor reads these surfaces without executing domain logic.

---

## Health Check Contracts

Each module may provide:

`health/checks.php`

Return structure:

- status: ok | warning | error
- checks: list
- repairs: optional auto-fix handlers

---

## Repair Model

Doctor may:

- suggest repair
- queue repair
- execute repair (admin-confirmed)

Never silently modify system state.

---

## Expanded Observability Contract

### Purpose
Observability gives operators and developers a live view of platform health. Doctor turns those signals into actionable diagnostics and repair guidance. Together they should cover module drift, route/provider failures, queue issues, manifest errors, tenancy leaks, and sync/runtime problems.

### What Doctor should inspect
- module folder shape and manifest validity
- service provider class resolution
- missing routes or route-name drift
- view namespace problems
- migration state mismatches
- permission/role seeding gaps
- package visibility drift
- AI/CMS/signal/Omni manifest integrity
- queue backlog and failed jobs
- cache/config route issues

### Suggested outputs
A doctor run should not just say pass/fail. It should include:
- check category
- status
- severity
- evidence or failing item
- recommended repair
- safe auto-fix availability when possible

### Observability areas
- application exceptions
- queue health
- scheduler health
- sync backlog
- communication delivery failures
- automation dead letters
- AI proposal/execution logs
- approval queue metrics
- module registry health summaries

### Design rules
- Store enough structured data to filter by tenant, module, engine, and severity.
- Prefer explicit checks over vague “something is wrong” messaging.
- Keep Doctor non-destructive by default, with deliberate repair paths.
- Surface health both in operator UI and in developer-facing docs/logs.

---
## What Doctor Should Inspect

A useful Doctor layer should inspect across both platform and modules:

- provider resolution
- route registration
- migration/table presence
- permission seed state
- manifest validity
- package and menu wiring
- queue health
- scheduler health
- sync backlog health
- signal approval backlog
- communication channel readiness

## Output Shape

Doctor output should be structured enough to support both UI and CLI review:

- area
- severity
- code
- message
- suspected cause
- repair hint
- evidence reference

## Severity Model

Suggested severities:

- **critical** — system cannot boot or key path is unsafe
- **warning** — system boots but a feature path is degraded
- **info** — advisory note, version drift, or optional improvement

## Rule

Doctor should report specific failures, not generic “module broken” messages. The point is to make repair actionable and bounded.


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
