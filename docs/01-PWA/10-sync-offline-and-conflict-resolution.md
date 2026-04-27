# 10. Sync, Offline, and Conflict Resolution

## Purpose
The system is intended to run across browsers, phones, tablets, field devices, and server nodes. That means sync is not an add-on. It is a primary architectural concern.

## Principle
The server is the coordination authority, but not the only place where work happens.

Clients must be able to:
- read cached operational state
- create work while offline
- stage media and proof records
- reconcile on reconnect
- recover safely from partial syncs

## Runtime Types

### Browser session
Usually online, but still should tolerate brief disconnects.

### Installed PWA
Can run in the field with flaky service and local storage.

### Mobile wrapper/native shell
May have better device APIs, background abilities, and push integration.

### Edge node
A semi-persistent endpoint that may hold local queues, local model/runtime, and orchestrate multiple users/devices.

## Sync Model
Prefer a hybrid model:
- server truth for canonical records
- client-side append queue for pending mutations
- cursor-based pull for deltas
- idempotent replay on reconnect

## What Must Sync
- assignments
- schedules
- site notes
- checklists
- inspections
- proof media metadata
- chats/messages relevant to assigned work
- notifications
- package/permission changes relevant to surface access

## What Can Stay Server-Only
- full analytics history
- nonessential logs
- bulky archives
- expensive AI history if not needed locally

## Offline Write Pattern
Each write generated offline should include:
- local UUID
- record type
- intended operation
- payload
- actor identity
- company_id
- device_id
- created_at_local
- sync_attempt_count

This becomes an outbox entry.

## Server Reconcile Pattern
When online returns:
1. client sends outbox entries in order
2. server validates tenant boundary and permissions
3. server applies or rejects each item idempotently
4. server returns canonical IDs / state / errors
5. client marks item synced, conflicted, or rejected

## Idempotency Doctrine
Every sync write must be replay-safe.

Use:
- client UUIDs
- operation tokens
- dedupe keys
- server-side conflict checks

Without idempotency, reconnect storms will duplicate work.

## Conflict Classes

### 1. Field overwrite conflict
Two actors changed the same field.

### 2. Relationship conflict
A visit was reassigned while a worker changed local status.

### 3. Sequence conflict
A local action assumes a record still exists or still has a prior status.

### 4. Media conflict
Proof files uploaded against a record that was canceled or replaced.

## Conflict Resolution Strategy
Not all conflicts should be solved the same way.

### Safe auto-merge
Use when fields are independent.
Example:
- worker updates arrival time
- office updates invoice note

### Server-wins
Use for:
- package/permission changes
- canceled jobs
- hard business locks

### Client-wins with audit
Use rarely, mainly for user drafts/preferences.

### Manual resolution
Use for:
- assignment changes
- conflicting financial edits
- checklist completion disputes

## Versioning Support
Each syncable record should carry one or more of:
- `updated_at`
- integer version
- change hash
- sync revision token

This lets the client know whether its base copy is stale.

## Media Sync
Large media should not block primary state sync.

Recommended pattern:
- sync metadata first
- upload blobs separately
- show proof placeholders while upload completes
- link uploaded files back when confirmed

## Service Worker Role
PWAs should use a service worker to:
- cache shell assets
- cache recent API responses prudently
- queue network writes where appropriate
- keep the app bootable in bad conditions

## Local Storage Strategy
Use the browser or wrapper storage as a working set, not as a second monolith.

Recommended local stores:
- current company context
- today/tomorrow assignments
- active site records
- pending outbox
- recently used templates/checklists
- last sync cursors

## Sync API Shape
The sync surface should be explicit.

Recommended endpoints:
- `POST /api/sync/push`
- `GET /api/sync/pull?cursor=...`
- `POST /api/sync/media`
- `GET /api/sync/bootstrap`

## AI and Offline
The AI layer should degrade gracefully.

### Online mode
- full server-assisted reasoning
- orchestration
- long-context retrieval

### Offline mode
- local summaries
- cached memory snippets
- form-filling help
- narrow device-side actions

The system should never present offline AI as if it has full live authority.

## Operational Safety Rules
When offline, block or warn on:
- payment execution
- package/license changes
- cross-company access
- destructive admin actions
- policy-critical approvals

## UX Rules
Users need to see:
- online/offline status
- pending sync count
- conflicts needing review
- last successful sync time
- safe retry controls

## Build Sequence
1. bootstrap endpoint
2. read cache model
3. outbox write pipeline
4. delta pull
5. media sync
6. conflict center
7. AI offline degradation

## Outcome
A serious operational PWA is not judged by how pretty it looks online. It is judged by whether it still works on a bad connection at the worst moment of the day.
