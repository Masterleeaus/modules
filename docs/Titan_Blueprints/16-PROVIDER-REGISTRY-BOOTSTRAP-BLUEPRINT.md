# Provider + Registry + Bootstrap Blueprint

Status: Canonical draft  
Layer: Boot sequence and shared registration

## Role

A large Laravel system stays coherent when providers and registries have clear ownership. This layer governs discovery, binding, registration, and cross-system boot order.

## Tree

```text
app/Providers/
├─ AppServiceProvider.php
├─ AuthServiceProvider.php
├─ BroadcastServiceProvider.php
├─ EventServiceProvider.php
├─ RouteServiceProvider.php
├─ PlatformServiceProvider.php
├─ ModuleRegistryServiceProvider.php
├─ NavigationServiceProvider.php
├─ PermissionServiceProvider.php
├─ AutomationServiceProvider.php
├─ WorkflowServiceProvider.php
├─ SignalServiceProvider.php
├─ AiServiceProvider.php
├─ OmniServiceProvider.php
├─ PwaServiceProvider.php
├─ CmsServiceProvider.php
├─ CommunicationsServiceProvider.php
└─ Filament/
   ├─ AdminPanelProvider.php
   └─ UserPanelProvider.php
```

## Provider Responsibilities

### PlatformServiceProvider
- boot base platform manager
- load platform manifest
- register shared singletons

### ModuleRegistryServiceProvider
- discover enabled modules
- register module manifests
- expose compatibility state

### Signal / Workflow / Automation Providers
- register contracts
- bind handlers
- connect queue listeners
- initialize shared registries

### AiServiceProvider
- register core orchestrators
- model routers
- tool registries
- governance evaluators

### Filament Panel Providers
- register plugins by panel
- keep panel composition separate from domain/runtime

## Registry Pattern

Useful registries:
- module registry
- navigation registry
- permission registry
- signal registry
- AI tool registry
- workflow definition registry
- PWA surface registry
- Omni channel registry

## Boot Order

1. core platform
2. identity and tenancy
3. permissions and modules
4. signals and workflows
5. communications and sync
6. AI orchestration
7. Filament and other UI surfaces
