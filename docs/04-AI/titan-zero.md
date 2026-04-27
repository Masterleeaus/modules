# Titan Zero Documentation

Layer: AI
Scope: Titan Zero core authority, intent inference, context assembly, confirmation logic, and audit control
Status: Draft v1
Depends On: Core Platform, Signals + Data Contracts, Worksuite authority boundaries, Titan Core execution router
Consumed By: AEGIS Core, specialist cores, AI routing, voice runtime, automation, modules, Omni, PWA nodes
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

Titan Zero is the sovereign AI authority of the Titan system. It does not exist as a cosmetic chatbot layer or a loose “AI feature.” It is the decision authority that interprets intent, classifies risk, assembles context, determines confirmation requirements, and authorizes or rejects downstream AI execution.

Titan Zero exists so that:
- AI behavior stays consistent across channels and modules
- approval and risk logic are centralized
- context is assembled once, correctly, before execution
- all AI runs are auditable
- execution providers remain replaceable without changing policy

## 2. Why it exists

Without Titan Zero, each module, extension, or channel would make its own AI decisions. That would create:
- conflicting rules
- silent actions
- duplicated prompts
- fractured memory behavior
- inconsistent confirmation thresholds
- weak auditability

Titan Zero solves this by sitting above providers and below user/module intent. The constitution-level rule is that Titan Zero is the sole AI authority, while Titan Core is the execution router. Titan Zero decides what is allowed; Titan Core runs only what Titan Zero approves.

## 3. Core responsibilities

- infer user and system intent from requests, signals, and channel input
- classify action risk and determine confirmation requirements
- assemble context packs from permitted operational, memory, and channel data
- validate capability requirements against manifests and declared tools
- authorize or deny AI execution requests
- write structured audit records for all AI runs
- coordinate specialist cores and reconcile their outputs into one system answer
- enforce the separation between reasoning authority and execution authority

## 4. Boundaries

### In scope

- intent inference
- risk classification
- context assembly
- confirmation decisions
- audit writing
- approval gating
- multi-core synthesis
- handoff to Titan Core
- policy enforcement across text and voice AI

### Out of scope

- direct provider execution
- owning voice transport
- direct database writes as an unchecked actor
- module-specific business workflows
- signal transport internals
- long-term storage implementation details of every memory backend

Titan Zero may propose, classify, authorize, and explain. It must not bypass the approval laws of the platform by executing domain actions directly.

## 5. Position in the stack

Titan Zero sits in the AI spine between incoming intent and provider execution.

### Simplified flow

1. Input arrives from user, module, automation, signal, or voice channel
2. Titan Zero interprets intent
3. Titan Zero classifies risk
4. Titan Zero assembles a context pack
5. Titan Zero validates needed capabilities against manifests/tools
6. Titan Zero decides whether confirmation is required
7. Titan Zero either:
   - rejects the request
   - requests confirmation
   - approves execution via Titan Core
8. Titan Core routes the approved request to an AI provider
9. Titan Zero links audit records to the run and any resulting artefacts

## 6. Relationship to Titan Core

Titan Zero and Titan Core must never be blurred.

### Titan Zero owns

- policy
- intent
- risk
- context
- approval
- audit law
- confirmation logic
- multi-core synthesis

### Titan Core owns

- provider abstraction
- model selection enforcement
- batching and retries
- failover
- usage accounting
- token and cost control
- low-level AI execution

### Rule

Titan Core must not decide what is allowed. Titan Zero must not directly execute providers.

## 7. Relationship to AEGIS

AEGIS is the governance and safety core within the broader AI platform. Titan Zero remains the sovereign user-facing authority and synthesis layer, while AEGIS is the stricter governance subsystem used for policy, risk, denials, safe modes, and cross-domain consistency checks.

### Practical split

- Titan Zero = final AI authority and unified intelligence layer
- AEGIS = governance, safety, and approval discipline inside the AI stack

Titan Zero may use AEGIS outputs, but AEGIS does not replace Titan Zero’s role as the authoritative AI interface for the platform.

## 8. Relationship to specialist cores

Titan Zero is not the only reasoning component. It stands above a family of specialist cores and reasoning lenses.

### Expected specialist roles

- Logic core: operational reasoning, structure, rules, cross-checking
- Creator core: presentation, interface, framing, communication polish
- Finance core: money reasoning, billing, pricing, accounting review
- Entropy core: uncertainty, exception handling, non-obvious edge pressure
- Additional lenses: equilibrium, alien, macro, micro, sentry, and others defined by current doctrine

### Reconciliation rule

Specialist cores may disagree.
Titan Zero is responsible for:
- collecting their outputs
- comparing their critiques
- resolving contradictions
- producing a single coherent result or proposal
- escalating when disagreement remains materially risky

## 9. Context pack architecture

Titan Zero should never reason from arbitrary raw sprawl. It should reason from compiled context packs.

### A context pack should include

- actor identity
- tenant scope
- channel/source metadata
- relevant objects
- recent conversation context
- relevant memory layers
- signal envelope or event context
- tool/capability context
- approval and policy constraints
- risk annotations
- traceable evidence links

### Design goals

- minimal but sufficient
- tenant safe
- channel aware
- redactable
- versionable
- auditable
- reusable across cores

## 10. Memory interaction

Titan Zero consumes memory; it does not equal memory.

### Memory layers it should resolve through

- session memory
- user memory
- tenant/company memory
- site memory
- job memory
- working memory
- recall policies

### Memory usage rules

- prefer scoped memory over global assumptions
- resolve by company boundary first
- preserve operational privacy
- distinguish verified facts from inferred summaries
- include enough provenance for review and debugging

Titan Zero should ask for memory through stable resolvers and context builders rather than poking randomly into data stores.

## 11. Risk and confirmation model

Titan Zero classifies risk before approving execution.

### Typical risk bands

- low: informational or draft-safe
- medium: may affect workflow, customer comms, or business state
- high: financially, legally, operationally, or reputationally significant

### Confirmation expectations

- low risk may proceed automatically if policy allows
- medium risk may require human review depending on role, tier, and domain
- high risk should require explicit confirmation and often governance checks

### Examples

- drafting an internal summary = low
- proposing a client message = medium
- triggering a job, quote, invoice, payment, or compliance action = medium to high depending on context
- provider escalation or high-cost image generation = governed by policy

## 12. Capability validation

Before execution, Titan Zero must validate that the requesting layer or module declares the necessary capabilities.

### Sources of capability truth

- ai_tools manifests
- lifecycle manifests
- signals manifests
- omni manifests
- cms manifests
- API surfaces
- module/provider registration

### Validation purpose

- stop undeclared tool usage
- enforce manifest contracts
- prevent channels from executing hidden actions
- align AI proposals with real system capability

## 13. Audit model

All AI runs must be logged. No silent AI actions.

### Audit record should capture

- who or what requested the run
- tenant scope
- source channel
- resolved intent
- risk class
- confirmation state
- context pack version/reference
- chosen provider/model
- execution result
- linked artefacts or proposals
- denial reason if rejected

### Audit principle

If an AI action matters, it must be reconstructable.

## 14. Runtime behavior

At runtime Titan Zero behaves as a policy-first orchestrator.

### Runtime stages

1. Receive request or signal
2. Normalize input
3. Resolve actor, company, and domain
4. Assemble context
5. Classify risk
6. Validate capability
7. Decide allow / deny / confirm
8. If allowed, send execution request to Titan Core
9. Collect outputs from core and specialist layers as needed
10. Write audit trail
11. Return answer, proposal, or denial

### Important behavior rules

- never silently leap from suggestion to execution
- never let a provider bypass policy
- never let channel-specific logic replace central AI law
- degrade safely when context or providers are incomplete

## 15. Failure modes

### Context failure

Problem:
- missing or partial context
Response:
- reduce scope
- ask for clarification or return a constrained draft
- mark low-confidence output

### Capability failure

Problem:
- requested action is not declared by manifests
Response:
- deny execution
- log capability failure
- explain missing capability path

### Provider failure

Problem:
- provider unavailable or times out
Response:
- Titan Core may retry or fail over if allowed
- Titan Zero keeps approval logic separate and records the failure path

### Governance conflict

Problem:
- AEGIS or policy layer blocks an otherwise useful request
Response:
- deny or require review
- return a structured explanation rather than silently downgrading rules

### Specialist disagreement

Problem:
- core outputs conflict materially
Response:
- Titan Zero synthesizes if safe
- otherwise escalates for review and marks reasoning conflict

## 16. Dependencies

### Upstream

- Core platform identity and tenancy
- Signals and data contracts
- manifests and tool declarations
- memory subsystems
- customer/channel input layers
- Worksuite operational system-of-record

### Downstream

- Titan Core execution
- AEGIS governance checks
- specialist cores
- Hello / Go voice runtime
- module actions and automation proposals
- Omni and PWA surfaces
- audit and observability layers

## 17. Contracts

Titan Zero needs stable contracts, not loose coupling by convention.

### Input contracts

- user message
- voice transcript
- signal envelope
- module AI request
- automation proposal request
- context snapshot request

### Output contracts

- approved execution request
- denial response
- confirmation-required response
- synthesized answer
- structured proposal
- audit event payload
- escalation event

### Required stable interfaces

- context pack builder
- risk classifier
- capability validator
- policy evaluator
- Titan Core execution handoff
- audit writer

## 18. Implementation notes

### App-tree placement

The AI blueprint expects Titan Zero to exist as a first-class platform layer under the AI subsystem, not as a helper. It should therefore map into platform AI structure and also appear across actions, services, jobs, events, API controllers, and related support layers.

### Worksuite doctrine alignment

- Worksuite remains the operational system of record
- Titan Zero governs AI approval and context law across that record
- Titan Zero must respect tenant boundaries using company_id as the primary tenant fence
- AI-capable modules should expose manifests so Titan Zero can validate capability honestly

### Documentation follow-up docs

This document should be followed by:
- `aegis-core.md`
- `specialist-cores.md`
- `orchestration.md`
- `context-packs.md`
- `memory-architecture.md`
- `model-routing.md`

## 19. Open questions

- Should Titan Zero always synthesize specialist-core outputs, or may some domains return a dominant-core answer directly?
- What is the exact schema and storage contract for `ai_runs` across Worksuite and Studio modes?
- How should tenant-configurable cost policies be represented when routing between local and external models?
- Which pieces of memory are mandatory in every context pack versus optional by domain?
- How will confirmation UX differ across chat, voice, PWA, and admin surfaces while keeping the same law?

## 20. Summary

Titan Zero is the AI law, approval, and synthesis layer of the Titan system. It exists to centralize intent inference, risk classification, context assembly, capability validation, confirmation logic, and audit writing. Titan Core executes; AEGIS governs; specialist cores advise; modules and channels request. Titan Zero is the authority that keeps the whole AI stack coherent.

    ## 21. Module and manifest integration

    Titan Zero should not assume capability from prompt wording alone. It should validate against module manifests and declared surfaces before allowing AI-assisted action.

### Relevant declarations
- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`
- API routes and registered providers

This fits the Worksuite module contract where modules are expected to be installable, package-aware, API-exposed, tenant-safe, and optionally AI-executable through declared manifests and routes.

    ## 22. Tenant boundary discipline

    Titan Zero must treat `company_id` as the primary tenant boundary for Worksuite-aligned modules. Context assembly, memory resolution, capability validation, and AI proposal generation should all fence by company scope first, then by role and channel.

This is especially important for:
- site and job memory
- signals and lifecycle participation
- module APIs
- customer communications
- package/module settings

    ## 23. Implementation discipline

    The Laravel guidance in project sources reinforces keeping controllers thin, using request validation, actions/services, DTO-style payload shaping, and explicit routes/controllers rather than sprawling controller logic.

For Titan Zero docs, this means:
- avoid hidden orchestration inside controllers
- prefer context builders, validators, resolvers, and actions/services
- keep runtime contracts explicit and reusable across web, API, queue, and CLI surfaces

