# 25. Developer Quality Gates, Testing, and Module Acceptance

## Purpose

This document defines how Titan/Worksuite code should be **proven safe to ship** before it is accepted into the platform. The goal is not only “does the code run?” but also:

- does it respect the constitution
- does it preserve tenant boundaries
- does it remain usable from API, PWA, automation, jobs, and panels
- does it avoid trapping domain logic in UI callbacks
- does it remain observable and recoverable under failure

This quality-gate layer is especially important because the platform is designed as a **multi-surface system**:
- Worksuite core
- domain modules
- Filament operator panels
- PWAs and device nodes
- API clients
- automation engines
- Titan Zero / AEGIS mediated AI actions

A feature is not complete if it only works in one of these surfaces.

---

## Core doctrine

Laravel’s architecture encourages thin controllers, form requests, reusable actions/services, queues for deferred work, and provider-based system composition. That means the safest place for business logic is not controller methods or panel callbacks, but reusable application classes that can be invoked from many contexts. Clean Laravel practice also improves testability by making controller tests smaller, action tests clearer, and side effects easier to isolate.

The Titan module blueprint sharpens this further:
- module owns domain logic
- Filament consumes module logic
- no duplicate CRUD logic
- no business rules trapped in Filament callbacks
- actions/services/events/jobs remain reusable across web, API, jobs, PWA, and AI surfaces

Therefore, the platform’s quality model must verify **architectural placement**, not only output correctness.

---

## The acceptance ladder

Every module, engine, or platform subsystem should pass through these gates in order.

### Gate 1 — Structural validity

A deliverable must be structurally valid before any runtime testing begins.

For a domain module, this means at minimum:

- valid `module.json`
- correct folder names and case
- valid provider classes
- route files exist
- migrations exist or are intentionally absent
- views and translations load without namespace mismatch
- manifests are valid JSON where present

For a platform subsystem, this means:

- providers are registered correctly
- contracts/interfaces resolve
- config files load
- route groups match intended panel or API surface
- jobs, events, and listeners resolve through the container

### Gate 2 — Installation validity

A module or subsystem must install safely into an already-running tenant system.

Checks:
- migrations are idempotent
- rerunning migrations does not duplicate tables/columns/indexes
- seeders are safe on re-run
- required permissions seed correctly
- package visibility data is created
- module settings are seeded for existing tenants
- new-company listeners seed future tenants automatically
- caches clear without breaking routes or view namespaces

### Gate 3 — Tenancy validity

Every domain feature must respect the tenant boundary.

Checks:
- `company_id` is the authoritative tenant fence
- `user_id` exists wherever actor ownership, audit, assignment, or user-scoped settings matter
- read queries are scoped by tenant
- mutations cannot target records from another tenant
- related records preserve the same company boundary
- exports, notifications, and automations do not leak cross-tenant data
- API endpoints enforce tenant scope even when the caller passes raw IDs

### Gate 4 — Surface validity

A feature is only accepted when it works from every intended surface:

- web/controller
- API
- Filament/operator panel
- queue/job
- automation engine
- AI tool invocation
- PWA/mobile consumer

If a feature only works through Filament, it is incomplete.

### Gate 5 — Governance validity

A feature must honor constitutional and AI governance rules.

Checks:
- Titan Zero remains the decision authority
- Titan Core remains execution-only
- Titan Hello owns operational voice session control
- Titan Go remains stateless
- CustomerConnect owns operational conversations
- Studio does not mutate Worksuite operational data directly
- risky AI actions require approval paths
- AI runs are auditable
- denial paths are explicit and recoverable

### Gate 6 — Reliability validity

The feature must behave well under retry, delay, duplication, timeout, and partial failure.

Checks:
- actions are idempotent where retries are possible
- queue jobs can be retried safely
- outbox/inbox patterns prevent double-send
- dead-letter handling exists for repeated failure
- external provider errors do not corrupt business state
- partial completion is logged and replayable
- user-visible status reflects real execution state

---

## The test pyramid for this system

A normal Laravel app can survive with controller and feature tests alone. This system cannot.

### 1. Unit tests

These cover:
- value objects
- DTO mapping
- policy rules
- pure service calculations
- risk scoring
- signal validation
- manifest parsing
- state transition guards

Unit tests are best for logic that has no infrastructure dependency.

### 2. Action/service tests

These are the most important layer for Titan/Worksuite.

They verify:
- action behavior
- tenancy enforcement
- permission requirements
- event dispatch
- status transitions
- side-effect intent

Examples:
- `CreateBookingActionTest`
- `AssignWorkerActionTest`
- `ApproveQuoteActionTest`
- `EmitSignalActionTest`

These tests should be the main confidence layer because actions are the shared source of truth across surfaces.

### 3. Feature tests

These verify:
- route behavior
- controller-to-action wiring
- request validation
- HTTP responses
- auth middleware
- panel page behavior
- API response envelopes

Feature tests should confirm that the application shell is wired correctly, not re-test all action details line by line.

### 4. Integration tests

These are required for:
- external providers
- communications channels
- AI routing
- PWA sync
- Duo mode cross-system APIs
- storage adapters
- payment gateways
- voice/STT/TTS adapters

Use adapters, fakes, or contract-level mocks when possible, but keep a smaller set of true integration tests for critical boundaries.

### 5. End-to-end / acceptance tests

Use these for:
- super admin package flows
- module enable/disable behavior
- company onboarding
- PWA bootstrap and sync
- operator approval surfaces
- customer communication threads
- Duo handoff and backfeed flows

These tests should be fewer, but they prove the system works as a whole.

---

## The “module doctor” concept should become a permanent quality gate

Because Worksuite uses a DB-backed module registry plus package assignment plus company module settings, module failures often come from **integration drift**, not syntax errors.

A robust doctor/audit layer should check:

- is the module discoverable on disk
- is it registered in `modules`
- do permissions exist
- do `module_settings` rows exist for existing companies
- does package UI surface it
- are routes loadable
- do required views resolve
- are translations present
- do migrations exist and are they idempotent
- are manifests valid JSON
- are super admin settings routes present when advertised
- are menu entries consistent with named routes

This doctor is not a convenience. In a module-rich SaaS, it is part of the acceptance pipeline.

---

## Acceptance checklist for domain modules

A domain module should not be marked “ready” until all of the following are true.

### Loader and structure
- folder name stable
- `module.json` valid
- providers resolve
- routes load
- views and translations load
- migrations load safely

### Registry and package presence
- appears in module list
- attaches to packages
- seeds `module_settings`
- is visible to the right audiences
- has readable display labels

### Business layer
- actions exist for core mutations
- validation lives in form requests or reusable validators
- policies exist for protected operations
- events/jobs handle side effects
- Filament is consuming actions, not owning domain logic

### Tenant safety
- `company_id` scoping is universal
- `user_id` is present where needed
- queries cannot cross tenant boundary
- imports/exports/notifications remain tenant-safe

### API and PWA readiness
- `Routes/api.php` exists where appropriate
- API controllers are distinct from web controllers
- response shape is predictable
- manifests advertise real capabilities
- sync-safe endpoints are available where mobile/node operation is intended

### AI and automation readiness
- `ai_tools.json` only advertises allowed actions
- `signals_manifest.json` maps real events
- lifecycle/surface manifests reflect actual module behavior
- actions called by AI remain tenant-safe and policy-checked
- AI never bypasses module actions

---

## CI pipeline shape for the platform

A serious CI pipeline for this system should be layered.

### Stage 1 — Static quality
- PHP syntax lint
- coding standards
- dead code scan
- duplicate class detection
- duplicate migration class detection
- invalid JSON manifest detection

### Stage 2 — Architectural quality
- check module structure against blueprint
- verify provider paths
- verify route file presence
- verify manifest presence/shape where required
- reject business logic embedded only in Filament resources for critical operations

### Stage 3 — Database safety
- run migrations on clean DB
- rerun migrations to confirm idempotency
- install selected module subsets
- confirm tenant seeders work
- confirm package assignment does not fail

### Stage 4 — Test suite
- unit tests
- action tests
- feature tests
- integration tests
- smoke acceptance tests

### Stage 5 — Build/runtime smoke
- optimize clear/build
- route cache/config cache checks
- queue boot checks
- panel/provider boot checks
- PWA/API bootstrap smoke tests

### Stage 6 — Report and publish
- attach a machine-readable health report
- list blocked modules
- list migrations with warnings
- emit compatibility matrix by module and panel surface

---

## Release categories

Not every change deserves the same release gate.

### Patch
Use for:
- bug fixes
- migration guards
- translation fixes
- null-safe rendering
- queue retry fixes
- route name corrections

### Minor
Use for:
- new module capability
- new manifest support
- new panel surfaces
- non-breaking API additions
- new automation rules
- new PWA sync features

### Major
Use for:
- authority changes
- tenancy model shifts
- breaking route/manifest/schema changes
- module registry changes
- Duo/AI law changes
- engine contract rewrites

Every major release should include migration notes and compatibility notes for modules, PWAs, and Duo integrations.

---

## What must be tested whenever AI touches a feature

Whenever a feature becomes AI-callable, the acceptance bar rises.

You must verify:
- tool intent maps to a real module action
- permission/policy checks still run
- tenant context is complete
- required confirmations are enforced
- denial paths are user-readable
- action outputs are normalized
- execution is logged in `ai_runs`
- retries do not duplicate state changes
- the same action still works from web/API/panel without AI

If AI invocation creates a different code path than normal invocation, the design is wrong.

---

## What must be tested whenever a feature is voice-enabled

Voice adds new failure modes:
- partial transcripts
- delayed audio
- interrupted sessions
- consent ambiguity
- repeated utterances
- confirmation misunderstanding

So voice-enabled flows need:
- session lifecycle tests
- transcript-to-intent tests
- confirmation escalation tests
- channel consent tests
- transcript audit logging checks
- graceful fallback to text-only behavior

Titan Hello owns session orchestration, so these tests must verify that voice state does not silently mutate business state outside approved module actions.

---

## What must be tested whenever a feature is PWA/node-enabled

PWA/node features must prove:
- bootstrap works on a fresh device
- trust registration is enforced
- offline actions store safely
- replay produces idempotent results
- conflicts are detected and resolved by policy
- stale data is visible as stale
- device loss/revocation actually blocks sync
- sync envelopes do not exceed capability boundaries

This is different from normal browser testing. PWAs are semi-autonomous clients and must be treated as such.

---

## Review questions for every PR / pass

Before merging, reviewers should ask:

1. Is the domain logic in actions/services/events/jobs, or trapped in controllers/panels?
2. Does this preserve the constitutional authority split?
3. Is `company_id` enforced everywhere it should be?
4. Does `user_id` exist where ownership/audit matters?
5. Is the feature package-visible and settings-visible where expected?
6. Can the same logic be invoked from API/PWA/automation/AI without duplication?
7. Are migrations rerunnable?
8. Are side effects queued or decoupled appropriately?
9. Are failure states auditable and recoverable?
10. Does this create any hidden cross-system authority conflict?

If the answer to any of these is unclear, the change is not ready.

---

## Final rule

In this platform, “working” is not enough.

A feature is only accepted when it is:

- structurally valid
- install-safe
- tenant-safe
- surface-complete
- governance-compliant
- reliability-aware
- test-covered
- auditable
- reusable across human, automation, and AI paths

That is the standard required for a real AI-supervised operating system, not just a collection of Laravel screens.
