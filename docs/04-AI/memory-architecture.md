# Memory Architecture Documentation

Layer: AI
Scope: Memory layers, scopes, recall policy, provenance, conditioning, and operational reuse
Status: Draft v1
Depends On: Titan Zero, context packs, tenant identity, site/job memory doctrine, privacy policy
Consumed By: Titan Zero, AEGIS, specialist cores, context builders, voice runtime, evaluation, future training loops
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

Memory architecture defines how Titan stores, resolves, scopes, and reuses knowledge over time. It exists so the system can remember what matters without collapsing into uncontrolled accumulation, privacy drift, or context pollution.

Titan needs memory because not every important fact lives in the current message, current job, or current screen. However, memory must remain disciplined, scoped, and auditable.

## 2. Why it exists

Without a formal memory architecture:
- the system forgets important recurring context
- users repeat preferences and rules endlessly
- site and job intelligence gets lost between visits
- AI outputs drift because recall is inconsistent
- privacy boundaries weaken
- different channels remember different truths

Memory architecture makes recall structured instead of accidental.

## 3. Core responsibilities

- define memory scopes and boundaries
- separate transient context from durable knowledge
- preserve provenance and confidence
- enforce tenant/company safety
- resolve relevant memory into context packs
- support conditioning of the assistant toward user/company preferences
- allow replay and audit of what was remembered
- avoid over-recall and irrelevant memory contamination

## 4. Core principles

### Scoped
Memory must belong to a clear scope.

### Useful
Only keep what is likely to help future work.

### Provenanced
Stored knowledge should point back to its source, trigger, or evidence.

### Bounded
Memory should not grow without hierarchy or retention rules.

### Privacy-first
Sensitive business and operational information should stay inside the correct tenant and surface only where allowed.

## 5. Memory layers

Titan should treat memory as layered, not monolithic.

### Session memory
Short-lived conversational state within the current interaction or near-term workflow.

Use for:
- current topic continuity
- temporary working assumptions
- clarification state
- in-progress task context

### Working memory
The active scratch context used by Titan during a complex multi-step task.

Use for:
- intermediate reasoning state
- current objective pack
- structured sub-results
- temporary coordination between cores

### User memory
Longer-lived preferences, habits, vocabulary, and interaction patterns linked to one user.

Use for:
- preferred terminology
- response format preferences
- standing workflow expectations
- common correction patterns

### Tenant or company memory
Shared operating rules, norms, and preferences across a business or tenant.

Use for:
- company operating standards
- preferred automation thresholds
- approval habits
- document style or escalation policy
- recurring scheduling or billing behaviors

### Site memory
Operational knowledge attached to a recurring location or service site.

Use for:
- access method
- hazard notes
- alarms or codes
- special instructions
- recurring customer expectations
- site-specific service history

### Job memory
Knowledge attached to a recurring or long-running job context.

Use for:
- ongoing issues
- repeated blockers
- job-specific preferences
- prior proof or completion patterns
- client-specific work notes

## 6. Site and job memory doctrine

Site and job memory are essential to Titan’s service-business operating model.

### Site memory examples

- gate codes
- entry method
- key or lockbox instructions
- preferred arrival behavior
- fragile areas
- repeat site hazards
- recurring client-specific notes

### Job memory examples

- prior missed detail
- repeat material requirement
- preferred crew pattern
- issue history
- proof expectations
- customer-specific completion sensitivity

This memory should be scoped operational memory, not casual chat memory.

## 7. Verified vs inferred memory

Memory must distinguish:
- verified memory
- inferred memory
- stale memory
- contradicted memory

### Verified
Stored from trusted operational source, approved update, or repeated validated use.

### Inferred
Derived from patterns or AI summaries, useful but not equal to confirmed fact.

### Stale
Potentially outdated due to age or changing conditions.

### Contradicted
Conflicts with newer or stronger evidence and needs resolution.

This distinction is crucial in operations-heavy environments.

## 8. Recall policy

Not all stored memory should be recalled every time.

### Recall should consider

- tenant scope
- domain relevance
- recency
- confidence
- evidence quality
- channel
- task type
- risk level

### Good recall behavior

- narrow first
- widen only when needed
- prefer high-confidence operational memory
- avoid flooding the pack with low-value history

## 9. Conditioning vs memory

Titan Academy doctrine makes it important to distinguish memory from conditioning.

### Memory
Specific remembered facts, preferences, or context.

### Conditioning
The learned operating style of a user or company based on repeated corrections, approvals, and patterns.

Examples of conditioning:
- how formal replies should be
- how much autonomy is tolerated
- whether to default to draft mode
- how much detail finance reviews require
- preferred naming of objects and workflows

Conditioning changes behavior. Memory supplies facts.

## 10. Storage and retrieval roles

### Storage role
Different events may write to memory:
- approved corrections
- validated site notes
- repeat user instructions
- workflow outcomes
- explicit save events
- trusted imported sources

### Retrieval role
Context builders should retrieve memory by:
- scope
- relevance
- domain
- confidence
- recency
- policy allowance

Memory should not be accessed randomly inside prompts.

## 11. Governance and safety

AEGIS should constrain memory use where needed.

### Governance concerns

- cross-tenant contamination
- unsafe recall of private data
- stale operational instructions
- overconfident inference masquerading as fact
- retrieval from disallowed memory scope

Governed recall matters as much as governed execution.

## 12. Memory and voice

Voice surfaces need memory too, but voice must remain especially careful with recall.

### Voice memory rules

- keep recall concise
- prefer high-confidence operational facts
- avoid dumping too much history into spoken output
- require strong confidence before speaking sensitive details aloud
- preserve confirmation logic for high-impact remembered instructions

## 13. Memory and evaluation

A good memory architecture should support:
- replay of what was recalled
- comparison of recalled context versus actual outcomes
- stale-memory detection
- contradiction tracking
- refinement of recall policies over time

This is necessary if Titan is going to improve rather than simply accumulate.

## 14. Failure modes

### Over-recall
Too much memory makes outputs noisy and brittle.

### Under-recall
Important recurring operational facts are missed.

### Stale recall
Old site or billing knowledge is treated as current.

### Cross-scope leakage
One user, company, or site sees another’s memory.

### Hidden inference
Derived notes are treated like verified business truth.

## 15. Implementation notes

Memory should map into dedicated memory resolvers and policy layers under the AI platform and should integrate with:
- context pack builders
- audit references
- site/job operational records
- Titan Academy refinement loops
- user/company preference layers
- replay/evaluation tools

It should not be reduced to a single chat transcript store.

## 16. Follow-up docs

This document should be followed by:
- `model-routing.md`
- `weighting-and-consensus.md`
- `evaluation.md`

## 17. Summary

Memory architecture gives Titan durable, scoped recall without sacrificing privacy, auditability, or operational clarity. It separates session, working, user, tenant, site, and job memory so the assistant can remember what matters, forget what should fade, and condition itself toward each business’s operating style over time.

    ## 18. Retention, expiry, and contradiction handling

    Memory should not be permanent by default.

### Recommended controls
- expiry windows for uncertain or operationally volatile notes
- confidence downgrade when memory is contradicted by newer evidence
- explicit invalidation path for site/job instructions that change
- review checkpoints for sensitive durable memory
- distinction between historical archive and active recall set

This reduces stale-memory failure in live service operations.

    ## 19. Testing and verification

    Memory behavior should be testable:
- scoped recall tests
- contradiction and expiry tests
- tenant-boundary tests
- site/job memory retrieval tests
- conditioning-versus-fact separation tests

This matters because memory bugs often appear as subtle operational drift rather than direct exceptions.

