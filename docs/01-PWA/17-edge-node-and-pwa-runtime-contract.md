# 17. Edge Node and PWA Runtime Contract

## Purpose

This document defines how a Titan/Worksuite PWA or mobile shell behaves as a **trusted edge node** inside the wider system.

It turns the earlier PWA idea into a practical runtime contract:

- the **server** remains the constitutional source of tenancy, permissions, packages, manifests, workflow policy, and audit
- the **edge node** owns local UX, offline work, device capabilities, transient context, and fast execution of approved local flows
- the **AI layer** proposes and arbitrates work, but still hands execution to the right domain, communication, or device subsystem

The result is not “a website that works offline.” It is a distributed operating model where each device becomes a privacy-aware, capability-aware, policy-bound node.

---

## Core principle

A PWA is not just another frontend.

In this system, a PWA node must be able to:

1. identify itself
2. receive a manifest-defined bootstrap payload
3. declare capabilities
4. cache assigned work and presentation surfaces
5. ingest signals and produce signals
6. sync state safely after offline periods
7. operate inside tenant and user boundaries
8. degrade cleanly when trust, auth, or network conditions change

This fits the broader platform direction in which modules expose APIs and optional Titan manifests, and where the platform grows shared engines for PWA, sync, signals, communications, workflows, and AI orchestration.

---

## Runtime layers

### 1. Device shell

The shell is the installed PWA or mobile wrapper.

Owns:

- app boot
- secure storage bindings
- service worker lifecycle
- push registration
- offline queues
- camera, mic, GPS, files, notifications
- theme, icon pack, install metadata
- low-latency local command routing

### 2. Surface runtime

This is the presentational layer that renders assigned surfaces.

Owns:

- forms
- lists
- dashboards
- command cards
- inbox views
- schedule views
- map/task views
- chat/action hybrids
- cached CMS and portal surfaces

### 3. Domain adapter layer

This layer connects the shell to domain modules.

Owns:

- module API clients
- typed DTO mapping
- optimistic local mutations
- replay-safe mutation envelopes
- local validation for known schemas
- fallback UI if module manifests change

### 4. Signal/runtime layer

This is how the edge participates in system coordination.

Owns:

- outbound signal queue
- inbound approved-action queue
- retry state
- replay tokens
- idempotency keys
- sync cursors
- conflict markers

### 5. Trust and identity layer

This determines what the device is allowed to do.

Owns:

- device identity
- trust level
- actor identity
- company boundary
- auth tokens
- local lock state
- revocation handling
- secure wipe triggers

---

## Canonical device record

The platform should keep a first-class device registry table for PWA/node runtime metadata.

Suggested table: `tz_pwa_devices`

Minimum fields:

- `id`
- `company_id`
- `user_id`
- `device_uuid`
- `device_name`
- `device_type`
- `os_family`
- `app_shell`
- `app_version`
- `trust_level`
- `public_key`
- `last_ip`
- `last_seen_at`
- `last_bootstrap_at`
- `last_sync_at`
- `status`
- `capabilities_json`
- `surface_profile`
- `push_token`
- `revoked_at`

### Why this matters

Without a durable device record, the platform cannot safely reason about:

- which device submitted a field proof event
- whether the actor was online or offline
- whether a sync should be accepted
- whether voice or camera tools are available
- whether the device should be allowed autonomous local actions

---

## Device trust model

Each node should operate under an explicit trust tier.

### Tier 0 — Untrusted
Used for new or unknown devices.

Allowed:

- login
- bootstrap request
- surface download
- read-only data retrieval
- low-risk draft creation

Blocked:

- silent execution
- sensitive exports
- privileged approvals
- bulk destructive actions

### Tier 1 — User trusted
Known device bound to a user.

Allowed:

- normal task execution
- routine draft creation
- offline queueing
- field events
- proof-of-work submission

### Tier 2 — Role trusted
Role-bound operational device such as dispatch tablet or office console.

Allowed:

- wider queue handling
- controlled approvals
- broader cached datasets
- higher throughput workflows

### Tier 3 — Sovereign/managed node
Company-managed or hardened device.

Allowed:

- automation assist modes
- background sync priority
- richer local capabilities
- greater continuity rights during degraded network conditions

Trust level must never replace authorization. It only affects **what sort of offline or delegated behavior is allowed**.

---

## Bootstrap contract

Every edge node should start with a formal bootstrap handshake.

### Step 1 — Manifest request
The shell identifies:

- app type
- platform version
- device UUID
- actor token
- requested surface profile
- known module versions
- last sync cursor

### Step 2 — Bootstrap response
The server returns a compact bootstrap pack containing:

- tenant identity
- user identity
- effective permissions
- package/module availability
- enabled manifests
- nav structure
- surface assignments
- feature flags
- trust tier
- current clocks/cursors
- cache invalidation instructions

### Step 3 — Local warmup
The shell stores:

- manifests
- nav maps
- critical labels
- pending jobs
- dashboard cards
- last-known records required for startup

### Step 4 — Delta sync
The shell fetches only what is needed after bootstrap.

This keeps startup light and makes it possible to support several different PWA personas from one system.

---

## Surface profiles

The runtime should support multiple node profiles instead of one universal PWA.

Examples:

- **Titan Portal** — client/user portal surfaces
- **Titan Go** — field worker surfaces
- **Titan Command** — owner/ops command surfaces
- **Titan Money** — finance and recovery surfaces
- **Titan Omni** — comms and unified inbox surfaces
- **Titan Studio mobile** — advisory/creative surfaces

A surface profile should define:

- nav modules
- home dashboard blocks
- allowed module APIs
- installable widgets
- sync priority classes
- offline record scope
- channel capabilities
- whether voice/chat is primary or secondary

This keeps the UX focused while still sharing one underlying platform.

---

## Offline contract

Offline support should be deliberate, not accidental.

### Must work offline

- cached navigation
- cached labels/translations
- assigned job list or work queue
- local notes/drafts
- checklists
- field proof captures
- queued outbound mutations
- local media staging

### May work offline if profile allows

- map tiles
- client/site memory
- limited CRM fragments
- template snippets
- price books
- risk guides
- local playbooks

### Must not be accepted offline without later validation

- privileged approvals
- money movement
- permission changes
- package changes
- high-risk deletions
- cross-tenant operations

---

## Local queue model

Each device should maintain separate local queues.

### Outbox
User-created actions waiting to be sent.

Examples:

- task status update
- proof-of-service upload
- note creation
- customer signature
- checklist completion

### Inflight
Requests sent but not yet acknowledged.

### Inbox
Approved or assigned actions returned by the server.

### Dead-letter
Mutations that failed permanently and need user or system review.

### Replay
Stored mutation envelopes that can be retried safely using idempotency keys.

---

## Signal contract for edge nodes

Nodes should not push arbitrary state changes directly.

They should emit **signal envelopes** or **mutation envelopes** with:

- envelope ID
- idempotency key
- company ID
- user ID
- device UUID
- module name
- action type
- payload
- evidence pointers
- local timestamp
- causal reference
- risk class

The server can then:

- validate schema
- validate tenant ownership
- validate actor rights
- score risk
- route to automation/governance
- approve, reject, defer, or request more data

This keeps edge nodes fast without making them sovereign.

---

## Media and evidence handling

PWAs should treat captured media as staged evidence, not instant truth.

### Flow

1. capture locally
2. store encrypted temp reference
3. attach metadata
4. create outbound signal/mutation
5. upload when network is available
6. receive server-issued evidence ID
7. link evidence to domain record

Metadata should include:

- company_id
- user_id
- device_uuid
- geo
- captured_at
- mime type
- hash
- related job/site/customer reference

This is essential for proof-of-service, inspections, safety, and disputes.

---

## Conflict policy

Offline-first systems need explicit conflict rules.

### Safe patterns

- append-only notes
- checklist item completion with versioning
- evidence attachment
- draft creation
- immutable event logs

### Conflict-prone patterns

- same record edited on two devices
- schedule reassignment
- inventory decrement
- invoice edits
- payment recovery status changes

### Policy

Prefer:

1. append-only events
2. server-side merge rules
3. explicit version numbers
4. conflict markers surfaced to users
5. replay-safe mutation envelopes

Avoid silent last-write-wins for operationally meaningful records.

---

## Security and revocation

The edge node must be revocable.

Revocation events should support:

- logout all devices for user
- revoke one device UUID
- force trust downgrade
- invalidate cached approvals
- expire push token
- require re-bootstrap
- trigger local wipe of sensitive stores

The local shell should also support:

- biometric gate
- passcode fallback
- idle lock
- root/jailbreak sensitivity where possible
- encrypted local stores for secrets
- no long-term storage of raw high-risk payloads

---

## Relationship to modules

Modules should never hardcode a single UI shell. Instead they should expose runtime metadata through manifests and APIs.

The PWA runtime consumes:

- `Routes/api.php`
- `ai_tools.json`
- `cms_manifest.json`
- `lifecycle_manifest.json`
- `signals_manifest.json`
- optional `omni_manifest.json`

That lets one module appear across:

- Filament operator panels
- web account UI
- chat tools
- Portal
- Go
- Command
- Omni
- external integrations

---

## Relationship to Filament

Filament remains the operator and control plane surface.

The PWA runtime is not a miniature clone of Filament.

### Filament should own

- operator dashboards
- admin review
- analytics
- approvals
- configuration
- clusters/resources/widgets

### Edge PWA should own

- task execution
- queue handling
- capture workflows
- field communication
- low-friction operational UX
- local-first/offline behavior

The same actions/services/events should power both surfaces.

---

## Recommended implementation order

1. create device registry and trust policy
2. define bootstrap payload contract
3. create surface profiles
4. build local outbox/inbox/replay queues
5. add manifest-aware module adapters
6. add staged media/evidence flow
7. add revocation and local wipe
8. add conflict markers and operator review
9. add profile-specific shells
10. add richer delegated AI/automation rights

---

## Final rule

An edge node is not “just another frontend.” It is a **policy-bound participant** in the system.

The server keeps constitutional truth.  
The device keeps speed, context, and local resilience.  
The AI layer coordinates, critiques, and proposes.  
The domain and communications engines still execute the real work.
