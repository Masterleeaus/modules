# 12. Filament Control Plane and Surface Design

## Purpose
Filament should not be treated merely as an admin panel add-on. In this system it can become the control plane for packages, module registry, device/node status, AI governance, and operational dashboards.

## Role of Filament
Filament is best used for:
- internal operator interfaces
- super admin tooling
- module/registry management
- package/product configuration
- audit and observability surfaces
- AI proposal review and approval pages

It is not automatically the best field-worker runtime.

## Surface Split
Use Filament for the control plane. Use PWAs for execution surfaces.

### Control Plane
- platform registry
- module doctor
- package builder
- AI governance dashboards
- signal/audit review
- device/node fleet management

### Execution Surfaces
- Titan Command
- Titan Go
- Titan Money
- Titan Portal
- Omni conversations

## Filament Resource Families
Recommended resource areas:

### Platform
- CompanyResource
- PackageResource
- ModuleRegistryResource
- ModuleVisibilityAuditResource
- DeviceFleetResource
- NodeResource

### AI / Governance
- SignalResource
- ProposalResource
- ApprovalQueueResource
- ToolRegistryResource
- MemoryIndexResource

### Operational
- SiteResource
- ServiceJobResource
- VisitResource
- ChecklistTemplateResource
- InspectionResource

### Finance
- InvoiceResource
- PaymentSessionResource
- CollectionQueueResource

## Panel Strategy
Prefer multiple Filament panels or well-separated navigation groups if necessary:
- Super Admin panel
- Company Admin panel
- Internal Ops panel

This is cleaner than one giant mixed panel.

## Navigation Doctrine
Sidebar items should be generated from:
- role/permission
- tenant scope
- package/module entitlement
- panel type

Avoid hardcoded sidebars that drift from package state.

## Why Filament Helps Here
Filament offers strong primitives for:
- form-driven config
- data tables
- filters
- relation managers
- actions and bulk actions
- widgets
- dashboards

These are ideal for a control plane that needs to move fast but remain structured.

## Where Filament Should Not Dominate
Avoid forcing Filament to be:
- the only chat interface
- the field-worker app shell
- the entire mobile UX
- the only CMS renderer

It should orchestrate and administer, not necessarily become every end-user experience.

## AI Review Center in Filament
One of the highest-value uses is an AI Review Center.

Pages should show:
- pending proposals
- risk score
- affected records
- suggested channel/action
- approval controls
- rollback/audit trail

This turns Filament into the human oversight layer.

## Package Builder in Filament
A strong package builder should expose:
- discovered modules
- visibility flags
- assignable-to-plan flags
- feature bundles
- channel limits
- AI/automation limits

This aligns with your package-driven SaaS model.

## Device and Node Fleet Pages
Filament can also host:
- online/offline node list
- device enrollment status
- sync lag
- last heartbeat
- installed capabilities
- pending repairs / drift notices

## CMS and Surface Coordination
Filament can manage the control plane for content surfaces without rendering the final site itself.

Useful resources:
- SurfaceManifestResource
- LandingTemplateResource
- BlockRegistryResource
- CampaignSurfaceBindingResource

## Control Plane UX Rules
- fast scanability
- dense but readable tables
- obvious state badges
- explicit risk status
- explicit tenant context
- one-click drilldown to source records

## Tenant Awareness
Every Filament resource must answer:
- Is this global or company-scoped?
- If company-scoped, how is `company_id` enforced?
- Can a super admin impersonate/switch company context safely?

## Build Sequence
1. Module registry resources
2. Package builder
3. Company/module settings views
4. AI review queue
5. Signal/audit pages
6. Device/node fleet pages
7. Operational dashboards

## Outcome
Filament becomes the governance cockpit and platform operating desk, while PWAs and mobile clients carry the workflow load in the field.
