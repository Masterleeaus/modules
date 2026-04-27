# 06. Node and Device Runtime

## 1. Why a node model matters

If the system is meant to run across phones, tablets, desktops, browsers, kiosks, and possibly local AI runtimes, then “client” is too vague a concept. Each participating device should be treated as a **node**.

A node is a runtime participant with:
- identity
- capability set
- local state
- sync state
- security posture
- optional local intelligence

## 2. Node classes

### Browser PWA node
Capabilities:
- installable shell
- local cache
- background sync where supported
- push notifications
- camera/file capture
- limited offline support

### Mobile device node
Capabilities:
- richer offline storage
- notifications
- camera/GPS/media
- background task execution
- possible voice capture
- optional local AI inference

### Desktop/admin node
Capabilities:
- larger local caches
- denser UI surfaces
- advanced operator tools
- broader file access
- better monitoring and debugging views

### Field-worker node
Capabilities:
- checklist flow
- photo/signature capture
- route/job context
- proof-of-service
- intermittent connectivity tolerance

### Edge/agent node
Future capability:
- always-on local relay
- sync broker
- local automation runner
- sensor/device integrations

## 3. Node identity model

Each node should have:
- node ID
- device type
- tenant/company binding
- user binding or shared terminal mode
- capability manifest
- trust level
- last sync checkpoint
- local app version

## 4. Local data model

Nodes should not try to mirror the full database. They should store only what they need.

Recommended local categories:
- active assignments
- recent records
- current customer/site context
- current conversation state
- attachments waiting upload
- pending mutations
- local settings/preferences
- AI drafts/proposals awaiting sync

## 5. Offline behavior model

A node should be able to:
- load assigned work offline
- capture changes offline
- queue mutations with idempotency keys
- present stale-state warnings when needed
- replay changes when back online

Not every module needs full offline write support, but field-critical flows should.

## 6. Sync design

### Pull side
Node requests:
- record deltas
- module manifests
- package/permission changes
- updated assignments
- message/task updates

### Push side
Node sends:
- pending mutations
- new attachments
- status updates
- drafts/proposals
- telemetry/audit events if enabled

### Server replies should include
- accepted mutations
- rejected mutations with reasons
- conflict markers
- refreshed records
- next sync cursor/checkpoint

## 7. Conflict model

Potential conflict types:
- same record edited on two nodes
- record changed centrally while node offline
- user lost permission/package access mid-session
- referenced record deleted or reassigned

Recommended strategy:
- use timestamps + version/revision fields
- detect rather than hide conflicts
- auto-resolve only low-risk fields
- escalate critical conflicts to human review or AI-assisted merge

## 8. Local AI possibilities

Some nodes may eventually support local AI tasks such as:
- voice transcription
- checklist summarization
- image tagging/classification
- note cleanup
- simple intent recognition

High-risk or context-heavy decisions should still route through the server-side governance layer.

## 9. Security model for nodes

Nodes must enforce:
- secure token/session storage
- encrypted local persistence where possible
- remote revocation support
- package/permission refresh
- tenant boundary even in cache design
- attachment upload validation

## 10. PWA shell implications

The node model means each PWA should include:
- sync manager
- stale-state indicator
- offline task queue UI
- retry/failure visibility
- node capability detection
- local notification handling

## 11. Good first node scenarios

### Titan Go node
- daily assigned jobs
- checklists and site memory
- photo uploads
- arrival/completion events
- offline-first

### Omni node
- message threads
- quick business commands
- AI summaries
- approval actions
- lighter offline needs

### Command node
- read-heavy command views
- approval queues
- live metrics and alerts
- less offline writing, more supervision

## 12. Server responsibilities in a node world

The server must:
- issue sync envelopes
- validate tenant-safe mutations
- replay accepted tasks safely
- reject stale/invalid actions clearly
- produce compact read models for nodes
- keep audit logs tying user + node + action together

## 13. Why this is strategically different

Most SaaS products treat mobile as a thinner copy of the web app. A node-based system treats each device as an operational participant with its own responsibilities and resilience model.

That is what allows the product to feel more like a real operating system than a hosted dashboard.

## 14. Final node sentence

A device in this platform is not just a viewer; it is a tenant-bound node with local state, sync responsibilities, and a role-specific execution surface inside the wider business graph.
