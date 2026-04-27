# Full System Directory Tree

Status: Canonical draft
Layer: Whole system

```text
project-root/
├─ app/
│  ├─ Console/
│  │  ├─ Commands/
│  │  └─ Kernel.php
│  ├─ Exceptions/
│  │  └─ Handler.php
│  ├─ Http/
│  │  ├─ Controllers/
│  │  │  ├─ Auth/
│  │  │  ├─ Admin/
│  │  │  ├─ User/
│  │  │  ├─ Api/
│  │  │  ├─ Web/
│  │  │  └─ Controller.php
│  │  ├─ Middleware/
│  │  ├─ Requests/
│  │  ├─ Resources/
│  │  └─ Kernel.php
│  ├─ Models/
│  │  ├─ Core/
│  │  ├─ System/
│  │  ├─ User/
│  │  ├─ Ops/
│  │  ├─ Finance/
│  │  ├─ Cms/
│  │  ├─ Omni/
│  │  ├─ Signals/
│  │  └─ Ai/
│  ├─ Policies/
│  ├─ Observers/
│  ├─ Events/
│  ├─ Listeners/
│  ├─ Notifications/
│  ├─ Mail/
│  ├─ Jobs/
│  ├─ Broadcasts/
│  ├─ Actions/
│  ├─ Services/
│  ├─ Data/
│  ├─ Contracts/
│  ├─ Repositories/
│  ├─ Support/
│  ├─ Platform/
│  ├─ Filament/
│  └─ Providers/
├─ bootstrap/
├─ config/
├─ database/
├─ lang/
├─ Modules/
├─ platform/
├─ public/
├─ resources/
├─ routes/
├─ storage/
├─ tests/
├─ docs/
├─ .env
├─ artisan
├─ composer.json
├─ package.json
└─ vite.config.js
```

## Intent

This is the top-level arrangement for a Titan / Worksuite / Filament / PWA / AI-driven Laravel system.

## Major Responsibilities

### `app/`
All runtime application code.

### `app/Platform/`
Shared system substrate: tenancy, permissions, navigation, module registry, packages, signals, automation, workflows, AI, sync, audit, observability.

### `app/Filament/`
Panel-only admin/operator UI layer.

### `Modules/`
Domain modules and their Filament adapters.

### `platform/`
Static contracts and manifests for platform-level registries.

### `resources/`
Blade views, assets, theme files, email views, frontend shells.

### `routes/`
Global route definitions plus grouped route trees.

### `tests/`
Feature, unit, integration, and browser test layers.
