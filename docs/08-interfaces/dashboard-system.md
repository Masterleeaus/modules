# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Dashboard architecture, command-centre composition, persistent monitoring surfaces, and cross-panel workspace behavior
Status: Draft v1
Depends On: Core Platform, Modules + Extensions, Workflows, AI, Communications, PWA + Nodes
Consumed By: Super Admin, Command Centre, Money, tenant admin, future mobile command shell
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define the dashboard system for Titan so persistent monitoring, approvals, metrics, and navigation work as a coherent operating layer across all panels and app surfaces.

## 2. Why it exists

Titan cannot rely on conversation alone. Operators also need scan-based awareness, dense state visibility, and always-available control surfaces. Dashboards provide the persistent operating picture that conversation cannot hold efficiently over time.

## 3. Core responsibilities

- provide role-specific overview surfaces for super admin, operators, finance, and managers
- surface KPIs, exceptions, queues, approvals, and health indicators
- provide fast navigation into records, workflows, and chat-driven workspaces
- unify dashboard behavior across web panels and mobile command shells
- separate persistent monitoring from ad hoc conversational work without fragmenting the product

## 4. Boundaries

### In scope

- dashboard home pages
- widget composition and grouping
- queue, alert, and status boards
- command-centre summaries
- dashboard-to-chat and dashboard-to-record handoff
- mobile command dashboard reductions

### Out of scope

- domain business rules
- AI orchestration internals
- offline sync engine implementation
- module manifest ownership
- detailed workflow state machine design

## 5. Architecture

## 5.1 Dashboard families

Titan should treat dashboards as a family of related surfaces, not one giant homepage.

### A. Super Admin dashboard

Purpose:

- tenancy health
- package/module visibility
- install/load failures
- system doctor findings
- AI/provider health
- queue and communications infrastructure status

### B. Command Centre dashboard

Purpose:

- operational overview
- approvals and exceptions
- jobs and schedule state
- worker status
- finance risk snapshot
- AI recommendations requiring review

### C. Money dashboard

Purpose:

- cash state and overdue risk
- quote/invoice conversion
- payment pipeline
- collections actions
- margin and cost warnings

### D. Tenant admin dashboard

Purpose:

- day-to-day company health
- upcoming work
- unresolved issues
- staff and customer events
- module-specific KPIs

### E. Mobile command dashboard

Purpose:

- compressed oversight on the move
- alerts
- approvals
- today’s critical metrics
- direct launch into field, money, or communication actions

## 5.2 Widget layers

Dashboards should compose from four widget classes.

### Status widgets

Used for:

- counts
- health states
- SLA timers
- queue length
- active incidents

### Work widgets

Used for:

- pending approvals
- unassigned jobs
- overdue invoices
- unread customer responses
- conflict reviews

### Insight widgets

Used for:

- trend charts
- anomaly highlights
- AI summary cards
- comparative metrics
- recommendation lists

### Navigation widgets

Used for:

- launch points into resources
- open command palette actions
- jump into current workflow contexts
- open chat workspace with preloaded scope

## 5.3 Dashboard composition rules

Each dashboard should answer these questions in order:

1. what needs attention now
2. what is drifting or blocked
3. what should be reviewed or approved
4. what major trends changed
5. where should the operator go next

This keeps dashboards operational rather than decorative.

## 5.4 Relationship to conversation

Dashboards and chat must share context.

Correct pattern:

- dashboard widget opens filtered table, record, or chat thread with preserved scope
- chat recommendation can open a dashboard queue or detailed widget view
- approval taken in dashboard is summarized back into chat thread if the thread initiated it

Incorrect pattern:

- dashboard and chat compute different truths
- approvals visible in one surface but not the other
- widget metrics based on a different query model than action surfaces

## 5.5 Density model

Not every dashboard should be equally dense.

### Dense dashboards

Use for super admin, finance, dispatch, and operator roles.

### Balanced dashboards

Use for tenant owners/managers.

### Reduced dashboards

Use for mobile command and smaller PWA command surfaces.

The data grammar should stay the same even when density changes.

## 6. Contracts

## 6.1 Dashboard input contracts

Dashboards consume:

- tenant and package context
- user role and capability context
- KPI aggregates
- workflow queue state
- approval queue state
- AI summary envelopes
- communications and job health state

## 6.2 Dashboard output contracts

Dashboards produce:

- human scanability
- action launch events
- filtered navigation requests
- approval outcomes
- review context for chat and canvas surfaces

## 7. Runtime behavior

## 7.1 Refresh behavior

Dashboards should distinguish between:

- real-time or near-real-time indicators
- periodic KPI refresh
- manual refresh for expensive views

Not all widgets deserve the same refresh cadence.

## 7.2 Alert discipline

Critical dashboards should prefer actionable alerts over noisy activity streams. A dashboard becomes useless when every minor change is rendered as equal urgency.

## 7.3 Mobile command behavior

The mobile command shell should retain only the highest value widgets:

- urgent alerts
- approvals
- status summaries
- quick launch actions

Detailed analytics should remain one step deeper.

## 8. Failure modes

- **dashboard bloat:** too many decorative widgets, not enough decision support
- **metric drift:** widgets and record views disagree on counts or statuses
- **alert fatigue:** every event becomes a red badge
- **context loss:** launching from dashboard to chat or record drops the current scope
- **role confusion:** same dashboard forced onto every audience

## 9. Dependencies

Upstream:

- platform identity, tenancy, packages, and permissions
- module read models and metrics
- workflow and approval queues
- AI summary and risk outputs
- communications and scheduling state

Downstream:

- panel navigation
- chat workspace
- approval screens
- mobile command shell
- module-specific detail pages

## 10. Open questions

- which dashboards must exist at MVP versus later phases
- which widgets are global versus module-owned
- where saved dashboard layouts should be user-specific or role-specific
- how much mobile command should mirror command-centre web widgets

## 11. Implementation notes

- prefer stable widget contracts over freeform ad hoc queries
- dashboard actions should call the same module actions and policies as other surfaces
- use dashboard widgets for summarization, not business rule ownership
- when a dashboard becomes process-heavy, hand off into a canvas, table, or resource page


## 11. Dashboard sourcing rules

Dashboards should consume approved read models, API resources, and widget DTOs rather than querying ad hoc from inside every widget. This allows the same business state to appear in:

- Filament dashboards
- chat structured blocks
- PWA summary cards
- notification digests

### Preferred sourcing order

1. module read model or service summary
2. API/resource transformer if needed cross-surface
3. widget-specific view model
4. chart/metric adapter

## 12. PWA reductions

Command and field PWAs should not clone desktop dashboards. Instead, each dashboard family needs a reduced mobile projection:

- fewer widgets
- stronger exception focus
- thumb-reachable primary actions
- install/offline/update awareness banners
- queue counts and risk summaries instead of dense tables

## 13. Health and governance widgets

A complete command dashboard needs more than business KPIs. It should also include:

- sync/offline health
- queue backlog
- signal approval backlog
- AI/provider status
- communications delivery failures
- package/module install anomalies


## 12. Dashboard families

The dashboard system should be deliberately split by role and decision horizon.

### Super Admin dashboards
- tenancy health
- package and module visibility
- platform incidents
- queue and node health
- publish status of app surfaces

### Command dashboards
- live ops metrics
- urgent exceptions
- dispatch state
- approvals waiting for action
- AI-recommended next actions

### Money dashboards
- invoices due
- collections risk
- payment sessions
- revenue and margin summaries
- approval-gated money actions

### Mobile command reductions
- alerts
- approvals
- live status changes
- top queues only

## 13. Dashboard to chat handoff

Every important dashboard block should support one or more of these movements:
- open the record
- open the workflow state
- open the related conversation
- ask Titan to summarize or propose next steps
- launch the structured action directly

This prevents dashboards from becoming dead reporting pages and keeps them inside the operating-system model.

## 14. Health and observability surfaces

Dashboards must also include non-business health surfaces because Titan is a system, not just an app. Required visibility includes:
- route and provider health
- queue backlog and failures
- offline sync health
- AI/provider availability
- node/device heartbeat where applicable
- audit and approval backlog
