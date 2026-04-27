# Filament Admin Surface

## Purpose

The Filament admin surface is the operator and governance shell for the system. It is not the Titan Zero brain, and it is not the worker-facing app. Its role is to expose administration, control, visibility, approvals, and configuration in a stable panel-based interface that can manage the full suite.

This surface should be treated as the control plane for the platform.

## What it owns

The admin surface owns:

- super admin settings
- tenant and package management
- module and extension controls
- dashboard and reporting surfaces for operators
- governance and approval queues
- app publishing controls
- navigation and shell configuration
- AI provider visibility and settings handoff
- audit, health, and observability surfaces

It does **not** own:

- core orchestration logic
- Titan Zero reasoning
- module business rules
- offline sync internals
- field-worker execution flows

## Position in the stack

The agreed architecture splits the system into three major layers:

1. **Brain layer** — Titan Zero, AEGIS, memory, tool routing, context packs.
2. **Control/UI layer** — Filament panels, resources, pages, widgets, forms, tables.
3. **Domain/module layer** — modules, actions, services, policies, APIs, manifests.

The Filament admin surface sits in the second layer. It consumes domain services and exposes them safely to operators.

## Why Filament is the right admin base

Filament is the best fit for the admin surface because it gives the system a reusable and modular control shell with:

- multi-panel architecture
- rich form and table systems
- dashboards and widgets
- role-based access patterns
- plugin ecosystem for PWA, analytics, notifications, and builder features
- compatibility with modular Laravel structures

This makes it the fastest route to a coherent command-and-control interface without rebuilding the entire admin foundation from scratch.

## Surface types inside the admin panel

The admin layer should not be one giant panel. It should be composed of related but distinct interface groups.

### 1. Super Admin surface

This is the highest-level platform management area.

It manages:

- tenants and package assignments
- module enable/disable state
- suite-wide branding defaults
- panel publishing rules
- AI provider/global settings
- feature flags
- integrations and credentials policy
- governance rules and approval defaults

### 2. Command Centre surface

This is the business oversight dashboard for owners, managers, and operations leads.

It should include:

- live KPI cards
- queues and alerts
- approvals waiting for review
- team and workload summaries
- revenue / receivables snapshot
- job and schedule state summaries
- AI recommendation summary panels
- timeline and telemetry surfaces

### 3. Specialist admin surfaces

These are focused operational work areas:

- Money
- Office/Admin
- CRM/Customers
- Services/Operations
- CMS/Website
- Integrations
- Agents/Automation controls

Each should be a panel or cluster of resources within a panel, depending on complexity.

## Core layout doctrine

The admin surface should follow one consistent shell structure.

### Header

Must carry:

- tenant context
- panel/app switcher
- alerts/notifications
- quick actions
- profile/user menu
- optional command palette trigger

### Sidebar

Must carry:

- stable section groups
- mode or app grouping
- clear iconography
- low cognitive load
- minimal duplication across panels

### Main canvas

Must support:

- dashboards
- resources
- approval screens
- builder pages
- drill-downs
- rich action areas

### Right-side assistant or action rail

Optional at first, but the architecture should leave room for:

- AI sidecar summaries
- suggested actions
- context-sensitive help
- approval rationale
- quick entity lookups

## Panel separation rules

The admin surface must separate concerns clearly.

### Keep in admin

- control-plane settings
- enterprise dashboards
- package and module controls
- approval and governance work
- publishing and deployment settings
- reporting and monitoring

### Keep out of admin

- chat-first end-user workflows
- worker-first mobile flows
- portal/customer self-service as primary interaction layer
- deep orchestration logic
- domain-specific business logic embedded in UI callbacks

## Relationship to modules

Modules remain the real domain engines.

The admin surface should consume module actions and resources, not redefine them.

### Correct pattern

- Filament form submits to request/data object
- action/service handles business logic
- events/jobs/notifications are fired in the module layer
- Filament shows the result

### Incorrect pattern

- business rules inside form callbacks
- side effects hidden in table actions
- policy decisions duplicated in UI code
- module state mutated directly inside widgets

## Relationship to Titan Zero

The admin surface is not the AI runtime. It is the management shell around the runtime.

The connection should work like this:

- admin UI provides configuration and visibility
- admin UI displays AI outputs and pending actions
- Titan Zero produces proposals, summaries, and assist actions
- approvals route back through admin where required

The admin surface should show:

- proposal queues
- confidence and rationale summaries
- action approval screens
- audit and trace views
- provider health and usage summaries

## Resource categories

The admin surface should group resources into predictable families.

### Platform

- companies/tenants
- packages
- modules
- settings
- integrations
- users / roles / permissions

### Operations

- customers
- sites
- service jobs
- visits
- schedules
- dispatch assignments
- issues and proofs

### Money

- quotes
- invoices
- payments
- receivables
- cost views
- payouts later

### Experience

- CMS surfaces
- portal publishing
- app shell publishing
- menu / nav controls
- themes / templates

### Intelligence

- AI provider settings
- memory tools
- approval queues
- automation/scout controls
- telemetry / audit / logs

## Dashboard design rules

Dashboards in the admin surface should be built for action, not decoration.

### Dashboard widgets should:

- show operational state clearly
- summarize risk, money, or workload
- link directly to action views
- support role-specific layouts
- avoid duplicating the same metric in many panels

### Dashboard pages should not:

- become dense report walls
- repeat entire CRUD screens
- hide approvals inside unrelated cards
- mix worker and executive concerns into one layout

## Multi-panel strategy

The Filament admin surface should be organized as several related panels, not one monolith.

Recommended initial structure:

- **Super Admin Panel**
- **Command Panel**
- **Money Panel**
- **Portal/CMS Panel**

Field Worker and Customer-facing mobile-first surfaces may still use Filament, but they should be treated as role apps, not administrative surfaces.

## Access model

The admin surface needs strong role separation.

### Super Admin

Can access:

- all panel registration
- all tenant controls
- package/module management
- publishing controls
- global defaults
- governance and AI control surfaces

### Company Owner/Admin

Can access:

- company-scoped dashboards
- company data resources
- publishing assigned to their tenant
- approvals relevant to their company
- app shell settings allowed by package

### Manager / Operator

Can access:

- operational dashboards
- work queues
- specific approvals
- restricted settings

### Finance / Office roles

Can access:

- money/admin specific surfaces
- limited cross-domain views

## PWA relationship

The admin surface may itself be installable as a PWA in selected cases, but its primary job is to manage the suite and publish the other app surfaces.

That means the admin panel should include:

- app profile management
- manifest/theme defaults
- role-specific app assignment
- installation and publishing status
- push and notification settings

It should not become the field app simply because it can be installed.

## Build order

The admin surface should be built in this order:

1. Filament base integration
2. panel tree definition
3. role-based access wiring
4. Super Admin shell
5. Command Centre shell
6. base dashboards and shared widgets
7. module resource integration
8. approvals/governance views
9. app publishing controls
10. telemetry and health surfaces

## Definition of done

The Filament admin surface is complete enough for the current phase when:

- the system has a stable multi-panel control plane
- super admin can manage tenants, packages, modules, and app surfaces
- business operators can work through dashboards and resources without custom admin sprawl
- Titan Zero outputs can be reviewed and approved without embedding orchestration in UI code
- new modules can plug into the admin shell through resources, widgets, and pages without redefining the platform


## Implementation additions

## 11. Panel-provider expectation

The current blueprint direction expects dedicated Filament panel providers for admin and user shells. The admin provider should own:

- panel identity and route prefix
- global middleware/guards
- theme and brand registration
- top-level navigation groups
- shared admin widgets/pages
- plugin registration for enabled modules

## 12. Governance surfaces to include

The admin shell should explicitly include non-business pages for:

- queue health
- signal approval backlog
- AI/provider configuration visibility
- package/module install state
- PWA publish/install settings
- communications channel health
- audit/replay/doctor results

## 13. Build rule

Use Filament for presentation and control only. Any create/update/approve action initiated in admin should route through module requests, actions, services, policies, jobs, and events rather than inline closures.


## Additional implementation anchors

### Panel families under admin control
The admin side should manage the suite rather than collapse it. It should be able to supervise:
- Super Admin
- Command Centre
- Money control surfaces
- app publishing and assignment
- module/package visibility
- AI supervision and approval queues

### PWA publish overlap
The admin surface should be able to configure which panels become installable app surfaces, but it should not become the runtime shell for every role. Publishing is an admin concern; execution remains role-surface specific.

### Health-first operator tooling
A mature admin plane should include route health, provider binding health, queue health, AI/provider health, and publish/install health. This is especially important in a modular Worksuite stack where silent registration drift creates real product failures.
