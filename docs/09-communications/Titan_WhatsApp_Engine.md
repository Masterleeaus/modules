# Titan Zero Documentation

Layer: Communications + Channels
Scope: WhatsApp delivery engine, rich mobile messaging, templates, media, replies and conversation continuity
Status: Draft v1
Depends On: Titan Channel Architecture, Titan Routing and Failover, Titan Message Templates, Titan SMS Engine
Consumed By: Omni router, unified inbox, receptionist flows, automation engine, workflow reminders, operator panels
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define the Titan WhatsApp engine as the canonical subsystem for rich conversational mobile messaging with media, structured templates, and persistent reply continuity.

## 2. Why it exists

WhatsApp offers a stronger conversational surface than SMS for many service-business workflows: appointment coordination, quote follow-up, field updates, media sharing, and customer support. Titan needs WhatsApp as a first-class governed channel, not a bolt-on integration.

The WhatsApp engine gives Titan:

- richer mobile messaging than SMS
- structured templates for business-initiated messages
- media and document delivery
- conversation continuity and readable operator history
- a strong path for Omni receptionist and support flows

## 3. Core responsibilities

- render WhatsApp-safe message variants from shared templates
- support business-initiated and user-initiated messaging paths
- send text, media, documents, and structured actions where supported
- maintain conversation linkage and inbound reply handling
- enforce template approval and channel policy requirements
- align with routing, failover, and escalation rules

## 4. Boundaries

### In scope

- text and rich WhatsApp messaging
- document and media sends
- inbound replies and conversation threading
- business-initiated template sends
- channel-specific suitability and policy checks
- normalized delivery and read-state events where available

### Out of scope

- generic social campaign management
- SMS carrier behavior
- voice calling
- UI-specific message composer design
- non-WhatsApp channel SDK details

## 5. Architecture

The WhatsApp engine should live under:

```text
app/Platform/Communications/WhatsApp/
├─ Builders/
├─ Providers/
├─ Templates/
├─ Conversations/
├─ Media/
├─ Inbound/
├─ Webhooks/
├─ Policies/
└─ Support/
```

The engine has six layers:

### Template layer

Supports approved business-initiated templates and channel-safe content rendering.

### Conversation layer

Maintains conversation ids, participants, timestamps, and inbound-outbound threading.

### Media layer

Handles images, PDFs, proof attachments, quotes, invoices, and other supported file types.

### Provider layer

Abstracts official APIs or tenant-authorized provider infrastructure.

### Webhook layer

Ingests send results, delivery updates, read updates, and inbound messages.

### Policy layer

Checks:

- recipient opt-in or allowed contact status
- template eligibility
- session or conversation window rules
- tenant restrictions
- channel-specific compliance constraints

## 6. Contracts

### Outbound WhatsApp contract

Minimum fields:

- tenant_id
- conversation_target
- body or template reference
- optional media references
- correlation_id
- delivery_class
- template_id if required
- metadata

### Session or template contract

The engine must distinguish between:

- freeform replies inside an active conversation window
- business-initiated messages requiring an approved template
- automation sends with structured action requirements

### Media contract

Media must be:

- authorized
- tenant-safe
- virus-checked or sanitized per platform policy
- available in supported format and size

## 7. Runtime behavior

Normal flow:

1. communication intent approved
2. routing selects WhatsApp as primary or fallback
3. engine determines freeform or template path
4. shared template variant rendered
5. optional media resolved
6. provider adapter dispatches message
7. webhook events update delivery and read state
8. inbound replies continue the conversation thread

WhatsApp should be preferred for:

- customer support and two-way messaging
- appointment coordination
- media-rich field updates
- quote and invoice follow-up where documents matter
- receptionist-style conversational flows

If WhatsApp is unavailable, Titan may fall back to SMS or email depending on urgency, payload type, and customer preference.

## 8. Failure modes

### Template mismatch

If a business-initiated message requires a preapproved template and none is valid, Titan must not send freeform content as a substitute. It should reroute, defer, or escalate.

### Media rejection

If file size or format is invalid, the send should fail before dispatch and create a visible operator or automation error.

### Conversation window violation

If freeform send rules are not satisfied, reroute through approved template paths or alternate channels.

### Webhook inconsistency

Preserve raw webhook payloads when normalization fails so operator review and replay remain possible.

## 9. Dependencies

Upstream:

- message templates
- routing and failover
- unified inbox or conversation state model
- file generation services
- consent and communications settings

Downstream:

- operator inboxes
- receptionist and support flows
- workflow reminders
- delivery tracking
- AI summaries and conversation assist

## 10. Open questions

- Should the MVP support one provider path only or abstract multiple providers immediately?
- How should template approval status be cached and refreshed per tenant?
- What conversation-state data should be retained for AI assist versus privacy minimization?

## 11. Implementation notes

Keep WhatsApp-specific session rules and template gating inside the engine, not inside UI callbacks. Reuse the shared template system and normalize provider webhook events into the same internal delivery and conversation model used by other channels wherever possible.
