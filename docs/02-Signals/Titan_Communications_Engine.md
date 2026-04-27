# Titan Communications Engine

## Purpose

Defines the shared communications runtime for Titan.
This engine standardizes how email, notifications, SMS, messaging channels, voice,
and future delivery methods are composed, routed, queued, audited, and retried.

It exists so modules do not each invent their own channel stack.

---

## Canonical Communications Tree

```text
app/Platform/Communications/
├─ Mail/
├─ Notifications/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Email/
├─ Voice/
├─ Push/
├─ Templates/
├─ Routing/
└─ Support/
```

---

## What the Communications Engine Owns

- outbound message composition
- channel abstraction
- template management
- formatting per channel
- dispatch routing
- queue-backed sending
- retry and failure handling
- channel failover
- delivery audit logging
- correlation with workflows, signals, and automation runs

---

## Relationship to Modules

Modules define communication intents through:

- actions
- events
- notifications
- mailables
- manifests where needed

The communications engine consumes those intents and provides a standardized delivery path.

---

## Omni Relationship

Communications and Omni overlap, but are not the same.

### Communications Engine
Owns delivery infrastructure and channel operations.

### Omni
Owns unified conversational routing, inbound/outbound normalization, and channel-aware interaction surfaces.

---

## Expanded Engine Design

### Purpose
The communications engine is the platform layer that normalizes inbound and outbound communication across email, SMS, WhatsApp, Messenger, Telegram, voice, push, and future channels. It should own delivery contracts, thread normalization, template rendering, routing, and queue-backed dispatch.

### Recommended structure
```text
app/Platform/Communications/
├─ Mail/
├─ Notifications/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Email/
├─ Voice/
├─ Push/
├─ Templates/
├─ Routing/
└─ Support/
```

### Core responsibilities
- normalize channel messages into one thread or conversation model
- render outbound templates and variants
- route messages to the right provider and channel
- queue sends and retries
- record delivery status and failures
- support approvals for sensitive outbound messages
- emit communication events for automation and analytics

### Inbound flow
1. provider webhook receives message or event
2. payload is normalized into a common envelope
3. tenant, thread, and participant resolution is applied
4. the message is stored and relevant events are emitted
5. AI or operator surfaces may summarize, triage, or draft responses

### Outbound flow
1. user, automation, or AI creates a draft or send request
2. permissions and approval policy are checked
3. channel routing selects the provider and format
4. the message is queued for dispatch
5. delivery and provider callbacks update state and audit logs

### Data model expectations
The communications layer should preserve:
- channel
- provider message IDs
- participants
- normalized thread/conversation ID
- tenant scope
- message direction
- content/body/attachments
- delivery state
- approval state when relevant

### Design rules
- The channel abstraction should not erase provider-specific metadata needed for debugging.
- Keep thread identity stable across UI shells.
- Queue all non-trivial delivery work.
- Allow AI to draft, summarize, and classify, but avoid silent sending of sensitive content without policy support.


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
