# Titan Module Blueprint

## Purpose

Defines the canonical structure for all Titan-compatible domain modules.
Ensures modules are reusable across API, PWA, automation engines, AI orchestration, and Filament UI surfaces.

---

## Canonical Module Tree

```text
Modules/<ModuleName>/
├─ module.json
├─ version.txt
├─ README.md
├─ CHANGELOG.md
├─ Config/
├─ Providers/
├─ Routes/
├─ Database/
├─ Http/
├─ Models/
├─ Entities/
├─ Policies/
├─ Actions/
├─ Services/
├─ Data/
├─ Events/
├─ Listeners/
├─ Observers/
├─ Jobs/
├─ Notifications/
├─ Mail/
├─ Exports/
├─ Imports/
├─ Support/
├─ Traits/
├─ Scopes/
├─ Console/
├─ Tests/
├─ manifests/
└─ Filament/
```

---

## Module Owns

Domain modules must own:

- migrations and seeders
- entities/models
- validation rules
- services
- business actions
- events and listeners
- jobs and notifications
- API resources
- tenant scoping
- Titan manifests

Filament must never be the source of business logic.

---

## Filament Owns

Filament handles operator interface layers only:

- Resources
- Pages
- Widgets
- Tables
- Forms
- Infolists
- Dashboards
- Navigation clusters

Filament consumes module logic instead of defining it.

---

## Action Pattern Rule

All write operations must route through Actions.

Example: `CreateBookingAction`

Called by:

- controllers
- API endpoints
- Filament resources
- import jobs
- automation engines

Never duplicate CRUD logic across surfaces.

---

## Manifest Requirements

Each module should expose:

- ai_tools.json
- signals_manifest.json
- lifecycle_manifest.json
- cms_manifest.json
- omni_manifest.json

These allow Titan Zero orchestration compatibility.

---

## Provider Responsibilities

Service providers must:

- register bindings
- load routes
- load migrations
- load translations
- load views

They must not contain domain logic.

---

## Validation Rule

Validation belongs inside FormRequest classes.

Never embed validation inside:

- controllers
- widgets
- Filament closures
- table actions

Centralized validation ensures API/UI parity.

---

## Events Rule

Events dispatch side-effects:

Example: `BookingCreated` → `ReminderJob`

Ensures domain logic stays deterministic and extensible.

---

## Queue Rule

Deferred work belongs in Jobs:

- reminders
- followups
- sync tasks
- external calls

Never execute these inline inside controllers.

---

## Titan Compatibility Goal

Modules must function across:

- REST API
- automation engines
- signal pipeline
- AI orchestration
- CLI commands
- background queues
- Filament panels

---

## Expanded Module Pattern

### Gold-standard structure
A serious domain module should be able to serve web, API, automation, AI, import/export, and PWA surfaces from one coherent codebase.

```text
Modules/<ModuleName>/
├─ module.json
├─ version.txt
├─ README.md
├─ CHANGELOG.md
├─ Config/
├─ Providers/
├─ Routes/
├─ Database/
├─ Http/
├─ Entities/
├─ Policies/
├─ Actions/
├─ Services/
├─ Data/
├─ Events/
├─ Listeners/
├─ Observers/
├─ Jobs/
├─ Notifications/
├─ Mail/
├─ Exports/
├─ Imports/
├─ Support/
├─ Traits/
├─ Scopes/
├─ Console/
├─ Tests/
├─ manifests/
└─ Filament/
```

### Ownership split
The module should own anything that must work across multiple execution surfaces:
- migrations and seeders
- entities/models
- requests and validation rules
- policies and permissions
- actions and services
- events, listeners, observers
- jobs, notifications, mail
- import/export logic
- APIs and resources
- tenant scoping
- manifests for AI, signals, CMS, lifecycle, and Omni

### Filament is a consumer, not the owner
Filament should provide admin/operator UI only:
- resources
- pages
- widgets
- tables
- forms
- relation managers
- dashboards
- approval screens

Business rules should live in actions/services so controller, API, job, AI, import, and Filament paths all call the same underlying logic.

### No-double-up rules
Avoid divergent implementations such as:
- web controller creates one way
- API controller creates another way
- Filament callback creates a third way

Preferred pattern:
- `CreateThingAction` is the source of truth
- all callers delegate to that action

### Module manifests
A mature module should be able to expose:
- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`

### Testing requirements
At minimum, add:
- feature tests for routes and permissions
- unit tests for services and actions
- integration tests for install/load behavior
- tenancy tests for `company_id` scoping
- manifest and registry validation tests


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
