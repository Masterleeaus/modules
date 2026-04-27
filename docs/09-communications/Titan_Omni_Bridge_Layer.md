# Titan Omni Bridge Layer

## Purpose

The Omni Bridge Layer connects channel-specific engines to one shared
communications runtime.

It prevents each channel from inventing its own delivery model, identity map,
message state rules, webhook behavior, and retry logic.

The bridge converts:

- email events
- SMS events
- WhatsApp events
- Telegram events
- Messenger events
- voice events

into one canonical Titan communications contract.

---

## Core Role

The bridge sits between:

- channel adapters
- unified inbox
- conversation state engine
- routing and failover engine
- delivery tracking
- automation engine
- Titan Zero / Titan Omni interfaces

It is the normalization layer for omnichannel operations.

---

## Location

Primary runtime:

`app/Platform/Communications/Routing/`
`app/Platform/Communications/Support/`
`app/Platform/Communications/Dispatch/`

Suggested bridge namespace:

`app/Platform/Communications/Bridge/`

---

## Why It Exists

Without a bridge:

- every channel stores messages differently
- delivery states drift
- actor identities split
- webhook contracts diverge
- retries become channel-specific
- unified inbox becomes fragile

With the bridge:

- channels publish a shared message envelope
- channels consume a shared dispatch request
- conversation history becomes portable
- automation can target any channel consistently

---

## Canonical Message Envelope

All channels should normalize into a shared envelope.

Example:

```json
{
  "tenant_id": 14,
  "conversation_id": "conv_882",
  "channel": "whatsapp",
  "direction": "inbound",
  "external_message_id": "wamid.abc123",
  "participant": {
    "external_id": "+61400000000",
    "display_name": "Jane Smith"
  },
  "payload": {
    "type": "text",
    "text": "Can I reschedule tomorrow?"
  },
  "timestamps": {
    "occurred_at": "2026-04-15T09:14:00Z",
    "received_at": "2026-04-15T09:14:02Z"
  }
}
```

This envelope is channel-agnostic.

---

## Dispatch Contract

Outgoing sends should use one shared request format.

Example:

```json
{
  "tenant_id": 14,
  "conversation_id": "conv_882",
  "preferred_channel": "sms",
  "message": {
    "type": "text",
    "text": "Your technician is on the way."
  },
  "policy": {
    "fallback_enabled": true,
    "allowed_channels": ["sms", "whatsapp", "email"]
  }
}
```

The bridge resolves the correct channel adapter and returns a normalized result.

---

## Bridge Responsibilities

The Omni Bridge Layer owns:

- channel normalization
- dispatch handoff
- participant identity mapping
- canonical conversation linking
- attachment metadata normalization
- capability checks
- response normalization
- fallback handoff preparation

It does not own:

- policy approval
- business workflow rules
- UI rendering
- long-term analytics logic

---

## Adapter Model

Each channel engine should expose a bridge adapter.

Examples:

- EmailBridgeAdapter
- SmsBridgeAdapter
- WhatsAppBridgeAdapter
- TelegramBridgeAdapter
- MessengerBridgeAdapter
- VoiceBridgeAdapter

Each adapter must implement:

- canSend()
- normalizeInbound()
- normalizeOutboundResult()
- normalizeWebhookEvent()
- resolveParticipant()
- mapCapabilities()

---

## Identity Mapping

The bridge must link external identities into one participant model.

Examples:

- phone number
- email address
- Messenger PSID
- Telegram chat ID
- WhatsApp user ID

Mapped to one internal participant record scoped by tenant.

This is required so the same customer can appear in one unified inbox across
multiple channels.

---

## Capability Matrix

Not all channels support all features.

The bridge should expose capabilities such as:

- text
- image
- file
- template message
- reaction
- quick reply
- voice
- read receipt
- typing state

Routing decisions use this matrix before dispatch.

---

## Conversation Linking

One conversation may span multiple channels.

The bridge decides whether an inbound message should:

- attach to an existing conversation
- create a new conversation
- fork into a channel-specific thread
- re-open a closed conversation

Rules should be deterministic and tenant-scoped.

---

## Attachment Normalization

Attachments from every channel should normalize into shared metadata.

Minimum fields:

- storage key
- mime type
- file size
- original filename
- preview support
- source channel
- external media id

This allows inbox rendering and automation to stay channel-neutral.

---

## Error Normalization

Channel APIs fail differently.

The bridge should normalize errors into a common model:

- unauthorized
- rate_limited
- invalid_recipient
- template_rejected
- unsupported_payload
- transient_delivery_failure
- permanent_delivery_failure

This feeds retries, routing, and operator visibility.

---

## Audit and Traceability

Every bridge action should log:

- tenant
- channel
- conversation
- actor
- adapter used
- external ids
- normalized outcome
- fallback decision
- retry correlation id

This supports supportability, replay, and AEGIS review.

---

## Relationship to Unified Inbox

The Unified Inbox should never parse raw provider payloads directly.

It must consume only normalized bridge envelopes.

This keeps inbox logic stable even when channel providers change.

---

## Relationship to Routing and Failover

Routing chooses the preferred path.

The bridge executes that path using a channel adapter.

If dispatch fails, the bridge returns a normalized error so routing/failover
can attempt the next allowed channel.

---

## Relationship to AI

Titan Zero and Titan Omni should call the bridge through tooling or
communications services, not channel SDKs directly.

This keeps AI execution:

- governed
- tenant-safe
- provider-agnostic
- replayable

---

## Outcome

The Omni Bridge Layer is the contract that turns many channel engines into one
omnichannel system.

It is the key layer that makes Titan Omni operationally coherent.
