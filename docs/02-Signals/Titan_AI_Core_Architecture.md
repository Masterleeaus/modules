# Titan AI Core Architecture

## Purpose

Defines the first-class AI layer inside Titan.
AI is not a helper utility. It is a core system engine responsible for reasoning,
proposal generation, orchestration, governance participation, memory use, and tool invocation.

---

## Canonical AI Tree

```text
app/Platform/Ai/
├─ Core/
│  ├─ TitanZero/
│  ├─ Aegis/
│  ├─ Equilibrium/
│  ├─ Micro/
│  ├─ Macro/
│  ├─ Logic/
│  ├─ Creator/
│  ├─ Finance/
│  ├─ Entropy/
│  └─ Sentry/
├─ Orchestration/
├─ Context/
├─ Memory/
├─ Routing/
├─ Tooling/
├─ Governance/
├─ Voice/
├─ Evaluation/
├─ Training/
└─ Support/
```

---

## Core Identity

### Titan Zero
The sovereign user-facing intelligence.
Owns unified synthesis, cross-module reasoning, proposal generation, and final user-facing intelligence assembly.

### AEGIS
The governance and authority layer.
Owns safety, approval constraints, risk checks, and policy consistency.

### Specialist Cores
Logic, Creator, Finance, Entropy, Micro, Macro and other specialist lenses feed and challenge Titan Zero.

---

## AI Is a Platform Engine

AI must sit beside:

- automation
- workflows
- signals
- communications
- sync
- audit
- observability

It is not just a set of helper methods or manifests.

---

## Memory Layers

- Session
- User
- Tenant
- Site / Job
- Working
- Recall Policies

This prevents memory from becoming a single undifferentiated store.

---

## Routing Layer

AI routing decides:

- when to use local models
- when to delegate to external models
- which model class fits the task
- privacy constraints
- latency constraints
- cost constraints

---

## Governance Layer

AI must operate under controlled execution modes:

- Suggest
- Queue For Approval
- Auto Execute

AI should not bypass the signal and governance pathway.

---

## Expanded Platform Contract

### AI as a governed platform layer
AI should be implemented as a first-class platform layer under `app/Platform/Ai/`, not as scattered helpers or one-off controller logic. The recommended subareas are:
- `Core/` for Titan Zero, AEGIS, Equilibrium, and specialist cores
- `Orchestration/` for consensus, critique, arbitration, and synthesis
- `Context/` for snapshot building, envelope compilation, retrieval, and tool context
- `Memory/` for session, user, tenant, site, job, and working memory
- `Routing/` for local versus external model policy and cost/privacy routing
- `Tooling/` for tool registries, executors, adapters, validators, and result normalization
- `Governance/` for proposals, approvals, denials, constraints, and safe modes
- `Voice/` for real-time voice, streams, and device adapters
- `Evaluation/` for evidence checks, hallucination checks, and policy checks
- `Training/` for refinement deltas, outcomes, and feedback loops

### AI operating sequence
A normal AI-assisted action should follow this order:
1. collect user intent, channel context, device context, and tenant scope
2. compile a bounded context pack from allowed records and manifests
3. route the task to the right reasoning mix and model path
4. produce one or more proposals rather than mutating state blindly
5. apply governance and risk scoring
6. queue for approval when required by policy, authority level, or sensitivity
7. invoke approved tools and actions through module contracts
8. write run logs, evidence, and output summaries back to the audit surface

### Memory contract
Memory should be partitioned, not treated as a single blob. The main memory classes are:
- **session memory** for the current conversation or task window
- **user memory** for user-specific preferences and recurring patterns
- **tenant memory** for company-wide defaults, standards, policies, and terminology
- **site/job memory** for operational context like gate codes, entry methods, repeat notes, and job quirks
- **working memory** for temporary synthesis artifacts and current decision state
- **recall policy** for controlling what may be surfaced to which shell and under which authority mode

### Authority modes
The AI layer should support explicit authority modes rather than hidden execution:
- **suggest** — AI drafts advice, explanations, or plans only
- **queue for approval** — AI prepares structured actions but a human must approve
- **auto execute** — AI may invoke tools directly only when policy, confidence, and permissions allow it

### Tool invocation rules
Every tool execution should be grounded in a manifest and a bounded context pack. The AI should not infer raw database writes. Instead it should call declared tools exposed by modules or platform services, with:
- stable tool names
- parameter schemas
- permission requirements
- approval requirements
- result envelopes
- failure codes
- audit logging

### Relationship to other platform engines
The AI layer should sit beside and cooperate with the platform engines for automation, workflows, signals, scheduling, communications, sync, audit, and observability. It should never silently replace them. AI proposes, routes, critiques, explains, and supervises; the platform engines execute through approved system paths.

### Implementation notes
- Keep controllers thin and move AI coordination into services, actions, and orchestrators.
- Use provider bindings so local and external model adapters can be swapped cleanly.
- Treat context size as a safety budget; prefer manifests and read models over broad schema introspection.
- Persist proposal logs, approval state, execution result, and evidence references for review.
- Add tests at the routing, approval, and tool-boundary levels, not only at the prompt level.

### Minimum acceptance checklist
- AI classes are grouped under a dedicated platform tree.
- Authority mode is explicit for every AI action path.
- Tool calls go through manifests and validators.
- Memory is partitioned by scope.
- Every run produces audit data.
- High-risk actions require approval rather than direct mutation.


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
