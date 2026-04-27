# 11. Security, Identity, and Governance

## Purpose
A system that coordinates operations, messaging, finance, and AI-driven proposals cannot treat security as a bolt-on. Identity, scope, approval, and audit must be first-class design elements.

## Core Security Layers

### 1. Identity
Who is this actor?

### 2. Scope
Which tenant/company may this actor access?

### 3. Capability
What can this actor do in this module or channel?

### 4. Governance
Which actions require approval before execution?

### 5. Audit
What happened, who proposed it, who approved it, and when?

## Identity Types
The system should support multiple identity categories:
- platform super admin
- company owner/admin
- employee/worker
- client/customer portal user
- device identity
- API integration identity
- AI assistant identity

Each identity type needs different privileges, UX, and audit semantics.

## Company Boundary
The tenant boundary is `company_id`.

Every operational module should treat `company_id` as mandatory unless the record is explicitly global/platform-level.

Global records should be rare and intentional.

## User Boundary
`user_id` represents a person, account, or direct actor. It does not replace `company_id`.

Use both when both are needed.

## Permission Model
The permission system should work at four levels:
- module visibility
- action permission
- record ownership/scope
- channel/tool execution rights

Examples:
- can view ServiceManagement
- can edit a visit
- can only view visits assigned to self
- can trigger WhatsApp send

## AI Governance Rule
AI should propose actions before executing sensitive ones.

Sensitive classes include:
- payment actions
- package/billing changes
- cross-channel outbound communication
- scheduling changes affecting staff/customers
- data deletion
- policy/role changes

## Approval Pipeline
Recommended stages:
1. propose
2. validate
3. authorize
4. approve or reject
5. execute
6. audit

This fits your broader signal-governance model and keeps the AI from acting as an unsupervised root user.

## Session Security
PWAs and browser clients should support:
- short-lived session tokens
- refresh workflows
- device-aware revocation
- step-up auth for sensitive actions

## Device Trust Levels
Not all devices should be treated equally.

Suggested trust classes:
- trusted enrolled device
- standard browser session
- temporary shared device
- offline field-only device

Higher-risk devices should receive tighter limits.

## Authentication Approaches
The stack can combine:
- password login
- SSO for enterprise/admins
- magic link for portal users where appropriate
- token auth for APIs
- optional biometric unlock at client/device layer

## Module Security Checklist
Every module should answer:
- What identities can access it?
- Is it super-admin only, tenant-admin only, or mixed?
- What routes are public, protected, or internal?
- Which actions require elevated approval?
- Which records must be company-scoped?

## Filament Security Model
Filament should act as a secure administration and control plane, not as an all-access shortcut.

Each resource/page should explicitly define:
- who can see nav item
- who can list records
- who can edit records
- whether tenant scoping is automatic or explicit

## Sidebar Separation
You asked about some links in user account and some in super admin.

Security principle:
- user account links should expose tenant-scoped operational surfaces
- super admin links should expose platform-wide control and configuration

Do not mix these in one generic route group.

## Settings Separation
There should be at least three settings classes:
- platform settings (super admin)
- company settings (tenant admin)
- user preferences (user-level)

AI should never guess which setting layer it is editing.

## Audit Doctrine
Critical entities should log:
- actor
- company
- action
- before state summary
- after state summary
- timestamp
- source surface
- device/session

## Event and Signal Security
Signals should carry enough context to verify:
- source company
- actor identity
- authorized tool/action
- record target
- replay/idempotency token

## Secret Management
Keep secrets out of module code.

Use environment/config-backed secret references for:
- API keys
- channel tokens
- webhook secrets
- payment credentials
- device certificates

## AI Safety Boundaries
The AI layer should have:
- allowed tools registry
- prohibited action classes
- required approval rules
- rate limits
- channel-specific compliance rules

## Zero-Trust Mindset
Assume:
- routes may be hit directly
- clients may send malformed payloads
- stale devices may reconnect late
- package visibility may drift from real permissions

Therefore:
- validate every request
- scope every query
- audit every sensitive action
- never trust UI visibility alone as authorization

## Outcome
A serious AI-driven operations platform wins trust when users can tell exactly:
- what the system knows
- what it is allowed to do
- what it proposed
- what it changed
- and how to unwind mistakes
