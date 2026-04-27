# Titan Zero Documentation

Layer: Automation
Scope: ProcessRecord integration for automation runtime state, lifecycle progression, replay, and audit reconstruction
Status: Draft v1
Depends On: automation-engines.md, lifecycle-engine.md, runtime-state-store.md, recovery-engine.md, signals, governance
Consumed By: Titan Zero, AEGIS, Sentinels, operations surfaces, replay tooling, support tooling, module actions
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define how automation runtime work is persisted into a durable **ProcessRecord** layer so the platform can reconstruct what happened, why it happened, and what should happen next.

## 2. Why it exists

Automation engines do not just need logs. They need a **rebuildable execution record**.

A simple queue-job history is not enough because Titan must support:

- cross-engine handoff
- lifecycle resumption after interruption
- operator review and approval pause states
- replay and rollback-aware analysis
- support investigation of partial execution
- future AI review of prior automation decisions

ProcessRecord exists so runtime work can be followed as a durable chain rather than inferred from scattered logs.

## 3. Core rule

A ProcessRecord is **not** the business entity itself.

It is the runtime record of:

- which entity or signal triggered work
- which engine accepted ownership
- which state the runtime is in now
- which decision or policy caused a transition
- whether side effects were emitted
- whether the work is resumable, replayable, quarantined, or complete

## 4. What it tracks

At minimum, the automation ProcessRecord should track:

- tenant boundary (`company_id`)
- source type and source id
- source signal or trigger key
- engine owner
- lifecycle stage or runtime phase
- current execution state
- idempotency key
- approval state if approval is required
- retry counters and retry policy snapshot
- timestamps for created, started, paused, resumed, failed, completed, quarantined
- last failure code and failure summary
- last emitted effect summary
- pointer to audit artifacts, attachments, or related envelopes

## 5. Recommended state model

A practical ProcessRecord lifecycle for automation is:

- `received`
- `validated`
- `accepted`
- `queued`
- `running`
- `waiting_approval`
- `paused`
- `retry_scheduled`
- `recovering`
- `quarantined`
- `completed`
- `cancelled`
- `denied`

This state model should be stable across engines even when each engine has its own internal sub-states.

## 6. Engine ownership model

Each ProcessRecord should have one clear **current owner**.

Examples:

- lifecycle engine owns progression decisions
- reminder engine owns reminder scheduling and dispatch preparation
- escalation engine owns breach handling
- recovery engine owns interrupted or failed work

Ownership can transfer, but ownership history must be appended rather than overwritten conceptually. A support operator should be able to see both the current owner and the handoff chain.

## 7. Relationship to runtime-state-store

The runtime state store answers:

- what is happening now?
- what should happen next?

ProcessRecord answers:

- what happened over time?
- what state transitions were durable?
- what evidence supports reconstruction?

The runtime state store may use denormalized or operationally convenient fields. ProcessRecord should remain the canonical historical spine for automation execution.

## 8. Relationship to signals

Every automation entry should be traceable back to a signal, trigger, or explicit user action.

ProcessRecord should therefore support:

- incoming signal id / key
- emitted signal ids
- approval proposal ids
- related entity ids
- node-origin metadata where applicable

This keeps automation reconstruction aligned with the signal-first architecture.

## 9. Relationship to approvals

When runtime work hits an approval boundary, the ProcessRecord must not disappear into a queue gap.

It should record:

- approval reason
- approval required by which rule or governance layer
- approval request id
- awaiting actor or role
- expiry or timeout policy
- outcome (`approved`, `denied`, `expired`, `cancelled`)

That allows approval runtime to resume or terminate work deterministically.

## 10. Relationship to retries and recovery

Retry state should be attached to the same ProcessRecord lineage rather than creating disconnected records for every retry.

Recommended pattern:

- one primary ProcessRecord per runtime intent
- retry attempts logged as child events or attempt rows
- recovery sessions linked back to the original record
- dead-letter quarantine linked to the same lineage

This prevents support tooling from mistaking five retries for five separate business actions.

## 11. Replay and audit value

Replay should consume ProcessRecord metadata rather than raw guesswork.

That means the ProcessRecord layer should preserve enough information to answer:

- what input envelope was used?
- what policy version applied?
- what idempotency key guarded the action?
- what decision caused the next transition?
- what side effects were emitted?
- did the prior run stop before or after external dispatch?

Without this, replay becomes unsafe and audit becomes speculative.

## 12. Minimum schema guidance

A ProcessRecord schema can vary, but the automation layer should expect fields conceptually like:

- `id`
- `company_id`
- `source_type`
- `source_id`
- `signal_key`
- `engine`
- `state`
- `phase`
- `idempotency_key`
- `approval_state`
- `attempt_count`
- `max_attempts`
- `last_error_code`
- `last_error_message`
- `next_run_at`
- `started_at`
- `paused_at`
- `completed_at`
- `quarantined_at`
- `meta`

The exact table name can differ, but the semantics should not.

## 13. UI and operator requirements

Operator surfaces should be able to render ProcessRecord data as:

- current state
- current owner engine
- blocked reason
- next scheduled action
- approval wait status
- retry count
- quarantine cause
- replay eligibility

This is the human-readable layer that turns deep runtime plumbing into supportable operations.

## 14. Anti-patterns

Avoid these mistakes:

- treating queue history as the only execution record
- creating a new unrelated row for every retry
- losing the link between approval pause and resumed work
- hiding side-effect emission behind generic “processed” status
- mixing business-entity status and automation-runtime status into one field
- storing only free-text logs without stable state fields

## 15. Recommended implementation split

- **signals** create or reference runtime intent
- **automation engines** mutate ProcessRecord state
- **approval runtime** appends approval pause/resume outcomes
- **recovery engine** consumes ProcessRecord lineage for repair/replay
- **operations UI** reads from ProcessRecord summaries
- **audit/replay tooling** relies on ProcessRecord as canonical runtime history

## 16. Outcome

With ProcessRecord integration in place, Titan automation becomes:

- traceable
- resumable
- replayable
- operator-readable
- governance-compatible
- safer across retries, approvals, and recovery

That makes ProcessRecord the durable spine of runtime automation, not just another log table.
