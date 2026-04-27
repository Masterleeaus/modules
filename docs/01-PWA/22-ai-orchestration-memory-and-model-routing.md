# 22. AI Orchestration, Memory, and Model Routing

## Purpose

This document defines the AI platform layer for Titan: the authority split between Titan Zero and Titan Core, the role of specialist cores, context-pack assembly, memory scopes, model-routing policy, and evaluation/governance rules.

The full system blueprint clearly shows that AI is too important to remain a few manifests and helper classes. It needs a first-class platform layer with orchestration, context building, memory, routing, tooling, governance, evaluation, and training/refinement. fileciteturn12file2turn12file3

---

## 1. Constitutional AI law

### 1.1 Titan Zero is the sole AI authority

The constitution is explicit: Titan Zero owns intent inference, risk classification, context assembly, confirmation decisions, and audit. No module, extension, or provider may bypass this path. fileciteturn16file19turn16file7

### 1.2 Titan Core is the execution router

Titan Core executes authorized requests against AI providers and enforces model selection and cost rules. It does not infer intent and does not decide whether an action is allowed. fileciteturn16file5turn16file19

### 1.3 Studio AI is capability, not operational authority

Titan Studio may reason, generate, and plan in its own marketing domain, but operational mutation still routes through Worksuite under Titan Zero supervision. fileciteturn16file14turn16file7

### 1.4 No silent AI side effects

All AI runs must be authorized, bounded by risk/confirmation policy, and logged. This includes drafting, classification, execution proposals, summaries, and voice interactions. fileciteturn16file6turn16file19

---

## 2. AI platform structure

The right shape, derived from the full engine blueprint, is:

```text
app/Platform/Ai/
├─ Core/
│  ├─ TitanZero/
│  ├─ Aegis/
│  ├─ Equilibrium/
│  ├─ Logic/
│  ├─ Creator/
│  ├─ Finance/
│  ├─ Entropy/
│  └─ Sentry/
├─ Orchestration/
├─ Context/
├─ Memory/
├─ Routing/
├─ Tooling/
├─ Governance/
├─ Voice/
├─ Evaluation/
├─ Training/
└─ Support/
```

This is the correct size for the AI layer in a system that spans modules, PWAs, voice, automation, and dual-app governance. fileciteturn12file3

---

## 3. Role split inside the AI layer

### 3.1 Titan Zero

Zero should own:

- intent inference
- risk classification
- context assembly
- confirmation requirements
- proposal generation
- final answer synthesis
- audit writing

Zero is the user-facing intelligence and the legal authority for AI behavior.

### 3.2 AEGIS

AEGIS should own:

- policy enforcement
- safe-mode constraints
- permission gates
- cross-domain consistency checks
- denial reasons
- anomaly escalation

AEGIS is the governance spine inside the AI layer.

### 3.3 Specialist cores

Specialist cores should exist as reasoning lenses, not disconnected products. For example:

- Logic → operational reasoning, structure, constraints
- Creator → expression, presentation, communication style
- Finance → money, pricing, recovery, accounting logic
- Entropy → challenge, search, edge-case pressure testing
- Sentry/judges → evaluation and contradiction finding

This matches the broader Titan preference for multi-core reasoning and critique rather than one flat assistant. fileciteturn12file3

### 3.4 Titan Core

Titan Core sits below authority and above providers. It should own:

- provider dispatch
- retries/timeouts
- token/cost logging
- response normalization
- adapter selection
- provider failover

It should not own intent, policy, or domain authority.

---

## 4. Context-pack assembly

The main product risk in AI-heavy systems is not just “wrong model,” but “wrong context.”

### 4.1 Context builder duties

Context builders should assemble a bounded, evidence-based pack from:

- user intent
- current route/surface
- tenant identity
- package/license state
- workflow state
- module manifests
- recent timeline/events
- permissions and constraints
- relevant memory slices
- tool affordances
- risk level

### 4.2 Envelope model

Each AI run should receive a structured envelope, not an ad hoc prompt string. A minimal envelope should include:

- actor
- tenant
- surface
- domain mode
- current object references
- permissions snapshot
- policy snapshot
- context facts
- allowed tools
- disallowed actions
- confirmation requirement
- expected output schema

This lines up well with the signal-envelope direction already present in the broader Titan architecture. fileciteturn16file18

---

## 5. Memory model

Memory should be layered, scoped, and policy-controlled.

### 5.1 Memory scopes

Recommended memory scopes:

- session memory
- user memory
- tenant/company memory
- site/location memory
- job/work-order memory
- workflow memory
- device/node memory
- policy memory

This matches the need to remember recurring site/job details, company preferences, and device-node state without flattening everything into one blob.

### 5.2 Working vs durable memory

Separate:

- working memory → short-lived context for the current task
- durable memory → reusable preferences, recurring facts, learned patterns

### 5.3 Recall policy

Memory retrieval should obey:

- tenant boundary (`company_id`)
- surface relevance
n- freshness
- privacy class
- action criticality
- user permissions

Do not simply retrieve “everything relevant.” Retrieval must be bounded and auditable.

---

## 6. Tooling and module integration

The module checklist expects modules to expose optional manifests such as `ai_tools.json`, `signals_manifest.json`, `lifecycle_manifest.json`, `cms_manifest.json`, and `omni_manifest.json`. That makes modules the tool and capability providers for the AI layer. fileciteturn11file1

### 6.1 Tool registry

The AI platform should maintain a normalized tool registry with:

- tool id
- module owner
- route/endpoint or action class
- domain mode
- required permissions
- risk category
- side-effect class
- confirmation requirement
- input schema
- output schema
- fallback behavior

### 6.2 Invocation pattern

AI should never call random controllers directly. Good pattern:

- module exposes a declared tool
- AI resolves that through a registry
- Titan Zero checks permission/risk
- Titan Core executes via a normalized adapter
- result is normalized and logged

This mirrors the module/plugin blueprint’s rule that the reusable action/service layer is the source of truth, while UI layers consume it. fileciteturn16file3turn12file0

---

## 7. Model routing policy

Routing across local and external models should be policy-driven.

### 7.1 Inputs to routing

Route selection should consider:

- risk level
- privacy classification
- domain type
- latency need
- cost ceiling
- modality (text, image, voice)
- context size
- tool-use requirement
- user tier/package
- offline availability

### 7.2 Typical routing examples

- low-risk summarization with tenant-safe data → local/private model
- policy-sensitive operational planning → Zero + Core with stronger reasoning path
- image-generation request → approved image-capable provider
- real-time voice turn → low-latency speech path via Hello/Go and approved AI path
- financial or compliance action proposal → stricter model + confirmation + audit

### 7.3 Routing outcomes

Routing should produce:

- chosen model/provider
- reason code
- expected cost band
- privacy basis
- confirmation requirement
- fallback path if failure occurs

---

## 8. Evaluation and anti-hallucination layer

A sophisticated AI system should include internal judges.

### 8.1 Evaluation functions

Evaluation should include:

- evidence sufficiency check
- contradiction detection
- policy compliance check
- unsafe-action detection
- schema validation
- cross-core disagreement scoring
- confidence annotation

### 8.2 When evaluation is mandatory

Evaluation should be mandatory for:

- operational actions
- finance actions
- compliance-related outputs
- irreversible side effects
- customer-facing sends
- voice-call action commitments

### 8.3 Outcome classes

Evaluation should yield one of:

- allow
- allow with confirmation
- retry with more context
- route to stronger model
- deny
- escalate to human/operator

---

## 9. Training, feedback, and refinement

The AI platform should not only answer; it should improve from outcomes.

### 9.1 Feedback sources

Use:

- explicit user approval/denial
- workflow outcome quality
- delivery success/failure
- edit distance between draft and final action
- operator overrides
- complaint/error events
- audit findings

### 9.2 Refinement artifacts

Store refinement deltas such as:

- preferred terminology
- confirmation preferences
- channel preferences
- routing adjustments
- tool ranking adjustments
- memory importance changes

### 9.3 Boundaries

Refinement must not silently rewrite constitutional rules, permission law, or domain ownership.

---

## 10. Laravel implementation guidance

The Laravel references strongly support a clean implementation style here.

### 10.1 Keep controllers thin

Request validation belongs in Form Requests; reusable logic belongs in actions/services; DTOs help decouple input shape from core logic. That pattern is ideal for AI endpoints and tool execution handlers. fileciteturn10file2turn16file9

### 10.2 Use providers and interfaces

Bindings for model routers, memory resolvers, evaluation judges, and provider adapters should live in service providers and be resolved through the container, not manually new’d up all over the app. fileciteturn16file11turn10file1

### 10.3 Queue non-essential work

Transcript post-processing, long-context retrieval, summarization, refinement writing, and evaluation runs should often be queued. fileciteturn16file13

### 10.4 Prefer named routes and structured APIs

Tool surfaces, AI APIs, and internal execution entrypoints should use predictable route naming and versioning conventions, not ad hoc controller paths. fileciteturn10file3turn16file5

---

## 11. Build contract

A production-ready Titan AI platform should satisfy this checklist:

- Titan Zero remains sole AI authority
- Titan Core remains execution router only
- specialist cores act as reasoning lenses, not rogue authorities
- context packs are structured and auditable
- memory is layered and tenant-safe
- tools are declared through manifests/registries
- model routing is policy-driven
- evaluation/judge layer exists for risky outputs
- refinement learns from outcomes without breaking constitutional law
- all AI runs are logged with reason, context, and outcome

This is how the AI layer becomes a governed operating substrate for Worksuite, Studio, PWAs, nodes, voice, and future surfaces.
