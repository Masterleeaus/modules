# 21. Communications, Voice, and Consent Architecture

## Purpose

This document defines the communications layer for the Titan system: operational messaging, timeline ownership, voice session control, consent handling, delivery routing, channel failover, and audit. It turns communications from a set of adapters into a governed platform service.

It is based on the Titan constitution’s explicit assignment of operational conversations to CustomerConnect, voice-session authority to Titan Hello, and stateless speech engine responsibility to Titan Go. Worksuite remains the operational system of record, while Studio retains marketing authority in its own domain. fileciteturn16file19turn16file5turn16file7

---

## 1. Constitutional rules

The communications layer must obey five non-negotiable rules.

### 1.1 CustomerConnect owns operational conversations

Operational conversations about jobs, invoices, quotes, reminders, scheduling, service updates, and customer support belong to CustomerConnect. It stores the messages against the customer timeline, enforces opt-in and throttling, and records delivery receipts. In duo mode, it also receives marketing event backfeed from Studio for unified customer history, but it does not become the marketing campaign engine. fileciteturn16file4turn16file5

### 1.2 Titan Hello owns voice sessions

Voice session control belongs to Titan Hello. Hello starts, pauses, resumes, and ends calls; binds them to timelines; and coordinates the speech layer. Hello is the operational voice authority, not just a transport adapter. fileciteturn16file0turn16file5

### 1.3 Titan Go is stateless

Titan Go is a speech provider, not a conversation owner. It provides STT/TTS as a pluggable engine. It does not own session state, customer context, or business decisions. fileciteturn16file0turn16file7

### 1.4 Studio owns marketing communications

Marketing campaigns, brand conversations, lead-gen outreach, and growth sequences remain in Titan Studio. In duo mode, Studio backfeeds events to Worksuite, but it does not directly own operational service threads. fileciteturn16file7turn16file14

### 1.5 No silent AI communication

No module may silently send AI-driven communications outside the Titan Zero → Titan Core path and the correct channel authority. AI may draft, classify, summarize, and propose. Sending still requires the correct domain owner, risk evaluation, and audit trail. fileciteturn16file6turn16file19

---

## 2. Communications platform structure

The full system blueprint implies a dedicated communications engine, not just a few notification classes. The right shape is:

```text
app/Platform/Communications/
├─ Mail/
├─ Notifications/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Email/
├─ Voice/
├─ Push/
├─ Templates/
├─ Routing/
├─ Consent/
├─ Delivery/
├─ Timelines/
├─ Receipts/
├─ Policies/
└─ Support/
```

This complements Laravel’s native mail, notifications, queues, events, scheduler, and service-provider model instead of fighting it. Deferred delivery, template rendering, provider selection, and side effects should be queued and decoupled from the web request lifecycle. fileciteturn16file12turn16file13turn10file2turn10file1

---

## 3. Channel model

The communications engine should treat each channel as a governed delivery surface with a shared contract.

### 3.1 Core channels

Operational system-native channels should include:

- SMS
- Email
- WhatsApp
- Telegram
- Messenger
- Push
- Voice
- Internal inbox/timeline

This aligns with the system preference for omnichannel delivery and with the module checklist’s expectation that modules expose API surfaces and Omni compatibility. fileciteturn11file1

### 3.2 Channel capabilities

Each channel adapter should declare capabilities such as:

- plain text
- rich text
- attachments
- templates
- replies
- delivery receipts
- opt-out support
- rate limits
- message windows
- voice escalation

### 3.3 Routing policy

Routing should not be hard-coded in controllers. It should resolve through platform policy:

- preferred channel by tenant
- customer consent state
- urgency level
- cost ceiling
- failure fallback path
- time-window rules
- role or workflow context

For example:

- invoice reminder → email then SMS fallback
- urgent schedule change → SMS then call escalation
- field-worker dispatch update → push then SMS
- voice session escalation → Titan Hello via Go

---

## 4. Consent and policy engine

Consent must be explicit and first-class.

### 4.1 Consent types

At minimum, store separate consent states for:

- operational SMS
- operational email
- operational voice
- operational push
- marketing SMS
- marketing email
- marketing voice
- marketing chat

Do not merge operational and marketing consent into a single boolean. The constitution explicitly separates operational and marketing authority, so consent must follow that split as well. fileciteturn16file5turn16file14

### 4.2 Consent state model

Recommended states:

- unknown
- granted
- denied
- revoked
- pending confirmation
- provider-suppressed

### 4.3 Consent events

All consent changes should emit auditable events such as:

- consent.granted
- consent.revoked
- consent.provider_suppressed
- consent.opt_out_received
- consent.double_confirmed

These then feed the signal and audit layers.

### 4.4 Enforcement

The communications engine must block sends when:

- consent is missing
- channel is disallowed for that message type
- quiet hours apply
- rate limit exceeded
- provider policy blocks content
- AI attempted to send without authorization

---

## 5. Timeline ownership and message records

Every operational message should be represented as a first-class record, not just a provider API call.

### 5.1 Required fields

A communications record should generally carry:

- `company_id`
- `customer_id` or `contact_id`
- `user_id` for actor when relevant
- channel
- direction (outbound/inbound)
- purpose (reminder, invoice, job update, support, etc.)
- template key or prompt lineage
- provider
- provider message id
- delivery state
- consent snapshot
- created_by / approved_by / sent_by
- correlation ids for workflow/signal linkage
- timestamps for queued, sent, delivered, failed, read, replied

This follows the tenant-boundary rule that company scope is mandatory and user attribution should exist where relevant. fileciteturn11file1

### 5.2 Timeline rules

- CustomerConnect owns operational timeline placement.
- Marketing events from Studio are mirrored into the timeline as external-origin events, not re-authored ops messages.
- Voice sessions should appear as linked communication sessions, with transcript and summary objects stored separately from raw audio if policy demands.

---

## 6. Voice architecture

Voice should not be treated like “SMS but audio.”

### 6.1 Voice session model

A voice session should have:

- session id
- company_id
- participant identities
- linked customer/timeline id
- linked workflow or job id
- session state
- current intent state
- transcript segments
- summary state
- escalation state
- confirmation markers
- provider metadata

### 6.2 Runtime split

The correct runtime split is:

- Titan Hello → session owner
- Titan Go → STT/TTS provider
- Titan Zero → intent and risk authority
- Titan Core → authorized AI execution router
- CustomerConnect → timeline and communication record owner

This is one of the clearest constitutional separations in the source set and should remain strict. fileciteturn16file0turn16file4turn16file19

### 6.3 Voice event flow

A healthy operational voice flow is:

1. Hello starts session
2. Go handles audio transcription/synthesis
3. Zero classifies intent and risk
4. Core executes only authorized AI/provider calls
5. Hello controls the next state of the conversation
6. CustomerConnect stores the timeline artifacts
7. Signals are emitted for workflow and audit

---

## 7. Templates, drafting, and AI assistance

Templates should be modular and channel-aware.

### 7.1 Template levels

Templates may exist at:

- platform default
- package default
- tenant/company override
- workflow-specific override
- user-approved draft instance

### 7.2 AI role

AI should be allowed to:

- classify conversation intent
- summarize threads
- propose drafts
- adapt tone within policy
- choose next-best channel under routing constraints
- turn transcripts into structured notes

AI should not be allowed to:

- bypass consent
- bypass approvals
- switch the domain owner
- silently send messages on its own

### 7.3 Reuse pattern

Keep content-generation logic in reusable actions/services, not in one controller, widget, or Filament closure. The Laravel guidance on thin controllers, form requests, actions/services, and DTO reuse directly supports this pattern. fileciteturn10file2turn16file9

---

## 8. Delivery reliability

Delivery should be queue-first.

### 8.1 Queue requirements

Use queues for:

- outbound sends
- retries
- receipt polling
- transcript summarization
- voice post-processing
- campaign fan-out
- attachment processing

This matches the Laravel performance and architecture guidance to move non-essential request work off the synchronous path. fileciteturn16file13turn10file1

### 8.2 Reliability components

The communications engine should include:

- outbox
- retry policies
- idempotency keys
- dead-letter queue
- receipt reconciliation
- duplicate suppression
- fallback routing
- provider health checks

### 8.3 Failure handling

Failure states should distinguish:

- transport failed
- provider rejected
- consent blocked
- policy blocked
- throttled
- content invalid
- bounced
- timeout
- delivery uncertain

---

## 9. Filament and operator surfaces

Filament should present communications as an operator control plane, not as the place where core communications logic lives.

Good Filament surfaces include:

- unified inbox
- consent management screen
- delivery diagnostics
- template editor
- voice session console
- escalation queue
- failed delivery review
- approval screens for sensitive AI drafts

But all business rules, provider bindings, delivery logic, and policy enforcement should remain in module/platform services and actions, not in panel callbacks. fileciteturn16file3turn12file0

---

## 10. Build contract

A production-ready Titan communications layer must satisfy this checklist:

- Worksuite owns operational messaging records
- CustomerConnect owns operational timelines
- Titan Studio owns marketing communications
- Titan Hello owns operational voice sessions
- Titan Go remains stateless
- consent is split by operational vs marketing purpose
- every message is tenant-scoped by `company_id`
- actor attribution uses `user_id` where relevant
- sends are queue-backed and auditable
- AI can draft and classify, but not silently send
- delivery routing is policy-driven, not controller-driven
- receipts and provider ids are stored
- failures, retries, and dead letters are explicit

This makes communications a governed platform subsystem rather than a pile of message adapters.
