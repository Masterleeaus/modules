# Module Permissions

## Purpose

Define the permission and authorization contract for Titan-ready Worksuite modules so access is predictable across user panels, superadmin areas, APIs, automation, and future AI-invoked actions.

## Scope

This document covers:

- seeded permissions
- module access verbs
- sidebar visibility requirements
- policy alignment
- audience separation
- permission use across controllers, APIs, and panel actions

This document does not define low-level role UI or package billing rules.

## Architecture Position

Permissions sit at the boundary between:

- module discovery and installation
- navigation rendering
- controller/action access
- policy enforcement
- AI and automation safe-mode constraints

The checklist makes seeded permissions and sidebar visibility part of the install contract, not a later enhancement. fileciteturn4file0

## Responsibilities

The module permission layer owns:

- defining module access verbs
- ensuring permissions exist at install time
- connecting permissions to routes, menus, and policies
- distinguishing tenant users from superadmin access
- making module behavior safe for non-UI execution paths

## Minimum Permission Set

Every module should seed, at minimum:

- `view_<module>`
- `create_<module>`
- `edit_<module>`
- `delete_<module>`

The Titan-ready checklist lists these as baseline visibility and action permissions. fileciteturn4file0

## Extended Permission Set

Modules may also define capability-specific verbs such as:

- `publish_<module>`
- `export_<module>`
- `approve_<module>`
- `assign_<module>`
- `manage_settings_<module>`

The example permission seeder in the checklist explicitly includes expanded verbs such as publish and export. fileciteturn4file0

## Seeder Contract

Permissions must be created through module seeders so a fresh install, upgrade, or repair pass can restore missing capabilities deterministically.

Typical seeder responsibilities:

- create missing permissions idempotently
- map permissions to appropriate roles
- preserve existing assignments where possible
- expose module access immediately after install

This matches Laravel’s migration-and-seeding workflow for consistent environment bootstrapping. fileciteturn4file1turn4file5

## Sidebar Visibility

A module must not appear in the sidebar only because its files exist.

Sidebar visibility requires:

- correct menu registration
- valid named target route
- permission gating
- installed/enabled module state

The checklist explicitly ties sidebar visibility to permission seeding plus menu registration. fileciteturn4file0

## Policy Alignment

Permissions and policies are complementary:

- permissions answer broad capability questions
- policies answer record/context-specific questions

Examples:

- permission: user may edit service jobs
- policy: user may edit this service job for this company

The full engine blueprint places policies as a first-class app layer, which means module permissions must map cleanly into policy checks instead of replacing them. fileciteturn4file7

## Audience Separation

### Tenant user access

Tenant users should receive permissions scoped to company-bound module behavior.

### Superadmin access

Superadmin access may include global configuration, module settings, diagnostics, or repair operations not exposed to tenant users.

### API access

API endpoints must enforce the same permission model as panel routes. A valid token without capability checks is not sufficient.

## Non-UI Execution Paths

Permissions must still matter when actions are triggered from:

- API requests
- queued jobs initiated by user actions
- imports/exports
- Titan chat/tool execution
- automation pipelines

The module blueprint warns against trapping business rules in Filament callbacks. Capability checks must survive outside the panel UI. fileciteturn4file6

## AI and Automation Safety

When AI tools or automation flows invoke module actions, permission checks should evaluate:

- actor identity
- tenant boundary
- allowed capability
- risk/approval requirements for sensitive operations

This aligns with the broader platform blueprint where Signals, Automation, and AI governance interact instead of bypassing one another. fileciteturn4file7

## Failure Modes

Common permission failures:

- module installs without seeding permissions
- menu appears but user lacks route capability
- APIs bypass panel permission logic
- superadmin rules leak into tenant space
- Filament actions enforce checks but controllers do not
- automation executes actions without actor/capability validation

## Observability

Permission diagnostics should include:

- seeded permission inventory
- role-to-permission mapping checks
- menu visibility tests by role
- API authorization smoke tests
- action-level denial logs for sensitive module operations

## Security Model

Permissions must respect:

- `company_id` as the tenant boundary
- least-privilege defaults
- explicit superadmin separation
- policy checks for record-level decisions
- no hidden privilege escalation through background execution paths

## Example Flow

1. Module installs.
2. Permission seeder creates `view_services`, `create_services`, `edit_services`, `delete_services`.
3. Menu item is registered.
4. Sidebar checks both enablement and permission.
5. Controller and API action both enforce capability.
6. Shared action executes only after authorization succeeds.

## Future Expansion

Later additions may include:

- permission manifests
- package-tier capability gating
- channel-specific permissions for Omni
- AI-tool capability maps
- approval-required permissions for high-risk actions
