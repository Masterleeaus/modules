# 03. Worksuite + Filament + PWA Model

## 1. Why combine Worksuite and Filament

Worksuite gives you the business backbone. Filament gives you a fast internal control-plane builder. PWAs give you role-specific execution surfaces. Combined properly, they can become a system that is:
- faster to build than custom everything
- more operationally deep than a pure admin panel
- more usable in the field than a web-only SaaS
- more adaptable to AI orchestration than a dashboard-centric product

## 2. Recommended role split

### Worksuite = system of record
Use Worksuite for:
- company and package lifecycle
- users, roles, permissions
- module registration
- operational tables and workflows
- queues, scheduler, events, notifications
- API and business rules

### Filament = system of control
Use Filament for:
- internal admin dashboards
- module builders
- AI command consoles
- visibility and health screens
- system doctor tools
- config and review surfaces
- ops and finance command centers

### PWA shells = system of execution
Use PWAs for:
- field workflows
- customer interactions
- approvals on the move
- offline tasks
- notifications and task response
- lightweight continuous use throughout the day

## 3. Surface map

### Web super admin
Purpose:
- packages
- pricing
- module enablement
- tenancy health
- marketplace/registry behavior
- system-wide defaults

Best stack:
- Worksuite base pages + Filament control modules where they improve speed

### Web tenant admin
Purpose:
- company settings
- staff administration
- module configuration
- job and finance management
- AI supervision

Best stack:
- Worksuite operational pages + Filament operator panels for dense management tasks

### Omni PWA
Purpose:
- chat-first business control
- customer communication
- lead and follow-up flows
- lightweight mobile actions

Best stack:
- PWA shell over internal APIs and AI tools

### Titan Go PWA
Purpose:
- worker tasks
- checklists
- proof-of-service
- site memory
- offline-first execution

### Titan Command PWA
Purpose:
- live ops view
- approvals
- maps, queue, alerts, dispatch

### Titan Money PWA
Purpose:
- payment sessions
- invoices
- overdue collections
- cost/revenue surfaces

## 4. Shared component strategy

Each shell should reuse the same component families where possible.

### Shared infrastructure components
- auth/session client
- API client
- local store
- sync engine
- notification manager
- feature flag loader
- AI command entry
- attachment uploader
- activity feed

### Shared business widgets
- customer card
- site card
- job card
- schedule block
- invoice card
- approval card
- message thread
- checklist renderer

### Shared AI widgets
- command composer
- suggestion chips
- confidence badge
- approval request card
- result summary card
- context snapshot viewer

## 5. Why Filament should not replace Worksuite modules

Filament is extremely useful, but it should not absorb the domain model and become the only place business logic lives.

Bad outcome:
- domain logic split between Worksuite pages and ad hoc Filament resources
- duplicated auth/policy patterns
- module APIs not reusable by mobile/PWA shells
- AI tools calling UI code instead of service code

Better outcome:
- Filament resources call module services/actions
- same services feed web, PWA, API, AI, and automation
- module remains portable and testable

## 6. The right pattern for AI-assisted screens

Every advanced control screen should have three regions:

### A. State region
Canonical business data, tables, metrics, records.

### B. Action region
Buttons, forms, workflow transitions, approvals.

### C. Intelligence region
AI summary, risks, next-best actions, command bar, recommendations.

This pattern works in:
- Filament admin panels
- Worksuite Blade pages
- PWA shells

## 7. Packaging strategy

Packages should not merely toggle random modules. They should define:
- visible modules
- allowed shells
- AI capability tier
- channel support
- automation depth
- local/offline capability flags

Example package structure:
- Solo
- Team
- Pro
- Omni
- Money
- Command
- Vertical overlays

## 8. PWA architecture pattern

### Frontend composition
- installable shell
- route-level lazy loading
- local storage/indexed DB
- background sync
- push notifications
- optimistic updates where safe

### Backend support
- stable API envelopes
- versioned endpoints
- capability negotiation
- sync tokens/checkpoints
- attachment handling
- offline replay safety

## 9. UI design rule

The system should not make users keep mentally switching between ten unrelated UI metaphors.

Preferred hierarchy:
- one command language
- one entity language
- one design system
- one approval model
- multiple shells tuned by role

## 10. What makes the system feel advanced

It should feel like:
- the same assistant follows work across web, mobile, and channel surfaces
- context persists when a manager starts on desktop and finishes on phone
- field actions update command center instantly when online, or replay safely later when offline
- modules expose clean capabilities instead of isolated screens

## 11. Recommended shell sequence

Build in this order:
1. Command/admin web foundation
2. Omni conversational shell
3. Titan Go field shell
4. Money shell
5. deeper owner/command shell polish

That sequence gives operational depth before cosmetic expansion.

## 12. Final model sentence

Worksuite should supply the business engine, Filament should supply the control plane, and PWAs should supply the role-specific execution surfaces, all bound together by the same AI, API, and tenant-safe module contracts.
