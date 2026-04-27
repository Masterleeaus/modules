# 24. Security, Identity, Device Trust, and Tenant Boundary

## Purpose

This document defines the trust model for the Worksuite + Titan system. The platform is not just a Laravel SaaS. It is intended to run across panels, PWAs, mobile devices, conversational interfaces, automation engines, communications channels, and distributed node-like clients. That means security cannot be limited to login screens and database guards. It must become a system-wide contract covering tenants, actors, devices, channels, approvals, and AI-mediated actions.

## Core principle

**`company_id` is the tenant boundary.**

Everything in the architecture should assume this as a hard rule. If a record participates in tenant-owned operational state, it must carry or inherit a clear tenant boundary. The earlier Worksuite/Titan module materials and your own checklist repeatedly make this explicit.

A second principle follows:

**`user_id` represents actor or owner context, not tenant context.**

This distinction matters because a system that confuses actor identity with tenant boundary will leak data or apply permissions incorrectly.

## Security layers

The trust model should be designed as several stacked layers.

### 1. Identity layer

Who is this principal?

Possible principals include:

- platform super admin
- tenant admin
- employee/operator
- client/customer portal user
- AI subsystem acting under system authority
- device/node identity
- external integration identity
- communications channel identity

Each principal must resolve to a stable identity object before policy is evaluated.

### 2. Tenant layer

Which tenant owns the requested context?

This should always resolve clearly through:

- authenticated tenant selection
- record scoping
- package entitlement checks
- route/context resolution
- device registration mapping
- channel ownership mapping

No request should “discover” tenant context loosely from UI state alone.

### 3. Capability layer

What is this principal allowed to do?

Capability is not one thing. It comes from several inputs:

- role and permission grants
- package entitlements
- module visibility/settings
- tenant configuration
- workflow approval state
- consent status
- AI risk classification
- device trust level
- channel-specific rules

### 4. Trust layer

How much confidence do we have in the environment making the request?

Examples:

- browser session with recent login
- super admin panel session
- registered PWA device
- field worker mobile app with biometric unlock
- external webhook
- messaging channel session
- AI-generated proposal awaiting approval

This should be represented explicitly, not informally.

### 5. Approval layer

Even if the user is authenticated and authorized, some actions still require approval. This is especially important in the Titan architecture because AI proposals, cross-domain mutations, and high-risk changes should not be treated the same as reading a list or editing a safe note.

## Identity domains

The architecture now contains multiple identity domains that must not be blurred.

### Human identity

This includes actual users across:

- super admin
- tenant admins
- internal staff
- clients/customers
- external collaborators

These identities should be consistent across panels, APIs, PWAs, and channel-driven sessions.

### Device identity

The PWA/node direction requires first-class device identities. A device is not just “a browser.” It is a participating platform endpoint with its own registration, trust, key material, last-seen state, capabilities, and assigned principal.

A device record should typically capture:

- device UUID
- `company_id`
- primary bound `user_id` if any
- platform type
- app version
- push token(s)
- local key fingerprints
- trust level
- last handshake timestamp
- last sync timestamp
- revocation state
- current risk flags

### System identity

Titan Zero, AEGIS, Titan Core, and related subsystems should not impersonate ordinary users silently. They need their own actor model or execution identity with explicit audit semantics.

When AI proposes or triggers actions, logs must show that the origin was system/AI-mediated, even if a user later approved it.

### Integration identity

External systems such as payment gateways, CRM bridges, webhooks, voice providers, messaging APIs, or imported bots must authenticate through a separate integration model with constrained permissions and isolated revocation.

## Device trust model

The AI PWA/node material implies a much richer device model than a normal SaaS. The platform should therefore treat device trust as a first-class concern.

Recommended trust states:

- `untrusted`
- `registered`
- `verified`
- `high_trust`
- `restricted`
- `revoked`

### Untrusted

The device is seen, but not yet approved for operational access.

### Registered

The device has completed initial handshake and belongs to a tenant or user context, but should not automatically gain access to sensitive flows.

### Verified

The device has completed stronger proofing such as login, OTP, certificate, secure storage key exchange, or admin approval.

### High trust

The device is recognized, bound to a stable user or managed fleet identity, and may be allowed to participate in stronger workflows such as offline execution, dispatch operations, or sensitive approvals.

### Restricted

The device is allowed partial access only. Example: read-only dashboards, limited sync, or communications-only surfaces.

### Revoked

The device should not receive new tokens, sync data, or push actions.

## Session and token strategy

Because the system spans panels, APIs, PWAs, and mobile/node-like clients, it should not force one auth style everywhere.

Recommended mix:

### Browser/panel sessions

Use normal authenticated session flows for operator and super admin panels.

### API/device tokens

Use token-based auth for PWA/mobile/device surfaces, with rotation and revocation.

### Short-lived action tokens

For high-risk transitions, use short-lived signed or challenge-based tokens rather than relying on a long session alone.

### Channel/webhook verification

External inbound events must use separate verification mechanisms such as signatures, secret headers, callback validation, or provider-specific trust checks.

## Tenant boundary rules

The tenant boundary should be defended at several points, not just one.

### Database layer

Tenant-owned tables should carry `company_id` unless the relationship is inherited in a fully safe way.

### Query layer

Controllers, services, repositories, widgets, resources, and API endpoints should always resolve records within tenant scope.

### Route/model binding layer

Avoid binding records by ID only when the route is tenant-sensitive. Binding must remain tenant-aware.

### Service/action layer

Actions should accept or derive tenant context explicitly and refuse mutation when context is missing or mismatched.

### UI layer

Never trust panel filters or visible navigation alone as the real enforcement boundary.

### Job/queue layer

Jobs must carry tenant and actor context where required. Deferred execution without tenant context is a common leakage vector.

## Package and module entitlements as security input

In this system, a user seeing a module is not merely cosmetic. Package visibility and `module_settings` are part of effective capability control.

Therefore, security checks should consider:

- is the module installed/discovered?
- is it enabled for this tenant?
- is it allowed by package?
- is it allowed for this role?
- is it permitted for this device/surface?

This matters because the same action may be legal in a super admin panel but unavailable in a tenant package or on a field-worker PWA.

## Approval and AI risk gating

A user having permission is still not enough for some actions. The Titan system requires an additional layer for:

- AI-generated customer-facing actions
- finance-impacting changes
- cross-domain updates
- compliance-sensitive workflow steps
- irreversible operations
- bulk communication or automation

These should flow through approval-aware policies. The policy result may be:

- allow immediately
- allow only with recent re-authentication
- allow only from a high-trust device
- require human approval
- deny and log

## Voice, channel, and conversational security

Because Titan Hello, Titan Go, and Omni-style channels are part of the system, communications security is not optional.

### Voice

Voice sessions should have:

- session identity
- active tenant resolution
- explicit consent rules where required
- transfer state
- transcript sensitivity labels
- action confirmation thresholds

### Messaging channels

Each channel session should know:

- channel identity
- tenant ownership
- consent state
- verified contact binding
- permitted automation depth
- whether free-form AI responses are allowed or only templated responses

### Chat/AI tools

Tool invocation from conversational UI should go through the same action/policy path as panel or API requests. Chat should never become a hidden superuser surface.

## Recommended platform structure

The platform blueprint already suggests the right homes:

```text
app/Platform/Identity/
app/Platform/Tenancy/
app/Platform/Permissions/
app/Platform/Pwa/
app/Platform/Api/
app/Platform/Audit/
app/Platform/Signals/
app/Platform/Communications/
app/Platform/Ai/Governance/
```

A practical trust-specific layout could include:

```text
app/Platform/Identity/
  Principals/
  Sessions/
  Challenges/
  Reauthentication/
  DeviceBindings/
  IntegrationAccounts/

app/Platform/Tenancy/
  Resolution/
  Scopes/
  Guards/
  Packages/
  ModuleAccess/

app/Platform/Pwa/
  Devices/
  Handshakes/
  Trust/
  Revocation/
  SyncSessions/

app/Platform/Audit/
  Trails/
  Evidence/
  ApprovalLogs/
  AccessLogs/
  RiskEvents/
```

## Audit model

A system this ambitious needs stronger audit than a typical CRUD app.

Every sensitive action should answer:

- who initiated it?
- which tenant was targeted?
- from which surface?
- from which device/channel?
- under which package/module entitlement?
- through which workflow/approval state?
- whether AI proposed or shaped it?
- what policy allowed or denied it?

That means audit records should carry at least:

- actor identity
- actor type
- `company_id`
- target entity type/id
- module/domain
- surface (`panel`, `api`, `pwa`, `voice`, `automation`, `ai`)
- device id if present
- signal/workflow reference if present
- result (`allowed`, `denied`, `proposed`, `approved`, `executed`)
- risk score or policy decision code if relevant

## Super admin versus tenant admin versus worker

The system should document separate authority classes.

### Super admin

Can manage platform-wide configuration, packages, module registry, tenant lifecycle, and high-level repair/doctor tooling.

### Tenant admin

Can manage tenant-owned configuration, staff, operational modules, and local automation within package entitlement limits.

### Worker/operator

Can access only the modules, data, and execution flows relevant to their role, device, and tenant.

### Customer/client

Can access only customer-safe surfaces and records intentionally exposed through portal/API/channel flows.

These roles must be enforced consistently across web routes, API routes, Filament panels, PWA surfaces, and AI/tool calls.

## Security doctrine for modules

Every serious module should satisfy the following:

- uses `company_id` as tenant boundary where relevant
- uses `user_id` for ownership/actor context where relevant
- has policy coverage for sensitive entities
- exposes API surfaces intentionally, not accidentally
- remains package-aware
- remains module-settings aware
- does not trap business rules in UI callbacks
- does not bypass approval-aware actions
- supports audit and actor attribution

## Recovery and revocation

Trust is not static. The platform should be able to:

- revoke device access
- rotate tokens
- invalidate channel sessions
- force re-authentication for risky actions
- freeze module access after package downgrade
- disable AI execution mode for a tenant
- replay auditable actions after restoration without widening access

## Final principle

A tenant-safe, AI-mediated, multi-surface operating system must treat security as a platform fabric, not a login feature. Identity, trust, capability, approval, and tenancy must all be explicit, portable across surfaces, and visible in audit. Only then can Worksuite, Filament, PWAs, devices, channels, and Titan AI operate as one coherent system without becoming one giant security risk.
