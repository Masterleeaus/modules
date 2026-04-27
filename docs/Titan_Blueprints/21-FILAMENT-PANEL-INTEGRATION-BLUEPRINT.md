# Filament Panel Integration Blueprint

## Positioning
Filament is the operator/admin surface, not the domain engine.

## Split of responsibility
### Domain module owns
- data model
- business actions
- validation semantics
- policies
- events, jobs, notifications
- imports/exports
- API endpoints
- signals/manifests

### Filament owns
- resources
- tables/forms/infolists
- dashboards/widgets
- admin workflows
- review/approval screens
- operator navigation

## Panel layout
Recommended split:
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/UserPanelProvider.php`
- shared components under `app/Filament/Shared/`
- module-scoped Filament code inside each module when tightly coupled

## Resource rule
A Filament resource should not become the only place where core logic exists.

Instead:
- form submit -> action/service
- bulk action -> action/service
- status transition -> action/service
- export button -> export class/service

## Navigation rule
Navigation labels can be UI-specific.
Route names, policies, and underlying actions should remain stable and reusable.

## Approval flows
Filament is ideal for:
- pending queues
- approval cards
- exception review
- reconciliation pages
- admin override interfaces

But approval decision handling should still call module/platform actions.

## Multi-panel concerns
If both admin and tenant-user panels exist:
- isolate navigation groups
- isolate authorization gates
- reuse underlying actions/services
- avoid duplicate resource logic where possible

## Plugin rule
Use plugins when packaging reusable panel behavior.
Use plain resources/pages when the surface is local to one product instance.

## Build checklist
- Filament never duplicates core business logic
- Resources call actions/services
- Panels use proper providers
- Policies still live outside Filament callbacks
- Operator UX is separated from domain engine
