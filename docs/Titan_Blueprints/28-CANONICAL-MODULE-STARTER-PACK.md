# Canonical Module Starter Pack

## Purpose
This is the standard starter for any serious Worksuite/Titan domain module.

## Starter contents

```text
Modules/<ModuleName>/
  module.json
  Config/
    config.php
    features.php
  Providers/
    <ModuleName>ServiceProvider.php
    RouteServiceProvider.php
    EventServiceProvider.php
    FilamentServiceProvider.php
  Routes/
    web.php
    api.php
  Database/
    Migrations/
    Seeders/
  Http/
    Controllers/
    Requests/
    Resources/
  Models/
  Policies/
  Actions/
  Services/
  Data/
  Events/
  Listeners/
  Jobs/
  Notifications/
  Mail/
  Exports/
  Imports/
  Support/
    Enums/
    DTOs/
    Helpers/
  manifests/
    ai_tools.json
    signals_manifest.json
    lifecycle_manifest.json
    api_manifest.json
  Filament/
    Plugin/
    Resources/
    Pages/
    Widgets/
  Tests/
```

## Required capabilities
- install safely and idempotently
- respect tenant boundary through `company_id`
- expose named routes
- provide request validation
- place business mutations in Actions
- place broader workflows in Services
- fire Events and queue Jobs for side effects
- expose manifest contracts for AI, signals, API, and lifecycle participation

## Minimum provider responsibilities
- merge config
- load migrations
- load web and api routes
- load views/translations if present
- bind services/actions where needed
- register event listeners
- register Filament plugin/resources through a dedicated Filament provider
