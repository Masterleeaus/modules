# Module Blueprint

Status: Canonical draft
Layer: Domain module

## Goal

A module is a domain engine, not just CRUD.

## Full Module Tree

```text
Modules/<ModuleName>/
├─ module.json
├─ version.txt
├─ README.md
├─ CHANGELOG.md
├─ Config/
│  ├─ config.php
│  ├─ features.php
│  ├─ permissions.php
│  ├─ navigation.php
│  ├─ package.php
│  └─ ai.php
├─ Providers/
│  ├─ <ModuleName>ServiceProvider.php
│  ├─ RouteServiceProvider.php
│  ├─ EventServiceProvider.php
│  ├─ FilamentServiceProvider.php
│  └─ ModuleBootServiceProvider.php
├─ Routes/
│  ├─ web.php
│  ├─ api.php
│  ├─ admin.php
│  ├─ user.php
│  ├─ console.php
│  └─ channels.php
├─ Database/
│  ├─ Migrations/
│  ├─ Seeders/
│  ├─ factories/
│  └─ states/
├─ Http/
│  ├─ Controllers/
│  │  ├─ Admin/
│  │  ├─ User/
│  │  ├─ Api/
│  │  └─ Web/
│  ├─ Middleware/
│  ├─ Requests/
│  └─ Resources/
├─ Models/
├─ Entities/
├─ Policies/
├─ Repositories/
├─ Actions/
├─ Services/
├─ Data/
├─ ValueObjects/
├─ Events/
├─ Listeners/
├─ Observers/
├─ Jobs/
├─ Notifications/
├─ Mail/
├─ Exports/
├─ Imports/
├─ Queries/
├─ Presenters/
├─ ViewModels/
├─ Workflows/
├─ Automation/
├─ Support/
│  ├─ Enums/
│  ├─ DTOs/
│  ├─ Helpers/
│  ├─ Rules/
│  ├─ Transformers/
│  ├─ Mappers/
│  └─ Builders/
├─ Traits/
├─ Scopes/
├─ Console/
│  └─ Commands/
├─ Resources/
│  ├─ views/
│  ├─ lang/
│  ├─ js/
│  ├─ css/
│  ├─ svg/
│  └─ dist/
├─ Tests/
│  ├─ Feature/
│  ├─ Unit/
│  ├─ Integration/
│  └─ Support/
├─ manifests/
│  ├─ ai_tools.json
│  ├─ signals_manifest.json
│  ├─ lifecycle_manifest.json
│  ├─ cms_manifest.json
│  ├─ omni_manifest.json
│  ├─ api_manifest.json
│  └─ package_manifest.json
└─ Filament/
```

## Module Owns

- domain models/entities
- migrations and seeders
- requests and API resources
- policies
- business actions and services
- events/listeners/observers
- jobs/notifications/mail
- exports/imports
- domain workflows/automation
- manifests for AI, signals, CMS, Omni, lifecycle, API, and package integration

## Provider Duties

- merge config
- load routes
- load views
- load translations
- load migrations
- bind services/contracts
- register events/listeners
- register Filament integration
- seed/verify navigation and permission hooks
