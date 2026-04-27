# Titan Conversation State Model

## Purpose

The Titan Conversation State Model defines how conversations move through
operational states across channels, users, AI agents, and workflow layers.

It gives the communications stack a deterministic state machine instead of
loose per-channel flags.

---

## Why It Exists

Without a shared state model, each channel drifts into different meanings for:

- unread
- pending
- waiting
- resolved
- escalated
- abandoned

That breaks routing, inbox logic, SLA timers, and AI reasoning.

The state model creates one canonical operational contract.

---

## Scope

Applies to:

- inbox threads
- outbound conversations
- inbound conversations
- support interactions
- lead conversations
- booking conversations
- follow-up sequences
- approval conversations
- AI-assisted threads

---

## Canonical Layers

Conversation state should be tracked across four dimensions:

- engagement state
- responsibility state
- delivery state
- workflow state

This avoids overloading one status field with too many meanings.

---

## Engagement State

Represents the live interaction condition.

Recommended values:

- new
- active
- idle
- paused
- closed
- archived

Meaning:

- new: first message or newly opened thread
- active: recent back-and-forth activity
- idle: no activity within expected window
- paused: intentionally suspended
- closed: operationally completed
- archived: retained, not active

---

## Responsibility State

Represents who the system is waiting on.

Recommended values:

- waiting_customer
- waiting_internal
- waiting_agent
- waiting_manager
- waiting_approval
- no_wait

This dimension is essential for routing and SLA logic.

---

## Delivery State

Represents transport outcome for the latest outbound message.

Recommended values:

- queued
- sent
- delivered
- read
- failed
- bounced
- unknown

This is channel-derived but normalized for cross-channel reasoning.

---

## Workflow State

Represents linkage to business workflow.

Recommended values:

- detached
- linked
- action_required
- approval_required
- blocked
- completed

Examples:

A customer message may be active and waiting_internal while also being linked
to a quote workflow with action_required.

---

## State Transitions

Transitions should be explicit and event-driven.

Examples:

- inbound message received → engagement becomes active
- outbound reply sent → responsibility becomes waiting_customer
- internal note added → engagement unchanged
- approval requested → workflow becomes approval_required
- approval granted → workflow returns action_required or linked
- no activity threshold reached → engagement becomes idle
- manager closes thread → engagement becomes closed

---

## Thread Lifecycle

Suggested overall lifecycle:

new → active → idle → active → closed → archived

This lifecycle should remain independent from delivery and workflow sub-states.

---

## Waiting Model

Only one waiting owner should exist at a time for a standard thread.

Priority order when computing waiting_on:

1. approval_required
2. waiting_customer
3. waiting_internal
4. waiting_manager
5. waiting_agent
6. no_wait

This keeps operator expectations clear.

---

## Merge and Split Rules

When threads merge:

- preserve source channel metadata
- recompute engagement state from latest activity
- preserve unresolved workflow flags
- retain audit history for both origins

When threads split:

- create new state records
- retain parent linkage
- copy only relevant waiting and workflow markers

---

## AI Participation Rules

AI may influence state but should not invent unsupported state transitions.

Allowed AI actions:

- suggest close
- suggest waiting state
- suggest escalation
- suggest workflow linkage

Restricted AI actions:

- final close without permission
- approval completion without approval path
- workflow completion without domain confirmation

---

## SLA Integration

State model powers response expectations.

Examples:

- active + waiting_internal + high priority → urgent internal reply due
- active + waiting_customer → customer follow-up timer
- idle + action_required → re-engagement candidate
- closed + delivered failed → closure exception

---

## Presence Interaction

Presence may enrich state, but not replace it.

Examples:

- customer online does not mean active
- agent offline does not mean waiting_internal
- typing indicators are transient overlays

Canonical state remains event-derived and persisted.

---

## Storage Model

Suggested persisted fields:

- engagement_state
- responsibility_state
- delivery_state
- workflow_state
- state_changed_at
- waiting_since
- closed_at
- archived_at

State history should also be journaled for audit and analytics.

---

## Signals

Recommended signals:

- conversation.opened
- conversation.activated
- conversation.idled
- conversation.closed
- conversation.archived
- conversation.waiting_changed
- conversation.workflow_blocked
- conversation.approval_requested

These support routing, reminders, and AI context packs.

---

## Analytics Value

A real state model enables:

- true queue aging
- resolution metrics
- approval bottleneck tracking
- response-time accuracy
- reopen analysis
- AI recommendation evaluation

---

## Outcome

The Conversation State Model gives Titan one operational truth for all
channels, making inboxes, SLAs, AI suggestions, automation, and analytics
consistent across the whole communications stack.
