# 02. Reference Architecture

## 1. Top-level stack

The target system has five coordinated layers.

### Layer A — Experience shells
User-facing surfaces:
- Web admin / Worksuite base UI
- Filament control surfaces
- Titan Omni PWA
- Titan Portal PWA
- Titan Command PWA
- Titan Go PWA/mobile
- Titan Money PWA/mobile
- Channel surfaces: WhatsApp, email, Messenger, Telegram, SMS, voice

### Layer B — Application core
The shared business platform:
- Laravel / Worksuite core
- tenancy, auth, permissions, settings, packages
- module registry
- REST + internal API
- queue, cache, scheduler, events

### Layer C — Domain modules
Operational modules such as:
- CRM
- Service / Jobs / Visits / Checklists / Inspections
- Dispatch / Routes / Timesheets / Assets / Inventory
- Quotes / Invoices / Payments / Money recovery
- Documents / Approvals / Customer communication
- CMS / Promotions / PWA surfaces
- AI tools and orchestration modules

### Layer D — AI and governance spine
AI control and orchestration:
- Titan Zero as primary conversational intelligence
- AEGIS/governance approval layer
- tool registry + context compiler
- memory and context snapshots
- approval queue / processing queue
- audit logs and run history

### Layer E — Node and sync layer
Distributed runtime:
- browser PWA local cache
- mobile device local store
- optional on-device inference
- local task queue
- sync transport
- replay/recovery / spool behavior

## 2. Worksuite + Filament division of responsibility

### Worksuite should remain the operational backbone
Owns:
- tenancy
- auth
- permissions
- packages
- module registry
- domain data
- business workflows
- queue/events/scheduler
- API foundation

### Filament should become the control-plane shell
Owns:
- fast admin surfaces
- operator consoles
- builder tools
- internal tooling
- visual configuration panels
- data-rich command screens

### Why this split matters
Filament is powerful for internal control surfaces, but it should not become the canonical owner of business logic. Worksuite modules should remain the source of truth for workflows and state.

## 3. System flow model

```text
User / Device / Channel
      ↓
Shell (Web, PWA, Voice, Chat, Mobile)
      ↓
Intent + Context Collection
      ↓
Titan Zero / AI Orchestrator
      ↓
Governance / Approval / Policy Layer
      ↓
Module Tool/API Contract
      ↓
Domain Service / Action
      ↓
Database + Events + Notifications + Sync
      ↓
Updated UI / Channel reply / Device sync
```

## 4. Core platform responsibilities

The core platform should provide:
- company and user identity
- permission and policy framework
- route registration contracts
- module registration and discovery
- stable response envelopes
- API versioning
- event dispatching
- queue orchestration
- cache strategy
- settings storage
- package/module visibility rules

The core must **not** own module-specific business logic.

## 5. Module architecture pattern

Each serious module should have:
- migrations
- models/entities
- form requests
- controllers for web/API surfaces
- services/actions
- policy/permission definitions
- event emitters/listeners
- menu/sidebar integration
- package visibility registration
- AI tool manifest
- optional PWA manifest
- optional CMS manifest
- optional signal lifecycle manifest

## 6. API model

The system needs three API classes.

### 6.1 Public tenant APIs
For mobile/PWA/channel clients.
Examples:
- `/api/v1/jobs`
- `/api/v1/sites`
- `/api/v1/checklists`
- `/api/v1/invoices`

### 6.2 Internal orchestration APIs
For AI/tool execution and cross-module actions.
Examples:
- `/internal/v1/tools/create-job`
- `/internal/v1/tools/schedule-visit`
- `/internal/v1/context/snapshot`

### 6.3 Node sync APIs
For replay, delta sync, offline state, acknowledgements.
Examples:
- `/sync/v1/pull`
- `/sync/v1/push`
- `/sync/v1/ack`

## 7. Data ownership model

### Core tables own:
- companies
- users
- roles/permissions
- settings
- packages
- module registry
- auth/session level state

### Modules own:
- domain records
- module settings
- workflow states
- approval records
- templates
- operational artifacts

### AI owns metadata, not business truth
AI records may include:
- prompts
- context snapshots
- tool proposals
- approval states
- run logs
- scoring/confidence

But AI should not be the only source of truth for jobs, invoices, customers, etc.

## 8. Event architecture

Events should be first-class.

Examples:
- `job.created`
- `visit.scheduled`
- `checklist.completed`
- `invoice.overdue`
- `payment.received`
- `customer.message.received`
- `approval.requested`
- `approval.granted`
- `node.sync.completed`

Events support:
- side effects
- analytics
- automation
- notification fanout
- debugging
- replay/recovery

## 9. PWA shell model

Each shell should share a common app substrate but have a different role-specific composition.

### Common substrate
- auth/session handling
- local cache
- API client
- sync engine
- notification layer
- command palette / chat surface
- feature flag loader
- offline state manager

### Shell-specific composition
- **Omni**: communication-first, customer-facing, conversation-centric
- **Portal**: staff/admin work management
- **Command**: owner operations, high-level dashboards, approvals, monitoring
- **Go**: field execution, proof of service, checklist flow, offline-first
- **Money**: invoices, collections, payment sessions, cashflow surfaces

## 10. Identity and boundary model

Every request should carry:
- company context
- user identity
- device/node identity when relevant
- session/channel context
- permission context

This allows the system to differentiate:
- company-wide actions
- personal user actions
- channel actions
- device-specific state

## 11. Sync model

Nodes should maintain:
- local read models
- local pending mutations
- conflict status
- sync watermark/checkpoint
- last successful full refresh
- capability declaration

Sync should be:
- incremental when possible
- replay-safe
- idempotent
- tenant-scoped
- auditable

## 12. AI orchestration model

The AI layer should:
1. parse intent
2. gather context
3. choose a mode
4. compile tool options
5. evaluate risk
6. request approval if needed
7. execute allowed action through modules
8. summarize result
9. log the run
10. emit events for observers and nodes

## 13. Operational modes

Recommended explicit modes:
- Jobs Mode
- Comms Mode
- Finance Mode
- Admin Mode
- Social/Marketing Mode
- Build/Studio Mode

This avoids one muddy assistant doing everything the same way.

## 14. Architectural differentiator

Most SaaS systems are:
- one web app
- one backend
- one API
- one permission layer

This system should instead be:
- one operational graph
- many coordinated shells
- AI-mediated tool execution
- modular business domains
- device-aware runtime
- tenant-safe and audit-first

## 15. Final architecture sentence

The reference architecture is a **Laravel/Worksuite core with Filament control surfaces, modular domain services, AI supervision, and multiple synchronized PWA/device shells that all operate against one tenant-safe operational graph**.
