# 26. Rollout Strategy, Environment Topology, and Evolution Roadmap

## Purpose

This document defines how the Titan/Worksuite platform should be **rolled out, evolved, and hardened over time** without losing constitutional boundaries or operational safety.

The system is not a single app released in one moment. It is a layered platform composed of:

- Worksuite core
- domain modules
- Filament operator panels
- APIs
- PWAs and device nodes
- communications engines
- automation/workflow engines
- Titan Zero / AEGIS / Titan Core / voice services
- Duo mode bridges with Titan Studio

Because of that, rollout must be staged. The right question is not “can we deploy it?” but:

- what layer is ready now
- what dependencies must come first
- what can be piloted safely
- what must remain behind approval gates
- what becomes the next foundation layer

---

## The north-star shape

The target end-state is a **federated operating system** with these characteristics:

- Worksuite remains the operational system of record
- modules own business domains
- Filament acts as operator/admin control plane
- PWAs become task-focused client shells
- device nodes perform local capture, buffering, and sync
- signals carry inter-system intent and event flow
- automation engines run guarded workflows
- Titan Zero authorizes AI decisions
- Titan Core executes approved AI/provider calls
- CustomerConnect owns operational conversations
- Titan Hello owns operational voice sessions
- Titan Go remains stateless speech infrastructure
- Duo mode lets Studio and Worksuite cooperate without breaking authority separation

That is the destination. Rollout should move toward it in measurable steps.

---

## Stage 0 — Stabilize the Laravel substrate

Before an “AI operating system” can exist, the Laravel foundation has to be clean and predictable.

Objectives:
- clean route structure
- predictable providers
- module registry stability
- package visibility working
- tenant scoping consistent
- migrations idempotent
- queues reliable
- controller bloat reduced
- actions/services introduced as shared logic layer
- testing discipline in place

At this stage, the platform is still mostly a modular SaaS, but it becomes ready to host larger systems.

### Deliverables
- provider map documented
- route naming/prefix rules enforced
- module doctor/reporting in place
- package/module/account visibility fixed
- migration rerun safety confirmed
- company seeding and module settings stable

### Why this stage matters
If the substrate is unstable, every later layer multiplies the chaos.

---

## Stage 1 — Establish the domain module contract

Once the Laravel base is stable, every business capability must be re-expressed as a proper domain module.

Objectives:
- modules follow the Titan-ready checklist
- modules own actions/services/events/jobs
- Filament no longer holds the only implementation of core behavior
- API surfaces become first-class
- manifests begin appearing consistently
- module settings and package behavior become reliable

### Deliverables
- module blueprint adopted
- shared acceptance checklist
- menu/sidebar/settings conventions stable
- module registry and package integration fixed
- capability manifests introduced gradually

### Exit criteria
A domain capability can now be called from:
- controller
- API
- queue/job
- Filament action
- future PWA/AI path

without being rewritten.

---

## Stage 2 — Establish the control plane

Now that modules are real domain engines, Filament can become the explicit operator surface.

Objectives:
- separate admin and user/operator panels
- create approval screens
- expose workflow dashboards
- expose queue/retry/dead-letter/health surfaces
- create package/module/tenant visibility tools
- show AI proposals, approvals, denials, and audit trails

### Deliverables
- AdminPanelProvider and UserPanelProvider pattern
- panel plugins per module/domain
- operator dashboards for jobs, communications, approvals, AI, sync health
- super admin module settings surfaces
- governance surfaces for Duo mode and AI permissions

### Why this matters
The control plane is where humans keep authority over an increasingly automated system.

---

## Stage 3 — Make APIs first-class

At this point, APIs stop being an afterthought.

Objectives:
- separate API routing from web routes
- standardize response envelopes
- version APIs
- expose module actions cleanly
- enforce auth, rate limiting, and tenant scoping
- prepare for PWA/mobile/node consumption

### Deliverables
- `/api/v1/...` structure
- consistent success/error envelopes
- module API resources/controllers
- Duo bridge endpoints
- context snapshot endpoints
- capability-aware API documentation

### Exit criteria
A PWA or external client can call the platform without scraping web behavior or panel logic.

---

## Stage 4 — Introduce PWA shells

Only after the API and module contract are stable should PWAs become serious.

Objectives:
- define bootstrap/handshake contract
- register devices with trust metadata
- support offline queues
- support sync and replay
- create task-focused PWA shells instead of one overloaded app

Suggested shells:
- Titan Portal
- Titan Command
- Titan Go field app
- money/finance shell
- customer/portal surfaces where appropriate

### Deliverables
- device registry
- trust levels
- manifest/bootstrap API
- sync status/reporting
- local queue/replay semantics
- capability-bound PWA surfaces

### Rollout advice
Start with one narrow PWA that solves one recurring operational flow well. Do not begin with a mega-app.

---

## Stage 5 — Introduce signal and workflow engines

Once modules, APIs, and PWAs exist, the platform can become event-native.

Objectives:
- define signal envelope
- emit module events consistently
- validate and log intake
- replay failed flows
- separate workflow definitions from UI
- add approvals, retries, idempotency, dead-letter handling

### Deliverables
- signal contracts
- outbox/inbox patterns
- approval queue
- workflow definitions and guards
- replay and audit tools
- stuck-state detection

### Why this matters
This is the point where the system starts acting like an operating system rather than a CRUD application.

---

## Stage 6 — Introduce AI as a governed layer

AI should come after domain, workflow, and signal contracts exist—not before.

Objectives:
- Titan Zero becomes the sole AI authority
- Titan Core becomes the exclusive execution router
- tools map to module actions
- context packs become formalized
- approvals and risk classes are enforced
- AI logs are first-class audit artifacts

### Deliverables
- tool registries
- capability validation
- ai_runs logging
- confirmation policies
- cost/latency/provider routing
- memory boundaries
- denial/fallback behavior

### Rollout advice
Begin with read-heavy and advisory tasks:
- summarize
- recommend
- classify
- draft
- prepare approvals

Only later allow write actions, and only through approved module actions.

---

## Stage 7 — Introduce voice channels

Voice is powerful, but it should not arrive before text, API, and workflow discipline are stable.

Objectives:
- Titan Hello owns session orchestration
- Titan Go provides STT/TTS only
- transcripts become first-class turns
- consent and recording rules are enforced
- voice actions obey the same AI approval law as text actions

### Deliverables
- session lifecycle APIs
- transcript logging
- fallback to text
- interruption handling
- consent enforcement
- voice audit trails linked to CustomerConnect and ai_runs

### Rollout advice
Start with staff-assist and narrow operational calls. Avoid broad autonomous voice execution at first.

---

## Stage 8 — Activate Duo mode

Only after both Worksuite and Studio have strong boundaries should Duo become a major rollout phase.

Objectives:
- shared identity and context snapshot flow
- event backfeed from Studio to Worksuite
- operational execution remains in Worksuite
- marketing authority remains in Studio
- no domain gets dual authority

### Deliverables
- context snapshot API
- Duo ingest API
- shared auth package or bridge
- event correlation IDs
- policy checks for cross-system calls
- admin visibility of mode and connection health

### Exit criteria
Studio can enrich operations without becoming the operational system of record.

---

## Environment topology

The platform should be run across explicit environments with clear promotion rules.

### Local
Purpose:
- module development
- migration iteration
- panel/API debugging
- contract testing

Properties:
- fake providers allowed
- seeded sample tenants
- no production voice/comms keys
- AI may use low-cost or mocked models

### Integration / QA
Purpose:
- realistic multi-module install tests
- package assignment tests
- PWA handshake tests
- queue/worker tests
- Duo contract tests

Properties:
- stable fixture data
- real queue stack
- provider sandboxes
- repeatable migrations

### Staging
Purpose:
- release candidate validation
- smoke test with near-production topology
- real cache/queue/storage behavior
- operator signoff

Properties:
- production-like infrastructure
- restricted real integrations
- seeded or masked tenant data
- approval surfaces tested end to end

### Production
Purpose:
- live operations

Properties:
- auditable changes only
- feature flags for risky subsystems
- staged rollout by tenant/group
- no experimental bypasses
- strict monitoring and rollback strategy

---

## Rollout unit: tenant cohorts, not whole-platform flips

This system should not be rolled out as “everyone gets everything at once.”

Better rollout units:
- internal test tenants
- pilot companies
- single-user solo cohort
- multi-user pro cohort
- one vertical overlay at a time
- one communication channel at a time
- one PWA shell at a time
- one AI permission tier at a time

### Why
A federated system has too many interacting parts to safely big-bang.

---

## Feature flag strategy

The platform should treat many capabilities as flaggable.

Good candidates:
- AI write actions
- Duo mode
- voice sessions
- WhatsApp/Telegram/Messenger channels
- automated follow-up
- workflow auto-approval
- PWA offline sync
- local model routing
- external AI provider failover

Flags should exist at multiple scopes:
- system-wide
- environment
- tenant
- package/tier
- role
- device trust level

---

## Migration strategy for a living platform

Because modules and engines evolve separately, migration strategy must be disciplined.

### Rules
- migrations are idempotent
- avoid destructive changes in one step
- use expand-and-contract when renaming/refactoring schema
- emit data migration reports
- preserve backward compatibility during phased rollout
- support mixed-version clients during transition windows

### Pattern
1. add new columns/tables/contracts
2. write both old and new if needed
3. backfill
4. switch reads
5. remove old path only after confidence window

This matters especially for:
- manifests
- API envelopes
- device sync contracts
- workflow state payloads
- AI run records
- communications event schemas

---

## Observability strategy during rollout

Every rollout phase should have different leading indicators.

### Module/platform substrate
- route load errors
- provider boot failures
- migration failures
- missing module settings
- package visibility mismatches

### API/PWA
- bootstrap failure rate
- sync retry count
- conflict rate
- stale device count
- trust failures

### Workflow/automation
- queued vs completed jobs
- dead letters
- retry loops
- approval backlog
- stuck states

### AI
- approval/denial ratio
- tool invocation errors
- policy rejection count
- hallucination/consistency flags
- per-tenant cost and latency

### Voice/comms
- delivery failure rate
- opt-out violation attempts
- transcript failure rate
- fallback-to-text rate
- session abandonment rate

If you cannot see these metrics, you are not ready to widen rollout.

---

## The final five docs in the roadmap should naturally close here

By the time the handbook reaches its end, the remaining docs should converge on:
- release/roadmap governance
- developer workflow and contribution standards
- AI tool registry and module action patterns
- vertical overlays and surface packs
- operating model for internal teams and external agents

This rollout doc is the bridge between architecture and execution. It answers not just “what should the system be?” but “how do we get there safely?”

---

## Final rule

The platform should evolve in this order:

1. stable Laravel substrate
2. stable module contract
3. stable control plane
4. stable API layer
5. stable PWA shells
6. stable signal/workflow engines
7. governed AI execution
8. governed voice channels
9. Duo mode federation
10. progressive autonomy

That sequence protects the most important truth in the whole system:

**authority, audit, and tenant safety must mature before autonomy does.**
