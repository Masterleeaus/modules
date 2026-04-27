# Titan Zero Documentation

Layer: Communications + Channels
Scope: Channel selection, fallback rules, retry classes, escalation paths
Status: Draft v1
Depends On: Titan Channel Architecture, Titan Signal Engine, Titan Automation Engine, Titan Workflow Engine
Consumed By: Omni router, channel adapters, campaign engine, receptionist flows, alerts and follow-up services
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan chooses a channel, retries delivery, fails over between channels, and escalates unresolved communication attempts.

## 2. Why it exists

A multi-channel system only becomes reliable when routing and recovery are explicit. If each channel retries differently, operators lose trust, automation becomes inconsistent, and AI reasoning cannot predict outcomes.

Titan needs one routing doctrine that answers:

- which channel should send first
- when retries should happen
- when fallback is allowed
- when escalation should create tasks or alerts
- how policy and consent affect routing

## 3. Core responsibilities

- rank channels for each message intent
- enforce consent and allowed-channel policy
- classify message urgency and delivery expectations
- manage retries without duplicate sends
- choose fallback channels when the primary path fails
- escalate repeated failures into visible operational work

## 4. Boundaries

### In scope

- primary channel selection
- fallback path definition
- retry schedules and classes
- idempotent resend behavior
- escalation to human review or alternate automation
- provider-level and channel-level failure handling

### Out of scope

- template authoring
- provider SDK implementation
- operator UI for composing messages
- conversation analytics beyond routing events

## 5. Architecture

Routing should live under a dedicated subsystem:

```text
app/Platform/Communications/Routing/
├─ Policies/
├─ ChannelRankers/
├─ RetryProfiles/
├─ FailoverPlans/
├─ EscalationRules/
├─ ConsentResolvers/
├─ DeliveryClasses/
└─ Support/
```

The routing engine evaluates four layers in order:

### Policy layer

Checks whether a message may be sent at all.

Examples:

- channel consent present or absent
- quiet hours or do-not-disturb windows
- tenant restrictions
- geography/provider restrictions
- compliance blocks for channel type

### Suitability layer

Determines which available channels can carry the payload.

Examples:

- voice-only alert
- media attachment present
- button interaction required
- long-form content better suited to email
- immediate short alert better suited to SMS or push

### Preference layer

Ranks channels by business intent and user/company preference.

Inputs can include:

- customer preferred channel
- worker preferred channel
- company default policy
- workflow-specific channel order
- cost sensitivity
- latency sensitivity

### Recovery layer

Defines what happens when delivery fails or times out.

Outputs:

- retry same provider
- retry same channel different provider
- fail over to alternate channel
- escalate to human task
- mark undeliverable and log

## 6. Contracts

### Delivery class

Each message should be assigned a delivery class such as:

- informational
- transactional
- urgent
- critical
- conversational
- campaign

Delivery class drives retry aggressiveness and escalation behavior.

### Retry profile

Retry profiles should define:

- max attempts
- backoff pattern
- provider retry limit
- channel retry limit
- dead-letter threshold
- escalation trigger

### Failover plan

A failover plan should declare ordered alternates.

Example:

- primary: WhatsApp
- secondary: SMS
- tertiary: email
- escalation: operator task

### Escalation rule

Escalation should define:

- condition
- destination
- severity
- notification audience
- linked entity or workflow reference

## 7. Runtime behavior

Standard routing flow:

1. message request enters communications planner
2. policy layer confirms send is allowed
3. suitability layer filters incompatible channels
4. preference layer ranks remaining channels
5. primary channel is selected and queued
6. delivery result is observed
7. recovery layer decides retry, failover, escalation, or closure

### Same-channel retry

Use when failure is transient and provider-level.

Examples:

- temporary gateway timeout
- rate limit window
- provider outage expected to clear quickly

### Same-channel alternate-provider retry

Use when a channel is still appropriate but the provider path is unstable.

Examples:

- SMS provider A timeout
- switch to SMS provider B

### Cross-channel failover

Use when the channel itself is failing to reach the recipient or cannot satisfy timing expectations.

Examples:

- WhatsApp undeliverable because destination is not reachable
- fail over to SMS for urgent alert

### Escalation

Use when the message is operationally important and repeated attempts fail.

Examples:

- create a staff callback task
- raise a dispatch warning
- notify command-center dashboard
- ask Titan Zero to propose next best action

## 8. Failure modes

### Retry storm risk

Mitigate with retry profiles, idempotency keys, and per-message caps.

### Policy bypass risk

All retries and failovers must re-check policy before dispatch.

### Duplicate human escalation

Escalation records must deduplicate against the original message correlation id.

### Wrong-channel downgrade

Suitability checks must run again before failover so Titan does not switch a rich interactive workflow into an unusable low-capability channel.

### Silent delivery uncertainty

Messages with no confirmed receipt state should transition into pending-observation windows, not assumed success.

## 9. Dependencies

Upstream:

- channel architecture
- template system
- signal engine
- automation engine
- workflows
- tenancy, permissions, and consent data

Downstream:

- channel adapters
- operator alerts
- receptionist flows
- campaign engine
- follow-up engine
- dead-letter and replay queues

## 10. Open questions

- Should each tenant be able to author custom routing policies or only choose from predefined profiles?
- Which delivery classes are mandatory for MVP?
- Should failover across paid channels consider spend thresholds at runtime?
- How should voice escalation interact with receptionist and command-center flows?

## 11. Implementation notes

- Keep routing decisions centralized in the communications layer.
- Do not hide retry rules inside provider adapters.
- Reuse automation escalation patterns rather than inventing parallel logic.
- Surface routing outcomes as signals so other engines can react consistently.
- Pair retry and failover logic with full audit logging for each attempt.
