# 01. System Doctrine

## 1. System intent

The goal is not to bolt AI onto Worksuite. The goal is to turn Worksuite into a **coordinated operating system for service businesses** where:
- the database remains the canonical operational ledger
- modules own business state and workflows
- AI interprets, plans, proposes, and supervises
- PWAs and mobile devices become specialized surfaces over the same governed system
- device nodes perform as much work locally as possible
- the server coordinates, audits, syncs, and enforces tenant-safe rules

This creates a product that feels like a business runs through one living system rather than through separate dashboards, apps, and disconnected automations.

## 2. Core product belief

The most valuable software for service businesses will not be a static dashboard.
It will be a **distributed operational intelligence system** with:
- conversational control
- modular workflow depth
- multiple role-specific surfaces
- device-local execution where possible
- AI approval and safety gates
- a persistent operational memory that survives channels and devices

## 3. Design laws

### 3.1 Business logic belongs in modules and services
Laravel guidance strongly supports keeping controllers thin and shifting validation, authorization, and business logic into form requests, actions, and services. That principle should become a platform rule across Worksuite. Controllers should coordinate requests, not own operational logic.

### 3.2 Modules are the business organs
The Worksuite core should provide tenancy, auth, permissions, settings, package wiring, and common infrastructure. Modules should own domain state and workflows: jobs, sites, dispatch, money, documents, customer messaging, etc.

### 3.3 AI is supervisory, not a hidden side effect
AI should not silently mutate critical business records. It should:
- interpret intent
- gather context
- propose actions
- request approval when confidence/risk demands it
- call approved tools
- write auditable traces

### 3.4 PWAs are first-class products, not responsive leftovers
Each PWA should be designed as a role-specific operating surface:
- Titan Omni: customer + communications + conversation shell
- Titan Portal: staff/admin portal
- Titan Command: owner/manager command surface
- Titan Go: field worker/cleaner mobile app
- Titan Money: money, invoicing, and collection app

These are not separate businesses. They are coordinated shells over the same tenant-safe system.

### 3.5 Devices are nodes
A phone, tablet, desktop, kiosk, or browser session should be treated as a node with:
- identity
- capabilities
- sync state
- optional local model/runtime
- local cache and task queue
- offline behavior

### 3.6 Tenant boundary is sacred
Every operational module must be scoped by `company_id` as the tenant boundary. `user_id` is actor/ownership context, not the tenant boundary. No AI workflow, API endpoint, queue job, or local sync process is allowed to cross tenant scope.

### 3.7 The system must degrade gracefully
The system should continue functioning when:
- internet is poor
- AI provider is unavailable
- queue backlog exists
- mobile device is offline
- channel provider fails
- one subsystem is stale

That requires deliberate local caching, replay, sync envelopes, and background recovery.

## 4. System personality

The system should feel:
- conversational
- operational
- calm
- explainable
- fast
- modular
- auditable
- privacy-preserving

It should not feel like:
- a black box automation toy
- a noisy chatbot wrapper
- a CRUD app with AI buttons scattered around it

## 5. Engineering doctrine adapted from the Laravel sources

### 5.1 Thin controllers
Use form requests, actions, DTOs, and services so controllers remain orchestration layers.

### 5.2 Clear route architecture
Use named routes, route groups, middleware groups, resource controllers where appropriate, and API/web separation. This is critical once the system spans admin, user, PWA, API, and node surfaces.

### 5.3 Service-container driven design
Use interfaces and provider bindings so AI providers, sync transports, channel drivers, local/remote execution paths, and model routers can be swapped cleanly.

### 5.4 Performance by default
Prefer eager loading, select only needed columns, remove dead packages, cache aggressively where safe, queue long-running work, and monitor query behavior.

### 5.5 Testability is architecture
The PDFs reinforce that clean separation improves testing. In this system, tests must exist at:
- module feature level
- tenant-boundary/security level
- workflow level
- API level
- sync/offline replay level
- AI approval boundary level

## 6. Product doctrine

### 6.1 Chat is the entry layer, not the only layer
Conversation should be the top interaction surface, but structured interfaces must appear when the task demands precision.

### 6.2 Every action should be reducible to a tool call
If the AI can do something, there should be a module-owned tool/API contract for it.

### 6.3 Every tool call must be explainable
The system should be able to say:
- what it saw
- what it inferred
- what rule/tool it used
- what it changed
- what it refused to do

### 6.4 Memory must be scoped
Memory is not one blob. The system needs:
- company memory
- user preference memory
- site/job memory
- channel memory
- device/node memory
- AI operational memory

### 6.5 The platform must be buildable by agents
Structure, contracts, manifests, route conventions, and module interfaces must be explicit enough that GitHub Copilot or other agents can build safely without improvising the architecture.

## 7. Final doctrine sentence

Worksuite should evolve into a **tenant-safe, AI-supervised, modular business operating system with multiple PWA/device shells, where modules own business state, AI owns interpretation and orchestration, and nodes execute locally wherever possible**.
