# Worksuite → AI-Controlled Interconnected PWA System

This doc set turns the Laravel engineering guidance from the uploaded PDFs into a concrete system design for evolving Worksuite and Filament into a network of AI-driven, privacy-first, interconnected PWAs, mobile clients, and device nodes.

## What is in this set

1. **01-system-doctrine.md**  
   Product doctrine, design laws, and non-negotiable architecture principles.
2. **02-reference-architecture.md**  
   The target technical architecture: core platform, AI spine, PWA shells, nodes, and communication model.
3. **03-worksuite-filament-pwa-model.md**  
   How Worksuite and Filament divide responsibilities and become one coordinated operating system.
4. **04-ai-control-governance.md**  
   How AI authority, approval, memory, execution, and safety gates should work.
5. **05-modules-api-and-pwa-contract.md**  
   The development contract every module must satisfy to work across web, mobile, AI, and package surfaces.
6. **06-node-device-runtime.md**  
   Device-node model for phones, tablets, desktops, kiosks, and worker devices.
7. **07-build-roadmap.md**  
   Phase-based plan to build the system from current Worksuite into a differentiated product.
8. **08-unique-product-theses.md**  
   What makes the software system different rather than just another Laravel SaaS app.

## Intended use

These docs are written as internal product and engineering guidance for:
- architecture planning
- module development
- agent/Copilot handoff
- PWA/mobile execution planning
- governance and AI design reviews
- future repo documentation

## Source synthesis

This set was shaped by four main source threads:
- Laravel architecture fundamentals: MVC, routing, Blade, migrations, service container, testing, and API support
- Clean Laravel practices: form requests, actions/services, DTOs, interfaces, strategy pattern, caching, eager loading, and queues
- Worksuite/Titan module doctrine: module manifests, package visibility, tenant boundary, API surfaces, AI manifests, and CMS/Omni compatibility
- Titan/Worksuite architectural context already established in project planning: privacy-first AI, tenant-safe modules, chat-first UI, multiple PWA shells, and signal/approval governance

## How to use this set

- Read **01** and **02** first for the big picture.
- Use **05** as the build checklist for any module or extension.
- Use **07** to sequence implementation work.
- Use **08** to keep the product differentiated and avoid drifting back into commodity SaaS patterns.

- 16-duo-mode-and-cross-system-constitution.md

- 17-edge-node-and-pwa-runtime-contract.md
- 18-signal-envelope-and-event-backfeed-contract.md
