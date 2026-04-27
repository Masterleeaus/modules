# Titan Messenger Engine

## Purpose

The Messenger Engine provides Titan with a Meta Messenger delivery and
conversation channel for support, follow-up, lead handling, operational updates,
and bot-assisted communication.

Messenger is modeled as part of the shared omnichannel communications layer.

It is not allowed to become a special-case silo.

---

## Core Responsibilities

The Messenger Engine owns:

- outbound Messenger delivery
- inbound webhook processing
- Messenger identity resolution
- payload normalization
- queued dispatch
- delivery status tracking
- integration with Omni routing and failover

It must align with the broader communications engine contract.

---

## Engine Location

Primary runtime location:

`app/Platform/Communications/Messenger/`

Suggested structure:

- Adapters/
- Dispatch/
- Inbound/
- Identity/
- Templates/
- Webhooks/
- Formatters/
- Support/

---

## Channel Abstraction

Messenger payloads are converted into the shared Titan communication envelope.

Required normalized fields:

- tenant_id
- channel = messenger
- external_thread_id
- external_message_id
- contact_id
- direction
- body
- attachments
- template_key
- sent_at
- delivered_at
- read_at
- failure_reason

This ensures Messenger can coexist inside the Unified Inbox with all other channels.

---

## Outbound Send Flow

Standard sequence:

1. signal or user action requests communication
2. Omni router selects Messenger
3. shared template is rendered
4. Messenger formatter converts payload
5. queued delivery job sends via provider
6. response is normalized and logged
7. retry or failover policy executes if required

Messenger sending must not happen inline inside controllers or UI actions.

---

## Inbound Webhook Flow

Inbound webhook responsibilities:

- verify request authenticity
- parse sender and conversation data
- resolve tenant-safe contact identity
- normalize message payload
- append event to conversation thread
- emit communication.messenger.received signal

This flow enables support, AI response, and automation-triggered follow-up.

---

## Identity Resolution

Messenger contacts may be linked using:

- Messenger user id
- linked contact records
- known conversation thread
- mapped page/channel configuration

Identity mapping must respect company boundaries.

Messenger identities are never global.

---

## Supported Content

Messenger should support normalized handling for:

- text messages
- attachments
- links
- structured prompts
- bot reply flows
- human handoff messages

Any channel-specific rich content should have a readable fallback for Titan’s
cross-channel template system.

---

## Template Integration

Messenger uses the shared Titan template layer.

Templates provide:

- message body
- placeholders and merge fields
- locale handling
- channel-specific formatting constraints
- fallback text for unsupported components

No Messenger-only template silo should be created unless explicitly required for provider policy.

---

## Routing + Failover

Messenger may be selected when:

- contact preference allows it
- workflow explicitly targets Messenger
- the conversation originated there
- channel availability rules prefer it
- failover policy routes from SMS, email, or WhatsApp to Messenger

If Messenger delivery fails, the engine should surface retryability and failover options,
not silently drop the event.

---

## Delivery States

Recommended delivery states:

- queued
- sent
- delivered
- read
- failed
- abandoned

Each state change should be stored in communications logs and exposed to the inbox timeline.

---

## Retry Policy

Retries follow the central communications retry framework.

Possible failure causes:

- invalid page configuration
- recipient unavailable
- permission revoked
- malformed payload
- provider outage
- timeout or rate limit

The engine records:

- attempt count
- normalized error code
- provider response snapshot
- retry eligibility
- escalation target

---

## Signals

Common Messenger signals:

- communication.messenger.send_requested
- communication.messenger.sent
- communication.messenger.failed
- communication.messenger.received
- communication.messenger.read

These can trigger workflow steps, support escalations, or AI summaries.

---

## Automation Integration

Messenger should support:

- lead intake replies
- booking follow-up
- status updates
- missed-service recovery
- review requests
- support conversations

Automation requests Messenger delivery through the communications engine, not by direct provider calls.

---

## AI Integration

Titan Zero may participate in Messenger conversations for:

- first response triage
- customer clarification
- scheduling prompts
- support summarization
- human handoff preparation

AI-originated Messenger sends must still respect:

- template rules
- policy gates
- approval requirements
- tenant-safe context resolution

---

## Governance

AEGIS may limit Messenger for:

- outbound campaigns without approval
- regulated messages
- high-risk transactional changes
- sensitive financial statements
- unsupported automated flows

This keeps Messenger aligned with global communications governance.

---

## Developer Rules

Do:

- keep Messenger inside shared channel abstractions
- use queued outbound dispatch
- normalize inbound payloads
- store delivery and read state transitions
- reuse shared routing, templates, and retry logic

Do not:

- hardcode Meta-specific rules into unrelated workflow logic
- duplicate inbox threading logic
- bypass tenant identity checks
- make Messenger the source of truth for communication history

---

## Outcome

The Messenger Engine makes Messenger a governed Titan channel that is:

- inbox-ready
- queue-backed
- routable
- automation-capable
- AI-compatible
- tenant-safe
- auditable

It extends Omni communications while preserving one unified channel architecture.
