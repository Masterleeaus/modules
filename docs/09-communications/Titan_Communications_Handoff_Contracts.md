# Titan Communications Handoff Contracts

## Purpose

Titan Communications Handoff Contracts defines how the communications layer
hands work to and from:

- automation engines
- workflow engine
- AI tool registry
- unified inbox
- node/device runtime
- signal engine
- governance and approval systems

This document exists to prevent channel logic, automation logic, and workflow
logic from drifting into overlapping responsibilities.

---

## Core Rule

Communications channels send and receive messages.

They do not own business workflow truth.

Workflow, automation, AI execution, and domain state must remain outside
channel engines.

Channels consume and emit structured contracts.

---

## Handoff Directions

There are five main handoff directions:

1. workflow → communications
2. automation → communications
3. communications → workflow
4. communications → AI/tooling
5. communications → node/runtime

Each direction needs a stable contract.

---

## Workflow to Communications

A workflow may request outbound communication for cases like:

- booking confirmation
- reminder sequence
- overdue invoice notice
- escalation update
- approval request
- dispatch exception

The request should include:

- tenant id
- message intent id
- target recipient reference
- approved template or message class
- allowed channels
- urgency
- correlation id
- related entity references
- approval reference if required

The workflow should not construct provider payloads itself.

---

## Automation to Communications

Automation engines may trigger communications from rules such as:

- after job completion
- before scheduled visit
- after no response window
- on payment overdue milestone
- on missed checklist or compliance event

Automation should hand off a communication intent, not raw channel details.

It should define:

- trigger source
- message class
- timing window
- retry expectations
- escalation policy
- recipient resolution mode

This keeps routing and failover inside communications infrastructure.

---

## Communications to Workflow

Inbound communications can create or update workflow state.

Examples:

- customer replies to reschedule
- worker reports access issue by message
- caller leaves urgent voicemail
- customer approves quote via channel action

The communications layer should not mutate business state directly.

Instead it emits structured signals such as:

- message.received
- message.intent.detected
- call.voicemail.captured
- communication.reply.classified
- approval.response.received

Workflow or module handlers then decide what to do.

---

## Communications to AI Tool Registry

AI may assist communications by:

- classifying inbound intent
- drafting response options
- choosing a safe template variant
- extracting structured entities
- proposing next best action

Communications should call the AI/tool layer using narrow contracts:

- current channel
- normalized message content
- conversation state reference
- tenant context
- allowed tool scope
- risk band

The AI layer returns proposals or structured analysis.
It should not bypass communication governance.

---

## Communications to Node Runtime

Communications may wake or update nodes by:

- push notification
- silent sync trigger
- unread conversation badge update
- call-back task creation
- approval prompt delivery

The handoff to nodes should carry compact, sync-friendly envelopes rather than
full domain records.

Example node envelope fields:

- node target or audience
- conversation id
- entity hint
- urgency
- action type
- deep link
- sync checkpoint hint

This matches the node-based runtime model rather than assuming the client is a thin view.

---

## Signal Engine Boundary

All cross-layer communication should eventually reduce to governed signals.

Examples:

- booking.reminder.requested
- message.dispatch.requested
- message.delivery.failed
- voicemail.captured
- channel.permission.denied
- communication.escalation.created

This lets AEGIS, audit, replay, and approval systems remain consistent
across channels and non-channel actions.

---

## Approval Boundary

Some handoffs require approval before communications can continue.

Examples:

- AI-drafted freeform outbound message
- financial collection escalation
- mass outbound campaign
- cross-channel failover into voice
- unusual schedule or commitment notice

Approval references must travel with the handoff contract so the next layer
knows whether execution is already authorized.

---

## Data Ownership Rules

Communications owns:

- channel payloads
- message dispatch state
- conversation transport metadata
- delivery receipts
- provider references

Workflow/domain layers own:

- booking state
- invoice state
- approval state
- dispatch state
- job completion truth

Node runtime owns:

- local cache
- local queue
- local badge/notification state

Keeping these ownership boundaries explicit prevents duplicated truths.

---

## Failure Handoffs

When communication fails, the failure handoff may route to:

- retry engine
- failover channel
- operator exception queue
- workflow escalation path
- AI summary for operator review

The contract should include:

- original message intent id
- failure class
- retry count
- current channel/provider
- recommended next step
- entity and conversation references

---

## Observability Handoffs

Communications should expose structured events for operations:

- dispatch accepted
- dispatch failed
- webhook delayed
- push subscription invalid
- telephony provider degraded
- inbox sync lagging

These handoffs feed observability and support dashboards without coupling them
to raw provider implementations.

---

## Responsibilities

Titan Communications Handoff Contracts owns:

- the boundary between channels and workflows
- the boundary between channels and automation
- the boundary between channels and AI/tooling
- the boundary between channels and node runtimes
- approval-aware communication intent structures
- cross-layer failure and observability envelopes
