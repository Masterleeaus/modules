# 09. Canonical Data Model

## Purpose
This document defines the canonical data model for a Worksuite-to-Titan operating system where Laravel modules, Filament surfaces, PWAs, mobile clients, and AI control layers all operate against a shared semantic backbone.

The goal is not to force every feature into one monolith. The goal is to ensure that every surface speaks the same core language.

## Core Rule
Every major workflow should map back to a stable operational object, even if the UI language changes by vertical.

Examples:
- cleaning may say `site`, `visit`, `checklist`, `issue`
- field service may say `location`, `job`, `work order`, `inspection`
- hospitality may say `property`, `turnover`, `room checklist`, `incident`

The semantic layer can vary. The canonical core should not.

## Core Entity Families

### Identity
- company
- user
- role
- permission
- device
- node
- channel identity

### Customer Graph
- contact
- company account
- site/location
- lead
- opportunity
- conversation thread

### Work Graph
- service agreement
- quote
- booking
- service job
- visit
- route run
- dispatch assignment
- checklist
- inspection
- issue
- proof of service

### Financial Graph
- estimate/quote
- invoice
- payment session
- payment
- payout/payroll event
- ledger entry
- subscription/package

### AI / Control Graph
- signal
- proposal
- approval
- execution log
- audit log
- memory record
- automation recipe
- tool invocation record

### Experience / Content Graph
- CMS surface
- template
- campaign
- landing page block
- notification
- message variant
- published asset

## Data Layer Split
The system should separate data into four broad layers:

### 1. Transactional Core
Business truth used by the application in real time.
Examples:
- jobs
- customers
- visits
- invoices
- assignments

### 2. Operational Metadata
State needed to make the transactional core usable.
Examples:
- statuses
- tags
- SLA flags
- priority
- routing hints
- package entitlements

### 3. AI Context Layer
Compressed context used by AI and automation.
Examples:
- summaries
- embeddings references
- memory pointers
- risk flags
- suggested next actions

### 4. Event / Signal Layer
Append-only or near-append-only records of change.
Examples:
- job.created
- invoice.sent
- worker.arrived
- payment.failed
- site.access_code.updated

## Shared Column Doctrine
For tenant-safe operational tables, prefer these columns where relevant:
- `id`
- `uuid`
- `company_id`
- `user_id`
- `created_by`
- `updated_by`
- `assigned_to`
- `status`
- `meta` JSON
- `created_at`
- `updated_at`
- `deleted_at`

### Meaning
- `company_id` = tenant boundary
- `user_id` = object owner / actor / direct subject where relevant
- `created_by` = who made it
- `assigned_to` = who is responsible now

Do not blur these meanings.

## Company Boundary Doctrine
Every operational record should be answerable to a tenant.

Questions every table should answer:
- Which company owns this record?
- Which user created it?
- Which user or team is responsible for it?
- Can the record exist outside a company boundary?

If the answer to the last question is no, the table needs `company_id`.

## Device and Node Identity
Because the system is intended to stretch across PWAs, mobile devices, browser clients, and node runtimes, devices must be modeled as first-class runtime participants.

Recommended tables or equivalent models:
- `devices`
- `device_sessions`
- `node_registrations`
- `node_capabilities`
- `sync_cursors`
- `offline_change_sets`

A device is not just a browser. It is an operational agent endpoint.

## AI-Ready Object Shape
Every core object should expose:
- stable identifier
- human label
- compact summary
- lifecycle status
- primary relationships
- last changed timestamp
- risk/priority hints

That gives Laravel controllers, Filament resources, API endpoints, and AI layers a stable contract.

## Suggested Canonical Objects

### Company
Represents a tenant/business.
Fields:
- id
- uuid
- name
- package_id
- timezone
- locale
- status

### User
Represents a person inside the tenant boundary.
Fields:
- id
- company_id
- name
- email
- role
- status
- last_active_at

### Site
Represents a service location or managed place.
Fields:
- id
- company_id
- customer_id
- name
- address
- access_notes
- geo
- service_preferences

### Service Job
Represents a work package.
Fields:
- id
- company_id
- site_id
- customer_id
- agreement_id nullable
- status
- priority
- requested_window
- assigned_team_id nullable

### Visit
Represents a dated execution of work.
Fields:
- id
- company_id
- service_job_id
- scheduled_start
- scheduled_end
- actual_start
- actual_end
- worker_id nullable
- status

### Checklist
Represents execution template or completion record.
Fields:
- id
- company_id
- visit_id nullable
- template_id nullable
- status
- completion_ratio

### Signal
Represents a machine-readable event.
Fields:
- id
- company_id
- type
- source_type
- source_id
- payload
- state
- emitted_at

## Package and Module Entitlements
The package system should not just say "module enabled".
It should support:
- module availability
- feature flags within module
- per-channel limits
- per-device limits
- AI capability limits
- automation volume limits

This allows one product surface to run multiple business shapes.

## PWA-Specific Modeling
Every PWA surface should map to one or more object subsets.

Examples:
- Titan Command: dispatch, map, exceptions, approvals
- Titan Go: today list, visit detail, proof, messages, supply issues
- Titan Money: invoices, payment sessions, collections, payroll signals
- Titan Portal: client-facing quotes, bookings, invoices, messages

A PWA should never invent its own private business language if the core object already exists.

## Filament Mapping
Filament resources should be generated around canonical object families, not ad hoc admin pages.

Good examples:
- CompanyResource
- PackageResource
- ModuleRegistryResource
- SiteResource
- ServiceJobResource
- VisitResource
- SignalResource
- DeviceResource

This keeps admin/UI aligned with the same core graph the PWAs and AI use.

## Documentation Rule
Every new module should include:
- canonical objects touched
- package visibility rules
- tenant columns
- public API resources
- emitted signals
- AI tools exposed
- PWA surfaces affected

## Outcome
Once the canonical data model is stable:
- modules become interoperable
- PWAs become thinner
- AI gets predictable context
- sync becomes simpler
- vertical overlays become mostly translation and workflow differences
