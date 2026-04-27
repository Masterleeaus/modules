<?php

namespace Database\Seeders;

use App\Models\JobType;
use App\Models\JobTypeChecklistItem;
use App\Models\Organization;
use Illuminate\Database\Seeder;

/**
 * Seeds standard cleaning business job types and their section-grouped checklist templates.
 * Run with: php artisan db:seed --class=CleaningJobTypeSeeder
 */
class CleaningJobTypeSeeder extends Seeder
{
    private const TEMPLATES = [
        [
            'name'        => 'Standard Clean',
            'color'       => '#6366f1',
            'description' => 'Regular maintenance cleaning for residential or commercial properties.',
            'checklist'   => [
                'Kitchen'   => ['Wipe counters', 'Clean stovetop', 'Wipe appliance exteriors', 'Clean sink & faucet', 'Mop floor'],
                'Bathrooms' => ['Scrub toilet', 'Clean sink & mirror', 'Wipe down shower/tub', 'Replace towels', 'Mop floor'],
                'Bedrooms'  => ['Dust surfaces', 'Change bed linens', 'Vacuum floor', 'Empty trash'],
                'Living Areas' => ['Dust all surfaces', 'Vacuum carpets/rugs', 'Mop hard floors', 'Wipe light switches & doorknobs'],
                'General'   => ['Take out trash', 'Lock up and return keys'],
            ],
        ],
        [
            'name'        => 'Deep Clean',
            'color'       => '#7c3aed',
            'description' => 'Thorough top-to-bottom cleaning including inside appliances and hard-to-reach areas.',
            'checklist'   => [
                'Kitchen'   => ['Clean inside oven', 'Clean inside microwave', 'Wipe inside refrigerator', 'Degrease range hood', 'Clean inside cabinets', 'Scrub sink', 'Mop & scrub floor'],
                'Bathrooms' => ['Scrub grout', 'Descale faucets & showerhead', 'Clean inside cabinets/vanity', 'Wash bath mat', 'Scrub toilet (inside & out)', 'Mop & scrub floor'],
                'Bedrooms'  => ['Dust ceiling fans & vents', 'Wipe baseboards', 'Clean light fixtures', 'Vacuum mattress', 'Change bed linens', 'Vacuum & mop floor'],
                'Living Areas' => ['Dust blinds & window sills', 'Clean windows (interior)', 'Vacuum under furniture', 'Wipe baseboards', 'Clean light fixtures'],
                'General'   => ['Wipe all doors & door frames', 'Clean all glass surfaces', 'Take out trash', 'Lock up and return keys'],
            ],
        ],
        [
            'name'        => 'Move-In / Move-Out',
            'color'       => '#0ea5e9',
            'description' => 'Comprehensive cleaning for vacancy handover — leaving the property spotless.',
            'checklist'   => [
                'Kitchen'   => ['Clean inside all cabinets & drawers', 'Clean inside oven & oven racks', 'Clean inside refrigerator & freezer', 'Defrost freezer if needed', 'Clean dishwasher interior', 'Degrease range hood & filter', 'Scrub sink & faucet', 'Wipe counters', 'Mop & scrub floor'],
                'Bathrooms' => ['Scrub & bleach toilet', 'Scrub tub & tile grout', 'Clean inside vanity', 'Replace shower curtain liner', 'Descale all fixtures', 'Clean mirrors', 'Mop & scrub floor'],
                'Bedrooms'  => ['Wipe all surfaces & baseboards', 'Clean closet shelves & rods', 'Vacuum & shampoo carpet OR mop hard floor', 'Clean window sills & blinds'],
                'Living Areas' => ['Clean all windows (interior)', 'Wipe all baseboards', 'Clean fireplace surround (if applicable)', 'Vacuum & shampoo carpet OR mop hard floor'],
                'General'   => ['Clean all light switches & outlets', 'Clean all door handles & lock faces', 'Wipe all walls for scuffs', 'Remove all debris', 'Lock up and return keys'],
            ],
        ],
        [
            'name'        => 'Post-Construction',
            'color'       => '#f59e0b',
            'description' => 'Heavy-duty cleanup after renovation or new construction — dust and debris removal.',
            'checklist'   => [
                'Dust & Debris' => ['Remove all construction debris', 'Vacuum all surfaces & duct vents', 'Wipe all surfaces (dust residue)', 'Clean window tracks & sills'],
                'Kitchen'       => ['Clean all cabinets inside & out', 'Wipe counters & backsplash', 'Clean appliances (if installed)', 'Mop floor'],
                'Bathrooms'     => ['Remove grout haze from tile', 'Scrub fixtures & remove stickers', 'Clean mirrors', 'Mop floor'],
                'Floors'        => ['Sweep/vacuum all hard floors', 'Mop hard floors (2 passes)', 'Vacuum carpets (2 passes)', 'Remove any floor protection film'],
                'General'       => ['Clean all windows (interior)', 'Wipe baseboards & door frames', 'Clean light fixtures & fans', 'Final walk-through & sign-off'],
            ],
        ],
    ];

    public function run(): void
    {
        // Find or create a demo org (only seed for org with id=1 or the first org)
        $org = Organization::first();

        if (! $org) {
            $this->command->warn('No organizations found — skipping CleaningJobTypeSeeder.');
            return;
        }

        foreach (self::TEMPLATES as $template) {
            /** @var JobType $jobType */
            $jobType = JobType::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $template['name']],
                [
                    'color'       => $template['color'],
                    'description' => $template['description'],
                    'is_active'   => true,
                ]
            );

            $sortOrder = 1;
            foreach ($template['checklist'] as $section => $items) {
                foreach ($items as $label) {
                    JobTypeChecklistItem::firstOrCreate(
                        ['job_type_id' => $jobType->id, 'label' => $label],
                        [
                            'section'    => $section,
                            'sort_order' => $sortOrder++,
                            'is_required' => true,
                        ]
                    );
                }
            }

            $this->command->info("Seeded job type: {$template['name']} with ".count(array_merge(...array_values($template['checklist']))).' checklist items.');
        }
    }
}
