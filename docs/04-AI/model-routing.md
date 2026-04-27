# Model Routing Documentation

Layer: AI
Scope: Local versus external model routing, privacy policy, cost policy, latency policy, and fallback control
Status: Draft v1
Depends On: Titan Zero, Titan Core, AEGIS, context packs, provider registry, cost controls
Consumed By: Titan Core execution, Titan Zero approval flow, voice runtime, PWA nodes, evaluation
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

Model routing defines how Titan chooses where AI work runs. It exists so the platform can decide between local models, server-side models, and external frontier providers without losing privacy, cost control, latency discipline, or governance consistency.

Model routing is not only a provider-selection problem. It is a policy problem.

## 2. Why it exists

Without a routing model:
- execution choices become inconsistent
- private data may leak to the wrong provider
- low-value tasks may waste high-cost models
- fast channels may get routed through slow paths
- voice and PWA behavior becomes unpredictable
- tenant trust weakens

Model routing gives the platform a repeatable, auditable answer to:
**Where should this run, and why?**

## 3. Core routing layers

Titan’s preferred architecture uses three broad execution layers.

### Private or client node
Runs on the user’s device or private client environment when possible.

Best for:
- privacy-sensitive work
- lightweight reasoning
- local/offline assistance
- device-native experiences
- low-latency voice helpers

### Server node
Runs on Titan-controlled server infrastructure.

Best for:
- shared orchestration
- heavier coordination
- tenant-level services
- cross-module AI assembly
- managed provider access

### External frontier models
Runs through approved third-party AI providers.

Best for:
- high-end reasoning
- richer generation tasks
- advanced multimodal work
- capacity bursts
- specialist tasks where external quality materially exceeds internal options

## 4. Routing principles

### Privacy first
Prefer the most private route that can still do the job well.

### Capability fit
Choose a model that can actually handle the task type and context size.

### Cost awareness
Do not waste high-cost models on low-value work.

### Latency awareness
Real-time or voice experiences should favor routes that can respond quickly.

### Governance consistency
A route is only valid if AEGIS and Titan Zero approve the use of that route.

## 5. Routing inputs

A routing decision should consider:

- task type
- domain sensitivity
- privacy classification
- context-pack size
- required capability
- risk level
- latency expectation
- channel type
- tenant cost policy
- provider availability
- current safe mode
- local-node capability

## 6. Privacy policy

Privacy policy should be one of the strongest routing constraints.

### Examples

- site access notes may be allowed on private or server node, but blocked from external routing
- customer communication drafts may be allowed externally only after redaction
- financial review work may require server or private path depending on policy
- tenant may forbid external routing for certain object classes entirely

Routing must obey these constraints, not merely “prefer” them.

## 7. Cost policy

Different tenants and task classes may justify different cost envelopes.

### Cost policy examples

- low-cost mode for routine drafts
- premium reasoning mode for high-stakes review
- external-model cap per tenant or user
- voice-specific budget
- image-generation budget for sales/quote workflows

Routing should be aware of value, not just technical possibility.

## 8. Latency policy

Some channels cannot tolerate slow orchestration.

### High-latency-tolerant
- report generation
- offline batch analysis
- overnight refinement
- asynchronous proposals

### Low-latency-sensitive
- voice interactions
- live operator assistance
- mobile quick actions
- real-time dispatch help

Routing should bias accordingly.

## 9. Channel-aware routing

The same task may route differently depending on channel.

### Examples

- PWA field-worker quick help may use device-local or server-fast route
- admin panel review may tolerate heavier server reasoning
- voice may prefer low-latency local or server streaming
- high-end proposal generation in back office may justify external frontier models

Routing should respect the user surface.

## 10. Role of Titan Zero

Titan Zero approves whether a routing path is acceptable in context.

Titan Zero should determine:
- whether the task may proceed
- what the risk and privacy class are
- whether external routing is permitted
- whether confirmation is required

Titan Zero does not directly implement low-level provider switching, but it approves the routing envelope.

## 11. Role of AEGIS

AEGIS constrains routing through governance.

AEGIS may:
- block external routing for a task
- force a safe route
- require redaction
- require review before high-cost route use
- enforce no-external mode in safe mode

Routing is invalid if governance says no.

## 12. Role of Titan Core

Titan Core is the execution router and provider abstraction layer.

Titan Core should own:
- provider registries
- model health and availability
- fallback paths
- retries
- timeouts
- usage metering
- execution adapters

Titan Core makes the routing plan real after Titan Zero and AEGIS approve it.

## 13. Fallback strategy

Routing should always consider graceful fallback.

### Example fallback ladder

1. preferred local model
2. approved server model
3. approved external model
4. constrained/draft-only fallback
5. deny and escalate if no safe route exists

Fallback should not silently change privacy class or capability class unless policy allows it.

## 14. Redaction and minimization

Sometimes a task may be routed externally only if context is reduced or redacted.

### Example
A customer-facing quote explanation may be routed externally if:
- raw customer identifiers are removed
- site secrets are stripped
- only relevant scoped facts remain

Redaction is part of routing discipline, not a side note.

## 15. Evaluation and learning

Model routing should improve over time through:
- cost outcome tracking
- latency tracking
- quality scoring
- failure analysis
- hallucination incidence
- user correction patterns

The system should learn which routes work best for which tasks without abandoning policy discipline.

## 16. Failure modes

### Overrouting to frontier models
Too expensive, privacy-risky, and unnecessary for routine work.

### Under-routing
Weak local model used for tasks needing higher capability.

### Privacy downgrade
Task silently sent to a less private route than policy intended.

### Channel mismatch
Voice or mobile gets a route too slow for usable interaction.

### Hidden fallback
The platform changes route class without clear audit trace.

## 17. Implementation notes

Model routing should map into:
- provider registry
- cost policy
- privacy policy
- latency policy
- execution adapters
- safe-mode controls
- audit logging

Routing decisions should always be traceable in audit output.

## 18. Follow-up docs

This document should be followed by:
- `weighting-and-consensus.md`
- `evaluation.md`

## 19. Summary

Model routing gives Titan a governed answer to where AI work should run: private node, server node, or external frontier provider. It balances privacy, cost, latency, capability, and fallback while keeping routing under Titan Zero authority, AEGIS governance, and Titan Core execution control.

    ## 20. Route classes for PWA, Omni, and module tools

    Routing policy should consider not just model capability, but the runtime surface:
- PWA field tools may prefer device-local or server-fast routes
- Omni channel flows may require stronger governance and redaction
- module tools may require server-side route discipline for auditability
- voice/Hello/Go surfaces should prefer low-latency routes with strict confirmation behavior

Routing should therefore be surface-aware, not only provider-aware.

    ## 21. Routing audit fields

    Every routed execution should ideally preserve:
- chosen route class
- chosen provider/model
- fallback path if used
- privacy classification
- latency/cost policy reason
- whether redaction/minimization was applied

These fields make later evaluation and debugging much more credible.

