# Titan Manifest System

## Purpose

The Titan Manifest System defines how modules expose structured capabilities
to Titan Zero, AEGIS governance, automation engines, Omni channels, CMS layers,
and PWA nodes.

Manifests make modules discoverable, composable, automatable, and AI-readable
without schema introspection.

They are the contract between:

- modules
- automation engines
- signal engine
- AI orchestration
- CMS renderer
- Omni communications
- PWA node runtime

## Manifest Philosophy

Titan modules are not passive data containers.

They are active capability providers.

Each module must declare:

- signals it emits
- signals it accepts
- lifecycle transitions
- AI tools it exposes
- CMS surfaces it supports
- Omni channels it integrates

This allows Titan Zero to reason over modules safely.

## Manifest Location

Each module includes:

`Modules/<ModuleName>/manifests/`

Standard manifest set:

- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`

Optional:

- `permissions_manifest.json`
- `pwa_manifest.json`
- `automation_manifest.json`
- `voice_manifest.json`
- `policy_manifest.json`

## Manifest Loading Order

During boot:

`ModuleRegistryServiceProvider`

collects manifests from all modules, then registers them into:

- Platform Signal Engine
- Platform Automation Engine
- Platform AI Tool Registry
- Platform CMS Renderer
- Platform Omni Router

## `signals_manifest.json`

Defines signal contracts.

Example:

```json
{
  "emits": [
    "job.created",
    "job.scheduled",
    "job.completed"
  ],
  "accepts": [
    "invoice.generate",
    "crew.assign"
  ]
}
```

Used by:

- Signal Engine
- Automation Engine
- AEGIS governance validation
- Sentinel approval chains

Signals are tenant-scoped events.

They are never executed directly.

They must pass:

Scout → SignalAI → AEGIS → Sentinel

before consumption.

## `lifecycle_manifest.json`

Defines allowed entity transitions.

Example:

```json
{
  "entity": "service_job",
  "states": [
    "draft",
    "planned",
    "scheduled",
    "dispatched",
    "in_progress",
    "completed",
    "invoiced",
    "closed"
  ]
}
```

Used by:

- Workflow Engine
- Lifecycle Engine
- Dispatch Engine
- Automation Engine
- AI planning layer

Prevents illegal transitions.

## `ai_tools.json`

Declares callable tools exposed to Titan Zero.

Example:

```json
{
  "tools": [
    {
      "name": "create_service_job",
      "action": "CreateServiceJobAction",
      "permission": "jobs.create"
    }
  ]
}
```

Used by:

- Tool registry
- AI orchestration
- Context pack builder
- Automation engine
- Voice interface

Allows Titan Zero to safely invoke module actions.

## `cms_manifest.json`

Declares CMS surfaces.

Example:

```json
{
  "pages": [
    "service-page",
    "quote-page"
  ],
  "components": [
    "service-list",
    "booking-form"
  ]
}
```

Used by:

- CMS renderer
- Landing generator
- Marketing engine
- Template builder

Allows modules to extend the website automatically.

## `omni_manifest.json`

Declares communications availability.

Example:

```json
{
  "channels": [
    "email",
    "sms",
    "whatsapp",
    "voice"
  ]
}
```

Used by:

- Omni router
- Notification engine
- Campaign engine
- Receptionist AI

Controls which channels support module actions.

## `automation_manifest.json`

Declares automation compatibility.

Example:

```json
{
  "triggers": [
    "job.completed"
  ],
  "actions": [
    "send_invoice",
    "request_review"
  ]
}
```

Used by:

- Lifecycle Engine
- Reminder Engine
- Follow-up Engine
- Campaign Engine

## `permissions_manifest.json`

Declares policy surfaces.

Example:

```json
{
  "permissions": [
    "jobs.create",
    "jobs.assign",
    "jobs.complete"
  ]
}
```

Used by:

- AEGIS governance
- Policy engine
- Filament panels
- API guards

## `pwa_manifest.json`

Declares offline node capability.

Example:

```json
{
  "offline_supported": true,
  "sync_entities": [
    "service_job",
    "checklist",
    "inspection"
  ]
}
```

Used by:

- Titan Go
- Titan Command
- Titan Portal
- Edge node runtime

## `voice_manifest.json`

Declares voice-executable actions.

Example:

```json
{
  "voice_actions": [
    "start_job",
    "complete_job",
    "report_issue"
  ]
}
```

Used by:

- Voice runtime
- Realtime AI adapter
- Command parser

## `policy_manifest.json`

Declares governance constraints.

Example:

```json
{
  "requires_approval": [
    "invoice.writeoff"
  ]
}
```

Used by:

- AEGIS
- Sentinel layer
- Risk scoring engine

## Manifest Validation Rules

Each manifest must:

- be JSON
- be schema-valid
- be deterministic
- be tenant-safe
- avoid runtime queries
- avoid dynamic closures

Manifests describe capability.

They never execute logic.

## Manifest Registry Flow

Boot sequence:

- scan `Modules/*`
- load `manifests/*`
- validate schema
- register contracts
- bind into engines
- expose to AI layer

After registration:

- modules become automation-aware
- modules become AI-callable
- modules become CMS-extensible
- modules become Omni-enabled
- modules become signal-connected

## Manifest Role Inside Titan Zero

Titan Zero reads manifests instead of schema.

This enables:

- fast capability discovery
- safe tool routing
- context-pack building
- automation planning
- cross-module reasoning

Manifests are the language Titan uses to understand the system.

They convert Laravel modules into a cognitive platform.
