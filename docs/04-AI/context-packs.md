# Context Packs Documentation

Layer: AI
Scope: Context assembly, structure, scoping, provenance, reuse, and runtime handoff
Status: Draft v1
Depends On: Titan Zero, memory layers, tenant identity, module manifests, signal envelopes, audit rules
Consumed By: Titan Zero, AEGIS, specialist cores, Titan Core execution prep, evaluation and replay
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

Context packs are the bounded bundles of information used by Titan to reason safely and consistently. They exist so the system does not depend on uncontrolled raw database reads, ad hoc prompt stuffing, or silent assumptions.

A context pack gives the AI runtime a disciplined answer to the question:
**What should this run know right now, and why?**

## 2. Why they exist

Without context packs:
- channels assemble different context from the same situation
- modules hand inconsistent information to the AI
- prompts become oversized and noisy
- provenance is lost
- tenant safety is harder to enforce
- replay and audit become weak

Context packs give the whole platform a stable context contract.

## 3. Core responsibilities

- gather only the context needed for the current run
- preserve tenant and company boundaries
- include provenance so facts can be traced
- distinguish verified data from inferred summaries
- support reuse across cores
- remain auditable and replayable
- provide bounded input for provider execution

## 4. Boundaries

### In scope

- actor identity
- tenant scope
- object references
- recent conversation state
- relevant memory layers
- signal or workflow context
- capability and policy constraints
- evidence/provenance references
- risk annotations

### Out of scope

- unlimited raw data dumping
- becoming a permanent storage system itself
- bypassing data-access policy
- letting each provider improvise its own context model

A context pack is a compiled runtime bundle, not the entire world.

## 5. Design principles

### Minimal but sufficient
Include enough information to reason well, but no more than necessary.

### Scoped
Everything in the pack should be consistent with company, role, and channel boundaries.

### Provenanced
Each important fact should be traceable to a source, object, or memory layer.

### Composable
A pack may combine multiple allowed inputs: identity, objects, memory, signals, manifests, and conversation history.

### Reusable
The same pack or sub-pack should be consumable by multiple cores and by replay/evaluation tooling.

## 6. Typical pack contents

A context pack should usually include:

- request metadata
- actor identity
- tenant/company identity
- channel/source metadata
- role and permission hints
- domain/object references
- current task or intent summary
- conversation slice if relevant
- memory slice if relevant
- signal envelope if triggered by events
- manifest/tool context
- risk and governance notes
- provenance references

## 7. Context layers

### Identity layer
Who is acting?
Includes:
- user, worker, admin, system actor, or module actor
- company boundary
- role hints
- session or channel identity

### Operational layer
What business objects matter right now?
Includes:
- jobs
- sites
- quotes
- invoices
- bookings
- service records
- communication objects
- workflow state

### Memory layer
What prior knowledge matters?
Includes:
- user memory
- tenant memory
- site memory
- job memory
- working memory
- prior preferences or standing rules

### Constraint layer
What limits apply?
Includes:
- policy
- capability declarations
- confirmation state
- safe mode
- role/permission fences

### Evidence layer
Why should the AI trust what it sees?
Includes:
- source references
- timestamps
- object IDs
- derived-versus-verified markers

## 8. Verified vs inferred data

A context pack must distinguish:
- verified facts
- derived fields
- inferred summaries
- uncertain notes

This is critical because AI synthesis becomes dangerous when inference is silently treated as fact.

### Example

- verified: job status = scheduled
- derived: time until appointment = 3 hours
- inferred: likely at risk due to missing access notes

All three may be useful, but they must not be confused.

## 9. Size and relevance discipline

Context packs should not become giant dumps.

### Preferred strategy

- start narrow
- include only relevant slices
- widen only when risk, ambiguity, or user need justifies it
- avoid redundant duplicate context between sections
- allow lane-specific subsets for specialist cores where appropriate

## 10. Pack variants

The platform may need multiple pack styles.

### Common examples

- conversational pack
- workflow pack
- signal-trigger pack
- review pack
- execution pack
- escalation pack
- replay pack

Each variant may share a common skeleton while changing emphasis.

## 11. Interaction with memory

Context packs consume memory through resolvers.

### Good pattern

- request context builder asks memory resolvers for relevant slices
- memory returns scoped, explainable context
- pack records which layers were included
- AI consumes the pack

### Bad pattern

- prompt directly pokes random memory tables
- hidden memory state changes per channel
- provenance is lost

## 12. Interaction with manifests and tools

A context pack should include the capability context needed for honest AI behavior.

### Useful manifest-derived additions

- declared tools
- allowed actions
- lifecycle participation
- channel permissions
- CMS/Omni surfaces if relevant
- API execution availability

This prevents the model from assuming it can do things the system has not declared.

## 13. Interaction with AEGIS

AEGIS needs context packs because governance should evaluate real state, not vague prompt text.

AEGIS may require the pack to expose:
- tenant scope
- role hints
- risk annotations
- capability references
- object states
- provenance markers

This lets governance make policy decisions on structured evidence.

## 14. Interaction with specialist cores

Specialist cores should receive:
- the shared core pack
- optional lane-specific sub-packs
- no hidden contradictory context

For example:
- Finance core may get more billing detail
- Creator core may get communication framing detail
- Logic core may get stronger workflow and dependency detail
- Entropy core may get anomaly-focused slices

The pack model should still keep these subsets traceable.

## 15. Interaction with Titan Core

Titan Core should receive execution-ready context, not raw sprawl.

That means:
- provider calls should be based on pack outputs
- execution prompts should cite the compiled pack
- audit and replay should reference the same pack ID/version
- model routing can consider pack size, type, and sensitivity

## 16. Replay and audit value

Context packs make replay possible.

A good replay path should be able to say:
- what the system knew
- what source it came from
- what was inferred
- what policy constraints existed
- which cores saw which slice
- what answer or action was produced

This is essential for debugging and trust.

## 17. Failure modes

### Oversized pack
Too much irrelevant context increases cost and confusion.

### Underpowered pack
Too little context creates shallow or wrong reasoning.

### Hidden inference
If inferred notes are unlabeled, AI may overstate certainty.

### Cross-tenant contamination
If tenant scoping fails, the entire architecture becomes unsafe.

### Channel divergence
If each channel builds its own pack differently, behavior becomes inconsistent.

## 18. Implementation notes

Context packs should be built by formal context builders and envelope builders, not improvised inside controllers or prompts.

They need:
- stable schema
- versioning
- provenance conventions
- tenant-safe resolvers
- sub-pack rules
- audit references
- replay support

## 19. Follow-up docs

This document should be followed by:
- `memory-architecture.md`
- `model-routing.md`
- `evaluation.md`
- `weighting-and-consensus.md`

## 20. Summary

Context packs are the bounded, provenance-aware runtime bundles Titan uses for reasoning and execution preparation. They keep AI inputs scoped, tenant-safe, auditable, and reusable across Titan Zero, AEGIS, specialist cores, and Titan Core, preventing the platform from reasoning through uncontrolled raw data or hidden assumptions.

    ## 21. Module, signal, and lifecycle attachments

    A context pack should be able to carry:
- current module or provider identity
- lifecycle stage and transition context
- signal envelope IDs and event metadata
- API surface/capability references
- package or tenant feature flags when they materially affect behavior

This matters because Titan should reason over declared system state, not over isolated prompts.

    ## 22. DTO and envelope pattern

    Context packs should be treated like DTO/envelope structures rather than free-form arrays built ad hoc in controllers. That improves:
- reuse across web/API/queue paths
- validation
- testability
- replay and audit
- future provider routing discipline

