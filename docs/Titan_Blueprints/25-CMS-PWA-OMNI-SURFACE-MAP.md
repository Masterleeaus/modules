# CMS + PWA + Omni Surface Map

## Goal
Map how one backend capability appears across site, app, and channel surfaces.

## CMS surface
Use CMS manifests to declare where module data can render into public/private web pages.

Examples:
- service cards
- booking widgets
- promotion banners
- portal summaries
- FAQ/support fragments

## PWA surface
PWA views should consume the same domain actions and APIs as other clients.

Common PWA concerns:
- offline cache
- sync queue
- optimistic actions
- reduced payloads
- role-specific mobile surfaces

## Omni surface
Omni is the outward channel layer.
It should reuse communications + domain + AI/tooling layers, not duplicate them.

Typical Omni actions:
- send quote link
- confirm booking
- chase payment
- follow up missed lead
- answer service/status questions

## Surface mapping pattern
One capability -> many surfaces:
- Domain action: `CreateBookingAction`
- CMS: booking widget
- PWA: mobile booking form
- API: `/api/bookings`
- Omni: conversational booking flow
- Filament: operator booking admin screen

## Contract rule
Surfaces may differ in UX, but underlying semantics should stay aligned:
- same statuses
- same transition rules
- same tenant boundary
- same audit trail
