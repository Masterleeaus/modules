# Titan PWA Node Policy Overlays

## Purpose

Defines how Titan PWA Nodes apply policy overlays that modify behavior without forking the core runtime. Overlays allow the same node engine to operate safely across different tenants, industries, contracts, regulatory environments, and deployment contexts.

Policy overlays are interpreted as bounded rule packs layered on top of the shared node backbone.

---

## Why Overlays Exist

A single node runtime must serve multiple environments that differ in:

- approval thresholds
- workflow restrictions
- retention obligations
- AI usage boundaries
- sync timing rules
- geo restrictions
- industry compliance rules
- customer contract promises

Overlays let those differences be expressed declaratively instead of creating separate runtimes.

---

## Overlay Classes

### 1. Tenant Overlays

Customize behavior per company or tenant.

Examples:

- who can approve which actions
- maximum offline duration before forced resync
- queue retention windows
- allowed channels
- allowed modules on node surfaces

### 2. Vertical Overlays

Apply industry-specific constraints.

Examples:

- medical equipment service
- cleaning compliance
- real-estate inspection
- logistics dispatch
- hospitality checklists

These overlays shape workflows, required fields, and compliance checkpoints.

### 3. Regional Overlays

Represent rules tied to geography.

Examples:

- data retention obligations
- working-time restrictions
- privacy constraints
- region-based consent flows
- local communications restrictions

### 4. Contract Overlays

Capture customer- or package-specific obligations.

Examples:

- SLA escalation timing
- audit retention duration
- approval chain requirements
- reporting frequency
- allowed automation scope

### 5. Device-Class Overlays

Adjust behavior by runtime surface.

Examples:

- mobile-only reduced context windows
- kiosk restrictions
- site node limited controls
- rugged offline-first policies

---

## Overlay Structure

Recommended overlay shape:

```json
{
  "overlay_id": "tenant-cleaning-au",
  "overlay_type": "tenant",
  "version": 1,
  "priority": 50,
  "rules": {},
  "meta": {}
}
```

Key properties:

- overlay_id
- overlay_type
- version
- priority
- rules
- compatibility metadata

---

## Evaluation Order

When multiple overlays apply, evaluation should be deterministic.

Suggested order:

1. core runtime defaults
2. device-class overlay
3. vertical overlay
4. regional overlay
5. tenant overlay
6. contract overlay
7. temporary emergency override

Later overlays may narrow or strengthen constraints, but should not silently weaken mandatory base safety guarantees.

---

## Overlay Rule Domains

Overlays may influence:

- governance thresholds
- sync cadence
- approval routing
- retention windows
- observability verbosity
- AI advisory permissions
- workflow step availability
- retry ceilings
- escalation timing
- operator prompts and warnings

They should not directly rewrite core storage or signal semantics without explicit version compatibility.

---

## Governance Relationship

Policy overlays are interpreted by the governance runtime, not by ad hoc UI conditions.

That means:

- permissions still flow through governance
- approval rules still produce governed outcomes
- restricted actions remain blocked even offline
- overlays alter policy decisions, not trust boundaries

This keeps policy consistent across node surfaces.

---

## Offline Behavior

Nodes should cache active overlays locally so offline behavior remains policy-aligned.

While offline, overlays may still enforce:

- blocked transitions
- required approvals
- disabled AI suggestions
- shortened retention of sensitive traces
- mandatory warnings before actions

If an overlay expires or becomes unverifiable, the node should degrade toward a stricter safe mode.

---

## Overlay Compatibility

Before activation, the node should verify:

- overlay version compatibility
- runtime feature compatibility
- governance compatibility
- signal/schema compatibility
- tenant scope applicability

Unsupported overlays must be rejected cleanly rather than partially applied.

---

## Priority and Conflict Handling

Overlay conflicts should resolve through:

1. stronger safety rule
2. narrower scope match
3. higher explicit priority
4. latest valid version
5. fallback to core safe default

No overlay conflict should result in ambiguous execution.

---

## Observability Requirements

Overlay application should be visible in telemetry.

Useful events include:

- overlay.loaded
- overlay.rejected
- overlay.expired
- overlay.conflict_detected
- overlay.safe_mode_applied

This makes policy-driven runtime differences inspectable.

---

## Example Overlay Effects

Examples:

- Cleaning tenant requires photographic proof before completion
- Medical-service vertical disables local AI classification for regulated incidents
- Regional overlay increases consent requirements before messaging
- Contract overlay requires supervisor approval for invoice-impacting job changes
- Device overlay hides high-risk bulk actions on handheld nodes

These are policy changes, not separate apps.

---

## Relationship to Other Docs

This document extends:

- node-governance-runtime.md
- node-sync-engine.md
- node-observability.md
- node-edge-ai-runtime.md
- node-upgrade-coordination.md

Policy overlays do not replace those layers; they specialize them safely.

---

## Future Related Docs

Possible next docs:

- overlay-authoring-guide.md
- node-emergency-safe-mode.md
- node-regulated-data-handling.md

---

## Overlay Precedence

When multiple overlays apply at once, precedence should be explicit rather than implied.

Recommended precedence:

1. hard platform safety rules
2. regulatory overlays
3. contractual overlays
4. tenant overlays
5. role or device overlays
6. temporary incident overlays

## Overlay Versioning

Each overlay pack should carry at minimum:

- overlay_id
- version
- scope
- effective_from
- compatibility targets
- rollback target
