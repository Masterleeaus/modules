# Titan Zero Documentation

Layer: Communications + Channels
Scope: Cross-channel abstraction, delivery model, shared message pipeline
Status: Draft v1
Depends On: Titan Manifest System, Titan Signal Engine, Titan Automation Engine, Titan Workflow Engine
Consumed By: Omni router, channel adapters, automation engine, Titan Zero, PWA clients, admin/operator panels
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define the canonical communications architecture for Titan so all outbound and inbound messaging channels behave as one governed system instead of separate feature silos.

## 2. Why it exists

Titan must support email, SMS, WhatsApp, Telegram, Messenger, push, and voice as first-class channels. Without a shared architecture, each channel would duplicate formatting, retries, consent checks, routing logic, logging, and automation handoffs.

The channel architecture gives Titan:

- one delivery pipeline
- one audit model
- one template contract
- one routing model
- one retry and escalation surface
- one channel capability registry

## 3. Core responsibilities

- abstract channels behind a common delivery contract
- normalize message intents before channel dispatch
- separate message planning from transport execution
- support inbound and outbound communications consistently
- expose governed hooks to automation, workflows, and AI
- preserve tenant boundaries and channel-specific policy rules

## 4. Boundaries

### In scope

- channel abstraction
- delivery pipeline stages
- inbound and outbound message model
- shared template and payload contracts
- capability registry for supported channel features
- queue-backed execution model
- logging and delivery state tracking
- integration points with automation and workflow engines

### Out of scope

- UI-specific composer layouts
- individual provider SDK code details
- marketing copy strategy
- domain-specific business rules for one module
- payment or invoicing domain logic

## 5. Architecture

Titan communications should live in a dedicated platform layer:

```text
app/Platform/Communications/
├─ Mail/
├─ Notifications/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Push/
├─ Voice/
├─ Templates/
├─ Routing/
└─ Support/
```

The architecture is split into six planes:

### Intent plane

Where Titan decides that a communication should happen.

Sources include:

- automation triggers
- workflow transitions
- approved AI tool actions
- manual operator actions
- system alerts
- inbound-reply handlers

Intent is expressed as a normalized message request, not as a raw provider call.

### Planning plane

Transforms business intent into a delivery plan.

Planning decides:

- target audience
- tenant and company scope
- preferred channel set
- template selection
- personalization data
- urgency and retry class
- approval requirements

### Channel registry plane

Maintains machine-readable knowledge of what each channel supports.

Example capability categories:

- text only
- rich media
- attachments
- quick replies
- interactive buttons
- voice synthesis
- delivery receipts
- inbound replies
- read receipts
- scheduled sends

This allows Titan to downgrade, reroute, or split messages when one channel cannot fulfill the full payload.

### Delivery plane

Executes queued sends through channel adapters.

Adapters should be thin transport-specific layers. They should not own core routing, consent, retry, or automation policy.

### Observation plane

Tracks delivery lifecycle events:

- queued
- sending
- sent
- delivered
- read
- failed
- retried
- escalated
- replied
- closed

These events feed logs, automation rules, and operator dashboards.

### Recovery plane

Handles retry, fallback, dead-letter capture, and replay-safe recovery.

## 6. Contracts

### MessageRequest

A normalized request object should contain:

- tenant_id
- company_id
- audience identifiers
- message type
- template key or inline payload reference
- preferred channels
- fallback channels
- urgency class
- schedule or send time
- correlation id
- originating signal or workflow id

### ChannelAdapter contract

Each channel adapter should implement the same core operations:

- validate payload
- transform payload to provider format
- dispatch send
- interpret provider response
- normalize delivery status events
- normalize inbound message events

### MessageLog contract

Every message attempt should be auditable with:

- message id
- tenant scope
- channel
- provider
- status
- timestamps
- failure reason
- retry count
- correlation id
- related entity references

### Capability registry contract

Each channel must declare:

- supported payload types
- delivery guarantees
- inbound support level
- template restrictions
- attachment restrictions
- throttle notes
- provider dependencies

## 7. Runtime behavior

Standard outbound runtime:

1. intent enters from workflow, automation, AI, or operator action
2. planner builds a MessageRequest
3. governance checks policy, tenant scope, and consent
4. routing engine selects primary and fallback channels
5. request is queued
6. adapter sends through provider
7. provider response is normalized
8. log/status events are stored
9. follow-up rules may schedule retries, escalations, or next actions

Standard inbound runtime:

1. provider webhook or polling event arrives
2. inbound adapter verifies authenticity and tenant mapping
3. payload is normalized into inbound message event
4. conversation/router decides destination flow
5. event may generate a signal, workflow step, operator task, or AI handoff
6. audit logs persist full trace

## 8. Failure modes

### Provider failure

The adapter returns normalized failure output. Routing may retry same channel or fail over to another channel depending on delivery class.

### Invalid payload for selected channel

Planner or adapter rejects before send. Routing may downgrade format or switch channel.

### Missing consent or blocked destination

Governance blocks send and logs a non-deliverable policy result.

### Duplicate send risk

Idempotency keys and correlation ids prevent duplicate provider attempts after retries or worker crashes.

### Inbound authenticity failure

Webhook event is rejected and logged as suspicious input.

### Dead-letter accumulation

Repeated failures move the message into a dead-letter queue for operator review, replay, or template correction.

## 9. Dependencies

Upstream:

- manifest system
- signal engine
- automation engine
- workflow engine
- AI tool registry
- tenancy and permission systems

Downstream:

- channel adapters
- operator dashboards
- customer communication histories
- notification center
- PWA message surfaces
- analytics and observability

## 10. Open questions

- Should all channels share one common conversation entity or only a common message entity?
- Which channels are mandatory for MVP versus extension-based?
- Should voice be modeled as a channel peer or as a specialized communications runtime?
- How much provider-specific metadata should be preserved in normalized logs?

## 11. Implementation notes

- Keep transport SDK logic outside business actions.
- Make channels consumers of the communications engine, not owners of system policy.
- Reuse queue, event, and notification infrastructure already native to Laravel.
- Preserve company_id as the core tenant identifier for new work.
- Pair this doc with the routing/failover doc before building concrete channel adapters.
