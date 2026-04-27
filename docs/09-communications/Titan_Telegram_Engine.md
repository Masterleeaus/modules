# Titan Telegram Engine

## Purpose

The Telegram Engine provides Titan with a structured messaging channel for
inbound conversations, outbound operational updates, bot-driven workflows,
and automation-triggered notifications.

It sits inside the Communications Engine and plugs into Omni routing,
templates, workflow handoff, signal processing, and delivery retry policies.

Telegram is treated as a channel surface, not a standalone business module.

---

## Core Role

The Telegram Engine is responsible for:

- sending outbound Telegram messages
- receiving inbound Telegram updates
- normalizing Telegram payloads into Titan conversation events
- mapping Telegram chat identities to tenant-scoped contacts
- routing delivery through queued dispatch
- reporting delivery attempts and failures
- exposing Telegram as a supported Omni channel

---

## Engine Location

Primary runtime location:

`app/Platform/Communications/Telegram/`

Suggested structure:

- Adapters/
- Dispatch/
- Formatters/
- Inbound/
- Identity/
- Templates/
- Webhooks/
- Support/

---

## Telegram Channel Model

Telegram messages are normalized into a shared channel envelope.

Canonical fields:

- tenant_id
- channel = telegram
- external_chat_id
- external_message_id
- contact_id
- direction
- template_key
- body
- attachments
- sent_at
- delivered_at
- failure_reason

This keeps Telegram compatible with the Unified Inbox and conversation state model.

---

## Outbound Flow

Outbound send sequence:

1. request created by user, workflow, automation, or AI
2. Omni router selects Telegram
3. template engine renders payload
4. formatter converts payload for Telegram
5. queued delivery job dispatches to provider
6. delivery result stored in communications log
7. retry or escalation triggered if needed

Telegram dispatch must always be queue-backed.

---

## Inbound Flow

Inbound updates enter through Telegram webhook handlers.

Responsibilities:

- verify source authenticity
- parse message/update payload
- resolve tenant and contact mapping
- normalize content into Titan event format
- append to conversation thread
- emit inbound.communication.received signal

Inbound Telegram traffic becomes part of the same conversation graph as email,
SMS, WhatsApp, and Messenger.

---

## Identity Mapping

The engine must map Telegram users and chats to Titan records safely.

Identity resolution may use:

- telegram user id
- chat id
- phone number if present
- linked contact record
- prior conversation thread

Mapping is always tenant-scoped.

A Telegram identity must never bleed across companies.

---

## Message Types

Supported payload classes:

- plain text
- rich text fallback-safe messages
- media attachments
- links
- quick response prompts represented as conversational options
- bot workflow prompts

Titan should degrade unsupported rich controls into readable text.

---

## Template Integration

Telegram uses the shared Titan template system.

Templates provide:

- body text
- variable placeholders
- locale-aware rendering
- channel-specific formatting rules
- fallback content for unsupported blocks

Template ownership remains in the shared communications layer, not inside the
Telegram adapter itself.

---

## Routing Rules

Omni may select Telegram when:

- the contact has Telegram as an approved channel
- the workflow specifies Telegram delivery
- Telegram is the cheapest valid route
- Telegram is the preferred conversational channel
- failover policy promotes Telegram after another channel fails

Routing decisions must be visible and auditable.

---

## Retry and Failure Handling

Telegram retries must follow central communications retry policy.

Failures may include:

- invalid chat mapping
- revoked bot access
- blocked bot
- provider timeout
- malformed payload
- rate limit hit

The engine should record:

- attempt count
- provider response
- normalized failure code
- retry eligibility
- escalation target

---

## Signals

Common Telegram-related signals:

- communication.telegram.send_requested
- communication.telegram.sent
- communication.telegram.failed
- communication.telegram.received
- communication.telegram.reply_detected

These plug into automation, workflow progression, and support monitoring.

---

## Automation Handoff

The Telegram Engine supports:

- reminder sends
- booking confirmations
- schedule updates
- follow-up prompts
- customer support flows
- internal ops alerts

Automation may request Telegram delivery, but delivery rules still pass through
routing, permission, and channel validation layers.

---

## AI + Omni Integration

Titan Zero may use Telegram for:

- assistant-originated replies
- guided customer conversations
- operational prompts
- recovery sequences

AI-generated Telegram messages must still use:

- policy validation
- template/render rules
- tenant-safe context
- audit logging

---

## Governance

AEGIS should be able to restrict Telegram usage for:

- sensitive financial messages
- regulated customer content
- unapproved outbound campaigns
- high-risk AI-generated content

This keeps Telegram under the same governance model as every other channel.

---

## Developer Rules

Do:

- keep Telegram channel-abstracted
- queue all outbound sends
- normalize inbound and outbound payloads
- preserve delivery logs
- reuse shared templates and routing logic

Do not:

- hardcode workflow rules inside Telegram handlers
- bypass Omni routing
- store Telegram-only state as the source of truth for conversation state
- duplicate retry logic already owned by the communications engine

---

## Outcome

The Telegram Engine makes Telegram a first-class Titan channel:

- routable
- templated
- auditable
- automation-aware
- AI-compatible
- tenant-safe
- inbox-compatible

It extends the communications engine without breaking the shared omnichannel model.
