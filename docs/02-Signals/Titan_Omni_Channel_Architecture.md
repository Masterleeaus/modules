# Titan Omni Channel Architecture

## Purpose

Defines the omnichannel interaction architecture for Titan.
Omni is the layer that unifies inbound and outbound conversational interaction across messaging, email,
voice, and future channels while keeping the underlying platform governed, auditable, and reusable.

---

## Canonical Omni Tree

```text
app/Platform/Omni/
├─ Contracts/
├─ Channels/
├─ Routing/
├─ Templates/
├─ Inbound/
├─ Outbound/
├─ Sessions/
├─ Normalization/
├─ Policies/
└─ Support/
```

---

## What Omni Owns

- channel registration
- inbound normalization
- outbound interaction routing
- conversation/session continuity
- cross-channel identity linking
- channel-aware templates and behaviors
- policy-aware handoff into signals, workflows, communications, and AI
- unified interaction logs

---

## Omni vs Communications

Communications delivers.
Omni coordinates interaction.

---

## Expanded Omni Contract

Titan Omni is the omnichannel conversation and communications shell over the governed operational system. It should unify channel events without flattening away the identity of each channel.

### Target channels
Core targets:
- WhatsApp
- Messenger
- Telegram
- Email
- SMS
- Voice

### Core model
Omni should normalize communication into:
- channel account
- participant/contact
- conversation/thread
- message/event
- delivery state
- approval state for outbound actions when required
- related operational context (customer, site, job, invoice, etc.)

### Inbound path
1. receive webhook/event from provider
2. normalize into common envelope
3. resolve tenant, participant, and thread
4. persist event and attachments
5. optionally classify or summarize with AI
6. expose to inbox, automation, and approval flows

### Outbound path
1. create message draft or operational reply
2. check permissions, policy, and approval requirements
3. render channel-specific template/format
4. queue delivery
5. track callbacks and delivery outcomes

### Why Omni is a platform layer
Omni is not just a UI inbox. It should connect to:
- communications engine
- AI tool routing
- customer memory
- workflow/automation triggers
- audit and observability
- PWA and web shells

### Rules
- maintain stable thread identity across shells
- never cross tenant boundaries
- queue provider delivery and callback handling
- keep AI-generated outbound content reviewable when sensitivity is high

---
## Channel Contract

Each Omni channel adapter should declare:

- capability set
- send modes supported
- inbound webhook/event shape
- delivery status mapping
- media/attachment support
- rate or provider limits
- tenant credential source
- fallback behavior

## Routing Rules

A message path should decide:

- which channel is preferred for this tenant/contact/use case
- whether fallback to another channel is allowed
- whether the message is campaign, transactional, reminder, support, or conversational
- whether approval is required before send
- which audit thread or conversation record should receive the result

## Anti-Patterns

Avoid:

- hard-coding per-channel logic into domain modules
- channel-specific templates as the only source of content truth
- losing delivery events after send
- channel adapters bypassing permission or tenant credential checks

The Omni layer should abstract channels while preserving delivery evidence and tenant control.


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
