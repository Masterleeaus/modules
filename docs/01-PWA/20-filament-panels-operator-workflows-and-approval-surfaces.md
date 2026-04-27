# 20. Filament Panels, Operator Workflows, and Approval Surfaces

## Purpose

This document defines how Filament should be used in the Titan/Worksuite stack. Filament is not the business engine and not the system constitution. It is the operator-facing control plane that lets humans inspect, approve, manage, and steer a much larger modular and AI-assisted platform.

This means Filament must remain powerful, but disciplined.

---

## Core doctrine

The module/plugin blueprint is explicit: modules own business logic, migrations, entities, policies, actions, services, events, listeners, jobs, notifications, imports/exports, API routes, tenant scoping, package settings, and Titan manifests; Filament owns resources, pages, widgets, tables, forms, infolists, bulk actions, operator dashboards, and approval screens. Filament must consume module actions rather than reimplement them. fileciteturn15file0turn15file1turn15file15

The full system blueprint confirms this by giving Filament its own app tree under `app/Filament/Admin`, `app/Filament/User`, and `app/Filament/Shared`, with separate `AdminPanelProvider` and `UserPanelProvider`. That means the system expects more than one panel and more than one operator audience. fileciteturn15file5

Laravel’s architecture guidance also supports thin controllers, centralized validation, reusable actions/services, queues, and event-driven side effects, which matches the requirement that panel actions should delegate to module/application logic instead of embedding it in UI callbacks. fileciteturn10file2turn10file3

---

## What Filament is for

Filament should be treated as the system’s visual operations shell.

Its job is to give humans clear, high-leverage surfaces for:

- monitoring
- triage
- approval
- override
- exception handling
- reporting
- admin setup
- package and tenant operations
- workflow visibility
- queue/retry insight
- AI proposal review

It is a cockpit, not the engine room.

---

## Panel split

### Admin panel

The admin panel is for platform operators, owners, super admins, or internal support teams. It should expose:

- package management
- module registry and module doctor
- tenant setup and tenancy oversight
- global settings
- AI governance dashboards
- communications and consent oversight
- automation reliability dashboards
- observability and audit surfaces
- policy and permission management

### User panel

The user panel is for tenant-side operators. It should expose:

- tenant dashboards
- work queues
- customer timelines
- job/quote/invoice workflows
- approvals and suggested actions
- field status views
- operational analytics
- module settings scoped to their company

### Shared layer

The shared Filament layer should contain reusable components that do not own business logic:

- shared widgets
- shared table columns and actions
- status badges
- timeline/infolist fragments
- cross-panel support components

---

## Filament as an approval system

The Titan constitution makes human review and AI authority boundaries central. Studio, Worksuite, Titan Zero, Titan Core, Hello, and Go all have explicit ownership rules, and operational mutations must pass through the correct authority path. That means Filament should serve as the human review layer where those boundaries are made visible and enforceable. fileciteturn15file12turn15file14turn15file17

Filament approval surfaces should handle:

- AI proposals awaiting approval
- escalations requiring operator review
- policy violations and blocked actions
- workflow steps waiting on a decision
- suspicious communications or opt-out conflicts
- tenant provisioning or mode-switch requests
- exception queues and dead-letter recovery

In other words, Filament is where operators see what the engines want to do and decide whether to permit, deny, defer, or amend it.

---

## Resource design rules

A Filament resource should be created when the operator needs a sustained CRUD-like management surface over a domain entity.

Use a resource for:

- customers
n- sites
- jobs
- quotes
- invoices
- templates
- campaigns
- devices
- manifests
- approvals
- audits

A resource should not become the only home of the domain logic. Instead:

- forms should hydrate DTOs or request-like inputs
- actions should call module actions/services
- bulk actions should trigger jobs/pipelines
- policy checks should live in policies or application rules
- notifications should be dispatched from domain events or services

This is how you avoid “Filament-only business law.”

---

## Page design rules

Use Filament pages when the operator needs a composite operational surface rather than entity CRUD.

Examples:

- dispatch board
- approval inbox
- signal replay center
- module doctor dashboard
- PWA device fleet dashboard
- communications routing monitor
- AI run explorer
- workflow stuck-state inspector
- duo mode migration console

Pages should assemble data, status, controls, and insights from multiple domains, but still delegate actual state changes to module actions or platform services.

---

## Widget design rules

Widgets are for fast situational awareness, not hidden logic. Good widget categories include:

- counts and trend cards
- queue depth and retry warnings
- proposal volume by class
- channel delivery health
- consent drift or opt-out anomalies
- offline device counts
- sync lag by tenant
- overdue approvals
- cost and token usage summaries

A widget should surface truth quickly. If it triggers an operation, that operation must still flow through a proper action/service or queue job.

---

## Operator workflow model

A healthy Titan/Worksuite Filament surface should map closely to operator workflows.

### Ingest

Operators see new items arriving:

- new leads
- new jobs
- incoming messages
- AI proposals
- failed sync events
- dead letters

### Assess

They inspect timeline, context, policy state, and prior actions.

### Decide

They approve, deny, reschedule, reroute, assign, or escalate.

### Execute

The panel dispatches a domain action, signal promotion, or queue job.

### Observe

Operators see audit traces, notifications, updated records, and resulting downstream state.

This aligns the panel with system behavior rather than with generic CRUD.

---

## Approval surface contract

Every approval-facing Filament component should show:

- proposal summary
- origin module or engine
- tenant and user context
- risk level
- policy checks
- evidence/context snapshot link
- expected side effects
- idempotency/retry state
- audit history
- available decisions

This supports the constitutional enforcement model, where violations, out-of-scope calls, and policy exceptions must be detected, logged, and recoverable. fileciteturn15file8turn15file9

---

## Role and tenant boundaries inside panels

Filament panels must reflect the same tenant law as the modules themselves.

That means:

- all user-panel queries are `company_id`-scoped
- `user_id` ownership and actor history is visible where relevant
- super admin surfaces can cross tenant boundaries only intentionally and with auditing
- no shared widget should accidentally aggregate tenant-private data into another tenant’s panel
- panel actions must respect package/module entitlements

A pretty panel with weak scoping is still a broken system.

---

## Filament and package visibility

A module might be technically installed but not commercially or operationally available to a tenant. Filament must therefore be registry-aware and package-aware.

Panel navigation should be filtered by:

- whether the module exists and is healthy
- whether the tenant’s package includes it
- whether the role has permission
- whether the module declares a panel surface
- whether the current `authority_mode` allows that surface

This is how the sidebar becomes a true capability reflection, not a hard-coded menu tree.

---

## Filament and PWA/device coordination

Filament remains the richer control surface, while PWAs and mobile apps are leaner execution surfaces. That means Filament should host the operator tooling for:

- device registration review
- node health
- trust level changes
- push/sync diagnostics
- remote wipe or revoke actions
- offline conflict review
- jobsite memory review

The PWA does the field work; Filament supervises the fleet.

---

## Filament and AI visibility

Filament should expose AI as an auditable subsystem, not a hidden black box. Useful surfaces include:

- AI runs table with model, cost, token usage, and escalation reason
- proposal queues by module and tenant
- disagreement or critique traces when multiple cores disagree
- blocked actions and reason codes
- memory or context pack previews
- tool registry explorer
- hallucination/policy check results

The constitutional docs require logging and traceability around AI runs and policy enforcement. Filament is the natural place for operators to inspect that layer. fileciteturn15file17turn15file18

---

## Bad patterns to forbid

### 1. Filament-only behavior

Bad: the panel page mutates the domain record directly in closures and no API/job/action path exists.

### 2. Duplicate CRUD law

Bad: web controller, API controller, and Filament resource each implement different creation logic.

### 3. Mixed audience panels

Bad: one panel exposes super admin tools and tenant operations in the same navigation without strong separation.

### 4. Hidden policy checks

Bad: approvals silently succeed or fail without the operator seeing the reason.

### 5. Unscoped aggregates

Bad: widgets summarize data across tenants for ordinary company users.

---

## Recommended build order

1. Establish Admin and User panel providers.
2. Move business logic into module actions/services first.
3. Build operator resources/pages on top of those actions.
4. Add approval surfaces for AI, workflows, and exceptions.
5. Add registry-aware, package-aware navigation.
6. Add AI, audit, sync, and device observability widgets.

---

## Final rule

Filament is where humans govern the system.

It should be the clearest place to see what the engines, modules, AI, and communications layers are doing — but never the place where core business law is secretly rewritten.
