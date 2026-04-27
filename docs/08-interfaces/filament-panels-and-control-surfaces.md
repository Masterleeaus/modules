# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Filament panels, control surfaces, operator dashboards, and role-specific shell separation
Status: Draft v1
Depends On: Core Platform, Modules + Extensions, AI, PWA + Nodes, Workflows
Consumed By: Super Admin, Command Centre, tenant admin panels, future package-controlled app surfaces
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Filament should be used inside Titan as the control-plane and operator surface without allowing UI code to become the domain engine.

## 2. Why it exists

Titan needs a fast, reusable admin and dashboard base. The agreed architecture uses Worksuite as the system of record, Filament as the system of control, and PWA shells as the system of execution. This document locks Filament into that role so future development does not bury business logic in panel callbacks or split the same rules across multiple surfaces.

## 3. Core responsibilities

- provide super-admin, owner, manager, finance, and operator panels
- render resources, forms, tables, widgets, approvals, and review queues
- host command-centre dashboards and dense management screens
- call stable module actions, services, policies, and exports rather than duplicating their logic
- provide panel-separated navigation and access rules for different roles

## 4. Boundaries

### In scope

- panel providers
- navigation groups and clusters
- resources, pages, widgets, tables, forms, and infolists
- operator review queues
- approval and exception screens
- package-driven visibility of panels or panel sections

### Out of scope

- core booking, finance, dispatch, or CRM business rules
- offline engine internals
- AI governance and memory policy
- module installation semantics
- domain event, queue, or notification ownership

## 5. Architecture

## 5.1 Panel model

Titan should treat Filament as a multi-panel shell, not as one giant admin. The recommended base split is:

- Super Admin Panel
- Command Centre Panel
- Tenant Admin Panel
- Money Panel
- Support or Portal Operator Panel
- future role-specific panels if packaging justifies them

This follows the panel-provider direction already established in the blueprints, where panel setup lives in dedicated provider classes and shared components live in shared Filament support folders.

## 5.2 Role of Filament in the wider stack

Filament should sit above the module and platform layers:

- **Worksuite / platform layer** owns tenancy, packages, permissions, domain entities, queues, scheduler, and operational tables
- **module layer** owns actions, validation semantics, policies, events, imports, exports, APIs, and manifests
- **Filament layer** owns UI composition for operators and administrators

This ensures the same module behavior can be consumed by web pages, APIs, PWAs, AI tools, and Filament surfaces without reimplementation.

## 5.3 Surface categories

### Super Admin surfaces

Use Filament for:

- package management
- module enablement and defaults
- company health and tenancy diagnostics
- AI/provider capability settings
- system doctor and observability screens
- app-surface publishing controls

### Command Centre surfaces

Use Filament for:

- KPI overview dashboards
- approvals
- exception queues
- finance summaries
- workflow bottlenecks
- signal and automation review
- risk or compliance warnings

### Tenant admin/operator surfaces

Use Filament for:

- dense records management
- operational tables
- status changes
- audit views
- bulk actions
- internal notes, reviews, and reconciliations

## 5.4 Resource rule

A Filament resource is never the system of record for business behavior.

Correct pattern:

- resource form submit -> request validation -> module action/service
- bulk action -> module action/service
- status button -> transition action/service
- export button -> export class or service
- approval button -> policy + approval action

Incorrect pattern:

- overlap logic in a table closure
- status rules hidden in a UI callback
- notifications sent only from page actions
- tenant safety implemented only in form closures

## 5.5 Shared design system

All panels should share:

- one visual language
- one entity vocabulary
- one action grammar
- one approval model
- one AI interaction zone pattern

Panels may differ by density and privilege, but should not feel like separate products.

## 6. Contracts

## 6.1 Inbound contracts to Filament

Filament consumes:

- module actions and services
- policies and permission checks
- read models and aggregate metrics
- exports/imports
- AI summaries and recommendation envelopes
- package visibility rules

## 6.2 Outbound contracts from Filament

Filament emits:

- user intent
- approval/deny decisions
- update requests
- bulk operation requests
- operator review outcomes
- navigation and view context for AI side panes

## 6.3 Required panel contracts

Each panel should define:

- audience
- role gate
- tenancy mode
- navigation groups
- default dashboard widgets
- permitted resources and actions
- AI side-pane or assistant behavior if enabled

## 7. Runtime behavior

## 7.1 Three-region screen rule

Advanced operator screens should be composed from three regions:

### A. State region

Tables, metrics, timelines, records, read models.

### B. Action region

Forms, transitions, approvals, bulk actions.

### C. Intelligence region

AI summaries, risks, suggested actions, confidence, review notes.

This pattern keeps Filament screens operational while still allowing chat-first or AI-assisted interactions beside the structured UI.

## 7.2 Multi-panel behavior

Users may have access to one or many panels. Panel switching must not duplicate the same domain logic in multiple places. Shared actions and policies must remain below the UI layer.

## 7.3 Package-aware behavior

Panels or panel sections may be hidden by package tier, but hidden UI must not be the only protection. The underlying actions and APIs must still enforce capability rules.

## 8. Failure modes

- **logic drift:** Filament action diverges from API or PWA behavior
- **callback trap:** rules buried inside form/table closures become impossible to reuse
- **panel sprawl:** too many panels without a clear audience or package boundary
- **role leakage:** admin-only functions bleed into tenant or worker panels
- **visual fragmentation:** each panel invents its own design and vocabulary

## 9. Dependencies

Upstream:

- Core platform tenancy, package, and permission systems
- Module actions, services, and policies
- Workflow and automation state
- AI output envelopes

Downstream:

- Super admin operations
- Command-centre dashboards
- tenant operators and reviewers
- AI-assisted review surfaces

## 10. Open questions

- Which panels should ship in MVP versus remain latent by package?
- Should Money be a distinct panel or a mode inside Command Centre in early releases?
- How much Agenytics-inspired chat rendering belongs inside Filament screens versus a separate chat workspace panel?
- Which approvals should remain in Filament only, and which need mirrored mobile/PWA review surfaces?

## 11. Implementation notes

- Use dedicated panel providers rather than a monolithic panel config.
- Keep shared components in a shared Filament layer, but keep domain-specific resources close to their modules.
- Never let a Filament resource become the only surface that knows how to create, approve, or transition core entities.
- Prefer module actions and policy classes for anything that must also work via API, AI, automation, import, or PWA.


## 11. Implementation anchors

The current blueprint direction expects dedicated panel provider classes such as `AdminPanelProvider.php` and `UserPanelProvider.php`, with shared Filament support separated from domain code. Filament plugins should register resources, pages, and widgets into panels rather than becoming the business layer itself. This means interface work should attach to panel providers and module Filament plugins while domain actions stay in module actions and services.

### Recommended registration pattern

- panel providers define high-level shell behavior
- module Filament plugins attach resources/pages/widgets per domain
- resources delegate to module actions and requests
- widgets consume view models, DTOs, and stable API/read models
- custom pages host dense operator flows such as dispatch boards and approval queues

## 12. Package and tenancy visibility

Because package integration and tenant boundary are first-class rules, panel surfaces should only expose modules and navigation that are enabled for the current company/package context. Filament should therefore read package/module settings and tenancy scope before rendering clusters, navigation groups, and resources.

### Shell rule

Never assume a panel shows every module. The shell should be assembled from:

- package visibility
- company/module settings
- user permission set
- role-specific panel access
- feature flags where needed

## 13. Anti-pattern checklist

Avoid these interface mistakes:

- placing validation or transition rules directly in Filament callbacks
- using a widget button to perform logic that bypasses module policies
- making Filament the only place an approval can occur
- binding navigation directly to legacy URLs instead of named routes
- mirroring the whole database in one panel instead of role-shaped surfaces


## 13. Multi-panel suite pattern

The agreed Filament shape is not one panel pretending to be every app. It is a suite of role-shaped panels and app surfaces that share one Laravel core and one domain contract.

Recommended first-wave panel families:
- Super Admin
- Command Centre
- Field Worker
- Money
- Customer Portal
- Chat Workspace

Filament should own the panel shell, navigation, resources, dashboards, and operator tooling for these surfaces. The panels must still consume the same module actions, APIs, policies, and manifests so the suite behaves like one system instead of six disconnected mini-apps.

## 14. PWA overlap rules

Filament can also act as the base for installable operator and role-facing PWAs when the panel is mobile-relevant. The most suitable early PWA candidates are Command, Field Worker, and Portal. The admin-heavy super-admin panel can remain web-primary.

When a panel is made installable:
- installability must not create a second logic path
- manifest and service worker are delivery concerns, not business-rule locations
- panel routes, actions, and policies remain the same system contracts
- offline behavior must degrade into queueing, replay, and stale-state warnings rather than silent false certainty

## 15. Admin-control doctrine

Filament is the best home for governance-heavy control surfaces:
- approvals and review queues
- package and module visibility
- panel publishing controls
- tenant health and route health
- AI supervision consoles
- queue, audit, and node health views

This keeps the control plane concentrated in a panel system designed for dense management work while preventing admin screens from becoming the new source of domain truth.
