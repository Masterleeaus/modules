# Titan Zero Documentation

Layer: Automation
Scope: Durable handoff and intake patterns for engine-safe automation delivery
Status: Draft v1
Depends On: Automation engines, signals, queues, process records, idempotency, runtime state
Consumed By: Titan Zero, AEGIS, module actions, communications, Duo backfeed, recovery engine
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan uses outbox and inbox relay patterns so automation work is handed off, retried, and replayed safely without losing causality or creating duplicate execution.

## 2. Why it exists

The engine blueprint explicitly gives the automation layer **Outbox** and **Inboxes** as first-class runtime folders, not incidental implementation details. The full platform also relies on asynchronous event pipelines, idempotent processing, and cross-system backfeed between Worksuite and Studio. fileciteturn3file15

Without an outbox/inbox relay:

- module code can emit side effects before its own transaction is durable
- retries can resend the same action without a shared dedupe boundary
- cross-system events can arrive without traceable ownership
- recovery has no stable source record to re-drive from
- customer timeline backfeed can diverge from operational truth

## 3. What the outbox is

The **outbox** is the durable list of runtime emissions produced by a successful local decision.

Typical outbox items:

- signal emissions
- reminder dispatch requests
- escalation notices
- customer communication sends
- Duo backfeed events
- approval requests
- recovery replay requests

Rule:

A module or engine should **commit its local state first**, then persist an outbox item that describes what must happen next.

## 4. What the inbox is

The **inbox** is the durable intake boundary for received runtime work.

Typical inbox items:

- inbound studio events
- module-emitted signals waiting for automation intake
- queue redeliveries awaiting dedupe check
- approval callbacks
- operator re-drive commands
- partner-system webhooks normalized into Titan envelopes

Rule:

An inbox item is not yet business progress. It is a **candidate input** that must be validated, deduplicated, and bound to a process record before execution continues.

## 5. Why this pattern matters in Titan

Titan is not one app with one request cycle. It spans:

- tenant-scoped module actions
- queued work
- communications channels
- AI/governance approvals
- Duo-mode cross-system exchange
- replay and recovery paths

That means delivery reliability cannot live only inside a controller, webhook, or queue worker. It needs a runtime relay contract.

## 6. Canonical relay flow

1. local module or engine decides an emission is required
2. business state is committed locally
3. an outbox item is written with correlation and idempotency data
4. relay workers move the item to the correct engine/channel boundary
5. receiver writes an inbox item
6. inbox intake validates schema, tenant, authority mode, and dedupe state
7. a process record/runtime record is created or resumed
8. execution proceeds, pauses for approval, retries, escalates, or dead-letters as needed

## 7. Required metadata

Every outbox/inbox record should carry:

- tenant/company identifier
- authority mode
- source module/engine
- event or action type
- correlation ID
- causation ID
- process record ID if known
- idempotency key
- attempt count
- created timestamp
- payload checksum or version marker

This is what lets replay, audit, and recovery stay deterministic.

## 8. Relationship to process records

The outbox/inbox relay is the **transport spine**.
The process record is the **execution spine**.

They should be linked, but not collapsed into one table.

Why:

- the same process may emit multiple outbox items
- the same inbox may attach to an existing process
- transport status and execution status change at different rates

## 9. Relationship to decisions and approvals

Decision envelopes describe *what Titan wants to do and why*.
Outbox items describe *what must now be handed off durably*.
Inbox items describe *what has arrived and awaits governed intake*.

Approvals can interrupt the flow at either edge:

- before an outbox item is allowed to relay
- after an inbox item is accepted but before execution continues

## 10. Required safety checks

### Before writing outbox

Check:

- local transaction success
- tenant scope
- authority-mode legality
- event type allowed by manifest/policy

### Before accepting inbox

Check:

- payload shape/version
- source trust
- idempotency key
- duplicate causation ID
- tenant routing
- authority-mode compatibility

## 11. Cross-system Duo pattern

The constitution material states that Studio → Worksuite and Worksuite → Studio exchanges use asynchronous event pipelines and idempotent processing. fileciteturn3file15 fileciteturn3file16

That means Duo backfeed should not be implemented as direct, hidden side effects. It should pass through:

- Studio outbox
- transport relay
- Worksuite inbox
- process/intake validation
- timeline or automation execution

And the reverse path should work the same way.

## 12. Dead-letter interaction

If relay fails repeatedly or intake rejects the payload permanently:

- do not silently drop the message
- preserve the outbox/inbox item
- attach reason codes
- move to dead-letter handling
- allow operator or recovery-driven re-drive

## 13. Anti-patterns

### Direct side-effect send inside business action

Bad:
- booking action both saves DB state and directly calls channel API

Good:
- booking action commits state, writes outbox, relay handles delivery

### Queue-only reliability

Bad:
- assuming queue retries alone are enough

Good:
- queue is transport; outbox/inbox is the durable relay contract

### No inbox dedupe

Bad:
- every redelivery becomes new execution

Good:
- inbox checks idempotency and existing process linkage first

## 14. Minimum implementation outcome

A Titan automation runtime using outbox/inbox relays should guarantee:

- durable emission after local commit
- durable intake before execution
- replayable history
- deduplicated redelivery
- cross-system causality tracing
- safe escalation, dead-letter, and recovery re-drive

## 15. Outcome

Outbox and inbox relays turn Titan automation into a durable, replayable runtime fabric. They prevent delivery mechanics from leaking into business actions and make cross-engine, cross-channel, and cross-system automation safe enough to retry, audit, quarantine, and recover.
