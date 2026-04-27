# Canonical Filament Starter Pack

## Purpose
This is the standard Filament adapter layer for a module. It is a **consumer** of module actions/services, never the source of truth.

## Starter contents

```text
Modules/<ModuleName>/Filament/
  Plugin/
    <ModuleName>Plugin.php
  Resources/
    <Thing>Resource.php
    <Thing>Resource/
      Pages/
      RelationManagers/
      Schemas/
  Pages/
  Widgets/
  Tables/
  Forms/
  Actions/
  Infolists/
  Clusters/
  Filters/
  Support/
```

## Filament responsibilities
- admin/operator CRUD surfaces
- dashboards and widgets
- filters, bulk actions, infolists
- panel navigation and grouping
- relation managers and review screens

## Forbidden responsibilities
- direct business rule ownership
- duplicate validation rules already needed outside the panel
- independent persistence paths that bypass module actions
- side-effect orchestration trapped in form closures

## Wiring rule
Every meaningful Filament mutation should call a module Action or Service.
