# Titan Presence and Session Sync

## Purpose

Titan Presence and Session Sync keeps conversation state coherent across:

- web panels
- PWAs
- mobile nodes
- channel adapters
- voice sessions
- operator consoles

It allows Titan Omni to understand who is active, where the latest session lives,
which device currently owns interaction, and when a conversation should be resumed,
parked, transferred, or timed out.

---

## Core Goals

The layer must provide:

- active session tracking
- device awareness
- channel continuity
- handoff-safe synchronization
- timeout and idle detection
- operator visibility
- replay-safe recovery

Presence is not only online status.

It is operational awareness of engagement.

---

## Runtime Location

Primary runtime:

`app/Platform/Communications/Presence/`

Suggested structure:

- `SessionRegistry/`
- `PresenceStore/`
- `Heartbeat/`
- `Ownership/`
- `Transfers/`
- `Timeouts/`
- `Recovery/`
- `Support/`

---

## Presence Model

A presence record should capture:

- tenant id
- channel
- conversation id
- participant id
- session id
- device id
- interface type
- status
- last heartbeat
- last activity
- ownership state

Example statuses:

- offline
- online
- active
- idle
- waiting
- transferred
- ended

---

## Session Types

Titan should distinguish between:

- user sessions
- operator sessions
- bot sessions
- voice sessions
- device sessions
- background sync sessions

A single conversation may touch several session types over its lifetime.

---

## Session Ownership

At any point, one session may be the active owner of a conversation.

Ownership examples:

- web panel currently active
- PWA resumed after offline recovery
- operator took over from bot
- voice runtime currently controls flow

Ownership changes must be logged and replayable.

---

## Heartbeat Model

Clients emit lightweight heartbeats to indicate liveness.

Heartbeat updates:

- last seen
- last active device
- interface state
- focus state
- channel continuity marker

Missed heartbeats move the session toward idle or disconnected.

---

## Idle and Timeout Rules

Session sync should support configurable policies for:

- idle thresholds
- conversation parking
- bot re-entry delay
- operator session expiry
- voice timeout
- stale lock release

These policies prevent ghost ownership.

---

## Multi-Device Continuity

Users may move between:

- desktop panel
- PWA
- mobile browser
- native wrapper
- messaging channel

Sync layer responsibilities:

- detect newest active session
- preserve draft state
- prevent duplicate sends
- unify unread counts
- preserve conversation cursor

---

## Bot to Human Handoff

During handoff:

- bot ownership is released
- operator ownership is assigned
- transcript cursor is preserved
- pending actions are frozen or reassigned
- customer-visible continuity is maintained

The sync layer must make handoff deterministic.

---

## Human to Bot Return

When an operator exits:

- ownership may return to bot
- open tasks are checked
- pending approvals are resolved
- re-entry note is attached
- conversation state is normalized

Bot re-entry should never lose context.

---

## Offline Recovery

For PWA and mobile nodes, the layer must support:

- local draft preservation
- queued outbound messages
- resume tokens
- sync conflict detection
- last-write policies
- merge-safe restoration

This aligns with Titan’s device-first model.

---

## Conversation Cursor

Every session should maintain a cursor describing:

- last message seen
- last message sent
- last event applied
- last sync checkpoint

This enables exact resume behavior.

---

## Conflict Handling

Common conflict cases:

- two active operators
- web and PWA both sending
- device reconnect after stale state
- bot action scheduled during human takeover

Resolution should use:

- ownership priority
- session freshness
- governance policy
- explicit transfer logs

---

## Audit Requirements

Presence changes should record:

- who became active
- who lost ownership
- why transfer occurred
- timeout reason
- session recovery source

These records support replay and incident review.

---

## Integration Points

Presence and Session Sync feeds:

- Unified Inbox
- Conversation State Model
- Omni Router
- Voice runtime
- Channel adapters
- Delivery tracking
- AI context pack builder

It is part of conversation infrastructure, not optional UI state.

---

## Result

With Presence and Session Sync, Titan maintains one coherent conversational runtime
across devices, channels, bots, and human operators without duplicate actions,
lost context, or unclear ownership.
