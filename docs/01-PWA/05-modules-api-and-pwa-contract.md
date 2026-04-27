# 05. Modules, APIs, and PWA Contract

## 1. Goal

A module in this system is not just a Laravel add-on. It is a business capability that must be able to:
- boot in Worksuite
- appear correctly in super admin and tenant contexts
- attach to packages
- respect tenant boundaries
- expose stable APIs
- power PWA/mobile shells
- optionally expose AI, CMS, lifecycle, and omni-channel manifests

## 2. Minimum module structure

```text
Modules/<ModuleName>/
  module.json
  Config/config.php
  Providers/
    <ModuleName>ServiceProvider.php
    RouteServiceProvider.php
  Routes/web.php
  Routes/api.php
  Database/Migrations/
  Database/Seeders/
  Http/Controllers/
  Resources/views/
  Resources/lang/en/
  Entities/ or Models/
```

## 3. Required loading contract

A module must:
- be discoverable from `Modules/<ModuleName>`
- have a valid `module.json`
- register its service provider(s)
- load routes, views, translations, migrations, and config
- use exact case-correct names on Linux hosts

## 4. Route contract

### User account routes
Usually under:
- `account/<module>`

### Super admin routes
Usually under:
- `account/...` with `superadmin.*` route names, or dedicated super admin group conventions used by the host app

### API routes
Versioned and stable:
- `/api/v1/<module-resource>`

### Rules
- always use named routes
- separate user and super admin surfaces clearly
- do not rely on raw URL strings in sidebars when route names exist
- keep controller namespaces aligned with provider registration

## 5. DB registration contract

A module should register itself in `modules` and create any needed permissions/settings hooks.

It must align these names consistently:
- folder name
- `module.json` name/alias
- DB `modules.module_name`
- `module_settings.module_name`
- package visibility references
- permission namespace
- translation keys where used

Name drift is one of the main reasons modules become invisible.

## 6. Package visibility contract

To work in this system, a module must be package-aware.

That means:
- it can appear in package creation/edit screens
- it can be toggled per package
- package changes can activate/deactivate it for tenant users
- user account visibility can derive from package settings or module settings

## 7. Tenant boundary contract

Every operational module must use:
- `company_id` as tenant boundary
- `user_id` where actor/ownership/audit is needed

Optional scoping fields:
- `location_id`
- `worker_id`
- `site_id`
- `channel_id`
- `device_id`

Queries must never rely only on `id` when tenant scope is required.

## 8. Migration contract

All migrations must be:
- idempotent
- safe on rerun
- safe against missing prerequisite tables/columns
- cautious with `after()` references
- explicit with foreign keys and cascade rules
- safe for partial installs and retries

## 9. Permission contract

A module should define:
- view
- create
- edit/update
- delete
- settings/admin actions
- publish/approve/export if relevant

Permissions should be module-namespaced and consistent.

## 10. Module settings contract

The module should seed `module_settings` for:
- existing companies on install
- future companies via company-created listener or observer pattern

Without this, modules often exist but remain invisible to users.

## 11. Sidebar and menu contract

### For user area
The module needs:
- route(s)
- permission checks
- sidebar/menu visibility wiring
- readable label

### For super admin
If it has admin controls, it needs:
- super admin route(s)
- settings/management page
- super admin sidebar entry or settings surface

## 12. API contract

A serious module should expose an API surface that PWAs and AI can consume without scraping views.

Each API should have:
- versioned path
- auth rules
- tenant scope
- stable response envelope
- validation object/form request
- predictable error format

## 13. AI contract

Modules should optionally expose an `ai_tools.json` or equivalent manifest defining:
- tool name
- purpose
- endpoint or action handler
- required parameters
- approval/risk level
- read/write classification

This allows Titan Zero to use modules as tools, not just as pages.

## 14. PWA contract

A module that wants to power PWAs should define:
- API endpoints for key flows
- offline-safe read models where appropriate
- sync-friendly IDs and timestamps
- attachment/file patterns that mobile/PWA clients can handle
- concise status models for task boards and cards

## 15. CMS contract

If a module contributes to public web surfaces, it should define a CMS manifest describing:
- surfaces it can render into
- widgets/cards/blocks available
- public-safe fields
- cache strategy

## 16. Omni/channel contract

If a module contributes to communication flows, it should define:
- channels supported
- message templates/intents
- inbound/outbound events
- handoff behavior
- AI tool availability for communication actions

## 17. Lifecycle/signal contract

Advanced modules should optionally emit:
- lifecycle stages
- signals/events
- machine-readable transitions

Examples:
- lead.created
- quote.sent
- booking.confirmed
- visit.completed
- invoice.overdue

## 18. Testing contract

Every module should have tests for:
- boot and route loading
- permission access
- tenant scoping
- package visibility
- API operations
- AI tool execution contract if present
- migration/install behavior

## 19. Performance contract

Every module should:
- avoid N+1 patterns
- eager load relationships where appropriate
- select only needed columns in list endpoints
- use queues for long-running work
- use cache where read-heavy and safe
- invalidate cache intentionally

## 20. Recommended build checklist

A module is ready when it:
- loads correctly
- appears in module list
- appears in packages
- respects tenant boundary
- appears to the right users after package enablement
- has working sidebar links
- has stable named routes
- has API endpoints
- has idempotent migrations
- has permissions
- seeds module settings
- can support AI and PWA surfaces if required

## 21. Final contract sentence

In this system, a module is complete only when it is simultaneously a Laravel feature, a package-aware product capability, a tenant-safe data owner, an API surface, and an AI/PWA-compatible tool provider.
