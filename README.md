# Titan BOS — Business Operating System for Service Industries

> **Titan BOS. Zero BS.**

A Laravel-based AI-controlled business operating system built for the cleaning industry and adjacent service verticals. One platform. Nine purpose-built nodes. Nineteen vertical overlays. Zero vendor lock-in.

---

## What It Is

Titan BOS replaces 5–7 separate tools most service businesses use:

| Replaces | With |
|---|---|
| Scheduling software (Jobber, ServiceM8) | Ground Zero + Titan Go |
| Customer portal | Zero Fuss |
| Marketing platform (Mailchimp + social) | Titan Studio |
| Communication tools (Intercom, Twilio) | Titan Hello |
| Invoicing & accounting sync | ZeroPay |
| AI assistant | Titan Zero (BYO API key) |
| Document generation | TitanDocs (built-in) |

**Total replacement value for a typical cleaning business: $200–800/month. Titan BOS delivers it from $79/month.**

---

## Zero Philosophy

Every product decision is governed by the Zero Philosophy:

- **Zero missed calls** — Titan Hello answers every inbound, every channel, always
- **Zero unanswered messages** — Titan Zero triages, responds, escalates
- **Zero surprise bills** — transparent pricing, no platform transaction fees
- **Zero vendor lock-in** — BYO AI key, BYO payment gateway, data portability
- **Zero AI data resale** — company data never trains third-party models
- **Zero code forks** — vertical specialisation via config overlay, not separate codebases
- **Zero hidden complexity** — Titan Solo proves the platform can run a business in 3 taps

---

## 9 Nodes

Each node is a purpose-built surface over the same shared backend modules.

| Node | Role | Type |
|---|---|---|
| **Titan Pro** | Owner / Director command centre | Filament Admin Panel |
| **Ground Zero** | Real-time dispatch control | Filament Panel |
| **Titan Go** | Field operator / cleaner on-site | Mobile PWA (offline-first) |
| **Zero Fuss** | Customer self-service portal | PWA |
| **Titan Zero** | AI orchestration + embedded AI | Chat surface + API |
| **ZeroPay** | Invoicing, payments, cashflow | PWA |
| **Titan Studio** | Marketing, content, lead funnel | Filament Panel |
| **Titan Solo** | Single-operator simplified mode | PWA |
| **Titan Hello** | Omni-channel receptionist | Background system |

---

## 19 Vertical Overlays

Each vertical is a config layer — not a code fork. Same platform, industry-native experience.

**Tier 1 — Core Cleaning**
Residential · Commercial & Office · Bond (End-of-Lease) · Airbnb / Short-Stay · Construction Site

**Tier 2 — Specialist High-Margin**
Biohazard & Crime Scene · Medical Equipment · Solar Panel (Industrial) · Industrial Window

**Tier 3 — Property & Exterior**
Pool Maintenance · Garden & Grounds · Property Manager Partner · Pressure Cleaning

**Tier 4 — Mobile Specialty**
Car Detailing · Pet Washing & Grooming · Oven / Appliance Deep Cleaning

Each overlay injects: terminology translation · workflow lifecycle model · compliance gates · checklists · artefact generators · AI knowledge pack.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12, PHP 8.2 |
| Module system | nwidart/laravel-modules 10.x |
| Admin panels | Filament 4 |
| AI engine | laravel/ai + moneo/laravel-rag (BYO key: OpenAI / Anthropic / Gemini) |
| Real-time | Pusher + Laravel Echo |
| Communications | Twilio (voice/SMS), Vonage, Telegram, OneSignal |
| Payments | Stripe · Square · PayID · PayPal · Razorpay · Mollie · BPAY |
| E-invoicing | PEPPOL / UBL (EInvoice module) |
| Document generation | DomPDF + custom TitanDocs engine |
| Storage | AWS S3 / local via Flysystem |
| Queue | Laravel queues (database / Redis) |
| Auth | Laravel Fortify + Sanctum |
| Search | Meilisearch / Scout-compatible |

---

## Architecture

```
Titan BOS
├── 9 Node surfaces (Filament panels + PWAs)
│   └── Each node reads/writes shared backend modules
├── 37 Backend modules (Modules/)
│   └── Each module: migrations, services, events, jobs, manifests, Filament plugin
├── Vertical Overlay System
│   └── Config injection at boot — no code forks
├── Titan Zero (AI)
│   └── Single AI entry point — all modules route through TitanZero::query()
│   └── BYO API key — no AI lock-in
│   └── Vertical knowledge packs — becomes a specialist per industry
└── Aegis (AI Safety)
    └── Output filtering, compliance gate enforcement, mandatory reporting
```

**Core rule:** No module calls an AI provider directly. No node forks backend code. No vertical forks a node.

---

## Module Map

The 37 backend modules power all 9 nodes:

**Operations core:** BookingModule · ManagedPremises · CleanQuality (Inspection+QualityControl) · TitanGo · TitanPWA

**Financial:** Accountings · EInvoice · Payroll · Purchase · SupplyChain (Suppliers+Inventory)

**People:** Recruit · Performance · Biometric · Letter

**Communications:** TitanHello · TitanReach (Sms+multi-channel) · TitanTalk · Webhooks

**AI & Platform:** TitanZero · TitanCore · Aitools

**Documents:** TitanDocs · TitanVault

**Client-facing:** Complaint (ClientFeedback) · Clients · Asset (CleanEquipment)

**Integrations:** TitanIntegrations · QRCode · EInvoice

---

## Pricing

| Plan | Nodes | Target | Price |
|---|---|---|---|
| Solo | Titan Solo + Titan Go + ZeroPay | 1-person operator | ~$79/mo |
| Grow | + Ground Zero + Zero Fuss + Titan Hello | 2–10 crew | ~$199/mo |
| Pro | All 9 nodes | 10+ crew | ~$399/mo |
| Enterprise | All nodes + multi-location + white-label | Franchise / group | Custom |

**+$15–25/active crew member above plan threshold.**
All 19 vertical overlays included at every tier. No transaction fees. No AI markup.

---

## Documentation

All architecture, module specs, and build blueprints live in `docs/`.

```
docs/README.md                          ← Start here
docs/philosophy/00-zero-philosophy.md  ← Product doctrine
docs/dashboards/                        ← 9-node specs + vertical overlay system
docs/Titan_Blueprints/                  ← 34 canonical build blueprints
docs/04-AI/titan-zero.md               ← AI orchestration spec
docs/vertical-ai-training-architecture.md ← Vertical AI specialisation
```

**Agent rule:** Read `docs/README.md` before any code, architecture decision, or module work.

---

## Development

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Build assets
npm run dev

# Queue worker
php artisan queue:work
```

**Active development branch:** `claude/setup-agent-docs-ewAYA`

Never push directly to `main`.

---

## Repository Structure

```
cleanly/
├── app/                    Core Laravel application
├── Modules/                37 domain modules (nwidart)
├── docs/                   Full architecture documentation
│   ├── philosophy/         Zero Philosophy doctrine
│   ├── dashboards/         9-node + vertical overlay specs
│   └── Titan_Blueprints/   34 canonical build blueprints (01–34)
├── resources/              Blade views, assets, AI knowledge packs
├── config/                 Platform + vertical config
├── database/               322 migrations + seeders
├── routes/                 web, api, web-public, web-settings, SuperAdmin
└── tests/                  Feature + unit tests
```

---

## Contributing

1. Read `docs/README.md` first — always
2. Check `docs/Titan_Blueprints/34-PLATFORM-AND-MODULE-CHECKLIST-MASTER.md` before marking work done
3. All queries must be scoped to `company_id` — never cross-tenant
4. All AI calls route through `TitanZero::query()` — never call providers directly
5. Vertical specialisation is config, not code — never fork a module or node for a vertical
