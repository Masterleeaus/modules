# Titan Channel Webhooks

## Purpose

Channel Webhooks are the inbound event gateway for the communications layer.

They receive delivery updates, inbound messages, read receipts, typing signals,
template status changes, media callbacks, and provider-side failures from each
supported channel.

Webhook handling must be:

- secure
- idempotent
- tenant-aware
- normalized
- replayable
- auditable

---

## Role in the Stack

Webhooks sit at the edge of the communications system.

They connect external providers to:

- Omni Bridge Layer
- Unified Inbox
- Delivery Tracking
- Conversation State Model
- Automation Engine
- Signal Engine

They are inbound transport adapters, not business logic containers.

---

## Location

Suggested locations:

`app/Platform/Communications/Webhooks/`
`app/Http/Controllers/Api/Communications/`
`routes/api.php`

Optional split by channel:

- EmailWebhookController
- SmsWebhookController
- WhatsAppWebhookController
- TelegramWebhookController
- MessengerWebhookController
- VoiceWebhookController

---

## Supported Event Families

Typical webhook event groups:

- inbound_message
- outbound_status
- delivered
- failed
- bounced
- opened
- clicked
- read
- reaction
- template_approved
- template_rejected
- media_ready
- call_started
- call_ended
- voicemail_created

Each provider maps these differently.

The webhook layer must normalize them before downstream use.

---

## Verification

Webhook endpoints must verify authenticity before accepting payloads.

Verification can include:

- signed headers
- HMAC signatures
- verification tokens
- app secrets
- IP allowlists where possible
- timestamp freshness checks

Unverified payloads must be rejected immediately.

---

## Idempotency

Providers may retry the same event multiple times.

Webhook intake must deduplicate by:

- external event id
- provider message id
- signature + timestamp hash
- tenant + channel + event key

Duplicate events should be safely acknowledged without repeating downstream effects.

---

## Canonical Webhook Envelope

All inbound webhooks should normalize into a canonical event envelope.

Example:

```json
{
  "tenant_id": 14,
  "channel": "sms",
  "provider": "twilio",
  "event_type": "delivered",
  "external_event_id": "evt_123",
  "external_message_id": "SM123",
  "occurred_at": "2026-04-15T12:00:00Z",
  "payload": {}
}
```

This envelope becomes the system contract.

---

## Intake Flow

Recommended flow:

1. receive request
2. verify signature
3. detect tenant/channel/provider
4. parse provider payload
5. deduplicate event
6. normalize envelope
7. persist raw + normalized event
8. dispatch downstream jobs/signals
9. return provider-compatible acknowledgment

This keeps ingress fast and safe.

---

## Tenant Resolution

Webhook handlers must resolve the correct tenant before processing.

Resolution can use:

- configured provider account mapping
- inbound phone number mapping
- email domain / mailbox mapping
- app/page identifiers
- channel credentials registry

No event should enter the system without a tenant boundary.

---

## Raw Payload Storage

Store both forms when feasible:

- raw provider payload
- normalized system envelope

Raw payload storage helps with:

- audits
- provider disputes
- parser fixes
- replay
- debugging edge cases

---

## Downstream Effects

After normalization, webhook events may trigger:

- inbox updates
- conversation state changes
- delivery tracking updates
- automation triggers
- operator notifications
- signal emission
- AI context updates

These effects should happen through jobs, listeners, or signals, not inline controller logic.

---

## Provider-Specific Parsing

Each provider should have a parser class.

Examples:

- TwilioWebhookParser
- MetaWhatsAppWebhookParser
- TelegramWebhookParser
- MessengerWebhookParser
- MailgunWebhookParser
- SendgridWebhookParser

Parsers should only translate payload shape into canonical fields.

They should not perform workflow logic.

---

## Security Rules

Webhook endpoints must:

- reject unsigned or invalid requests
- rate-limit abusive senders
- cap payload size
- sanitize logged content where needed
- isolate media fetching from request handling
- avoid executing user-supplied content

All webhook handlers are public attack surfaces and must be treated as such.

---

## Replay Support

Replay should be possible for normalized events and, where safe, raw payloads.

Replay supports:

- parser corrections
- recovery after downstream outages
- test reproduction
- audit reconstruction

Replay must preserve original timestamps and correlation ids.

---

## Failure Handling

If downstream processing fails, the webhook layer should:

- persist the failure
- keep the normalized event
- queue retries where appropriate
- avoid blocking the provider acknowledgment when safe

Do not rely on the provider to be the only retry mechanism.

---

## Relationship to Delivery Tracking

Delivery tracking should not pollute webhook controllers.

Webhook handlers only normalize and forward tracking events.

Delivery status projection belongs in the delivery tracking subsystem.

---

## Relationship to Signal Engine

Important normalized events can emit signals such as:

- message.received
- message.delivered
- message.failed
- conversation.reopened
- template.approved

These then flow into governance-aware automation.

---

## Relationship to AI

AI should never consume raw provider payloads directly.

AI context should derive from normalized webhook events, routed through the
Omni Bridge and conversation services.

---

## Outcome

Channel Webhooks convert unreliable, provider-specific external callbacks into
trusted internal communication events.

They are the inbound edge contract for Titan Omni.
