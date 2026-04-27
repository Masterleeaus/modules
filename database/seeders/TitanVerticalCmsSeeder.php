<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TitanVerticalCmsSeeder extends Seeder
{
    public function run(): void
    {
        $registry = config('titan_verticals', []);
        $verticals = Arr::get($registry, 'verticals', []);
        $tiers = Arr::get($registry, 'tiers', []);

        CmsPage::updateOrCreate(['slug' => 'vertical-registry'], [
            'title' => 'Titan BOS Vertical Registry',
            'summary' => 'One operating system, multiple specialised service platforms.',
            'meta_title' => 'Titan BOS — Cleaning Vertical Registry',
            'meta_description' => 'Explore Titan BOS overlays for cleaning, exterior property services, mobile specialty services, and high-compliance workflows.',
            'status' => 'published',
            'published_at' => now(),
            'website_content' => $this->registryBlocks($verticals, $tiers),
        ]);

        foreach ($verticals as $vertical) {
            CmsPage::updateOrCreate(['slug' => 'vertical-'.$vertical['slug']], [
                'title' => $vertical['name'].' Platform',
                'summary' => $vertical['description'],
                'meta_title' => 'Titan BOS — '.$vertical['name'],
                'meta_description' => $vertical['description'].' Built on Titan BOS with specialised dashboards, AI training, lifecycle models, compliance logic, and artefact generators.',
                'status' => 'published',
                'published_at' => now(),
                'website_content' => $this->verticalBlocks($vertical, $tiers[$vertical['tier']] ?? 'Titan BOS Vertical'),
            ]);
        }
    }

    protected function registryBlocks(array $verticals, array $tiers): array
    {
        $cards = collect($verticals)->map(fn (array $vertical): array => [
            'title' => $vertical['name'],
            'description' => $vertical['description'],
            'url' => '/verticals/'.$vertical['slug'],
            'tier' => $tiers[$vertical['tier']] ?? $vertical['tier'],
            'dashboards' => $vertical['dashboards'],
        ])->values()->all();

        return [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => 'Titan BOS — Vertical Registry',
                'headline' => 'One operating system. Multiple specialised service platforms.',
                'subheadline' => 'Activate vertical overlays for cleaning, exterior property services, mobile specialty services, and high-compliance workflows. Each version loads terminology, lifecycle logic, compliance packs, checklists, AI knowledge, dashboards, and artefact generators.',
                'primary_button_label' => 'Start with Residential Cleaning',
                'primary_button_url' => '/verticals/residential-cleaning',
                'secondary_button_label' => 'View all verticals',
                'secondary_button_url' => '#verticals',
                'note' => 'Companies can activate one vertical or run multi-vertical mode across several service lines.',
            ]],
            ['type' => 'VerticalGridBlock', 'data' => [
                'heading' => 'Supported Titan BOS vertical overlays',
                'body' => 'Each card is a deployable front-site version and can be edited from the CMS.',
                'verticals' => $cards,
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Cross-vertical infrastructure modules',
                'body' => 'Shared engines are reused across every vertical, then specialised per service model.',
                'features' => [
                    ['title' => 'Compliance Engine', 'description' => 'SWMS, JSA, PPE verification, and certification tracking.'],
                    ['title' => 'Evidence Engine', 'description' => 'Before/after photos, QR presence verification, and inspection scoring.'],
                    ['title' => 'Artefact Engine', 'description' => 'Handover reports, bond packs, certificates, summaries, and condition reports.'],
                    ['title' => 'Scheduling Engine', 'description' => 'Recurring, event-triggered, weather-triggered, and calendar-triggered scheduling.'],
                    ['title' => 'AI Knowledge Packs', 'description' => 'Manuals, standards, inspection rules, terminology, and training flows.'],
                    ['title' => 'Multi-Vertical Mode', 'description' => 'Dashboards adapt dynamically by job type across activated verticals.'],
                ],
            ]],
            ['type' => 'CtaBlock', 'data' => [
                'heading' => 'Turn Titan BOS into your specialised service platform.',
                'body' => 'Select a vertical during onboarding, load the correct AI knowledge pack, and adapt dashboards instantly.',
                'button_label' => 'Create account',
                'button_url' => '/register',
            ]],
        ];
    }

    protected function verticalBlocks(array $vertical, string $tierLabel): array
    {
        $requirements = collect($vertical['requirements'])->map(fn (string $requirement): array => [
            'title' => Str::headline($requirement),
            'description' => Str::headline($requirement).' is enabled as a workflow, checklist, data field, automation, or evidence rule inside the '.$vertical['name'].' overlay.',
        ])->values()->all();

        $dashboards = collect($vertical['dashboards'])->map(fn (string $dashboard): array => [
            'title' => $dashboard,
            'description' => $this->dashboardDescription($dashboard),
        ])->values()->all();

        return [
            ['type' => 'HeroBlock', 'data' => [
                'eyebrow' => $tierLabel,
                'headline' => $vertical['name'].' OS',
                'subheadline' => $vertical['description'].' Titan BOS loads specialised terminology, workflows, AI training, lifecycle models, compliance rules, dashboards, and client-ready artefacts for this vertical.',
                'primary_button_label' => 'Start this vertical',
                'primary_button_url' => '/register?vertical='.$vertical['slug'],
                'secondary_button_label' => 'Back to registry',
                'secondary_button_url' => '/verticals',
                'note' => 'Includes: '.$vertical['includes'],
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Specialised operating requirements',
                'body' => 'These requirements are converted into onboarding defaults, job templates, checklists, AI prompts, and artefact logic.',
                'features' => $requirements,
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Primary dashboards activated',
                'body' => 'The interface adapts around the dashboards most relevant to this service model.',
                'features' => $dashboards,
            ]],
            ['type' => 'FeatureGridBlock', 'data' => [
                'heading' => 'Loaded Titan BOS engines',
                'body' => 'Every vertical inherits shared Titan BOS infrastructure, then receives vertical-specific logic.',
                'features' => [
                    ['title' => 'Lifecycle Model', 'description' => 'Jobs progress through the exact lifecycle required by '.$vertical['name'].'.'],
                    ['title' => 'Compliance Pack', 'description' => 'Checklists, verification steps, and evidence requirements are injected for this service type.'],
                    ['title' => 'AI Knowledge Pack', 'description' => 'Terminology, standards, inspection rules, and technician coaching are specialised for this vertical.'],
                    ['title' => 'Artefact Generators', 'description' => 'Reports, packs, certificates, receipts, summaries, or condition documents are generated from job evidence.'],
                ],
            ]],
            ['type' => 'CtaBlock', 'data' => [
                'heading' => 'Deploy '.$vertical['name'].' as a specialised Titan BOS version.',
                'body' => 'Use this CMS page as the public landing page, onboarding vertical, and AI knowledge-pack selector for this service line.',
                'button_label' => 'Activate '.$vertical['name'],
                'button_url' => '/register?vertical='.$vertical['slug'],
            ]],
        ];
    }

    protected function dashboardDescription(string $dashboard): string
    {
        return match ($dashboard) {
            'Titan Solo' => 'Solo-operator execution, repeat visits, supplies, and route efficiency.',
            'Titan Go' => 'Mobile field execution, checklists, job status, proof, and technician workflow.',
            'Ground Zero' => 'Operational command centre for scheduling, dispatch, inspections, and team visibility.',
            'ZeroPay' => 'Payments, invoices, receipts, packages, and payment-linked workflows.',
            'Titan Pro' => 'Advanced operations for teams, contracts, reporting, compliance, and scale.',
            'Zero Fuss' => 'Client, agent, property manager, and partner-facing workflow simplification.',
            'Titan Zero' => 'Core intelligence layer for lifecycle modelling, evidence, compliance, and artefacts.',
            'Titan Hello' => 'Customer communication, notifications, lead capture, and status updates.',
            'Titan Studio' => 'Visual assets, before/after proof, service presentation, and portfolio workflows.',
            default => 'Specialised dashboard surface activated for this vertical.',
        };
    }
}
