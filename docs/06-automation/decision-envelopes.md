# Titan Zero Documentation

Layer: Automation
Scope: Decision envelopes used to carry automation decisions, context hashes, risk state, and execution readiness between engines
Status: Draft v1
Depends On: automation-engines.md, engine-coordination-patterns.md, approval-runtime.md, runtime-state-store.md, signals, AI orchestration
Consumed By: Titan Zero, AEGIS, recovery engine, replay tooling, module actions, operator review surfaces
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define the structured **decision envelope** that wraps automation decisions before those decisions are handed between engines, approvals, recovery, replay, or downstream execution surfaces.

## 2. Why it exists

A runtime engine should not hand off work using vague booleans such as “approved” or “ready”. Titan needs a consistent object that tells the next consumer:

- what decision was made
- what context it was made from
- how risky it was
- what evidence supported it
- whether execution is allowed, deferred, blocked, or review-only

Decision envelopes exist so automation handoff is structured, serializable, and replayable.

## 3. Core rule

A decision envelope is **not** the full raw payload and **not** the full chain of thought.

It is a bounded runtime object carrying the minimum durable decision data required for:

- execution handoff
- governance review
- operator interpretation
- replay and audit
- recovery and compensation logic

## 4. What a decision envelope should contain

At minimum, a decision envelope should include:

- envelope id
- tenant boundary (`company_id`)
- originating engine or subsystem
- source entity or signal reference
- decision type
- decision outcome
- readiness status
- risk tier
- confidence / certainty score if available
- required approvals or policy gates
- context hash or envelope hash
- evidence references
- idempotency key
- permitted next actions
- blocked next actions
- expiry or freshness policy
- created timestamp

## 5. Typical decision outcomes

Examples of standardized outcomes:

- `execute_now`
- `queue_for_execution`
- `wait_for_approval`
- `defer_until_condition`
- `retry_later`
- `recover_before_continue`
- `quarantine`
- `deny`
- `manual_review`

The important point is that these outcomes are explicit and machine-readable.

## 6. Readiness is separate from decision

A useful pattern is to separate:

- **decision** — what Titan believes should happen
- **readiness** — whether the system is currently allowed and able to do it

Example:

- decision: `send_followup`
- readiness: `blocked_waiting_approval`

This prevents downstream layers from pretending a sensible decision is immediately executable.

## 7. Context hashing

Decision envelopes should carry a stable hash of the context pack or minimal envelope used to produce the decision.

This supports:

- replay verification
- audit integrity
- duplicate suppression checks
- comparison between old and new decision runs

If the same input context should logically produce the same next-step decision, the hash becomes a strong diagnostic tool.

## 8. Evidence references

Decision envelopes should not embed every attachment or raw record. They should point to evidence.

Useful evidence references include:

- source signal ids
- process record ids
- related job / booking / invoice ids
- policy snapshot ids
- attachment ids
- retrieval chunk ids
- communications ids

This keeps the envelope bounded while still audit-friendly.

## 9. Relationship to approvals

Approvals should consume a decision envelope, not rebuild the decision from scratch.

That means approval runtime can evaluate:

- what Titan proposed
- why it proposed it
- what risk tier it assigned
- what action would happen if approved
- what freshness window applies

When an approval outcome arrives, it should append back onto the same envelope lineage or issue a derived envelope with the approval result attached.

## 10. Relationship to AI orchestration

The broader system already expects structured context packs, specialist routing, and governance-aware handoff. Decision envelopes are the automation-friendly form of that output.

In practice:

- AI produces structured proposal or recommendation output
- governance constrains it
- automation receives a decision envelope, not raw prose
- engines consume the envelope deterministically

This keeps the AI layer and runtime layer connected without forcing the runtime to interpret free-form language.

## 11. Relationship to ProcessRecord

ProcessRecord stores the execution lineage.

Decision envelope stores the portable decision object used at a point in that lineage.

Recommended relationship:

- ProcessRecord points to current decision envelope
- ProcessRecord history references prior envelopes
- replay can compare historical envelopes against regenerated ones

## 12. Relationship to recovery

Recovery should know whether it is retrying the same decision or generating a new one.

Decision envelopes make that possible.

Examples:

- same decision envelope, new retry attempt
- same intent, regenerated envelope after approval change
- same ProcessRecord, replacement envelope after policy update

Without the envelope boundary, recovery logic tends to blur safe repetition and changed intent.

## 13. Relationship to module actions

Modules should not receive raw orchestration internals. They should receive a stable execution-ready decision object.

So module actions should be able to consume:

- action name
- target ids
- permitted effect
- payload summary
- policy flags
- idempotency key
- audit references

This keeps module execution clean and reduces tight coupling to orchestration internals.

## 14. Minimum schema guidance

A practical decision envelope structure can include:

- `id`
- `company_id`
- `source_type`
- `source_id`
- `engine`
- `decision_type`
- `decision_outcome`
- `readiness`
- `risk_tier`
- `confidence_score`
- `context_hash`
- `idempotency_key`
- `expires_at`
- `meta`

The `meta` field can hold bounded structured details, but the stable top-level fields should stay consistent.

## 15. Operator-facing interpretation

Operator tools should render decision envelopes in plain language, for example:

- proposed action
- why it was proposed
- risk level
- readiness status
- whether approval is required
- what will happen on approval
- when the envelope expires

This makes envelopes useful to humans without requiring them to understand low-level runtime plumbing.

## 16. Anti-patterns

Avoid these mistakes:

- passing raw AI prose directly into execution code
- rebuilding a decision from logs at approval time
- collapsing readiness, risk, and decision into one text field
- storing full raw context instead of bounded references plus a hash
- letting every engine invent a different envelope shape

## 17. Recommended implementation split

- **AI / reasoning layer** proposes structured decisions
- **governance layer** constrains and annotates them
- **decision envelope builder** serializes the bounded decision object
- **automation engines** consume envelopes for execution or handoff
- **approval runtime** pauses, resumes, or denies envelope progression
- **recovery/replay tooling** compares and reconstructs envelope history

## 18. Outcome

With decision envelopes in place, Titan automation gains a stable contract between reasoning and execution.

That makes the system:

- safer
- easier to audit
- easier to replay
- easier to hand off between engines
- more deterministic for module execution
- more understandable for operators

Decision envelopes are therefore the transport object for automation intent, not just another metadata wrapper.
