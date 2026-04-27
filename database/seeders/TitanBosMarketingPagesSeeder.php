<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class TitanBosMarketingPagesSeeder extends Seeder
{
    public function run(): void
    {
        $apps = $this->apps();

        $this->page('home', 'Titan BOS. Zero BS.', 'The AI operating system for cleaning businesses that removes friction instead of adding software.', [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => 'Titan BOS — The Zero Philosophy',
                'headline' => 'Titan BOS. Zero BS.',
                'subheadline' => 'The AI operating system for cleaning businesses. Titan runs the business. Zero removes the stress — across scheduling, compliance, training, evidence, payments, inspections, customer communication, and niche-specific operations.',
                'primary_button_label' => 'Start your Titan system',
                'primary_button_url' => '/register',
                'secondary_button_label' => 'Explore the apps',
                'secondary_button_url' => '/apps',
                'note' => 'Built for residential, commercial, bond, Airbnb, construction, pressure washing, window cleaning, solar, detailing, and specialist cleaning services.',
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Zero is the operating doctrine.',
                'body' => 'Zero is not branding decoration. It means removing unnecessary system burden from cleaning businesses instead of adding more tools to manage.',
                'features' => [
                    ['title' => 'Zero hidden pricing', 'description' => 'Subscription-first structure with no surprise AI token traps, forced add-ons, or confusing usage charges.'],
                    ['title' => 'Zero AI lock-in', 'description' => 'Use BYO API keys, optional external providers, optional local models like Ollama, or no mandatory AI at all.'],
                    ['title' => 'Zero data resale', 'description' => 'Your prompts, job records, client messages, API keys, workflows, and communication channels remain yours.'],
                    ['title' => 'Zero workflow duplication', 'description' => 'Each app shares the same operational record, so teams are not retyping jobs across disconnected tools.'],
                    ['title' => 'Zero learning curve friction', 'description' => 'Vertical terminology, checklists, training flows, and artefacts load around the cleaning niche you actually operate.'],
                    ['title' => 'Zero platform dependency', 'description' => 'Titan orchestrates execution. It does not harvest your business or trap your processes inside black-box systems.'],
                ],
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Super niche AI for cleaning operators.',
                'body' => 'Titan does not treat all service work the same. Each cleaning vertical loads its own knowledge pack, lifecycle model, compliance logic, training flow, artefact generator, and job language.',
                'features' => [
                    ['title' => 'Vertical AI knowledge packs', 'description' => 'Residential, commercial, bond, Airbnb, construction, biohazard, medical equipment, solar, windows, pools, gardens, pressure cleaning, detailing, pet washing, and appliance cleaning.'],
                    ['title' => 'Built-in staff training', 'description' => 'Every niche can ship procedure guides, inspection standards, safety requirements, equipment workflows, and AI-guided learning.'],
                    ['title' => 'Artefact generation', 'description' => 'Create bond packs, handover reports, compliance certificates, condition reports, inspection summaries, and evidence bundles.'],
                    ['title' => 'Compliance automation', 'description' => 'SWMS, JSA, PPE checks, certification tracking, audit logs, chain-of-custody workflows, and site-specific evidence.'],
                    ['title' => 'Evidence-first execution', 'description' => 'Before/after photos, QR presence verification, checklist scoring, damage reporting, and client-ready documentation.'],
                    ['title' => 'Lifecycle models', 'description' => 'Recurring domestic cycles, contract maintenance, tenancy turnover, checkout resets, staged builder cleans, weather-triggered exterior work, and mobile specialty services.'],
                ],
            ]],
            ['type' => 'AppGridBlock', 'data' => [
                'heading' => 'Up to nine apps. One cleaning business operating system.',
                'body' => 'Titan BOS is a suite of specialised apps that share one operational brain. Use one app or activate the full stack as your business grows.',
                'apps' => $this->appCards($apps),
            ]],
            ['type' => 'CtaBlock', 'data' => [
                'heading' => 'Hello receives. Zero thinks. Titan executes.',
                'body' => 'Start with one vertical, then activate more overlays as your cleaning company expands into new services.',
                'button_label' => 'Launch Titan Zero',
                'button_url' => '/register',
            ]],
        ], 'Titan BOS — Zero BS for Cleaning Businesses', 'A niche AI operating system for cleaning businesses with up to nine apps, vertical training, compliance packs, artefact generation, and Zero Philosophy ownership.');

        $this->page('features', 'Features built for cleaning businesses', 'Niche AI, vertical training, compliance, evidence, scheduling, payments, communications, and multi-vertical operations for service businesses.', $this->featuresBlocks(), 'Titan BOS Features — Cleaning Business AI OS', 'Explore Titan BOS features for cleaning businesses: AI knowledge packs, ZeroPay payment operations, scheduling, evidence, compliance, training, client communication, and reporting.');
        $this->page('faq', 'Frequently asked questions', 'Answers about Titan BOS, Zero Philosophy, cleaning verticals, ZeroPay, AI usage, local models, pricing, data ownership, and app structure.', $this->faqBlocks(), 'Titan BOS FAQ — Zero BS', 'Frequently asked questions about Titan BOS, ZeroPay, cleaning business AI, Zero AI lock-in, pricing, data ownership, and vertical overlays.');
        $this->page('apps', 'Titan apps', 'Nine specialised apps for running cleaning businesses from intake to dispatch, execution, evidence, compliance, payments, training, and property manager coordination.', [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => 'Titan App Suite',
                'headline' => 'Nine specialised apps. One shared operating system.',
                'subheadline' => 'Each app removes a category of business stress while sharing the same data, AI context, vertical rules, and customer history.',
                'primary_button_label' => 'Start free trial',
                'primary_button_url' => '/register',
                'secondary_button_label' => 'View features',
                'secondary_button_url' => '/features',
            ]],
            ['type' => 'AppGridBlock', 'data' => [
                'heading' => 'Choose the apps your cleaning business needs.',
                'body' => 'Start with the essentials, then activate more apps as you grow into multi-vertical operations.',
                'apps' => $this->appCards($apps),
            ]],
        ], 'Titan BOS Apps — Cleaning Business Operating System', 'Explore Titan Go, Ground Zero, Titan Pro, Titan Solo, Titan Hello, Titan Zero, ZeroPay, Zero Fuss, and Titan Studio.');

        foreach ($apps as $app) {
            $this->page('app-'.$app['slug'], $app['name'], $app['summary'], $this->appPageBlocks($app), 'Titan BOS — '.$app['name'], $app['summary'].' Built for cleaning businesses using Titan BOS.');
        }
    }

    private function page(string $slug, string $title, string $summary, array $blocks, ?string $metaTitle = null, ?string $metaDescription = null): void
    {
        CmsPage::updateOrCreate(['slug' => $slug], [
            'title' => $title,
            'summary' => $summary,
            'meta_title' => $metaTitle ?: $title,
            'meta_description' => $metaDescription ?: $summary,
            'status' => 'published',
            'published_at' => now(),
            'website_content' => $blocks,
        ]);
    }

    private function apps(): array
    {
        return [
            ['name' => 'Titan Go', 'slug' => 'titan-go', 'category' => 'Field Execution', 'summary' => 'Mobile job execution for cleaning technicians in the field.', 'description' => 'Run checklists, photos, sign-offs, client notes, evidence capture, and vertical-specific job workflows from the technician app.', 'stress' => 'Zero job confusion', 'capabilities' => ['Checklists', 'Photos', 'Sign-offs', 'Evidence', 'Mobile workflows'], 'best' => ['Residential cleaning', 'Bond cleaning', 'Airbnb turnover', 'Pressure cleaning', 'Car detailing'], 'ai' => ['Explains job steps for the selected niche', 'Guides before/after evidence capture', 'Summarises site notes into handover-ready language']],
            ['name' => 'Ground Zero', 'slug' => 'ground-zero', 'category' => 'Operations Control', 'summary' => 'The command centre for scheduling, routing, dispatch, and job lifecycle visibility.', 'description' => 'Coordinate teams, route clusters, job states, compliance checkpoints, and multi-vertical operations from one control centre.', 'stress' => 'Zero dispatch chaos', 'capabilities' => ['Scheduling', 'Routing', 'Dispatch', 'Lifecycle tracking', 'Team visibility'], 'best' => ['Commercial cleaning', 'Airbnb turnover', 'Construction cleans', 'Solar panel cleaning', 'Garden maintenance'], 'ai' => ['Clusters work by location and urgency', 'Suggests scheduling improvements', 'Flags missing compliance or evidence before close-out']],
            ['name' => 'Titan Pro', 'slug' => 'titan-pro', 'category' => 'Contract Operations', 'summary' => 'Contract, enterprise, inspection, and multi-site management for larger cleaning operators.', 'description' => 'Manage inspections, supplies, attendance, site standards, logbooks, contracts, and performance across commercial accounts.', 'stress' => 'Zero admin overload', 'capabilities' => ['Inspection scoring', 'Digital logbooks', 'Supply monitoring', 'Multi-site tracking', 'Attendance verification'], 'best' => ['Commercial cleaning', 'Schools', 'Clinics', 'Industrial solar', 'High-rise windows'], 'ai' => ['Turns site issues into action items', 'Generates account review summaries', 'Compares inspection outcomes by site and team']],
            ['name' => 'Titan Solo', 'slug' => 'titan-solo', 'category' => 'Owner Operator', 'summary' => 'A lean operating app for solo cleaners and small mobile teams.', 'description' => 'Optimise repeat visits, client preferences, supply tracking, routes, add-ons, reminders, and simple quoting for owner-operators.', 'stress' => 'Zero small-business overwhelm', 'capabilities' => ['Repeat visits', 'Client preferences', 'Route optimisation', 'Supply tracking', 'Add-on prompts'], 'best' => ['Residential cleaning', 'Pool maintenance', 'Gardens', 'Car detailing', 'Appliance cleaning'], 'ai' => ['Suggests next best add-ons', 'Remembers client preferences', 'Creates simple follow-up messages and service summaries']],
            ['name' => 'Titan Hello', 'slug' => 'titan-hello', 'category' => 'Client Communication', 'summary' => 'Automated customer communication for cleaning service businesses.', 'description' => 'Handle confirmations, arrival messages, reminders, service updates, handover notes, and client follow-up from one communication layer.', 'stress' => 'Zero missed calls', 'capabilities' => ['Confirmations', 'Arrival messages', 'Reminders', 'Follow-ups', 'Client updates'], 'best' => ['Airbnb turnover', 'Property managers', 'Residential cleaning', 'Pet grooming'], 'ai' => ['Drafts niche-aware client messages', 'Summarises completed work', 'Turns job notes into professional updates']],
            ['name' => 'Titan Zero', 'slug' => 'titan-zero', 'category' => 'AI + Compliance', 'summary' => 'The primary assistant persona for removing noise, generating artefacts, and training staff.', 'description' => 'Titan Zero guides decisions, explains workflows, trains staff, generates documents, and operates vertical intelligence layers.', 'stress' => 'Zero training confusion', 'capabilities' => ['AI guidance', 'Compliance packs', 'Artefacts', 'Training flows', 'Workflow explanation'], 'best' => ['Bond cleaning', 'Biohazard cleaning', 'Medical equipment cleaning', 'Construction cleans'], 'ai' => ['Runs vertical knowledge packs', 'Generates reports and certificates', 'Explains niche checklists and compliance requirements']],
            ['name' => 'ZeroPay', 'slug' => 'zeropay', 'category' => 'Payment Operations', 'summary' => 'Privacy-first, device-first, zero-fee-first payment operations for service businesses.', 'description' => 'ZeroPay turns invoices, deposits, booking deposits, and outstanding balances into smart payment sessions with QR codes, payment links, multi-rail checkout, AI follow-up, reconciliation assistance, and optional voice-based recovery.', 'stress' => 'Zero unpaid invoices', 'capabilities' => ['Payment sessions', 'QR codes', 'Payment links', 'PayID and bank transfer', 'Cash confirmation', 'AI follow-up', 'Voice recovery', 'Reconciliation assist'], 'best' => ['Bond cleaning', 'Residential recurring', 'Car detailing', 'Property manager work', 'Commercial cleaning'], 'ai' => ['Drafts collections messages with polite service-business etiquette', 'Recommends the best follow-up channel and timing', 'Suggests zero-fee methods first', 'Detects abandoned sessions and payment friction', 'Suggests bank-transfer matches and escalation paths']],
            ['name' => 'Zero Fuss', 'slug' => 'zero-fuss', 'category' => 'Property Manager Layer', 'summary' => 'A property-manager-ready layer for agents, keys, turnover, and verification bundles.', 'description' => 'Manage agent portals, key custody, bond pack verification, maintenance coordination, and property manager communication.', 'stress' => 'Zero customer friction', 'capabilities' => ['Agent portals', 'Key custody', 'Bond packs', 'Maintenance coordination', 'Verification bundles'], 'best' => ['Bond cleaning', 'Property manager partners', 'Airbnb turnover'], 'ai' => ['Builds agent-ready handover packs', 'Summarises key and access history', 'Detects missing inspection evidence']],
            ['name' => 'Titan Studio', 'slug' => 'titan-studio', 'category' => 'Training + Knowledge', 'summary' => 'Training, onboarding, and standards management for cleaning teams.', 'description' => 'Create onboarding flows, service standards, procedure walkthroughs, vertical training modules, and AI-guided learning paths.', 'stress' => 'Zero inconsistent training', 'capabilities' => ['Onboarding', 'Training modules', 'Procedure guides', 'Service standards', 'AI learning flows'], 'best' => ['Car detailing', 'Commercial cleaning', 'Biohazard cleaning', 'Multi-vertical teams'], 'ai' => ['Creates role-specific training flows', 'Converts SOPs into checklists', 'Answers staff questions with tenant-scoped knowledge']],
        ];
    }

    private function appCards(array $apps): array
    {
        return array_map(fn (array $app): array => [
            'name' => $app['name'],
            'category' => $app['category'],
            'description' => $app['summary'],
            'url' => '/apps/'.$app['slug'],
            'capabilities' => array_slice($app['capabilities'], 0, 4),
        ], $apps);
    }

    private function appPageBlocks(array $app): array
    {
        if ($app['slug'] === 'zeropay') {
            return $this->zeroPayBlocks($app);
        }

        return [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => $app['category'].' — '.$app['stress'],
                'headline' => $app['name'],
                'subheadline' => $app['description'],
                'primary_button_label' => 'Start with '.$app['name'],
                'primary_button_url' => '/register',
                'secondary_button_label' => 'Back to all apps',
                'secondary_button_url' => '/apps',
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'What '.$app['name'].' does',
                'body' => 'Purpose-built for cleaning businesses, not generic field service teams.',
                'features' => array_map(fn ($item): array => ['title' => $item, 'description' => $this->capabilityDescription($item, $app['name'])], $app['capabilities']),
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'AI enhancements',
                'body' => 'Every app can use Titan Zero intelligence while keeping your business data tenant-scoped and provider-flexible.',
                'features' => array_map(fn ($item): array => ['title' => $item, 'description' => 'Designed to reduce manual decisions and make niche cleaning workflows easier to execute consistently.'], $app['ai']),
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Best-fit cleaning verticals',
                'body' => $app['name'].' adapts to the terminology, checklists, evidence, and training requirements of each active vertical.',
                'features' => array_map(fn ($item): array => ['title' => $item, 'description' => 'Loads niche workflows, checklists, training guidance, and artefact expectations for this service line.'], $app['best']),
            ]],
            ['type' => 'CtaBlock', 'data' => [
                'heading' => $app['stress'].'.',
                'body' => 'Activate '.$app['name'].' as part of the Titan BOS cleaning operating system.',
                'button_label' => 'Create account',
                'button_url' => '/register',
            ]],
        ];
    }

    private function zeroPayBlocks(array $app): array
    {
        return [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => 'ZeroPay — Payment Operations',
                'headline' => 'Zero unpaid invoices. Zero surprise payment fees.',
                'subheadline' => 'ZeroPay is a privacy-first, device-first, zero-fee-first payment operations module that helps cleaning and service businesses get invoices, deposits, and balances paid faster without forcing customers through one expensive gateway.',
                'primary_button_label' => 'Start with ZeroPay',
                'primary_button_url' => '/register',
                'secondary_button_label' => 'Explore all apps',
                'secondary_button_url' => '/apps',
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'What ZeroPay is',
                'body' => 'ZeroPay is not a full accounting system and not just a payment-link plugin. It sits above your existing invoice and accounting layer as the payment conversion, orchestration, and recovery module.',
                'features' => [
                    ['title' => 'Payment session engine', 'description' => 'Turn any invoice, deposit, booking deposit, or outstanding balance into a canonical ZeroPay Session with amount, customer, reference, status, expiry, documents, rails, attempts, and follow-up metadata.'],
                    ['title' => 'QR and payment-link system', 'description' => 'Generate invoice QR, deposit QR, merchant receive-money QR, static business QR, dynamic amount QR, fallback session QR, and secure payment links.'],
                    ['title' => 'Multi-rail checkout layer', 'description' => 'Let the customer choose PayID, bank transfer, cash, card, PayPal, Stripe-backed card processing, or optional crypto such as Cryptomus where enabled.'],
                    ['title' => 'Follow-up and recovery module', 'description' => 'Send reminders, resend links, nudge zero-fee methods, switch channels, escalate overdue balances, and recover abandoned payment sessions.'],
                    ['title' => 'Reconciliation assist', 'description' => 'Suggest bank-transfer matches, detect duplicates, review partial payments, flag mismatches, and support cash balancing before posting to accounting.'],
                    ['title' => 'Worksuite/Titan overlay', 'description' => 'Worksuite or Titan remains the source of truth for invoices, payments, customers, companies, bookings, and financial reporting. ZeroPay helps those items actually get paid.'],
                ],
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Zero-fee-first payment rails',
                'body' => 'ZeroPay promotes payment methods that protect margin first, while still allowing convenience rails when customers prefer them.',
                'features' => [
                    ['title' => 'PayID-first flows', 'description' => 'Present PayID details, dynamic references, QR-assisted bank payment flows, receipt upload, and confirmation support.'],
                    ['title' => 'Bank transfer support', 'description' => 'Show bank details, create clean references, track pending confirmation, and queue unmatched transfers for review.'],
                    ['title' => 'Cash as a first-class method', 'description' => 'Let field staff mark cash received, capture collection records per session, and send cash items for supervisor review or later reconciliation.'],
                    ['title' => 'Convenience rails when needed', 'description' => 'Offer card, PayPal, Stripe-backed card processing, or optional crypto while keeping base amount, fee amount, and paid total auditable.'],
                    ['title' => 'Session delivery everywhere', 'description' => 'Send payment sessions by QR code, email, SMS, WhatsApp, Messenger, Telegram, portal link, app screen, printed invoice, or voice-assisted follow-up.'],
                    ['title' => 'Token-scoped documents', 'description' => 'Provide temporary secure invoice and receipt downloads with 7, 14, or 30 day access windows.'],
                ],
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'AI-assisted collections without AI taking over money movement',
                'body' => 'ZeroPay AI assists, recommends, drafts, queues, and flags. It does not move money directly.',
                'features' => [
                    ['title' => 'Smart follow-up drafting', 'description' => 'Draft polite invoice reminders, overdue balance messages, deposit nudges, payment-link resends, and channel-specific follow-ups.'],
                    ['title' => 'Channel recommendations', 'description' => 'Recommend whether to follow up by email, SMS, WhatsApp, Messenger, Telegram, portal, mobile app, or phone.'],
                    ['title' => 'Zero-fee method nudges', 'description' => 'Suggest PayID, bank transfer, or cash first when suitable, helping the business reduce payment-processing margin loss.'],
                    ['title' => 'Abandoned session detection', 'description' => 'Identify customers who opened a session, chose a method, failed, paused, or need a softer recovery message.'],
                    ['title' => 'Bank-transfer match suggestions', 'description' => 'Suggest likely invoice matches from references, amounts, customer history, partial payments, duplicates, and confidence scores.'],
                    ['title' => 'Voice recovery tier', 'description' => 'At higher tiers, orchestrate AI reminder calls, configurable call windows, do-not-call logic, escalation rules, optional human handoff, and operator voice controls through providers like Twilio, ElevenLabs, or Google speech.'],
                ],
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Built for service-business payment operations',
                'body' => 'Cleaning businesses often need deposits, balances, onsite payments, agency workflows, and fast recovery — not a generic card-only checkout.',
                'features' => [
                    ['title' => 'Bond cleaning deposits', 'description' => 'Send booking deposits, balance requests, final invoice links, bond pack receipts, and agent-ready payment status updates.'],
                    ['title' => 'Recurring domestic balances', 'description' => 'Handle repeat-service invoices, subscription-like balances, reminders, and zero-fee transfer preferences.'],
                    ['title' => 'Commercial invoice follow-up', 'description' => 'Track attempts, statement reminders, overdue escalation, and document access for office, retail, school, and clinic cleaning contracts.'],
                    ['title' => 'Mobile detailing payments', 'description' => 'Use QR codes, onsite cash/card options, add-on upsells, deposits, and proof-of-payment flows.'],
                    ['title' => 'Property manager workflows', 'description' => 'Send payment sessions through portal links, agent notifications, key/handover events, and final receipt bundles.'],
                    ['title' => 'Privacy-first BYO keys', 'description' => 'Prefer user-owned gateway credentials, communication provider accounts, AI keys, and minimal centralization. Titan orchestrates instead of hoarding.'],
                ],
            ]],
            ['type' => 'CtaBlock', 'data' => [
                'heading' => 'ZeroPay helps payable work become paid work.',
                'body' => 'Create one smart payment session, send it through one or many channels, let the customer choose the easiest rail, then notify the business when payment is complete.',
                'button_label' => 'Activate ZeroPay',
                'button_url' => '/register',
            ]],
        ];
    }

    private function featuresBlocks(): array
    {
        return [
            ['type' => 'HeroBlock', 'data' => ['eyebrow' => 'Features', 'headline' => 'Cleaning business features without generic SaaS clutter.', 'subheadline' => 'Titan BOS combines niche AI, vertical training, scheduling, compliance, evidence, artefacts, payment operations, communications, and multi-vertical workflows into one operating system.', 'primary_button_label' => 'Explore apps', 'primary_button_url' => '/apps', 'secondary_button_label' => 'View verticals', 'secondary_button_url' => '/verticals']],
            ['type' => 'FeatureGridBlock', 'data' => ['heading' => 'AI and vertical intelligence', 'body' => 'Niche AI layers are loaded by service type, not generic task templates.', 'features' => [
                ['title' => 'Vertical AI knowledge packs', 'description' => 'Manuals, standards, inspection rules, terminology, client communication patterns, and training flows per niche.'],
                ['title' => 'BYO AI providers', 'description' => 'Use your own API keys, optional external inference, or local models like Ollama.'],
                ['title' => 'AI-guided staff training', 'description' => 'Convert procedures and standards into role-based onboarding, job-time coaching, and niche-specific learning.'],
                ['title' => 'Artefact generators', 'description' => 'Bond packs, handover reports, compliance certificates, efficiency summaries, condition reports, and evidence bundles.'],
                ['title' => 'Tenant-scoped execution', 'description' => 'AI operates around your business records without turning Titan into surveillance software.'],
                ['title' => 'Decision support', 'description' => 'Reduce noise across dispatch, compliance, payments, follow-up, documentation, and staff training.'],
            ]]],
            ['type' => 'FeatureGridBlock', 'data' => ['heading' => 'Cleaning operations', 'body' => 'Run daily work with workflows designed for residential, commercial, bond, Airbnb, construction, exterior, and specialist cleaning.', 'features' => [
                ['title' => 'Recurring scheduling', 'description' => 'Weekly, fortnightly, service interval, and repeat-visit automation.'],
                ['title' => 'Route clustering', 'description' => 'Group jobs by area, urgency, operator, service type, and vertical rules.'],
                ['title' => 'Evidence engine', 'description' => 'Before/after photos, QR presence verification, inspection scores, damage logs, and close-out proof.'],
                ['title' => 'Compliance engine', 'description' => 'SWMS, JSA, PPE verification, permit logging, certification tracking, chain-of-custody, and audit records.'],
                ['title' => 'Property manager workflows', 'description' => 'Agent links, key custody, bond verification, notification automation, maintenance coordination, and audit trails.'],
                ['title' => 'Weather/event triggers', 'description' => 'Exterior services can react to weather windows, booking calendars, checkouts, and service triggers.'],
            ]]],
            ['type' => 'FeatureGridBlock', 'data' => ['heading' => 'ZeroPay payment operations', 'body' => 'Payments are treated as operations, not just gateway transactions.', 'features' => [
                ['title' => 'Smart payment sessions', 'description' => 'Convert invoices, deposits, booking deposits, and outstanding balances into reusable sessions with QR, links, status, expiry, rails, documents, and follow-up data.'],
                ['title' => 'Zero-fee-first rails', 'description' => 'Promote PayID, bank transfer, and cash before convenience rails so service businesses can reduce processing-fee margin loss.'],
                ['title' => 'Multi-channel delivery', 'description' => 'Send payment sessions through email, SMS, WhatsApp, Messenger, Telegram, portal links, printed invoices, app screens, and QR codes.'],
                ['title' => 'AI payment recovery', 'description' => 'Draft reminders, suggest channels, detect abandoned sessions, recommend escalation, and keep collections professional.'],
                ['title' => 'Reconciliation assist', 'description' => 'Suggest likely bank-transfer matches, flag partial payments, identify duplicates, and queue mismatches for review.'],
                ['title' => 'Voice recovery tier', 'description' => 'At higher tiers, use AI reminder calls, call windows, do-not-call rules, human handoff, and voice-control surfaces.'],
            ]]],
            ['type' => 'FeatureGridBlock', 'data' => ['heading' => 'Zero Doctrine infrastructure', 'body' => 'The platform is designed around ownership, privacy, and reduced dependency.', 'features' => [
                ['title' => 'Zero hidden pricing', 'description' => 'Subscription-first structure with no surprise token traps or forced integration fees.'],
                ['title' => 'Zero AI lock-in', 'description' => 'Use BYO keys, optional providers, optional local models, or no mandatory AI.'],
                ['title' => 'Zero data resale', 'description' => 'Your prompts, records, integrations, communication channels, and workflows remain yours.'],
                ['title' => 'Zero vendor dependency', 'description' => 'Titan orchestrates workflows instead of making your business dependent on one black-box provider.'],
                ['title' => 'Zero duplicated work', 'description' => 'Apps share one operational layer so jobs, evidence, customers, training, and payments stay connected.'],
                ['title' => 'Zero learning curve friction', 'description' => 'Interfaces and training adapt to your active cleaning verticals and staff roles.'],
            ]]],
        ];
    }

    private function faqBlocks(): array
    {
        return [
            ['type' => 'HeroBlock', 'data' => ['eyebrow' => 'FAQ', 'headline' => 'Questions about Titan BOS.', 'subheadline' => 'Straight answers about Zero Philosophy, cleaning verticals, ZeroPay, AI usage, pricing, data ownership, training, and the nine-app structure.', 'primary_button_label' => 'Start free trial', 'primary_button_url' => '/register', 'secondary_button_label' => 'View features', 'secondary_button_url' => '/features']],
            ['type' => 'FaqBlock', 'data' => ['heading' => 'Frequently asked questions', 'items' => [
                ['question' => 'Is Titan BOS only for cleaning businesses?', 'answer' => 'This version is built specifically for cleaning and adjacent service verticals including residential, commercial, bond, Airbnb, construction, exterior cleaning, detailing, pet washing, appliance cleaning, sanitation, and property services.'],
                ['question' => 'What does Zero BS mean?', 'answer' => 'Zero BS means zero hidden pricing, zero AI lock-in, zero data resale, zero forced integrations, zero workflow duplication, zero platform dependency, and zero unnecessary operational friction.'],
                ['question' => 'What is ZeroPay?', 'answer' => 'ZeroPay is a privacy-first, device-first, zero-fee-first payment operations module. It converts invoices, deposits, booking deposits, and outstanding balances into smart payment sessions with QR codes, payment links, multi-rail checkout, follow-up automation, AI collections support, and reconciliation assistance.'],
                ['question' => 'Is ZeroPay a full accounting system?', 'answer' => 'No. ZeroPay sits on top of your existing accounting and invoice system. Worksuite or Titan remains the source of truth for invoices, payments, customers, companies, bookings, and financial reporting. ZeroPay improves payment completion and recovery.'],
                ['question' => 'Which payment methods can ZeroPay support?', 'answer' => 'ZeroPay can present zero-fee-first rails like PayID, bank transfer, and cash, while optionally exposing convenience rails like card, PayPal, Stripe-backed card processing, and optional crypto such as Cryptomus where enabled.'],
                ['question' => 'How does ZeroPay reduce payment fees?', 'answer' => 'ZeroPay promotes PayID, bank transfer, and cash as preferred methods before paid convenience rails. It can still offer card or PayPal when needed, while keeping base amount, fee amount, and paid total separate for clean reconciliation.'],
                ['question' => 'Does ZeroPay move money directly with AI?', 'answer' => 'No. ZeroPay AI assists, recommends, drafts, queues, and flags. It can draft reminders, suggest channels, identify abandoned sessions, recommend zero-fee rails, and suggest bank-transfer matches, but it does not directly move money.'],
                ['question' => 'Do I have to use AI in Titan BOS?', 'answer' => 'No. AI is optional. You can bring your own API keys, use supported local models like Ollama, use external providers, or run workflows without mandatory AI usage.'],
                ['question' => 'Does Titan resell my prompts or data?', 'answer' => 'No. Titan is designed around ownership. Your prompts, records, integrations, keys, communication channels, payment settings, and workflow logic belong to you.'],
                ['question' => 'What are vertical overlays?', 'answer' => 'Vertical overlays specialise the system for a cleaning niche by loading terminology, checklists, lifecycle models, compliance rules, artefact generators, app behaviour, dashboards, and training packs.'],
                ['question' => 'Can I run more than one cleaning vertical?', 'answer' => 'Yes. Multi-vertical mode lets a company operate commercial cleaning, pressure washing, window cleaning, solar panel cleaning, bond cleaning, detailing, and other services together. Each job can load the correct workflow.'],
                ['question' => 'Are the nine apps separate subscriptions?', 'answer' => 'They are presented as apps because each removes a different operational stress, but they share one Titan BOS data layer and operating system. Packaging can be configured by tier or deployment model.'],
                ['question' => 'Does Titan include training?', 'answer' => 'Yes. Titan Studio and Titan Zero can provide onboarding, procedure walkthroughs, service standards, safety guidance, vertical training modules, and AI-guided learning for each niche.'],
                ['question' => 'Can Titan work with local AI models?', 'answer' => 'Yes. The Zero AI strategy supports BYO API keys, optional external inference providers, and local model options such as Ollama where configured.'],
                ['question' => 'Is Titan a surveillance platform?', 'answer' => 'No. Titan is intended to orchestrate workflows, not harvest them. The Zero Doctrine is privacy-first, device-first, user-key-first, and ownership-first.'],
            ]]],
        ];
    }

    private function capabilityDescription(string $capability, string $appName): string
    {
        return $appName.' includes '.$capability.' workflows tuned for cleaning businesses, with niche terminology, evidence expectations, and operational context.';
    }
}
