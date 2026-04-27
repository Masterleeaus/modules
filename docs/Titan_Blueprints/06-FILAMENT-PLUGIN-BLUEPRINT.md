# Filament Plugin Blueprint

Status: Canonical draft
Layer: Admin/operator UI

## Principle

Filament consumes module engines. It does not replace them.

## Filament Tree Inside a Module

```text
Modules/<ModuleName>/Filament/
├─ Plugin/
│  └─ <ModuleName>Plugin.php
├─ Resources/
│  ├─ <Thing>Resource.php
│  └─ <Thing>Resource/
│     ├─ Pages/
│     ├─ RelationManagers/
│     ├─ Widgets/
│     └─ Schemas/
├─ Pages/
├─ Widgets/
├─ Tables/
├─ Forms/
├─ Actions/
├─ Infolists/
├─ Clusters/
├─ Filters/
└─ Support/
```

## Filament Owns

- resources
- pages
- widgets
- tables
- forms
- infolists
- relation managers
- clusters/navigation grouping
- bulk actions
- operator dashboards
- admin summaries and triage surfaces

## Filament Does Not Own

- domain models
- business rules
- state transitions
- jobs
- notifications
- mail
- signal contracts
- AI contracts
- module install logic

## Panel Integration

Filament panel providers should register plugins by panel type:
- admin panel
- user/operator panel

## Good Pattern

- resource action calls module action
- page workflow calls module service
- widget queries read models/view models only
