# Titan AI Tool Registry

## Purpose

The AI Tool Registry exposes module capabilities to Titan Zero
as structured callable actions.

It replaces direct controller invocation with governed execution paths.

Tools are discovered through:

`ai_tools.json`

inside module manifests.

## Registry Location

Core runtime:

`app/Platform/Ai/Tooling/Registries/`

Responsibilities:

- discover tools
- validate permissions
- bind actions
- normalize outputs
- publish tool catalog

## Tool Definition Example

Example:

```json
{
  "name": "create_service_job",
  "action": "CreateServiceJobAction",
  "permission": "jobs.create"
}
```

Each tool maps to:

- Action class
- Policy check
- Signal emission
- Audit entry

## Tool Resolution Flow

When Titan Zero selects a tool:

- resolve manifest entry
- check tenant scope
- validate permission
- hydrate DTO
- execute action
- emit signal
- return normalized result

Execution is never direct model mutation.

Actions are always used.

## Tool Categories

Registry organizes tools into:

- CRUD tools
- workflow tools
- automation triggers
- communications tools
- finance tools
- CMS tools
- Omni tools
- voice tools

Supports selective exposure by interface type.

## Context Pack Integration

Tool metadata feeds:

`ContextPack` builder

Example fields:

- input schema
- required permissions
- entity scope
- risk level
- latency profile

Allows Titan Zero to reason before execution.

## Permission Enforcement

Before execution, policy engine verifies:

- role permissions
- tenant ownership
- approval requirements
- AEGIS restrictions

Unauthorized tools are excluded from candidate set.

## Tool Output Normalization

Registry standardizes responses:

```json
{
  "status": "success",
  "entity": "service_job",
  "id": 882,
  "signals_emitted": ["job.created"]
}
```

This ensures compatibility with:

- automation engine
- workflow engine
- signal engine
- chat interface
- voice interface

## Tool Exposure Layers

Registry supports scoped availability for:

- chat interface
- Filament UI
- API endpoints
- voice runtime
- automation engine
- PWA nodes

Each layer can filter tools independently.

## Tool Governance Hooks

AEGIS may require approval for:

- financial mutations
- contract edits
- schedule overrides
- cross-tenant operations

Registry enforces approval routing automatically.

## Tool Versioning

Each tool supports:

- version id
- schema hash
- permission revision
- manifest revision

Ensures backward compatibility across nodes.

## Tool Registry Responsibilities

Owns:

- tool discovery
- capability indexing
- permission validation
- execution routing
- result normalization
- approval gating
- context-pack enrichment

This converts modules into AI-operable infrastructure.
