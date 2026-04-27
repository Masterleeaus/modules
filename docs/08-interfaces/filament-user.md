# Filament User Surface

## Purpose

The Filament user surface is the role-specific operational shell for people who use the system to do work rather than manage the platform itself.

It is the user-facing counterpart to the Filament admin surface.

Where the admin surface controls the system, the user surface lets different roles operate inside it.

## What it owns

The user surface owns:

- role-specific app panels
- daily operational workflows
- lightweight dashboards
- structured forms and task flows
- entity views and worklists
- action shortcuts
- mobile/PWA-capable role apps

It does **not** own:

- platform-wide administration
- tenant/package control
- deep AI orchestration
- core module business rules
- offline engine internals

## Position in the architecture

The user surface belongs to the UI shell layer.

It sits between:

- **domain modules**, which contain the real data rules and actions
- **Titan Zero**, which provides orchestration, context-aware assist, and proposals
- **PWA/runtime infrastructure**, which makes chosen panels installable and mobile-friendly

The user panel is therefore a work surface, not a logic engine.

## Why a separate user surface matters

Without a dedicated user surface, the product tends to collapse into one of two bad shapes:

1. a bloated admin panel that workers and customers are forced to use
2. a chat-only shell with poor structured workflows

A proper user surface gives each role the correct balance of:

- structure
- speed
- visibility
- mobile suitability
- conversational augmentation

## Core principle

The user panel should be **role-shaped**, not system-shaped.

That means each panel should feel like an app for a specific kind of work, not a mirror of the whole database.

## Main user-facing panel families

### 1. Field Worker app

This is a mobile-first operational panel.

It should focus on:

- today’s jobs
- schedule / next task
- job details
- checklist completion
- proof capture
- notes/issues
- site access memory
- quick communication
- status changes

### 2. Command mobile / manager app

This is a slim oversight app.

It should focus on:

- live alerts
- team status
- approvals
- quick dispatch context
- problem escalation
- AI recommendations relevant to operations

### 3. Money app

This is a finance-first user panel.

It should focus on:

- invoice status
- overdue items
- quote review
- payment tracking
- margin/cost summaries
- action prompts for follow-up

### 4. Customer portal app

This is a self-service or customer-assist panel.

It should focus on:

- bookings and service history
- quotes and approvals
- invoices and payments
- document/forms access
- support/help entry points
- customer-side assistant interaction

### 5. Chat OS workspace

This is the hybrid user surface where conversational interaction becomes the primary interface.

It should focus on:

- chat threads
- structured output rendering
- approvals
- action cards
- entity side panels
- command-style workflows

## Relationship to chat-first UX

The user surface is where the agreed “chat-first but not chat-only” doctrine becomes practical.

### Correct model

- user begins in a role app
- user can converse with Titan Zero in context
- Titan Zero returns narrative + structured actions
- user hands off from chat into forms, cards, tables, approvals, or entity pages
- completed structured actions return to the conversation timeline when relevant

### Wrong model

- forcing every task through chat
- hiding all forms behind prompts
- making structured workflows impossible without the assistant
- treating the user surface as only a CRUD panel

## Layout rules

The user surface should be simpler and more task-focused than the admin surface.

### Header

Should include:

- role app title
- tenant context if needed
- notifications
- quick actions
- optional assistant trigger

### Navigation

Should be narrow and role-specific.

Examples:

- Today / Jobs / Checklists / More
- Home / Money / Follow-ups / Reports
- Home / Chat / Help / Profile

### Main canvas

Should prioritize:

- task lists
- action cards
- schedule views
- compact widgets
- mobile-fit detail screens

### Assistant surface

Should be contextual rather than dominant unless the panel is the dedicated Chat OS workspace.

## Mobile and PWA behavior

The user surface is where Filament’s PWA potential matters most.

Panels like Field Worker, Command mobile, and Portal should be designed for:

- installation on mobile home screen
- touch-first interactions
- constrained navigation depth
- offline-aware forms and queues where supported
- simple bottom-nav structures
- limited but meaningful notifications

This is one reason the user surface must be separate from the admin control plane.

## Resource design rules

User-facing resources should be narrowed to the role’s actual tasks.

### Good user resource shape

A field worker sees:

- assigned jobs only
- site notes and access details
- checklist tasks
- proof upload
- issue reporting

A finance user sees:

- invoices, quotes, payments, reminders
- limited customer/service context

A customer sees:

- only their bookings, documents, approvals, and payments

### Bad user resource shape

- exposing generic admin CRUD for everything
- showing irrelevant settings
- showing entire cross-domain datasets to narrow roles
- mirroring the admin menu tree

## User panel relationship to modules

The user surface consumes module actions and data through stable interfaces.

Examples:

- CompleteVisitAction
- SubmitProofAction
- ApproveQuoteAction
- RecordPaymentAction
- ReportIssueAction

The panel should call these actions through forms, buttons, and workflows.

It should not recreate these business rules in panel callbacks.

## User panel relationship to Titan Zero

Titan Zero in the user surface should behave like a contextual co-pilot.

Examples:

- summarize today’s workload
- explain a site’s special access rules
- draft a follow-up based on account state
- suggest a next action when a task stalls
- surface approvals needing attention

The user surface should provide the current context to Titan Zero, including:

- user role
- current entity or task
- current panel/app
- company/tenant
- recent events or state

Titan Zero then returns:

- concise answer
- structured cards/tables/charts when needed
- suggested next actions
- approval requests or warnings

## Conversation and structure balance

The user surface should always support two directions:

### Structured to conversational

A user opens a task and asks for help.

### Conversational to structured

A user asks Titan Zero to perform or prepare an action, then lands in a refined screen to confirm or complete it.

This keeps the system flexible without becoming ambiguous.

## Visual doctrine

The user surface should feel lighter and more immediate than the admin surface.

### Desired characteristics

- fast comprehension
- low cognitive load
- clear hierarchy
- touch-friendly density
- role-specific language
- short paths to action

### Avoid

- admin-like overload
- giant nav trees
- many nested tabs for mobile-first panels
- settings-heavy screens in user apps
- exposing back-office complexity directly

## Initial user-surface build order

1. define role apps and panel boundaries
2. build Field Worker shell
3. build Command mobile shell
4. build Money shell
5. build Customer Portal shell
6. build Chat OS workspace shell
7. connect contextual assistant entry points
8. wire app-specific resources and actions
9. apply PWA installability to selected apps
10. refine handoff between chat and structured actions

## Success criteria

The Filament user surface is working as intended when:

- each role sees an app shaped for their work
- the apps feel like a suite, not copies of the admin panel
- users can move naturally between task views and conversational assist
- mobile-first panels are concise and installable where needed
- modules remain the source of truth for actions and rules
- Titan Zero augments decisions without absorbing the whole UI layer

## Long-term role

Over time, the user surface becomes the family of role apps across the platform:

- worker app
- command app
- money app
- portal app
- chat workspace

The Filament user surface is therefore not just “the front end.” It is the structured operational suite that sits between the underlying engines and the people doing the work.


## Implementation additions

## 11. User panel shaping rules

Each user-facing Filament panel should be shaped around work, not around database completeness.

### Good

- Today board for field worker
- approvals + risk view for command user
- invoice chase and cash view for money user
- lightweight history and documents for portal user

### Bad

- exposing every resource in the same navigation tree
- copying super-admin settings into user panels
- making operators navigate through setup screens to do daily work

## 12. PWA overlap

Where a user panel is promoted into an installable PWA shell, the panel must gain:

- manifest/service-worker registration
- offline/update indicators
- reduced navigation depth
- large mobile-safe actions
- read/write behavior aligned to node/offline capability

## 13. Chat augmentation rule

The user panel should be chat-augmentable, not chat-replaced. Structured work remains available, while chat provides:

- lookup
- summarization
- draft actions
- next-step suggestions
- approvals where appropriate


## Additional implementation anchors

### Role-shaped panel doctrine
The user-facing Filament surfaces should be split into role-appropriate apps rather than one generic user panel. Recommended early families:
- Command
- Field Worker
- Money
- Portal
- Chat Workspace

### PWA suitability
User surfaces are the best candidates for installable PWAs because they support repeated operational use. The worker, portal, and mobile-command shells should therefore be designed with installability, notification flow, and lightweight navigation in mind.

### Chat coexistence
The user surface should not compete with the conversational OS. It should provide structured work areas that conversation can enter, summarize, and launch actions within. Chat remains the coordination layer; the user panel remains the structured execution layer.
