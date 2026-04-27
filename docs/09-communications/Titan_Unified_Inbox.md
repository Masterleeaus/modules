# Titan Unified Inbox

## Purpose

The Titan Unified Inbox is the operator-facing conversation surface that merges
messages, events, and AI-assisted actions from all supported channels into one
tenant-scoped workspace.

It sits above individual channel engines and below Titan Zero, Omni routing,
and workflow automation.

The inbox is the operational control layer for:

- email
- SMS
- WhatsApp
- Telegram
- Messenger
- voice follow-up records
- internal notes
- approval prompts
- AI-suggested replies

---

## Design Goals

The inbox must provide:

- one thread model across channels
- tenant-safe isolation
- message chronology
- participant identity resolution
- channel-aware reply handling
- assignment and ownership
- SLA visibility
- AI suggestion surfaces
- approval-aware actions
- auditability

---

## Core Principle

Channels stay channel-native.

The inbox stays system-native.

This means Titan never flattens away channel meaning. It normalizes enough to
support one operator surface while preserving source-channel constraints.

---

## Runtime Position

Primary layer:

`app/Platform/Communications/`

Related subsystems:

- Routing
- Templates
- Notifications
- Omni
- Signals
- Workflow Engine
- AI Tool Registry

Suggested inbox namespace:

`app/Platform/Communications/Inbox/`

---

## Core Components

Suggested structure:

- Threads/
- Messages/
- Participants/
- Assignments/
- Labels/
- SLAs/
- Drafts/
- Suggestions/
- Approvals/
- Audit/
- Search/
- Support/

---

## Thread Model

A thread is the canonical operational conversation container.

A thread may include:

- one channel only
- multiple linked channels for the same contact
- human and AI participants
- internal notes
- approval messages
- workflow-linked messages

Thread fields should include:

- company_id
- thread_key
- primary_channel
- contact_id
- status
- assigned_user_id
- priority
- last_message_at
- unread_count
- waiting_on
- requires_approval
- archived_at

---

## Message Model

Each message record should preserve source fidelity.

Recommended fields:

- company_id
- thread_id
- channel
- external_message_id
- direction
- sender_ref
- recipient_ref
- body_text
- body_rich
- attachments_json
- delivery_status
- received_at
- sent_at
- failed_at
- metadata_json

---

## Participant Resolution

The inbox must resolve participants into a stable system identity when possible.

Resolution sources:

- contacts
- customers
- workers
- companies
- external unknown participants

This allows one conversation to unify across:

- customer email
- customer SMS
- customer WhatsApp

without losing source detail.

---

## Assignment Model

Threads may be assigned to:

- unassigned queue
- specific user
- team queue
- AI triage queue
- approval queue

Assignment rules should support:

- manual assignment
- auto-routing
- skills-based routing
- channel-based routing
- priority escalation

---

## Status Model

Recommended thread statuses:

- open
- pending
- waiting_customer
- waiting_internal
- waiting_approval
- resolved
- archived
- blocked

Statuses should be workflow-aware, not just cosmetic.

---

## Reply Handling

Replies are always sent through the owning channel engine.

Inbox does not send directly.

Flow:

select thread
build reply payload
resolve channel policy
send via channel adapter
store outbound message
update thread state
emit conversation signal

---

## Drafts

The inbox should support:

- human drafts
- AI-generated drafts
- approval-required drafts
- scheduled drafts
- reusable quick replies

Drafts should store:

- target thread
- channel
- template reference
- author type
- approval state
- scheduled send time

---

## AI Assist Layer

Titan Zero can enrich the inbox with:

- suggested replies
- tone variants
- escalation detection
- sentiment summaries
- missing-context prompts
- next-best-action suggestions
- workflow trigger suggestions

AI suggestions must never bypass permissions or approval rules.

---

## Search and Filtering

Required filters:

- channel
- assignee
- status
- unread
- label
- SLA risk
- last activity
- contact
- company
- approval state

Search should index:

- message text
- participant names
- external IDs
- labels
- template usage
- thread metadata

---

## SLA Surfaces

The inbox should expose:

- first response due
- next response due
- idle threshold
- escalation threshold
- aging buckets

These timers feed:

- routing
- reminders
- escalation engine
- manager dashboards

---

## Audit Requirements

All inbox actions should log:

- assignment changes
- status changes
- reply sends
- draft approvals
- AI suggestion acceptance
- label changes
- merges and splits

This preserves operator accountability and replayability.

---

## Signals

Recommended signals:

- thread.created
- thread.assigned
- thread.unread
- thread.replied
- thread.awaiting_approval
- thread.resolved
- message.received
- message.sent
- message.failed

These should be declared through module manifests and routed into the Signal Engine.

---

## Relationship to Omni

Omni owns channel reach.

Unified Inbox owns channel operations.

Omni decides where messages can go.
Inbox decides how operators and AI work with the resulting conversations.

---

## Outcome

The Unified Inbox turns many disconnected channels into one governed,
searchable, AI-augmented operational workspace without erasing channel truth.
