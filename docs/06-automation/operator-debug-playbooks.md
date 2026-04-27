# Titan Zero Documentation

Layer: Automation
Scope: Operator and support playbooks for diagnosing stuck, duplicated, denied, retried, quarantined, and replayed automation runs
Status: Draft v1
Depends On: runtime-state-store.md, process-record-integration.md, approval-runtime.md, retry-strategy.md, dead-letter-queues.md, outbox-inbox-relays.md, trigger-evaluation.md, engine-coordination-patterns.md
Consumed By: Operators, support teams, implementation engineers, incident responders, QA teams
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Provide practical debugging playbooks for the automation runtime so operators and engineers can diagnose failures consistently.

## 2. Why it exists

Architecture docs explain how the runtime should work. Operators need to know what to do when it does not.

Without playbooks:

- failures look random
- support teams inspect the wrong tables
- approvals appear lost when they are only paused
- duplicate runs are confused with retries
- dead letters get ignored until they become customer-visible incidents
- replay happens without proper safety checks

## 3. Core debugging rule

Always debug in this order:

1. identify the originating signal, proposal, or request
2. find the ProcessRecord
3. locate the active or last automation run
4. check approval state
5. check idempotency / overlap / lock state
6. inspect retry and dead-letter records
7. inspect outbox/inbox and delivery telemetry
8. inspect module action logs last

Do not start by guessing from the UI alone.

## 4. Playbook A: “Nothing happened” after a user action

### Symptom

An operator or customer says they triggered an action, but no visible result occurred.

### Check sequence

- confirm the originating module action or signal exists
- verify Signal Intake accepted it
- verify AEGIS did not reject it on policy grounds
- verify Sentinel did not block it on readiness grounds
- locate Decision Envelope if AI/governed path was used
- find ProcessRecord and confirm a run was opened
- inspect whether the run is paused in approval state
- inspect outbox/inbox if the issue is really delivery, not execution

### Common causes

- signal validation failure
- approval pause waiting for operator
- duplicate suppression by idempotency key
- hidden overlap lock preventing execution
- downstream delivery failure after action succeeded

## 5. Playbook B: duplicate execution suspected

### Symptom

A booking, message, reminder, or billing action appears to have run twice.

### Check sequence

- compare originating signal IDs
- compare idempotency keys
- compare run causation chain
- check whether one path is a retry and the other is a fresh run
- inspect lock acquisition / overlap policy
- inspect relay workers for duplicate outbox consumption
- inspect module action logs for side effects written twice

### Common causes

- idempotency scope too weak
- downstream channel duplicate send with missing relay dedupe
- manual replay launched without replay-safe checks
- two valid triggers converging on the same action with no coalescing policy

### Important distinction

A retry is not a duplicate if it belongs to the same governed attempt chain.

## 6. Playbook C: stuck in approval

### Symptom

An automation item is visible but never advances.

### Check sequence

- locate `automation_approvals` record
- confirm approval status: pending, denied, expired, approved
- confirm approval row is attached to the correct ProcessRecord and run
- confirm operator audience and permission routing
- confirm resume event fired after approval
- confirm runtime worker picked up the resumed item

### Common causes

- approval not assigned to a visible operator surface
- approval approved in UI but resume event not emitted
- approval expired and was never escalated
- denied state hidden as pending in the UI layer

## 7. Playbook D: repeated retries with no resolution

### Symptom

The same run keeps retrying or appears to be bouncing.

### Check sequence

- inspect retry policy attached to engine family
- inspect attempt count and next scheduled retry
- inspect error classification: recoverable vs terminal
- confirm service health of downstream dependency
- inspect whether each retry is hitting the same root cause
- confirm exhaustion policy routes to dead letter correctly

### Common causes

- recoverable classification incorrectly applied to terminal errors
- retry policy too aggressive for outage conditions
- missing circuit-breaker or dependency health gate
- repeated lock contention mistaken for action failure

## 8. Playbook E: dead-letter quarantine

### Symptom

A runtime item is quarantined and user-facing work is incomplete.

### Check sequence

- inspect dead-letter payload, cause, engine family, and causation chain
- inspect last retry error and exhaustion reason
- inspect whether escalation notice was sent
- inspect related ProcessRecord state
- classify re-drive as safe, unsafe, or needs data repair
- only then choose replay or manual remediation

### Common causes

- downstream API outage
- malformed payload that passed too far downstream
- tenant/module configuration drift
- approval state inconsistency
- missing related entity in module data

## 9. Playbook F: replay after repair

### Symptom

A dead-lettered item is ready to be retried after dependency restoration or data repair.

### Safe replay sequence

- confirm root cause is resolved
- confirm replay is allowed by policy
- rebuild or validate context from ProcessRecord
- reconstruct Decision Envelope if needed
- generate a new governed run
- re-check idempotency and locks
- execute under normal runtime controls
- verify audit marks the dead-letter as resolved

### Never do this

- do not push the raw old payload straight back into a worker queue without governance
- do not bypass idempotency checks to “just make it work”

## 10. Playbook G: reminder sent but no escalation followed

### Symptom

A critical reminder was missed, but escalation never happened.

### Check sequence

- inspect reminder send record and delivery result
- inspect acknowledgement window policy
- inspect trigger-evaluation logs for reminder breach detection
- inspect whether escalation policy was enabled for that message type
- inspect whether process was already closed or suppressed

### Common causes

- no breach event emitted after timeout
- suppression event cancelled the escalation branch
- escalation policy disabled for that tenant or module
- reminder considered delivered-and-complete despite no acknowledgement requirement

## 11. Playbook H: channel delivered failure but action succeeded

### Symptom

Core business state updated, but customer/worker communication was not sent.

### Check sequence

- inspect module action success
- inspect outbox row creation
- inspect relay worker consumption
- inspect channel adapter response
- inspect retry/dead-letter path for delivery-only failure

### Why it matters

This is not the same as business action failure. Delivery can fail after state mutation.

## 12. Playbook I: trigger should have fired but did not

### Symptom

A timed or event-driven automation never started.

### Check sequence

- inspect trigger definition and enablement
- inspect recurrence/window rules
- inspect overlap locks
- inspect tenant feature flags
- inspect missing prerequisite lifecycle stage
- inspect whether suppression condition was met

### Common causes

- trigger disabled by feature flag
- lifecycle never reached the prerequisite stage
- window expired before evaluation worker ran
- overlap policy coalesced it away

## 13. Minimum operator dashboard views

Operators should be able to see:

- pending approvals
- active runs by engine family
- retry queues and attempt counts
- dead-letter quarantine list
- replay-eligible items
- recent escalations
- delivery failures by channel
- ProcessRecord summary per entity

## 14. Minimum support payload to collect before escalation to engineering

Capture:

- tenant/company identifier
- originating action or signal key
- ProcessRecord ID
- automation run ID
- approval record ID if present
- idempotency key
- retry count
- dead-letter ID if present
- related outbox/inbox row IDs
- last error code and timestamp

## 15. Anti-patterns

Avoid:

- fixing runtime issues only in UI code
- replaying without checking ProcessRecord and idempotency
- deleting dead-letter rows to hide the incident
- approving denied/expired items by direct DB mutation
- classifying all failures as transient retries
- debugging from customer-facing messages alone

## 16. Escalation threshold guidance

Escalate to engineering when:

- duplicate execution impacts business state
- replay safety is unclear
- approval resumes are broken system-wide
- dead-letter growth is systemic, not isolated
- delivery failures span multiple channels or tenants
- ProcessRecord causation chain is missing or corrupt

## 17. Related docs

- `runtime-state-store.md`
- `process-record-integration.md`
- `approval-runtime.md`
- `retry-strategy.md`
- `dead-letter-queues.md`
- `outbox-inbox-relays.md`
- `trigger-evaluation.md`
- `worked-engine-examples.md`
- `mermaid-flow-diagrams.md`
