<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class ImportMarketingSiteToCmsSeeder extends Seeder
{
    public function run(): void
    {
        CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Titan Zero FieldOps Hub',
                'summary' => 'Imported from the existing FieldOps Hub marketing homepage and converted into editable CMS blocks.',
                'meta_title' => 'Titan Zero — Field Service Management Platform',
                'meta_description' => 'Dispatch technicians, track jobs, send estimates, collect payments, and manage field service operations from one platform.',
                'status' => 'published',
                'published_at' => now(),
                'website_content' => self::blocks(),
            ]
        );
    }

    public static function blocks(): array
    {
        return [
            [
                'type' => 'HeroBlock',
                'data' => [
                    'eyebrow' => 'FieldOps Hub for growing crews',
                    'headline' => 'Field service under control.',
                    'subheadline' => 'Dispatch technicians, track jobs in real time, send estimates, collect payments — all from one fast, modern platform built for field service teams.',
                    'primary_button_label' => 'Start your free 14-day trial',
                    'primary_button_url' => '/register',
                    'secondary_button_label' => 'See pricing',
                    'secondary_button_url' => '/#pricing',
                    'note' => 'No credit card required. Cancel any time.',
                ],
            ],
            [
                'type' => 'FeatureGridBlock',
                'data' => [
                    'heading' => 'Everything your field team needs',
                    'body' => 'Converted from the existing homepage sections into editable CMS blocks.',
                    'features' => [
                        ['title' => 'Smart Job Scheduling', 'description' => 'Create and assign jobs in seconds. Track status in real time from scheduled through completion — every step visible to your whole team.'],
                        ['title' => 'Live Dispatch & GPS Tracking', 'description' => 'See exactly where every technician is on a live map. Assign jobs to the nearest available tech and watch their trail update in real time.'],
                        ['title' => 'Estimates Customers Accept Online', 'description' => 'Send Good/Better/Best estimate packages with a shareable link. Customers accept or decline online — no phone tag required.'],
                        ['title' => 'Invoicing & Online Payments', 'description' => 'Generate invoices from completed jobs. Customers pay online via Stripe. Record cash, check, or card payments with a full audit trail.'],
                        ['title' => 'Technician Mobile App', 'description' => 'Your field team gets a mobile-optimized PWA that works offline. Check job details, update status, and complete checklists from the field.'],
                        ['title' => 'Reports That Drive Decisions', 'description' => 'Owner dashboard with live KPIs. Drill into job profitability by type, revenue per technician, and trends over time.'],
                    ],
                ],
            ],
            [
                'type' => 'PricingBlock',
                'data' => [
                    'heading' => 'Simple pricing for growing crews',
                    'body' => 'The original Starter, Growth, and Pro pricing content is now editable from the CMS.',
                    'tiers' => [
                        [
                            'name' => 'Starter',
                            'price' => '$79/mo',
                            'description' => 'Perfect for small crews just getting organized.',
                            'features' => ['Up to 3 technician seats', 'Customers & properties', 'Job scheduling & tracking', 'Estimates with online acceptance', 'Invoicing & payment recording', 'Technician mobile PWA', 'Standard reporting', 'Email support'],
                            'highlight' => false,
                        ],
                        [
                            'name' => 'Growth',
                            'price' => '$149/mo',
                            'description' => 'For growing operations managing a real crew.',
                            'features' => ['Everything in Starter', 'Up to 10 technician seats', 'Automated SMS & email notifications', 'Live dispatch map with GPS tracking', 'Multi-tier estimate packages', 'Stripe online payments', 'Job profitability & technician reports', 'Priority support'],
                            'highlight' => true,
                        ],
                        [
                            'name' => 'Pro',
                            'price' => '$249/mo',
                            'description' => 'Unlimited scale for established operations.',
                            'features' => ['Everything in Growth', 'Unlimited technician seats', 'Dedicated onboarding call', 'Custom integrations on request', 'SLA-backed uptime', 'White-glove migration support', 'Early access to new features', 'Dedicated Slack channel'],
                            'highlight' => false,
                        ],
                    ],
                ],
            ],
            [
                'type' => 'FaqBlock',
                'data' => [
                    'heading' => 'Frequently asked questions',
                    'items' => [
                        ['question' => 'Can I switch plans later?', 'answer' => 'Yes. Upgrade or downgrade any time from your billing settings. Changes take effect immediately and are prorated.'],
                        ['question' => 'How does annual billing work?', 'answer' => 'Annual plans are billed once a year at roughly 20% off the monthly rate. You can switch to annual from your billing settings at any time.'],
                        ['question' => 'What happens when I hit my technician seat limit?', 'answer' => 'You will be prompted to upgrade before adding a new technician. Your existing team and data are never affected.'],
                        ['question' => 'Is there a free trial?', 'answer' => 'The existing homepage offered a 14-day trial on all plans — no credit card required.'],
                        ['question' => 'What integrations are included?', 'answer' => 'Stripe for online payments, Twilio for SMS notifications, SendGrid for email, and Google Maps for live dispatch. Each is configurable per organization.'],
                        ['question' => 'How is data isolated between organizations?', 'answer' => 'Every record — customers, jobs, invoices, technicians — is scoped to your organization. There is no cross-tenant data access by design.'],
                    ],
                ],
            ],
            [
                'type' => 'CtaBlock',
                'data' => [
                    'heading' => 'Ready to get field service under control?',
                    'body' => 'Start managing jobs, crews, estimates, payments, and reporting from one Titan Zero-powered platform.',
                    'button_label' => 'Create account',
                    'button_url' => '/register',
                ],
            ],
        ];
    }
}
