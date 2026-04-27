# Node Installation and Deployment

## Purpose
This document defines how Agent 5 should install, publish, verify, and deploy the PWA/node layer so the runtime is actually reachable from browser nodes and device shells.

## Deployment targets
- browser PWA shell
- public manifest endpoint
- public service worker endpoint
- offline fallback page
- icons and static assets
- authenticated API endpoints for push and sync

## Minimum published surfaces
A valid deployment should expose:
- `/titanpwa/manifest.json`
- `/titanpwa-sw.js`
- `/titanpwa/offline` or published `/offline.html`
- published icons under `public/vendor/titanpwa/icons/`
- authenticated `/api/titanpwa/*` endpoints

## Install order
1. Confirm module discovery and provider boot.
2. Run database migrations for push subscriptions and sync queue tables.
3. Publish service worker, offline page, icons, and CSS/JS assets.
4. Generate VAPID keys if push is enabled.
5. Confirm route reachability and correct MIME/content type behavior.
6. Register PWA head/scripts in the shell layout.
7. Verify authenticated sync + push APIs with a real session.

## Environment requirements
- HTTPS required for installability and push subscriptions.
- service worker must be reachable from the expected scope.
- VAPID public/private keys must be present for web push.
- auth token/session strategy must match the node shell design.

## Agent verification checklist
- manifest loads without auth redirect
- service worker returns JS, not HTML error page
- offline fallback is cacheable and reachable
- push key endpoint returns configured public key
- sync queue endpoints require auth and return JSON
- published assets resolve from the active domain

## Failure modes
- service worker served behind wrong path/scope
- manifest route exists but content type is wrong
- offline page published but not cached
- VAPID keys missing
- auth middleware blocking public PWA assets
- cache busting causing stale shell/runtime mismatch
