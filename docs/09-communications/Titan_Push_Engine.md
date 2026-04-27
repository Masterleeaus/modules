# Titan Push Engine

## Purpose

Titan Push Engine defines how push notifications are composed, routed, delivered,
tracked, and reconciled across browser PWAs, mobile nodes, desktop shells,
and future device-native runtimes.

It provides a channel-specific engine for:

- PWA/browser notifications
- mobile push notifications
- desktop notification surfaces
- silent data push where supported
- action-enabled notification cards
- re-engagement nudges
- urgent operational alerts

This keeps push as a first-class channel inside Titan Omni rather than a
side feature hidden inside front-end code.

---

## Architectural Role

Titan Push sits inside the communications layer and connects:

- message templates
- routing and failover
- delivery tracking
- node/device runtime
- unified inbox
- presence and session sync
- AI tool registry
- workflow and automation engines

Push is both a notification surface and an operational wake-up path for nodes.

---

## Runtime Location

Core runtime should live in:

`app/Platform/Communications/Push/`

Recommended structure:

- Providers/
- PayloadBuilders/
- Templates/
- DeviceRegistry/
- SubscriptionStore/
- Actions/
- Delivery/
- Receipts/
- Reconciliation/
- Support/

---

## Channel Characteristics

Push differs from email and SMS because it depends on:

- active device or subscription registration
- node capability manifest
- browser or OS permission grants
- app install state
- token freshness
- platform-specific payload limits

Push delivery is device-bound, not purely identity-bound.

---

## Device and Subscription Model

Push targets are resolved from a tenant-safe subscription registry.

A subscription should include:

- tenant id
- user id or contact id where relevant
- node id
- device class
- platform
- push token or endpoint
- permission state
- last seen timestamp
- app version
- capability flags

A stale or revoked subscription must not remain an active routing target.

---

## Push Message Classes

Titan Push should support at minimum:

- informational notification
- urgent alert
- approval request
- reminder
- conversation activity notice
- workflow action prompt
- silent refresh trigger
- call-back or voicemail notice

Each class can carry different rules for urgency, collapse behavior,
expiry, and follow-up.

---

## Payload Model

Push payloads should separate:

- title
- body
- icon/badge data
- deep link
- action buttons
- metadata
- collapse key
- expiry or TTL
- analytics correlation ids

Where supported, payloads may include lightweight structured data so the
node can refresh state without opening the full app.

---

## Deep Linking and Actions

Push notifications should be able to open directly into:

- a job
- a conversation thread
- an approval item
- an invoice
- a dispatch alert
- an operator task
- a voicemail summary
- a workflow exception view

Action buttons may allow:

- approve
- dismiss
- snooze
- call back
- mark read
- open thread

High-risk actions still route through policy and approval checks.

---

## Node Runtime Alignment

Push is tightly aligned with the node model.

Nodes may use push to:

- wake sync loops
- refresh conversations
- surface urgent operational events
- prompt foreground actions
- recover attention after missed messages

Push should never assume full local state is present. It should wake the
node and let the node fetch or reconcile the latest safe read model.

---

## Routing Strategy

Titan routes push using:

- user presence
- node/device availability
- permission state
- urgency class
- tenant policy
- fallback channel rules

Example:

- active operator on browser PWA → browser push
- offline browser but active mobile node → mobile push
- no healthy push target → escalate to SMS or voice if permitted

---

## Delivery and Receipts

Push providers vary in the quality of receipt data.

Titan should store:

- dispatch attempt
- provider acceptance
- token invalidation signal
- platform error
- receipt or delivery callback where available
- open/click telemetry when allowed

Push receipts often indicate partial truth only, so reconciliation is important.

---

## Expiry and Collapse

Push must support:

- TTL / message expiry
- collapse keys for redundant alerts
- deduplication by message intent id
- replacement of stale notifications

This avoids flooding users with outdated operational prompts.

---

## Security and Privacy

Push content should avoid exposing unnecessary sensitive data on lock screens.

Policy controls may define:

- public-safe body
- masked customer data
- hidden financial details
- action suppression on locked devices
- restricted notification content by role

This is especially important for shared or field devices.

---

## Failure Handling

Push failures may result from:

- expired token
- denied OS permission
- stale subscription
- provider outage
- payload too large
- unsupported action format
- tenant policy block

Retry behavior should be conservative and token-aware.
Persistent invalid tokens should be retired, not retried indefinitely.

---

## Unified Inbox Integration

Push is not the system of record.

It is an attention channel.

Every push intent should link back to:

- conversation state
- workflow item
- approval item
- operational entity

This ensures the inbox or core domain remains authoritative.

---

## Responsibilities

Titan Push Engine owns:

- device subscription routing
- payload construction
- deep link and action design
- push-target resolution
- token freshness handling
- safe lock-screen content policy
- push-specific receipts and reconciliation
- fallback into the broader communications stack
