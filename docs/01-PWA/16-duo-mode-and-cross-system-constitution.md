# 16. Duo Mode and Cross-System Constitution

## Purpose

This document defines how **Worksuite**, **Titan Studio**, and the shared Titan AI spine must cooperate without collapsing into one confused system.

The key rule is simple:

- **Worksuite** is the operational system of record.
- **Titan Studio** is the intelligence, marketing, and advisory system.
- **Titan Zero** is the authority layer for AI decisions and approval.
- **Titan Core** is the execution router for approved AI tasks.
- **Titan Hello** owns operational voice sessions.
- **Titan Go** is a stateless speech engine.

This constitutional split appears repeatedly in the Titan source material and should govern architecture, APIs, UX, extension design, and future refactors.

---

## The constitutional law

### Worksuite owns operations

Worksuite remains the single source of truth for operational data and authority. It owns jobs, invoices, compliance, finance, clients, service communications, and the operational timeline. Studio may enrich, advise, and observe, but it must not replace or duplicate Worksuite's core operational authority.

**Implication:** if a feature can create, schedule, approve, invoice, or alter field operations, it must ultimately route through Worksuite.

### Titan Studio owns intelligence and marketing surfaces

Titan Studio can operate alone, but in **Duo mode** it must defer to Worksuite for operations. Studio owns marketing, branding, onboarding assistance, advisory behavior, jobsite guidance, content, and other intelligence-heavy surfaces. It can propose, analyze, draft, and optimize, but must not directly mutate operational records that belong to Worksuite.

**Implication:** a Studio workflow may generate a lead, proposal, script, playbook, or campaign, but when the action crosses into operational execution, the handoff belongs to Worksuite.

### Titan Zero is the only AI authority

All AI actions must pass through Titan Zero. It is responsible for:

- intent inference
- risk classification
- context building
- confirmation requirements
- approval or rejection
- audit linkage

No extension, module, panel, plugin, or developer path may bypass Titan Zero.

### Titan Core executes, but does not decide

Titan Core routes approved requests to the correct provider, enforces model and cost policy, tracks usage, and handles batching or failover. It does not infer intent and does not decide whether an action is allowed.

This separation is essential:

- **Zero decides**
- **Core executes**

### Titan Hello owns operational voice sessions

Operational calls, service updates, and field voice interactions are governed by Titan Hello. It manages session lifecycle, transcript routing, and permission enforcement. It should attach operational voice history to Worksuite-owned communication records.

### Titan Go is only a voice engine

Titan Go is deliberately stateless. It performs speech-to-text, text-to-speech, and low-latency audio functions, but it owns no business state, no sessions, and no decision logic.

---

## Duo mode

### What Duo mode is

Duo mode is the operating model where **Titan Studio** and **Worksuite** run together as one coordinated product family while still remaining separate applications and separate authorities.

Duo mode introduces:

- shared constitutional rules
- cross-system APIs
- context snapshot exchange
- event backfeed
- authority boundaries
- explicit handoff rules

Duo mode is not a merge. It is a federation with law.

### What changes in Duo mode

When Duo mode is enabled:

- Studio may read approved operational context snapshots
- Worksuite remains authoritative for ops state
- CustomerConnect becomes the operational conversation system
- Studio may emit events or proposals into Worksuite
- AI actions still route through Titan Zero and Titan Core
- voice and operational message channels must respect Worksuite authority

### What must not happen in Duo mode

The following are forbidden:

- Studio directly editing operational source-of-truth records
- modules bypassing Titan Zero
- direct provider calls from features that should go through Titan Core
- parallel copies of customer operational truth in both systems
- marketing systems silently mutating operations
- voice services owning session logic outside Hello

---

## Cross-system ownership map

### Worksuite

Worksuite owns:

- customers in operational context
- jobs and service visits
- quotes and invoices
- operational compliance data
- operational communications
- package and module operational behavior
- operational audit trail

### Titan Studio

Studio owns:

- marketing and brand content
- advisory drafting
- content generation
- onboarding intelligence
- promotional and campaign intelligence
- marketing voice bots in standalone Studio mode
- optimization suggestions for growth surfaces

### Shared Titan AI spine

The shared AI spine owns:

- intent interpretation
- approval rules
- AI routing
- cost governance
- risk controls
- audit structure
- model/provider abstraction

### CustomerConnect

CustomerConnect owns:

- operational conversations
- SMS, email, WhatsApp, and chat tied to jobs, quotes, invoices, reminders, and service history
- consent and delivery receipts
- operational timeline continuity

### Hello and Go

- **Hello** owns operational voice orchestration
- **Go** owns STT/TTS execution only

---

## API and handoff rules

### The core API rule

The core API may expose infrastructure and shared contract surfaces, but it should not embed business logic. Business logic belongs in modules.

### Handoffs from Studio to Worksuite

A proper handoff should look like this:

1. Studio produces a lead, draft, recommendation, or event.
2. Titan Zero classifies intent and risk.
3. Worksuite receives a normalized payload through a Duo ingest or module API surface.
4. The relevant Worksuite module validates tenancy, permissions, and schema.
5. Titan Core executes any approved AI work if needed.
6. CustomerConnect or the relevant operational module records the interaction.

### Event backfeed from Worksuite to Studio

Worksuite may publish normalized events back to Studio for:

- personalization
- campaign suppression or timing
- intelligence refinement
- customer context enrichment
- reporting

But this backfeed must be asynchronous, idempotent, and non-authoritative for operational state.

---

## Module obligations under the constitution

A Titan-ready module in this environment must do more than boot in Laravel.

It must:

- respect `company_id` as the tenant boundary
- use `user_id` where actor/ownership matters
- expose API routes where PWA, node, or AI surfaces need them
- declare manifests where appropriate
- avoid burying business logic inside Filament-only callbacks
- keep domain logic reusable from API, jobs, chat, imports, exports, and panels
- integrate with package/module settings
- avoid bypassing the AI spine

### What modules must not do

A module must not:

- call AI providers directly for governed actions
- create a hidden second source of truth
- trap critical logic inside panel widgets or closures
- break tenant boundary by querying without `company_id`
- expose admin or user surfaces without consistent authority checks

---

## Filament's role in the constitutional model

Filament should be treated as a **control plane**, not the business engine.

That means Filament is ideal for:

- dashboards
- approval queues
- review surfaces
- admin settings
- operator workflows
- tables, forms, widgets, and drill-downs

But Filament must consume module actions and services rather than becoming the only place where the business rules live.

This keeps the same domain behavior available to:

- panels
- APIs
- PWAs
- queue jobs
- Titan chat
- imports and exports
- automation engines

---

## PWA and node implications

The constitutional split matters even more once PWAs and nodes are added.

### Device/node rule

A PWA, mobile app, or edge node should be able to:

- bootstrap from a known contract
- sync against module APIs
- submit signal envelopes
- work offline where appropriate
- hold only scoped local state
- defer authority decisions upward when risk is high

### Why this matters

Without constitutional separation, device apps end up becoming mini-backends with their own policy drift.

The correct pattern is:

- modules own domain logic
- platform engines own orchestration
- AI spine owns governed decisions
- PWAs and devices own interaction surfaces and offline behavior

---

## Design consequences

### Build for federation, not collapse

The system should behave like a federation of bounded authorities rather than one giant application with fuzzy ownership.

### Favor envelopes and contracts

Cross-system traffic should use:

- versioned payloads
- response envelopes
- idempotent event IDs
- scoped context snapshots
- declared capabilities and manifests

### Keep audit first-class

Because the constitution is partly about accountability, every sensitive AI or cross-system action must be auditable.

### Prefer reuse over parallel implementations

If the same action is needed from panel, API, PWA, import, and chat, build one action/service path and consume it everywhere.

---

## Recommended implementation checklist

For every new feature, ask:

1. Which system owns this truth?
2. Is this marketing/advisory or operational?
3. Does this require Titan Zero approval?
4. Does execution belong to Titan Core?
5. Should CustomerConnect own the conversation history?
6. Is the voice session owned by Hello?
7. Is Go being kept stateless?
8. Is the module still tenant-safe?
9. Can the same logic run from panel, API, PWA, and automation?
10. Is the handoff auditable and idempotent?

If these questions are answered early, the architecture stays coherent.

---

## Final position

The Titan ecosystem becomes much stronger when treated as a constitutional platform:

- **Worksuite** protects operational truth.
- **Studio** expands intelligence and marketing capability.
- **Titan Zero** governs AI authority.
- **Titan Core** executes approved AI tasks.
- **CustomerConnect** owns operational conversations.
- **Titan Hello** governs operational voice.
- **Titan Go** remains a stateless speech engine.

This is the boundary set that lets the platform scale into AI-controlled PWAs, nodes, mobile surfaces, and multi-product coordination without turning into one tangled system.
