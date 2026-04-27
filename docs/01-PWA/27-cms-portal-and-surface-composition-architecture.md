# 27. CMS, Portal, and Surface Composition Architecture

## Purpose

This document defines how the system should compose customer-facing, staff-facing, and public-facing surfaces across Worksuite, Titan Studio, Filament panels, PWAs, and future mobile shells.

The goal is not to make the CMS a separate toy website builder. The goal is to make surface composition a first-class platform capability so that modules can project data and actions into:

- public websites
- service pages
- booking widgets
- customer portals
- staff portals
- PWA views
- chat-first and AI-mediated surfaces

## Core doctrine

The module contract already requires modules to optionally expose a `cms_manifest.json` so the platform can render module data into website pages, portal UI, and PWA views. That means CMS integration is not an afterthought. It is part of the module’s platform contract.

The system therefore needs three layers:

1. **Domain layer** — modules own the data, policies, and actions.
2. **Surface composition layer** — CMS/portal infrastructure decides how those module capabilities appear on websites, portals, and app shells.
3. **Runtime delivery layer** — Filament, Blade, APIs, PWAs, and native/mobile shells render the chosen surfaces.

This keeps business logic inside modules and prevents websites or panels from becoming shadow systems.

## Surface classes

The platform should formally distinguish the following surface classes.

### Public marketing surfaces

Examples:
- home page
- landing pages
- service pages
- promotional surfaces
- booking funnels
- quote-request forms
- SEO content pages

Authority:
- Titan Studio owns marketing composition in standalone mode.
- In Duo mode, Studio still owns marketing composition, but operational data comes from Worksuite through safe read interfaces.

### Operational customer surfaces

Examples:
- customer portal
- quote acceptance page
- invoice payment page
- booking status page
- compliance/document portal
- support/conversation views

Authority:
- Worksuite is the system of record.
- These surfaces may be rendered through CMS-like tooling, but they must not bypass module actions, policies, or tenant scoping.

### Staff/operator surfaces

Examples:
- Filament admin pages
- dispatch boards
- review/approval screens
- module dashboards
- exception queues

Authority:
- Worksuite/Filament panels consume module actions and present operator workflows.

### Edge/app surfaces

Examples:
- PWA task views
- field-worker flows
- offline forms
- mobile-first dashboards
- kiosk/device shells

Authority:
- Node/PWA runtime consumes APIs, signals, manifests, and sync contracts.
- UI shells may differ, but domain truth remains in modules and platform services.

## Surface composition model

Each module should be able to declare surfaces in a way that is discoverable, governable, and renderable.

### Minimum manifest concepts

A module’s surface manifest should express:

- surface keys
- audience
- route intent
- data contract
- policy requirement
- render mode
- cacheability
- sync/offline eligibility
- AI eligibility

Example conceptual entries:

- `homepage_promotions`
- `services_page`
- `booking_widget`
- `portal_invoice_status`
- `portal_job_timeline`
- `pwa_today_jobs`
- `pwa_site_memory`

The manifest should not contain application logic. It should describe capability and rendering expectations.

## Website and portal composition pipeline

The platform should support a composition pipeline like this:

1. **Module publishes capability**
   - via manifests, routes, resources, and APIs.
2. **Registry indexes capability**
   - module registry records available surfaces.
3. **Composer selects surface placement**
   - admin/studio chooses where a surface appears.
4. **Policy engine validates visibility**
   - tenancy, package, permissions, and audience checks.
5. **Renderer resolves data contract**
   - through module actions/resources/API adapters.
6. **Shell renders**
   - Blade, Filament, PWA, or future native shell.

This makes surfaces composable without making the CMS own business rules.

## Blade, Filament, and PWA roles

### Blade

Blade should remain the default HTML renderer for public and portal surfaces when SSR and SEO matter.

Use Blade for:
- websites
- service pages
- customer-access flows
- legal/compliance pages
- hybrid portal pages

### Filament

Filament should not be the CMS, but it can host:
- surface editors
- placement controls
- preview tools
- approval queues
- operator dashboards
- page assembly tools for admin users

Filament is the operator control plane for surfaces, not the universal rendering substrate.

### PWA runtime

PWAs should render:
- task-first operational surfaces
- offline-first field flows
- device-aware layouts
- sync-aware lists and forms
- action queues and pending items

A PWA surface may reuse the same domain capability as a website surface, but via a different runtime contract.

## Public/portal routing doctrine

The platform should avoid letting each module invent random public paths.

Recommended routing families:

- `/services/...`
- `/portal/...`
- `/pay/...`
- `/book/...`
- `/quote/...`
- `/track/...`
- `/docs/...`

Behind those stable families, the module registry maps route intent to modules and actions.

This preserves consistency, helps SEO, and makes cross-surface governance easier.

## Tenant-safe portal composition

Portal surfaces must always respect tenant boundaries.

Required rules:

- every portal-visible record is company-scoped
- customer-facing access never depends only on numeric IDs
- signed links or tokens should be used for public access flows
- operational mutations go through module actions, not view callbacks
- package and permission gates still apply where relevant

Customer portal convenience must not create cross-tenant leakage.

## Surface composition and package gating

Package selection should not only toggle modules. It should also affect surface availability.

Examples:

- a basic package may include invoice viewing but not payment plans
- a higher package may include booking widget embedding
- a communications add-on may expose WhatsApp-visible portal entry points
- a field-service overlay may unlock site memory and proof-of-service views

So the system should support:
- module enablement
- feature enablement
- surface enablement
- channel enablement

as distinct but related layers.

## Surface analytics and feedback loops

Surface composition should emit events.

At minimum:
- surface rendered
- widget started
- form submitted
- quote requested
- payment initiated
- payment completed
- portal message opened
- handoff triggered

These events should feed:
- marketing attribution
- operational readiness
- AI context
- UX tuning
- package/product decisions

## AI role in surface composition

Titan Zero should not silently redesign pages. But it should be allowed to:

- recommend surface placement
- propose copy/content drafts
- infer missing portal steps
- suggest which surfaces should appear by audience or lifecycle stage
- propose escalation from marketing page to operational handoff

All promotion from draft to live should remain governed.

## Recommended platform folders

The system should include clear homes for this capability.

Suggested platform areas:

- `app/Platform/Cms/`
- `app/Platform/Navigation/`
- `app/Platform/Modules/`
- `app/Platform/Packages/`
- `app/Platform/Pwa/`
- `app/Platform/Api/`

And in modules:

- `manifests/cms_manifest.json`
- `Routes/web.php`
- `Routes/api.php`
- `Http/Resources/`
- `Actions/`
- `Policies/`

## Build guidance

When adding a new surface capability, developers should answer:

1. Which module owns the data?
2. Which action is the source of truth?
3. Which audience is this surface for?
4. Is it public, authenticated, or signed-link access?
5. Is it website, portal, Filament, or PWA?
6. Is it package-gated?
7. Does it need offline support?
8. What events should it emit?
9. Can Titan propose drafts for it?
10. What is the fallback if the richer shell is unavailable?

## Final rule

The CMS, portal, and PWA layers must all be treated as **surface systems**, not alternate business systems.

Modules own truth. Platform services own composition. Shells own rendering.
