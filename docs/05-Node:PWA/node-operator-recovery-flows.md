# Titan PWA Node Operator Recovery Flows

## Purpose

Defines how operators are guided through recovery when Titan PWA Nodes encounter sync failures, approval conflicts, corrupted local state, upgrade rollbacks, or prolonged offline drift.

This document turns technical recovery states into practical human workflows.

---

## Why Operator Recovery Matters

Even with durable queues and automated recovery, some failures still need visible operator handling.

Examples:

- a replay batch keeps failing
- an approval response invalidates local progress
- a storage migration rolls back
- a required overlay cannot be loaded
- local AI suggestions become unavailable
- the node is too stale to safely continue

Operators need structured flows, not vague error states.

---

## Recovery Flow Principles

Recovery UX should be:

- explicit
- minimally disruptive
- permission-aware
- tenant-safe
- resumable
- auditable

The node should explain what happened and what action is available next.

---

## Recovery Flow Classes

### 1. Connectivity Recovery

Used when sync is blocked by network conditions.

Operator should see:

- current offline state
- queue depth
- last successful sync
- whether safe local work can continue
- what will replay automatically later

Possible actions:

- continue offline
- retry now
- inspect queued work

### 2. Approval Conflict Recovery

Used when upstream approval denies or alters a locally staged transition.

Operator should see:

- action attempted
- approval result
- denial reason
- affected record
- next allowed steps

Possible actions:

- reopen task
- request supervisor review
- resubmit with changes

### 3. Replay Failure Recovery

Used when one or more queued mutations cannot be committed.

Operator should see:

- failed item count
- first failure reason
- whether other items were preserved
- whether local work is still safe to continue

Possible actions:

- retry batch
- isolate failed item
- send for manual review

### 4. Storage Recovery

Used when local storage integrity or migration fails.

Operator should see:

- recovery mode entered
- whether checkpoint restore succeeded
- whether unsynced work was preserved
- whether restart is required

Possible actions:

- restart node
- confirm recovery restore
- escalate to support/admin

### 5. Policy Recovery

Used when overlays, permissions, or governance rules block local behavior.

Operator should see:

- blocked action
- policy or permission source
- whether issue is temporary, role-based, or tenant-level

Possible actions:

- request access
- switch workflow path
- save and hand off

---

## Minimum Recovery Screen Elements

Every recovery flow should expose:

- what failed
- whether data is safe
- what the operator can do now
- what the system is doing automatically
- whether escalation is needed
- a trace or reference id

This reduces panic and duplicated work.

---

## Safe Continuation Rules

Recovery flows should clearly distinguish between:

### Safe to Continue

Local work may proceed and queue later.

### Continue with Limits

Only low-risk actions allowed until reconciliation.

### Must Pause

Governed or structurally unsafe situation; operator must stop or escalate.

This boundary should be explicit on screen.

---

## Escalation Paths

When recovery cannot be handled locally, escalation may route to:

- supervisor
- tenant admin
- support surface
- governed approval queue
- reconciliation dashboard

Escalation should preserve context so operators do not need to rewrite the incident manually.

---

## Recovery State Persistence

Recovery flows should survive refresh and app restart.

Persist at least:

- recovery class
- affected entity
- latest reason code
- available actions
- last attempted recovery step

This prevents losing the operator’s place in the recovery process.

---

## Reason Codes and Language

Technical reason codes are useful, but operator-facing wording should be plain.

Example mapping:

- STALE_STATE → “This task changed elsewhere before your update synced.”
- APPROVAL_REQUIRED → “This action needs approval before it can continue.”
- DUPLICATE_REPLAY → “This update was already received and won’t be sent again.”
- OVERLAY_BLOCKED → “Your current company policy does not allow this action here.”

This keeps recovery understandable.

---

## Relationship to Backbone Docs

This document operationalizes outcomes from:

- node-sync-engine.md
- node-governance-runtime.md
- node-runtime-storage-and-arbitration.md
- node-upgrade-coordination.md
- node-policy-overlays.md

It defines the human workflow on top of those technical states.

---

## Future Related Docs

Possible next docs:

- support-reconciliation-playbook.md
- supervisor-recovery-review.md
- node-incident-capture.md

---

## Recovery Entry Conditions

Recovery UX should trigger when any of the following persists beyond safe thresholds:

- repeated sync failure
- blocked approval resolution
- corrupted local checkpoint
- queue replay failure
- overlay incompatibility
- storage migration rollback
- stale runtime after required upgrade

## Recovery Outcomes

A recovery flow should always end in a clear state:

- resumed
- rolled back safely
- escalated upstream
- awaiting approval
- node locked pending intervention
