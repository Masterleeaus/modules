# Canonical Platform Starter Pack

## Purpose
This document defines the minimum **buildable** starter set for the Titan platform layer so new work starts from a consistent base rather than ad hoc folders.

## Starter contents

```text
app/Platform/
  Core/
    PlatformServiceProvider.php
    PlatformManager.php
    PlatformRegistry.php
  Tenancy/
    Contracts/TenantResolverInterface.php
    Services/CompanyTenantResolver.php
  Modules/
    Contracts/ModuleManifestReaderInterface.php
    Discovery/ModuleManifestReader.php
  Signals/
    Contracts/SignalEmitterInterface.php
    DTOs/SignalEnvelope.php
    Emitters/DatabaseSignalEmitter.php
  Ai/
    Contracts/AiToolRegistryInterface.php
    Registry/AiToolRegistry.php
  Pwa/
    Contracts/PwaSurfaceRegistryInterface.php
    Shell/PwaSurfaceRegistry.php
platform/
  platform_manifest.json
config/
  platform.php
  ai.php
  signals.php
```

## Required capabilities
- read master platform manifest
- resolve current tenant by `company_id`
- discover module manifests
- register AI tools from module manifests
- emit signals through a stable contract
- expose PWA surface registry
- boot through a dedicated provider

## Required service providers
- `PlatformServiceProvider`
- `ModuleRegistryServiceProvider`
- `AiServiceProvider`
- `SignalServiceProvider`
- `PwaServiceProvider`

## Build rules
- no domain logic inside platform starter
- all shared services must be contract-first
- all runtime registries must be swappable
- all tenant-aware services must resolve `company_id`
- all platform JSON manifests must be machine-readable and versioned
