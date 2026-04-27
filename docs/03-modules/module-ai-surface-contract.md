# Module AI Surface Contract

## Purpose

Define how a module exposes machine-readable capability information to Titan Zero so the AI can understand what the module can do without scanning the full schema or inferring behavior from UI code.

## Scope

This document covers:

- AI-readable capability exposure
- tool and signal declarations
- entity and action visibility
- permission and tenancy metadata
- automation, scout, and sentinel compatibility
- AI-safe invocation boundaries

This document does not replace the dedicated manifests, API, or signals documents. It defines how those surfaces become readable and usable by Titan Zero.

## Architecture Position

The AI surface contract sits at the boundary between:

- module manifests
- API and action exposure
- signals and lifecycle flows
- package/permission/tenant rules
- Titan Zero orchestration and governance

It is the module’s AI-facing envelope.

## Responsibilities

A module AI surface must let Titan Zero determine:

- what the module is
- which entities it owns
- which actions/tools it exposes
- which signals it emits or consumes
- which permissions gate those actions
- which tenant boundary rules apply
- which automation/lifecycle hooks exist
- whether the module is safe to invoke in a given context

## Core Principle

Titan should read the module through manifest and envelope metadata, not by exploring every table, controller, or UI callback.

This reduces drift, limits scan cost, and keeps AI interaction bounded and auditable.

## Required AI-Readable Declarations

A strong module AI surface should expose:

- tools
- signals
- entities
- permissions
- tenancy boundary

These may be distributed across manifests, but together they must form a coherent machine-readable contract.

## Tool Contract

Tool exposure should be stable enough for chat, automation, and guided execution.

A tool definition should make it possible to identify:

- tool name
- purpose
- endpoint or action target
- required inputs
- permission gates
- tenant scope
- expected output shape

This commonly aligns with `ai_tools.json`.

## Signal Contract

AI also needs to know which signals the module can emit, consume, or react to.

This should be discoverable from `signals_manifest.json` and related module docs so Titan can reason about downstream effects and approval boundaries.

## Entity Contract

The AI surface should identify the primary domain entities the module works with.

Examples:
- booking
- promotion
- service
- campaign
- work order

This does not require full schema exposure. It requires enough semantic labeling for the AI to reason safely about the module’s domain.

## Permission Contract

The AI must know the permission or policy boundary for module actions.

That means the module’s AI surface should communicate:

- whether an action is read, create, update, delete, publish, export, etc.
- which permission family applies
- whether elevated approval is required
- whether package state can disable the action

## Tenancy Contract

The AI surface must explicitly respect tenant fencing.

At minimum, it should make clear that:

- `company_id` is the tenant boundary
- reads and writes are tenant-scoped
- actions cannot cross tenant boundaries
- tool execution must preserve tenant context

## Automation, Scout, and Sentinel Hooks

A stronger AI surface may also expose:

- automation hooks
- lifecycle stages
- scout triggers
- sentinel readiness validators

This allows Titan Zero to reason not only about direct tool use, but about whether a proposed action is lifecycle-valid and approval-ready.

## Invocation Boundary Rules

The AI surface must prevent the module from becoming an unbounded execution target.

That means:

- AI should not infer hidden actions from UI routes
- tool calls should route through explicit APIs/actions
- permissions and tenant context should be checked
- signals and approvals should remain visible to governance layers

## Failure Modes

Common AI-surface failures include:

- module has APIs but no AI-readable metadata
- tool manifest points to unstable endpoints
- permissions are not declared clearly enough for safe invocation
- AI can see UI names but not actual action contracts
- module exposes entities ambiguously
- tenant boundary is implied but never stated

## Observability

AI-surface use should be traceable through:

- tool invocation logs
- signal logs
- approval/rejection traces
- module health diagnostics
- manifest validation warnings

## Security Model

A valid AI surface contract must be:

- explicit
- permission-aware
- tenant-fenced
- package-aware
- approval-compatible

AI-readable does not mean AI-unrestricted.

## Example Flow

1. Titan Zero scans module manifests.
2. It learns the module’s entities, tools, signals, and permission gates.
3. User requests an action through chat.
4. Titan resolves tenant context and checks package/permission boundaries.
5. Tool invocation uses the explicit module contract rather than guessing from UI or schema.
6. Signals, approvals, and audit traces remain intact.

## Future Expansion

This contract should later support:

- richer typed tool schemas
- evidence requirements per action
- risk levels
- lifecycle-step metadata
- AI-readable field semantics
- structured readiness signals for scout/sentinel flows
