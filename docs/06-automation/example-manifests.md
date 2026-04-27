# Titan Zero Documentation

Layer: Automation
Scope: Example manifest contracts for automation-enabled modules, engines, signals, and channel surfaces
Status: Draft v1
Depends On: automation-engines.md, lifecycle-engine.md, trigger-evaluation.md, decision-envelopes.md, module manifest doctrine
Consumed By: Module developers, Titan Zero, AEGIS, Sentinels, installer/doctor, API/PWA surfaces, communications layer
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Provide concrete example manifests that show how automation-ready modules should declare lifecycle stages, signals, tools, CMS surfaces, Omni channels, and approval-sensitive runtime hooks.

## 2. Why it exists

The automation docs define the runtime model, but module builders still need a practical contract.

Without examples:

- manifests drift into inconsistent shapes
- modules expose signals but not lifecycle hooks
- AI tools call actions that are invisible to governance
- installer and doctor checks cannot confirm automation readiness
- PWA, portal, and Omni surfaces guess at capabilities

This document turns abstract doctrine into repeatable starter manifests.

## 3. Core rule

A manifest is not just metadata. It is a machine-readable promise about:

- what the module can emit
- what the module can consume
- which actions are approval-sensitive
- which lifecycle stages it participates in
- which surfaces can render or trigger it

If a capability is not in a manifest, automation should treat it as unavailable by default.

## 4. Manifest set

A full automation-ready module can expose:

- `ai_tools.json`
- `signals_manifest.json`
- `lifecycle_manifest.json`
- `cms_manifest.json`
- `omni_manifest.json`
- optional `approval_manifest.json`
- optional `runtime_policy.json`

Not every module needs every manifest, but automation-facing modules should publish enough to be discoverable and governable.

## 5. Example: signals manifest

```json
{
  "module": "BookingManagement",
  "version": 1,
  "signals": [
    {
      "key": "booking.created",
      "source": "booking",
      "approval_required": false,
      "idempotency_scope": "booking_id",
      "handoff": ["lifecycle", "reminder"]
    },
    {
      "key": "booking.reschedule_requested",
      "source": "booking",
      "approval_required": true,
      "approval_mode": "operator_or_policy",
      "idempotency_scope": "booking_id+requested_start",
      "handoff": ["approval", "lifecycle"]
    },
    {
      "key": "booking.no_access_reported",
      "source": "visit",
      "approval_required": true,
      "handoff": ["escalation", "recovery"]
    }
  ]
}
```

### Notes

- `key` must be globally stable
- `approval_required` must match runtime policy
- `handoff` tells automation which engine families may accept the signal
- `idempotency_scope` tells the runtime how duplicates are suppressed

## 6. Example: lifecycle manifest

```json
{
  "module": "BookingManagement",
  "entity": "booking",
  "stages": [
    "lead",
    "quote",
    "booking",
    "scheduled",
    "dispatched",
    "on_site",
    "completed",
    "invoiced",
    "followup"
  ],
  "transitions": [
    {
      "from": "booking",
      "to": "scheduled",
      "trigger": "booking.confirmed",
      "engine": "lifecycle"
    },
    {
      "from": "scheduled",
      "to": "dispatched",
      "trigger": "dispatch.assigned",
      "engine": "lifecycle"
    },
    {
      "from": "completed",
      "to": "followup",
      "trigger": "visit.completed",
      "engine": "followup"
    }
  ]
}
```

### Notes

- lifecycle stages are business-facing
- engine names are runtime-facing
- the manifest should not hide whether a transition is operator-triggered or signal-triggered

## 7. Example: AI tools manifest

```json
{
  "module": "BookingManagement",
  "tools": [
    {
      "name": "create_booking",
      "description": "Create a booking draft for the current tenant.",
      "endpoint": "/api/bookings",
      "method": "POST",
      "approval_required": false,
      "result_envelope": "booking.created"
    },
    {
      "name": "reschedule_booking",
      "description": "Request a booking reschedule.",
      "endpoint": "/api/bookings/{id}/reschedule",
      "method": "POST",
      "approval_required": true,
      "result_envelope": "booking.reschedule_requested"
    }
  ]
}
```

### Notes

- tools should map to approved module actions, not ad hoc controller code
- `result_envelope` points the AI and automation layer to the expected downstream signal shape

## 8. Example: Omni manifest

```json
{
  "module": "BookingManagement",
  "channels": ["whatsapp", "sms", "email"],
  "messages": [
    {
      "key": "booking.reminder",
      "engine": "reminder",
      "requires_template": true,
      "requires_opt_in": true
    },
    {
      "key": "booking.followup",
      "engine": "followup",
      "requires_template": true,
      "requires_opt_in": true
    }
  ]
}
```

## 9. Example: CMS manifest

```json
{
  "module": "BookingManagement",
  "surfaces": [
    "services_page",
    "booking_widget",
    "customer_portal_booking_status"
  ],
  "renderables": [
    {
      "key": "booking_widget",
      "data_source": "booking.availability.summary"
    }
  ]
}
```

## 10. Example: approval manifest

```json
{
  "module": "BookingManagement",
  "approval_paths": [
    {
      "action": "reschedule_booking",
      "mode": "operator_or_policy",
      "required_roles": ["admin", "dispatcher"]
    },
    {
      "action": "cancel_booking",
      "mode": "strict_operator",
      "required_roles": ["admin"]
    }
  ]
}
```

## 11. Doctor / installer checks

Installer, health, and module doctor tooling should verify:

- manifest file exists when the module claims the capability
- JSON is valid and schema-complete
- engine names are recognized
- signal keys are unique
- tenant boundary is not omitted from action-capable modules
- approval declarations match runtime policy
- AI tool endpoints actually exist
- CMS and Omni surfaces reference valid renderables/templates

## 12. Recommended conventions

- use stable lowercase keys for machine identifiers
- version manifests explicitly
- keep display labels out of manifests where possible
- put business terminology in IndustryKit/translation layers, not in machine keys
- make approval sensitivity explicit, never implied
- treat manifests as code-reviewed contracts, not side notes

## 13. Relationship to automation runtime

Manifests do not execute automation. They enable it.

The runtime uses manifests to:

- discover capabilities
- validate whether a trigger is legal
- select an engine family
- decide whether approval is mandatory
- know which channel or surface can consume a result

## 14. Minimum recommended set by module type

### Core operational modules

Should publish:

- signals manifest
- lifecycle manifest
- AI tools manifest
- approval manifest if actions can mutate real operations

### CMS-facing modules

Should publish:

- CMS manifest
- signals manifest if content changes can trigger runtime work

### Omni-facing modules

Should publish:

- Omni manifest
- signals manifest
- approval manifest if outbound comms are policy-sensitive

## 15. Summary

Example manifests are the bridge between module doctrine and automation reality. They let Titan Zero, AEGIS, and the runtime engine discover module capabilities without guessing, and they give doctor/install tooling a concrete contract to validate.
