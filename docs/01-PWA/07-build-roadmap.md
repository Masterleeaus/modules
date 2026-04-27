# 07. Build Roadmap

## Phase 1 — Stabilize the platform contract

### Goals
- make modules install/load reliably
- make package visibility trustworthy
- harden tenant boundaries
- standardize route and API contracts
- clean controller/service separation

### Outputs
- module contract documentation adopted
- idempotent migration sweep complete
- `modules` + `module_settings` + package visibility logic stabilized
- route names and route groups normalized
- super admin and user sidebar wiring standardized
- API response envelope standard

## Phase 2 — Establish AI toolability

### Goals
- make modules callable as tools
- create AI execution registry
- make approval flow visible

### Outputs
- AI tool manifests for target modules
- context snapshot builder
- approval queue and run log
- first operational tools: create note, schedule visit, draft quote, summarize account, send message draft

## Phase 3 — Build the command plane

### Goals
- create a strong operator surface for owners/admins
- use Filament where it adds speed and clarity

### Outputs
- command dashboard
- approval center
- AI operations console
- package/module health screens
- queue and node health views
- audit/run log viewers

## Phase 4 — Launch core PWAs

### Goals
- build role-specific shells, starting with the most operationally valuable

### Suggested order
1. Titan Omni
2. Titan Go
3. Titan Command
4. Titan Money

### Outputs
- installable shells
- auth/session integration
- notification pipeline
- shared API client + sync manager

## Phase 5 — Offline and node runtime

### Goals
- make field work resilient
- make device state first-class

### Outputs
- local cache strategy
- sync endpoints and cursors
- pending mutation replay
- stale/conflict handling
- proof-of-service attachment queue

## Phase 6 — Communications spine

### Goals
- unify inbound and outbound communication channels
- let AI participate safely in communication flows

### Outputs
- channel adapters
- communication event model
- message thread normalization
- customer memory hooks
- AI-draft + approval flow for outbound messages

## Phase 7 — Money and recovery intelligence

### Goals
- make the system not only operationally useful, but directly revenue-improving

### Outputs
- payment session engine
- invoice follow-up automations
- overdue collections assistant
- customer risk scoring
- manager approval controls for sensitive money actions

## Phase 8 — Vertical overlays

### Goals
- create specialized versions for cleaning, field service, medical equipment, Airbnb, etc.

### Outputs
- vertical templates
- terminology overlays
- checklist packs
- domain-specific AI prompt packs
- module presets and package presets

## Phase 9 — Local intelligence and edge patterns

### Goals
- push more capability to devices where privacy and responsiveness benefit

### Outputs
- local summarization/transcription where appropriate
- node capability negotiation
- optional edge relay patterns
- stronger offline autonomy for field shells

## Phase 10 — Differentiation polish

### Goals
- turn the system from “good architecture” into a market-defining product

### Outputs
- continuous cross-shell context handoff
- persistent business memory
- explainable AI operations
- smooth voice + chat + action transitions
- beautiful, calm, role-specific experiences

## Delivery priorities

### Highest ROI first
1. package/module visibility correctness
2. tenant-safe API + tool contract
3. command/admin AI control plane
4. Titan Go field shell
5. Omni communications shell
6. money automation

## Avoid these traps

- building isolated mini apps with duplicated logic
- putting business logic directly inside Filament resources
- treating chat as the only UI
- letting AI bypass services and validations
- making offline support an afterthought
- designing modules without API/PWA contracts

## Final roadmap sentence

The fastest path to a revolutionary system is not to build everything at once; it is to lock the platform contract, make the modules toolable, then launch a small set of powerful role-specific shells over one governed operational core.
