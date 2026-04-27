# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Navigation shell, panel switching, mode routing, and cross-surface movement across admin, PWA, and chat-first interfaces
Status: Draft v1
Depends On: Core Platform, Dashboard System, Filament Panels, Modules + Extensions, PWA + Nodes, Workflows
Consumed By: Super Admin, Command Centre, Field Worker, Money, Portal, chat workspace, future voice surfaces
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define the navigation shell for Titan so users can move through the system cleanly across panels, modes, entities, and conversations without losing context or feeling like each app is a different product.

## 2. Why it exists

Titan is not a single screen application. It includes role-specific panels, installable PWAs, conversational workspaces, mobile flows, and admin control surfaces. Without a formal navigation shell, every module will invent its own movement rules and the suite will fragment. This document defines the operating shell above module screens.

## 3. Core responsibilities

- define how users enter, switch, and exit surfaces
- separate panel navigation from mode navigation and entity navigation
- preserve conversational and operational context during movement
- support mobile-first and desktop-first shells without changing the information architecture
- keep navigation aligned with permissions, tenancy, and role-specific app surfaces

## 4. Navigation layers

Titan navigation has four layers.

### 4.1 Surface layer

Which app or panel the user is in.

Examples:
- Super Admin
- Command Centre
- Field Worker
- Money
- Portal
- Chat Workspace

### 4.2 Mode layer

Which operational lens the user is using within a surface.

Preferred modes:
- Jobs Mode
- Comms Mode
- Finance Mode
- Admin Mode
- Social Media Mode

### 4.3 Entity layer

Which business object the user is focused on.

Examples:
- customer
- site
- service job
- invoice
- worker
- workflow item

### 4.4 Task layer

Which action or step the user is performing right now.

Examples:
- create draft quote
- reschedule visit
- approve automation
- capture proof
- send follow-up

## 5. Shell doctrine

The shell must stay stable while the center changes. This is how Titan keeps the feeling of one operating system.

Stable elements:
- global frame
- panel identity
- tenant identity
- main navigation logic
- command entry
- alerts and quick actions

Changeable elements:
- center workspace
- side panes
- action trays
- chat thread contents
- entity details
- mode-specific widgets

## 6. Desktop navigation model

Desktop shells should use a three-zone pattern.

### Left rail

Persistent navigation.

Contains:
- app switcher or panel switcher
- main mode navigation
- major entity groups
- saved views or smart queues

### Center workspace

Primary task area.

Contains:
- dashboards
- tables
- boards
- forms
- chat workspace
- timelines

### Right pane

Context and assistance.

Contains:
- Titan Zero conversation
- entity context
- approvals
- quick actions
- related activity

## 7. Mobile navigation model

Mobile surfaces should use a lighter shell.

Recommended pattern:
- top bar for title and alerts
- bottom nav for highest-frequency destinations
- stacked pages for deeper tasks
- slide-over or inline assistant access
- focused action sheet for task execution

### Example bottom nav

#### Field Worker
- Today
- Jobs
- Chat
- Alerts
- More

#### Money
- Overview
- Invoices
- Payments
- Chat
- More

#### Portal
- Home
- Bookings
- Bills
- Help
- Account

## 8. Panel switching

Users with access to multiple panels should switch panels deliberately, not accidentally.

### Rules

- panel switch keeps tenant context where valid
- panel switch may carry selected entity if user has access in target panel
- panel switch should preserve recent history and return path
- panel switch must respect permissions and package access

### Example

Owner moves from Command Centre to Money while keeping current company and selected overdue invoice queue.

## 9. Mode switching

Modes are not the same as panels. Panels are app shells; modes are operational lenses inside a shell.

### Rules

- mode switch changes labels, widgets, and shortcuts more than frame
- same entity may look different by mode
- mode switch should be fast and reversible
- mode should influence what Titan Zero assumes in context packs

## 10. Entity-aware navigation

Entity navigation must stay universal.

Required entity affordances:
- open entity from list, card, chat, or alert
- show breadcrumb or trail of context
- allow quick jump to related entities
- keep entity side panel available where useful

Example chain:
- customer
- site
- active agreement
- next visit
- latest invoice

## 11. Conversational navigation

Chat does not replace navigation; it coexists with it.

### Chat can do
- open entities
- change mode
- suggest routes
- pin context
- create drafts and tasks

### Chat should not do alone
- hide where user is
- replace all primary navigation
- trap the user in one thread with no return path

This keeps the chat workspace inside the shell rather than above it.

## 12. Command entry layer

Every major shell should support a quick command layer.

Capabilities:
- search entities
- jump to surfaces
- launch create flows
- trigger approved actions
- open saved queues
- invoke AI help in current context

This command layer should be consistent across desktop panels and simplified on mobile.

## 13. Navigation object model

A reusable navigation item should support:
- `id`
- `label`
- `icon`
- `route` or `action`
- `mode`
- `panel`
- `permission`
- `tenant_scope`
- `badge`
- `children`
- `priority`
- `visibility_rules`

This allows package-aware and role-aware shell assembly.

## 14. Notifications and interrupts

The shell must define where interrupts appear.

### Low severity
- passive badges
- inbox counters
- footer notices

### Medium severity
- toasts
- queue indicators
- highlighted widgets

### High severity
- command-centre alerts
- top-bar warning states
- modal confirmation for destructive or risky items

Interrupts must route the user to the right task layer without disorienting them.

## 15. Package and tenant shaping

The shell should be assembled from permissions, package access, and tenant settings.

Examples:
- company on starter plan sees Command Centre + Portal only
- field-only users see Field Worker panel only
- finance role sees Money plus selected command alerts
- social mode appears only if package enabled

## 16. Voice-aware navigation

Voice should operate on top of this shell, not beside it.

Examples:
- “Open overdue invoices.”
- “Switch to jobs mode.”
- “Take me back to the Smith Medical site.”
- “Open today’s first booking.”

Voice maps to the same navigation objects and routes used by click and touch.

## 17. Cross-surface continuity

The same user should be able to move from desktop to PWA without losing mental model.

Continuity anchors:
- same mode names
- same entity names
- same status labels
- same action verbs
- similar iconography
- similar alert semantics

The mobile shell may be smaller, but it must not speak a different language.

## 18. Recommended first implementation

### Desktop
- Super Admin panel shell
- Command Centre shell
- Money shell
- chat workspace pane

### Mobile/PWA
- Field Worker shell
- Portal shell
- compact Command shell

### Shared
- command palette
- entity deep links
- mode switcher
- tenant switcher where appropriate

## 19. Anti-patterns

Avoid:
- each module owning its own unrelated menu logic
- different names for the same business object across surfaces without translation rules
- separate mobile app information architecture that contradicts desktop shell
- hiding current context when switching from dashboard to chat or form
- routing critical tasks only through chat with no visible system path

## 20. Done criteria

This document is complete when a developer can build multiple Titan panels and PWAs that feel like one suite, with stable movement rules from dashboard to chat to entity view to action flow.


## 11. Named-route doctrine

The shell should move users with named routes and panel-aware route generation, not raw path strings. This keeps navigation resilient when modules, prefixes, or panel structures evolve.

### Navigation sources

- panel providers
- module route names
- package/module visibility
- mode router
- entity context

## 12. Surface switching

Because Filament supports multiple panels, Titan should treat panel switching as a first-class shell behavior.

### Expected switches

- Super Admin -> Command Centre
- Command Centre -> Money
- Command Centre -> Chat Workspace
- Command Centre -> Field Worker preview or dispatch detail
- Portal -> support/chat handoff

A switch should preserve tenant, role, and relevant entity context where safe.

## 13. Offline-aware shell behavior

PWA-capable shells need navigation states for:

- online
- degraded/offline read-only
- offline with queued local actions
- update available

Navigation should expose those states visibly so users understand whether a destination is fully live, locally cached, or waiting to sync.


## 13. Suite-of-apps navigation

Titan should behave like a coordinated suite, not a monolith pretending to be one panel. The shell therefore needs first-class support for moving between:
- Super Admin
- Command Centre
- Field Worker
- Money
- Portal
- Chat Workspace

The switch between these surfaces must preserve tenant, role, and current entity context where safe to do so.

## 14. Named-route doctrine

Navigation should prefer stable named routes and panel-aware route registration rather than raw paths scattered across views. This keeps cross-surface movement resilient when panels, prefixes, or role-specific shells evolve.

## 15. Mobile shell reductions

Navigation on mobile PWAs must intentionally reduce choice density. Field and Portal shells should favor:
- bottom-nav or short-tab structures
- action-first entry points
- entity recall from recent context
- alert and queue shortcuts
- fast return to chat or current task

The goal is not to mirror desktop navigation on a smaller screen. The goal is to preserve system meaning while reducing movement cost.
