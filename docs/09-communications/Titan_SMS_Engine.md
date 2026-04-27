# Titan Zero Documentation

Layer: Communications + Channels
Scope: SMS delivery engine, compact transactional messaging, delivery receipts, consent and fallback
Status: Draft v1
Depends On: Titan Channel Architecture, Titan Routing and Failover, Titan Message Templates
Consumed By: Omni router, automation engine, alerts, reminders, workflow escalations, field operations
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define the Titan SMS engine as the canonical subsystem for short-form, high-urgency, low-latency mobile messaging.

## 2. Why it exists

SMS is often the fastest broadly reachable channel for reminders, status changes, urgent alerts, verification steps, and lightweight conversational nudges. Titan needs SMS to operate as a governed channel with explicit consent, compact template rendering, provider abstraction, and reliable delivery tracking.

The SMS engine gives Titan:

- a short-form delivery path for urgent or time-sensitive communication
- consistent handling of delivery receipts and retries
- mobile-first reminders for jobs, crews, customers, and operators
- channel-aware fallbacks when richer payloads do not fit SMS

## 3. Core responsibilities

- render compact SMS-safe text from shared templates
- enforce SMS-specific consent and quiet-hour policy
- send through provider adapters with queue-backed dispatch
- track provider message ids and delivery receipts
- split or reject oversized messages according to policy
- integrate replies into conversation and operator workflows where supported

## 4. Boundaries

### In scope

- transactional SMS
- reminder SMS
- operational alert SMS
- verification and acknowledgement messages
- provider abstraction
- delivery receipt ingestion

### Out of scope

- general marketing campaign strategy
- rich interactive message UIs
- voice calls
- WhatsApp-specific media and button features

## 5. Architecture

The SMS engine should live under:

```text
app/Platform/Communications/Sms/
├─ Builders/
├─ Providers/
├─ Receipts/
├─ Consent/
├─ Segmentation/
├─ Inbound/
├─ Policies/
└─ Support/
```

The engine is built around five layers:

### Render layer

Builds a compact message body from the shared template system using the SMS variant.

### Policy layer

Checks:

- recipient consent
- do-not-disturb windows
- regulatory restrictions
- sender identity rules
- tenant-level allowed use cases

### Dispatch layer

Queues SMS sends through one or more providers and stores a normalized outbound record.

### Receipt layer

Normalizes provider delivery states such as:

- queued
- sent
- delivered
- failed
- undeliverable
- expired

### Inbound layer

Handles replies where the provider and number type support inbound messaging.

## 6. Contracts

### Outbound SMS contract

Minimum fields:

- tenant_id
- recipient_phone
- body
- correlation_id
- template_id
- delivery_class
- policy_flags

### Delivery constraints

The engine must know:

- max segment length by encoding
- concatenation rules
- whether links are shortened or left raw
- whether Unicode is allowed for the route
- whether the payload should be summarized instead of expanded

### Consent contract

Consent must be explicit per phone endpoint and may include:

- transactional allowed
- reminder allowed
- marketing allowed
- quiet hours preference
- preferred fallback channel

## 7. Runtime behavior

Normal flow:

1. approved message intent created
2. routing selects SMS as primary or fallback
3. SMS template variant rendered
4. segmentation check runs
5. consent and quiet-hour policy evaluated
6. provider adapter dispatches message
7. receipt events update delivery state
8. reply, if available, enters conversation or operator flow

SMS should be preferred for:

- urgent short alerts
- booking reminders
- worker arrival or delay notices
- OTP or verification flows
- escalation when email is too slow or unread

When content exceeds practical SMS size, Titan should either:

- summarize and include a secure link
- reroute to email or WhatsApp based on suitability
- split into multiple segments only when policy allows

## 8. Failure modes

### Oversized or multi-segment sprawl

If a message becomes too long or expensive, Titan should reroute or summarize instead of blindly segmenting.

### Carrier or provider rejection

Normalize the error, suppress duplicate retries where invalid numbers are detected, and create a contact-quality signal.

### Quiet-hour conflict

Queue for later or reroute according to delivery class and emergency policy.

### Missing consent

Do not send. Log the decision and expose it to operators or automation review.

## 9. Dependencies

Upstream:

- message templates
- routing and failover
- contact consent model
- unified inbox or conversation state model
- tenant communications settings

Downstream:

- delivery tracking
- escalation rules
- operator alerts
- workflow reminders
- AI communication summaries

## 10. Open questions

- Should segmented SMS be allowed for all transactional classes or only selected ones?
- What default quiet-hour policy should apply across tenants before customization?
- How should short-link generation be governed for privacy-sensitive workflows?

## 11. Implementation notes

Do not hide SMS policy inside provider adapters or UI actions. Keep segmentation, quiet hours, and consent checks in the engine so every workflow, AI action, or manual send behaves consistently. Normalize provider receipts into one internal state model before analytics or automation consume them.
