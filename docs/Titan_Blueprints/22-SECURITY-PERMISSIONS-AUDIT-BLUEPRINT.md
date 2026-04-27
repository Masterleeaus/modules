# Security, Permissions, and Audit Blueprint

## Security posture
The system should assume:
- tenant isolation matters
- admin override paths are sensitive
- AI proposals must not silently execute privileged work
- channel actions can be high-impact

## Permission layers
### Route / middleware
Use middleware for coarse access control:
- authenticated user
- admin-only
- super-admin only
- feature-enabled checks

### Policy layer
Use policies for entity-level decisions:
- can view this quote?
- can edit this booking?
- can approve this payout?

### Feature/settings layer
Use module settings and package toggles to gate availability by company/package.

## Recommended permission families
Per module/resource:
- view
- create
- update
- delete
- export
- approve
- manage_settings

## Audit strategy
High-impact actions should log:
- actor
- tenant
- entity
- previous state
- next state
- source surface (`web`, `api`, `filament`, `ai`, `job`, `channel`)
- timestamp
- correlation/request/signal ID where available

## AI-specific controls
AI-originated proposals should be auditable separately from human actions.

Recommended states:
- proposed
- reviewed
- approved
- denied
- executed by downstream service
- failed

## Sensitive domains
Add stronger checks for:
- finance
- payroll
- external communications
- deletions/purges
- role/permission changes
- tenant-crossing actions

## Secure defaults
- deny by default where uncertain
- require explicit enablement for dangerous automations
- keep secrets in env/secret stores
- separate read-only diagnostics from write-capable tools
