# Module Lifecycle Upgrade Repair

## Purpose

Define the operational lifecycle of a module after discovery, covering install, enable, disable, upgrade, uninstall, and repair transitions so module evolution stays safe and tenant-aware.

## Scope

This document covers:

- lifecycle states
- transition rules
- version-aware upgrade expectations
- repair-mode behavior
- rollback and compatibility handling
- operational safety checks

This document does not replace installation or health-check docs; it extends them across the module’s ongoing life.

## Architecture Position

Lifecycle management sits between initial installation and long-term runtime trust.

It connects:

- registry state
- migrations and seeders
- settings and packages
- route/provider changes
- health checks and repair tooling
- manifests and capability drift control

## Responsibilities

A lifecycle layer must manage these transitions safely:

- install
- enable
- upgrade
- disable
- uninstall
- repair

It must preserve tenant safety, runtime consistency, and compatibility across those transitions.

## Lifecycle States

A practical state model includes:

- discovered
- installed
- enabled
- disabled
- upgrade-pending
- repair-needed
- uninstalled

The system should avoid ambiguous states such as “partially active but unknown.”

## Install to Enable Transition

After installation completes, the module should only move to enabled when:

- providers boot
- routes load
- migrations are current
- settings and permissions exist
- package/tenant visibility can be evaluated
- health checks pass

## Upgrade Rules

Upgrades should be version-aware and migration-safe.

An upgrade process should:

- compare current vs target version
- apply required migrations safely
- backfill new settings and permissions
- validate changed manifests
- rebuild registry/cache
- preserve tenant-specific values unless explicit migrations state otherwise

## Data Preservation Rules

Upgrades and repairs must prefer preserving:

- tenant data
- settings overrides
- package assignments
- audit history
- signal/log continuity

A module upgrade should not silently reset business data or tenant configuration.

## Capability Drift Control

When a new version changes:

- routes
- manifests
- settings keys
- permissions
- API endpoints
- UI surfaces

the lifecycle process must reconcile those changes explicitly rather than assuming the runtime will self-correct.

## Disable Rules

Disabling a module should:

- remove it from active tenant/runtime exposure
- preserve data unless explicitly archived/removed
- prevent new UI/API entry where appropriate
- keep enough metadata for later re-enable or repair

Disable should not be treated as uninstall.

## Uninstall Rules

Uninstall is destructive and should be deliberate.

A safe uninstall flow should:

- confirm no critical dependencies still rely on the module
- document what data will be removed or retained
- clean registry and visibility state
- avoid leaving broken menu/package references behind

## Repair Mode

Repair exists for drifted or partially broken modules.

Repair may include:

- re-reading manifests
- rebuilding the registry cache
- reseeding missing permissions
- backfilling settings
- revalidating route/provider wiring
- reconciling package or menu visibility drift

Repair should be idempotent and repeatable.

## Rollback Expectations

Where possible, lifecycle management should support rollback or at least failure isolation.

That means the system should surface:

- which phase failed
- which changes were already applied
- whether rerun is safe
- whether manual intervention is required

## Failure Modes

Common lifecycle failures include:

- migrations succeed but permissions/settings drift
- upgrade changes route names and breaks menus
- new manifests are added but never registered
- disable hides UI but leaves APIs live
- repair reseeds too aggressively and overwrites tenant values
- uninstall removes files but leaves database/package drift behind

## Observability

Lifecycle operations should be visible through:

- install/upgrade logs
- migration output
- registry rebuild output
- doctor/repair results
- health-check summaries
- package and menu sync diagnostics

## Security Model

Lifecycle transitions must continue to honor:

- `company_id` tenant fencing
- package entitlements
- permission/policy boundaries
- AI/automation governance where capabilities are exposed

A module should never become “temporarily unrestricted” during upgrade or repair.

## Example Flow

1. Module is installed and enabled.
2. A new version is uploaded.
3. Upgrade detects version delta.
4. Migrations, settings backfills, and manifest checks run.
5. Registry and route visibility are rebuilt.
6. Health checks validate the post-upgrade state.
7. If drift remains, repair mode can backfill missing contracts safely.

## Future Expansion

This lifecycle contract should later support:

- explicit state-machine modeling
- upgrade recipes
- semantic compatibility scoring
- dependency-aware blocking
- dry-run repair diagnostics
