# AEGIS Core Documentation

Layer: AI
Scope: Governance, policy enforcement, risk controls, denials, safe modes, and approval discipline
Status: Draft v1
Depends On: Titan Zero, Core Platform identity + tenancy, manifest contracts, audit pipeline, policy store
Consumed By: Titan Zero, Titan Core, specialist cores, automations, signals, module tools, Omni, PWA nodes
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

AEGIS is the governance and safety core inside the Titan AI stack. It exists to enforce policy, risk, and approval law across AI activity so that no provider, module, channel, or specialist core can quietly bypass the platform’s operating rules.

AEGIS is not the user-facing intelligence layer. It is the disciplined governance layer that validates whether an AI action should proceed, be denied, be downgraded into draft-only form, or be escalated for explicit human confirmation.

## 2. Why it exists

A multi-core AI system without a governance core becomes fragile very quickly. Different channels, prompts, or providers begin drifting into different standards of behavior. AEGIS exists to stop that drift.

Without AEGIS:
- different modules could apply different risk thresholds
- automation could silently outrun approval law
- voice channels could become less safe than text channels
- provider changes could alter behavior without governance review
- high-risk AI outputs could be treated like harmless drafts

AEGIS gives the whole system one governance spine.

## 3. Core responsibilities

- enforce AI policy consistently across all channels and modules
- evaluate risk before approved execution proceeds
- deny undeclared or disallowed capability use
- decide whether confirmation is required
- apply safe modes and domain restrictions
- validate cross-domain consistency for proposed actions
- record denials, policy failures, and governance overrides
- expose auditable reasons for approval, denial, or escalation

## 4. Boundaries

### In scope

- governance
- policy enforcement
- denials
- safe modes
- escalation requirements
- approval discipline
- capability law
- high-risk gating
- cross-domain consistency checks

### Out of scope

- synthesizing the main user-facing answer
- owning model execution
- acting as the primary chatbot personality
- replacing specialist reasoning
- performing module business logic itself

AEGIS governs. It does not exist to be the whole AI.

## 5. Relationship to Titan Zero

Titan Zero is the sovereign AI authority and unified synthesis layer.
AEGIS is the governance and safety core used by Titan Zero to enforce law.

### Titan Zero asks:
- What is the user or system trying to do?
- What context is needed?
- What is the coherent answer or proposal?

### AEGIS asks:
- Is this allowed?
- What is the risk?
- Does this require confirmation?
- Does this violate policy, capability, or consistency rules?
- Should this be denied, downgraded, or escalated?

### Rule

Titan Zero remains the authoritative AI layer.
AEGIS remains the authoritative governance layer inside that stack.

## 6. Relationship to Titan Core

Titan Core executes approved AI work against providers and model routes.

AEGIS does not execute providers. It only constrains whether execution should be allowed and under what conditions.

### Core split

- Titan Zero: intent + context + synthesis
- AEGIS: policy + risk + governance
- Titan Core: execution + routing + failover + usage control

This three-way split is essential to keep the architecture auditable and replaceable.

## 7. Policy domains

AEGIS should evaluate policy across several concurrent domains rather than one flat allow/deny list.

### Core policy domains

- tenant and company boundary policy
- role and permission policy
- tool and manifest policy
- cost and provider policy
- communication policy
- workflow and lifecycle policy
- approval and review policy
- safety and compliance policy

### Examples

- a module may expose a tool, but the current role cannot use it
- a capability may be declared, but only for draft generation
- a provider may be allowed for text, but not for voice
- a channel may allow summaries, but not autonomous customer messages

## 8. Risk model

AEGIS should work with a structured risk model, not vague labels.

### Suggested risk dimensions

- financial risk
- operational risk
- customer-facing reputational risk
- legal/compliance risk
- data/privacy risk
- autonomy risk
- cross-domain inconsistency risk

### Suggested outputs

- low risk
- medium risk
- high risk
- blocked risk
- uncertain / requires review

Risk should be explainable, not magical.

## 9. Confirmation model

AEGIS decides whether a request can:
- proceed automatically
- proceed only as draft/proposal
- require explicit confirmation
- be denied outright

### Example confirmation expectations

- informational summary = allow
- internal operator suggestion = usually allow or draft
- client-facing communication = often confirm
- payment collection or invoice send = domain and amount dependent
- scheduling or dispatch change = confirm if customer or crew impact is material
- policy-sensitive or cross-domain mutation = high confirmation

## 10. Capability law

AEGIS must validate that any requested action is backed by declared system capability.

### Sources of capability truth

- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`
- API surfaces
- module registration and provider wiring

Capability law prevents invisible actions from sneaking in through prompts.

## 11. Safe modes

AEGIS should be able to force the platform into restricted operating modes.

### Examples

- draft-only mode
- explanation-only mode
- no-external-provider mode
- no-customer-comms mode
- no-financial-action mode
- review-all mode

Safe modes are especially important during:
- rollout
- incident handling
- tenant onboarding
- unstable provider periods
- compliance-sensitive operations

## 12. Denials

A denial should never be silent or vague.

### A denial should include

- denial type
- rule triggered
- object or capability involved
- risk note
- suggested next path if available

### Example denial classes

- undeclared capability
- tenant boundary violation
- unsafe autonomy request
- insufficient confirmation
- role mismatch
- blocked provider route
- cross-domain inconsistency

## 13. Cross-domain consistency

One of AEGIS’s most important jobs is to detect when an action appears valid locally but wrong globally.

### Examples

- finance wants to send an invoice but jobs state shows work unresolved
- dispatch wants to reassign work but payroll or availability state conflicts
- communications wants to promise a date that scheduling has not approved
- a site-specific rule conflicts with a generic workflow default

AEGIS should stop local correctness from becoming global error.

## 14. Audit and traceability

AEGIS must write auditable governance records for:
- approvals
- denials
- escalations
- safe-mode triggers
- overrides
- policy failures
- cross-domain conflicts

If Titan Zero is the AI authority, AEGIS is the legal record of why authority was or was not exercised.

## 15. Runtime flow

### Simplified runtime sequence

1. Titan Zero resolves intent
2. Context pack is assembled
3. Requested capability is identified
4. AEGIS checks:
   - policy
   - risk
   - role
   - tenant boundary
   - channel constraints
   - confirmation state
   - cross-domain consistency
5. AEGIS returns:
   - allow
   - allow as draft
   - require confirmation
   - deny
6. Titan Zero continues accordingly
7. Titan Core executes only if permitted

## 16. Failure modes

### Policy ambiguity
If policy rules conflict or are incomplete, AEGIS should bias toward constrained output or confirmation rather than silent execution.

### Provider mismatch
If the provider route is valid technically but policy blocks it, AEGIS must deny or reroute under approved alternatives.

### Domain contradiction
If modules disagree materially, AEGIS should escalate rather than guess.

### Missing manifest
If a capability is not declared, AEGIS must deny rather than “trust the prompt.”

## 17. Dependencies

### Upstream

- Titan Zero intent classification
- context packs
- module manifests
- role and permission systems
- tenant/company identity
- policy storage or configuration

### Downstream

- Titan Core execution routing
- audit writers
- approval queues
- operator review surfaces
- safe-mode toggles
- denial UX in chat, admin, and voice

## 18. Implementation notes

AEGIS should exist as a formal platform subsystem, not a loose helper class.

It should map into:
- platform AI governance
- policy evaluators
- risk scoring
- approval constraints
- safe mode control
- audit + observability

It should also remain reusable across:
- text chat
- voice
- PWA
- admin panels
- automations
- module AI tools

## 19. Open questions

- Which governance rules should be hard-coded versus tenant-configurable?
- How should safe modes be represented across Worksuite, Omni, and voice interfaces?
- Should AEGIS produce structured risk scores only, or also recommended mitigations?
- How should temporary emergency overrides be stored, audited, and expired?

## 20. Summary

AEGIS is the governance and safety core of the Titan AI stack. It exists to enforce policy, risk, capability law, confirmation discipline, and cross-domain consistency before AI execution is allowed to happen. Titan Zero synthesizes; Titan Core executes; AEGIS governs.

    ## 21. Manifest, API, and signal enforcement

    AEGIS should treat undeclared capabilities as blocked by default.

### Practical enforcement targets
- no AI action without declared tool or surface
- no lifecycle transition without lifecycle participation
- no signal-dependent flow without signal declaration
- no channel automation without Omni/channel declaration
- no hidden module API usage outside declared routes and permissions

This makes governance testable instead of dependent on prompt trust.

    ## 22. Governance implementation style

    AEGIS checks should be implemented as explicit policy evaluators, validators, and decision services rather than scattered controller conditions or view callbacks. This keeps governance reusable across:
- web routes
- API routes
- queues/jobs
- signal handlers
- voice and PWA surfaces

