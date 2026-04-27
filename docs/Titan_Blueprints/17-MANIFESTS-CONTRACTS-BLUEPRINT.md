# Manifests + Contracts Blueprint

Status: Canonical draft  
Layer: Discovery, declarations, cross-layer compatibility

## Role

Manifests declare what modules and surfaces can do without forcing deep runtime introspection. Contracts keep cross-layer integration stable.

## Module-Level Manifests

```text
Modules/<ModuleName>/manifests/
├─ ai_tools.json
├─ signals_manifest.json
├─ lifecycle_manifest.json
├─ cms_manifest.json
├─ omni_manifest.json
├─ api_manifest.json
├─ pwa_contract.json
└─ permissions_manifest.json
```

## Suggested Manifest Purposes

### `ai_tools.json`
- callable tools
- input schema
- permissions
- risk class
- approval mode

### `signals_manifest.json`
- produced signals
- consumed signals
- schema version
- handler bindings

### `lifecycle_manifest.json`
- supported lifecycle stages
- step labels
- entry / exit triggers

### `cms_manifest.json`
- renderable content surfaces
- embeddable widgets
- public pages and fragments

### `omni_manifest.json`
- supported channels
- auto-response capabilities
- campaign compatibility

### `api_manifest.json`
- public/internal endpoints
- auth expectations
- resource contracts

### `pwa_contract.json`
- offline cards
- action set
- minimal sync fields
- push triggers
- installable surface flags

## Global Platform Manifest

```text
platform/
├─ platform_manifest.json
├─ providers_manifest.json
├─ registries_manifest.json
└─ surfaces_manifest.json
```

## Rule

Use manifests for discovery and capability declaration. Use providers and code for execution.
