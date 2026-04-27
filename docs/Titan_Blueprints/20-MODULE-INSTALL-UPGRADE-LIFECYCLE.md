# Module Install + Upgrade Lifecycle

## Goal
Make every module installable, upgradeable, removable, and re-runnable without drift.

## Install phases
1. Discover module
2. Validate manifest
3. Register provider(s)
4. Run idempotent schema changes
5. Seed permissions
6. Seed package/module settings
7. Seed navigation/menu entries
8. Register API/UI/manifests
9. Clear caches
10. Verify health

## Discovery contract
A module should be discoverable from:
- `Modules/<ModuleName>/module.json`
- provider list in manifest
- optional manifests directory for AI/CMS/Omni/signals/lifecycle

## Required install checks
- Folder name matches module name/casing
- Provider namespace resolves
- Route files load
- View namespace loads
- Migration classes are unique
- Tables/columns are safe on re-run
- Permissions exist
- Module settings exist for existing companies
- Package/module attachment is available
- Menus use route names, not raw URLs

## Migration rules
- Must be idempotent
- Must guard existing tables/columns/indexes
- Must avoid destructive assumptions during upgrade
- Must support fresh install and late install on populated systems

Examples:
- `Schema::hasTable()`
- `Schema::hasColumn()`
- defensive FK creation

## Seeder rules
Use seeders for:
- permissions
- module defaults
- package toggles
- automation defaults
- demo/system roles only where appropriate

Seeders should be safe to run repeatedly.

## Upgrade rules
Upgrades should:
- preserve existing data
- add nullable/new columns first when possible
- backfill in controlled passes if needed
- avoid breaking route names and permission keys casually

## Uninstall posture
Preferred approach:
- disable first
- preserve data by default
- offer explicit purge path separately

## Verification checklist
After install/upgrade, verify:
- module appears in admin list
- user and/or admin routes resolve
- sidebar entries render correctly
- settings page opens
- package screen can toggle module
- permissions gate correctly
- tenant data saves under correct `company_id`
- APIs respond
- manifests are discoverable
