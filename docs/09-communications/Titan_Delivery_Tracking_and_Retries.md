# Titan Delivery Tracking and Retries

## Purpose

Titan Delivery Tracking and Retries defines how outbound communications are
observed, reconciled, retried, and escalated across all supported channels.

It sits between:

- channel adapters
- outbound message queue
- provider webhooks
- unified inbox state
- operator alerts
- analytics and audit

This layer guarantees that communication is treated as operational work,
not fire-and-forget output.

---

## Core Principles

Delivery tracking must be:

- channel-aware
- provider-aware
- tenant-scoped
- idempotent
- replayable
- auditable

Retries must be:

- policy-driven
- bounded
- deduplicated
- visible to operators
- safe against duplicate sends

---

## Runtime Location

Core runtime lives in:

`app/Platform/Communications/Tracking/`

Recommended structure:

- StatusResolvers/
- RetryPolicies/
- BackoffStrategies/
- DeliveryReceipts/
- FailureClassifiers/
- Escalations/
- Reconciliation/
- Audit/

---

## Delivery Lifecycle

Common outbound states:

- queued
- accepted
- dispatched
- provider_received
- delivered
- read
- failed
- expired
- cancelled
- escalated

Not every channel supports every state.

The tracking layer normalizes provider-specific statuses into a Titan status
model for downstream systems.

---

## Provider Receipt Model

Providers return one or more of:

- message id
- provider status
- error code
- delivery timestamp
- read timestamp
- recipient handle
- failure reason

Titan stores both:

- raw provider payload
- normalized delivery event

This preserves audit fidelity while still enabling cross-channel logic.

---

## Tracking Sources

Delivery state can be updated from:

- immediate send response
- asynchronous provider webhook
- periodic reconciliation pull
- operator manual correction
- replayed receipt ingestion

Webhooks are preferred.

Polling and reconciliation exist as recovery paths.

---

## Failure Classification

Failures are classified into:

- transient failure
- permanent failure
- policy failure
- provider outage
- invalid recipient
- tenant restriction
- duplicate suppression
- expired send window

Classification determines whether retry is allowed.

---

## Retry Policy

Retries must be configured per:

- channel
- provider
- message type
- urgency class
- tenant policy

Example strategy:

- retry 1 after 1 minute
- retry 2 after 5 minutes
- retry 3 after 15 minutes
- then escalate

Retries stop immediately for:

- invalid destination
- permission failure
- opted-out recipient
- hard provider reject

---

## Backoff Strategies

Supported patterns:

- fixed delay
- exponential backoff
- capped exponential
- business-hours retry
- channel-switch fallback

Example:

email failure may retry on email first,
then escalate to SMS or voice if policy allows.

---

## Deduplication

Retry execution must not create duplicate user-visible messages.

Titan deduplicates using:

- tenant id
- message intent id
- provider message id
- recipient
- channel
- correlation id

This is critical when:

- provider accepted but webhook was delayed
- send response timed out
- reconciliation happens after a manual resend

---

## Escalation Rules

Escalations can route to:

- operator inbox
- channel failover
- workflow exception queue
- AEGIS review
- voice fallback
- customer support queue

Example:

failed payment chase email → retry → SMS reminder → operator alert

---

## Unified Inbox Integration

Delivery tracking updates conversation surfaces with:

- latest outbound state
- failed delivery badge
- read indicators
- retry pending marker
- escalation status

Operators must be able to see whether a message truly arrived,
not just whether it was sent.

---

## Analytics

Delivery tracking feeds:

- channel success rate
- provider reliability
- read rate
- average delivery latency
- retry volume
- escalation volume

This supports provider selection and policy tuning.

---

## Audit Model

Every delivery event records:

- tenant id
- channel
- provider
- message intent id
- provider message id
- normalized status
- raw payload reference
- timestamp
- retry attempt number

This creates a defensible operational record.

---

## Responsibilities

Titan Delivery Tracking and Retries owns:

- delivery normalization
- retry scheduling
- failure classification
- deduplication
- escalation routing
- receipt reconciliation
- conversation delivery visibility
- analytics and audit
