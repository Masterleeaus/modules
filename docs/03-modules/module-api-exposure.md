# Module API Exposure

## Purpose

Define how a Titan-ready module exposes stable API surfaces for web, PWA, mobile, Omni, Titan Zero, and external integrations.

## Scope

This document covers:

- required API route presence
- route ownership and namespace expectations
- controller/application-layer boundaries
- authentication and authorization expectations
- response consistency
- API compatibility with PWA, Titan Go, Titan Command, Titan Portal, and AI tooling

This document does not define:
- manifest schema details
- menu wiring
- internal business logic classes

## Architecture Position

Module APIs are one of the primary execution surfaces of a module.

They sit between:

- external callers and front-end clients
- PWA and mobile runtime
- AI tool adapters
- automation and workflow execution
- module actions and services

## Responsibilities

A module API layer must:

- provide stable endpoints in `Routes/api.php`
- delegate business logic to module actions/services
- enforce tenant boundary checks
- return consistent payloads
- remain usable outside panel UI
- support future UI layers without duplicating logic

## Required Route Surface

A Titan-ready module should define `Routes/api.php`.

Typical examples include:

- `/api/promotions`
- `/api/services`
- `/api/bookings`

The exact routes may vary by domain, but the contract must be explicit, named where appropriate, and versionable if the surface becomes public.

## Controller Placement

API controllers should live under:

- `Http/Controllers/Api`

This keeps API request handling separate from web/panel flows while still using the same module-owned actions and services.

## Thin API Layer Rule

API controllers should:

- accept requests
- validate input
- resolve tenant/user context
- call module actions/services
- normalize responses

API controllers should not:

- contain the only copy of business rules
- duplicate logic that already exists in web/panel flows
- trap workflow rules in controller methods

## Validation Contract

Validation that matters everywhere should be centralized in:

- request classes
- actions
- services
- policies

This keeps the API compatible with web, import, automation, and AI execution paths.

## Authentication and Authorization

The module API must be able to support authenticated use by:

- tenant web apps
- PWAs
- mobile shells
- Titan-facing tools
- trusted external integrations

Authorization checks must respect:

- `company_id` tenant boundary
- user permissions
- package/module availability
- policy restrictions

## Response Shape Expectations

API responses should be consistent and machine-consumable.

Recommended response qualities:

- stable keys
- explicit status
- readable error codes/messages
- predictable pagination where needed
- normalized resource shapes

This is critical for:

- Titan chat tools
- automation pipes
- PWA sync/runtime
- external system integrations

## PWA and Mobile Compatibility

The API layer is required for first-class support of:

- Titan Go
- Titan Command
- Titan Portal
- device/PWA surfaces

That means the API must be usable independently of Filament or Blade surfaces.

## AI Tool Compatibility

Module APIs may back AI tools exposed through `ai_tools.json`.

An AI tool contract should be able to point to a stable endpoint, with clear input and output expectations.

This enables:

- chat execution
- signal proposals
- lifecycle actions
- automation invocations

## Signal and Workflow Compatibility

API-triggered actions may emit signals and enter workflow or automation engines.

So the API must not bypass:

- signals registration
- governance checks
- approval rules
- audit logging

## Versioning and Change Safety

When API behavior changes, the module should:

- preserve backwards compatibility where possible
- document breaking changes
- normalize renamed fields
- keep stable action semantics across UI surfaces

## Failure Modes

Common API failures include:

- route exists but bypasses tenant checks
- panel logic works but API path does not
- response shape differs per controller
- AI tool points at unstable endpoint
- PWA assumes fields the API no longer returns

## Observability

API activity should be visible through:

- request logs
- audit logs
- signal logs
- approval/rejection traces
- error monitoring

## Security Model

API routes must never treat the module as globally accessible by default.

They must enforce:

- authentication where needed
- authorization checks
- tenant fencing
- input validation
- safe error responses

## Example Flow

1. PWA submits a booking create request.
2. API controller validates and resolves tenant context.
3. Controller calls `CreateBookingAction`.
4. Action persists domain data and emits module events/signals.
5. API returns normalized result for UI and AI consumers.

## Future Expansion

This contract should later support:

- explicit version namespaces
- async action receipts
- webhook callbacks
- offline sync acknowledgements
- richer API resource transformers
