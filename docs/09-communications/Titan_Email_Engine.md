# Titan Zero Documentation

Layer: Communications + Channels
Scope: Email delivery engine, mail rendering, provider abstraction, receipts and reply handling
Status: Draft v1
Depends On: Titan Channel Architecture, Titan Routing and Failover, Titan Message Templates
Consumed By: Omni router, automation engine, workflow engine, unified inbox, operator panels
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define the Titan email engine as the canonical subsystem for outbound and inbound email communication.

## 2. Why it exists

Email remains essential for long-form, document-heavy, and traceable business communication. Titan needs email to behave as a governed system channel, not as ad hoc mail sends hidden inside controllers or job callbacks.

The email engine provides:

- templated outbound messages
- provider abstraction
- delivery and bounce tracking
- thread-safe reply handling
- attachment support
- routing and fallback alignment

## 3. Core responsibilities

- render approved email content from shared templates
- queue outbound delivery through provider adapters
- support HTML and plain-text variants
- attach documents and secure action links
- ingest reply metadata and provider events
- log delivery, open, bounce, and complaint states when available

## 4. Boundaries

### In scope

- transactional and conversational email
- attachments and secure links
- provider abstraction
- reply-to routing
- delivery event ingestion
- threading identifiers

### Out of scope

- generic campaign strategy
- domain business rules for specific modules
- operator-rich email editor design
- non-email channels

## 5. Architecture

The email engine should live under:

```text
app/Platform/Communications/Mail/
├─ Mailables/
├─ Builders/
├─ Providers/
├─ EventIngestion/
├─ Threads/
├─ Attachments/
├─ ReplyRouting/
├─ Policies/
└─ Support/
```

The engine has five layers:

### Message build layer

Builds email payloads from shared templates and render context.

### Provider layer

Sends mail through configured providers such as SMTP, API-based mail services, or tenant-supplied infrastructure.

### Threading layer

Preserves message-id, reply-to, correlation-id, and tenant-safe thread references.

### Event layer

Processes provider webhooks for:

- delivered
- deferred
- bounced
- complained
- opened
- clicked
- replied

### Inbound layer

Routes inbound replies into conversation records, operator inboxes, or automation handlers.

## 6. Contracts

### Outbound email contract

Minimum fields:

- tenant_id
- to
- subject
- html_body or text_body
- correlation_id
- delivery_class
- template_id
- metadata

### Attachment contract

Attachments should reference approved files only and may include:

- invoices
- quotes
- job reports
- proof images
- PDFs
- CSV exports

### Thread contract

Every outbound email should include:

- provider message id
- internal message id
- conversation id where relevant
- reply routing token

## 7. Runtime behavior

Normal flow:

1. communication intent approved
2. routing chooses email as primary or fallback
3. template engine renders subject and body
4. email builder composes payload and attachments
5. provider adapter queues or sends
6. provider events update delivery state
7. replies feed unified inbox or automation consumers

Email should be preferred for:

- long-form explanations
- document attachments
- receipts and statements
- lower-urgency but high-detail communication
- channels where archival visibility matters

## 8. Failure modes

### Hard bounce

Mark the address undeliverable, suppress repeated sends by policy, and create a contact hygiene signal.

### Soft bounce or temporary deferral

Retry according to routing retry profile before failover or escalation.

### Attachment failure

Fail before send if the attachment cannot be resolved, authorized, or generated.

### Reply parsing errors

Preserve the raw inbound payload for operator review rather than dropping the message.

## 9. Dependencies

Upstream:

- message templates
- routing and failover
- unified inbox model
- file or document generation services
- tenant mail settings

Downstream:

- conversation state model
- delivery tracking
- operator notifications
- AI summarization surfaces
- workflow and automation signals

## 10. Open questions

- Should tenant BYO mail providers be first-class from MVP or phase two?
- What minimum event set is required when providers do not support opens or clicks?
- How should shared inbox aliases map to departments or workflow lanes?

## 11. Implementation notes

Keep provider logic thin and normalize all provider events into one internal delivery model. Controllers and module actions should never send raw mail directly; they should emit message intents or call channel-level actions that pass through this engine.
