# Tenancy + Identity Boundary Blueprint

## Purpose
Define the non-negotiable boundary rules for Worksuite/Titan modules, platform services, PWA apps, APIs, and AI tooling.

## Canonical tenant key
- `company_id` is the primary tenant boundary for new Worksuite work.
- Do not invent parallel tenant identifiers for new modules.
- `user_id` identifies the acting human or system principal inside the tenant.
- Legacy `team_id` may be read for compatibility but should not be the authoritative partition for new design.

## Required scoping rule
Every tenant-owned record must be queryable by `company_id`.

Typical tenant-owned tables:
- jobs
- quotes
- invoices
- visits
- messages
- campaigns
- prompts
- approved_logs
- signal logs
- AI memory records tied to company context

## Ownership columns
Recommended minimum:
- `company_id`
- `user_id` when a creator/actor matters
- `created_by` / `updated_by` for audit-heavy domains

Optional, domain-specific:
- `site_id`
- `customer_id`
- `worker_id`
- `package_id`
- `channel_id`

## Enforcement layers
### Database
- Add indexes on `company_id`.
- Prefer composite indexes on common scopes, for example:
  - (`company_id`, `status`)
  - (`company_id`, `scheduled_start`)
  - (`company_id`, `created_at`)

### Model layer
- Add reusable tenant scopes.
- Never rely on UI filtering alone.
- Make cross-tenant queries explicit and rare.

### Policy layer
- Policies must validate the actor belongs to the same `company_id` unless a super-admin/system path is intended.

### API layer
- Never trust inbound `company_id` from public clients unless explicitly validated against authenticated scope.
- Resolve tenant from auth/session/token/domain/context first.

### Queue + jobs
- Queue payloads must carry enough tenant context to rehydrate correctly.
- Jobs should fail closed if tenant resolution is ambiguous.

## AI boundary
AI tools may propose actions, but tool execution must run inside resolved tenant context.

Every AI action proposal should carry:
- actor
- company_id
- target entity IDs
- source channel
- approval state if needed

## Super-admin exception
Super-admin surfaces may cross tenants, but must do so intentionally:
- dedicated guards
- dedicated routes
- explicit UI treatment
- elevated audit logging

## Build checklist
- Tenant-owned tables contain `company_id`
- Queries are tenant-scoped by default
- Policies enforce tenant ownership
- APIs derive tenant from auth/context
- Jobs carry tenant context
- AI actions include tenant envelope
