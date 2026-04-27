<?php

namespace Modules\PMCore\database\seeders;

use Illuminate\Database\Seeder;
use Modules\PMCore\app\Enums\JobType;
use Modules\PMCore\app\Models\ProjectChecklist;

class CleaningChecklistSeeder extends Seeder
{
    /**
     * Template checklists keyed by JobType value.
     * Each entry: [ 'section' => 'Section name', 'items' => [...] ]
     */
    private array $templates = [
        JobType::RESIDENTIAL => [
            [
                'name'  => 'Residential Clean — Kitchen',
                'items' => [
                    ['description' => 'Wipe down all bench tops and splashback', 'is_required' => true],
                    ['description' => 'Clean inside and outside of microwave', 'is_required' => false],
                    ['description' => 'Wipe exterior of oven, stove top, and rangehood', 'is_required' => true],
                    ['description' => 'Clean sink and taps', 'is_required' => true],
                    ['description' => 'Wipe cabinet doors and handles', 'is_required' => false],
                    ['description' => 'Mop floor', 'is_required' => true],
                ],
            ],
            [
                'name'  => 'Residential Clean — Bathrooms & Toilets',
                'items' => [
                    ['description' => 'Scrub and disinfect toilet bowl, seat, and cistern', 'is_required' => true],
                    ['description' => 'Clean basin and taps', 'is_required' => true],
                    ['description' => 'Wipe mirrors', 'is_required' => false],
                    ['description' => 'Scrub shower recess and screen', 'is_required' => true],
                    ['description' => 'Mop floor and disinfect', 'is_required' => true],
                ],
            ],
            [
                'name'  => 'Residential Clean — General Areas',
                'items' => [
                    ['description' => 'Vacuum all carpeted areas', 'is_required' => true],
                    ['description' => 'Mop hard floors', 'is_required' => true],
                    ['description' => 'Dust skirting boards and window sills', 'is_required' => false],
                    ['description' => 'Remove cobwebs from ceilings and corners', 'is_required' => false],
                    ['description' => 'Empty and wipe all bins', 'is_required' => true],
                ],
            ],
        ],

        JobType::BOND => [
            [
                'name'  => 'Bond Clean — Kitchen',
                'items' => [
                    ['description' => 'Clean inside oven — remove racks, degrease and scrub', 'is_required' => true],
                    ['description' => 'Clean rangehood filters', 'is_required' => true],
                    ['description' => 'Wipe all bench tops', 'is_required' => true],
                    ['description' => 'Clean inside and outside of all cupboards and drawers', 'is_required' => true],
                    ['description' => 'Descale and clean sink and taps', 'is_required' => true],
                    ['description' => 'Clean dishwasher interior and door seal', 'is_required' => false],
                    ['description' => 'Mop floor', 'is_required' => true],
                ],
            ],
            [
                'name'  => 'Bond Clean — Bathrooms',
                'items' => [
                    ['description' => 'Remove soap scum from tiles, grout, and screens', 'is_required' => true],
                    ['description' => 'Descale shower head and taps', 'is_required' => true],
                    ['description' => 'Disinfect toilet — bowl, seat, lid, cistern, and base', 'is_required' => true],
                    ['description' => 'Clean vanity inside and out, mirror polished', 'is_required' => true],
                    ['description' => 'Clean exhaust fan cover', 'is_required' => false],
                    ['description' => 'Mop and disinfect floor', 'is_required' => true],
                ],
            ],
            [
                'name'  => 'Bond Clean — Bedrooms & Living',
                'items' => [
                    ['description' => 'Vacuum all carpets including under furniture', 'is_required' => true],
                    ['description' => 'Wipe all light switches and power points', 'is_required' => true],
                    ['description' => 'Clean inside built-in wardrobe — rails, shelves, tracks', 'is_required' => true],
                    ['description' => 'Wipe skirting boards', 'is_required' => true],
                    ['description' => 'Wipe window sills, tracks, and screens', 'is_required' => true],
                    ['description' => 'Remove cobwebs throughout', 'is_required' => true],
                    ['description' => 'Spot clean walls', 'is_required' => false],
                ],
            ],
            [
                'name'  => 'Bond Clean — Laundry',
                'items' => [
                    ['description' => 'Clean inside laundry tub and taps', 'is_required' => true],
                    ['description' => 'Wipe washing machine exterior and drum', 'is_required' => false],
                    ['description' => 'Clean dryer lint filter and drum', 'is_required' => false],
                    ['description' => 'Mop floor', 'is_required' => true],
                ],
            ],
        ],

        JobType::CARPET => [
            [
                'name'  => 'Carpet Steam Clean',
                'items' => [
                    ['description' => 'Pre-vacuum entire carpeted area', 'is_required' => true],
                    ['description' => 'Pre-treat stains and high-traffic areas', 'is_required' => false],
                    ['description' => 'Apply pre-spray deodoriser', 'is_required' => false],
                    ['description' => 'Steam clean using hot water extraction method', 'is_required' => true],
                    ['description' => 'Extract excess moisture', 'is_required' => true],
                    ['description' => 'Apply deodoriser / sanitiser post-clean', 'is_required' => false],
                    ['description' => 'Place drying fans / ventilate area', 'is_required' => false],
                    ['description' => 'Inspect with client before departure', 'is_required' => true],
                ],
            ],
        ],

        JobType::COMMERCIAL => [
            [
                'name'  => 'Commercial Clean — Office Areas',
                'items' => [
                    ['description' => 'Vacuum all carpeted areas', 'is_required' => true],
                    ['description' => 'Mop all hard floors', 'is_required' => true],
                    ['description' => 'Empty and re-line all waste bins', 'is_required' => true],
                    ['description' => 'Wipe desks and workstations (remove items first)', 'is_required' => false],
                    ['description' => 'Wipe phone handsets and keyboards with disinfectant', 'is_required' => false],
                    ['description' => 'Clean internal glass and partitions', 'is_required' => false],
                    ['description' => 'Dust skirting boards and windowsills', 'is_required' => false],
                ],
            ],
            [
                'name'  => 'Commercial Clean — Amenities / Bathrooms',
                'items' => [
                    ['description' => 'Disinfect and clean all toilets and urinals', 'is_required' => true],
                    ['description' => 'Clean and disinfect all basins and taps', 'is_required' => true],
                    ['description' => 'Restock toilet paper, soap, and hand towel dispensers', 'is_required' => true],
                    ['description' => 'Wipe mirrors', 'is_required' => false],
                    ['description' => 'Mop floors with disinfectant', 'is_required' => true],
                    ['description' => 'Empty sanitary bins where applicable', 'is_required' => true],
                ],
            ],
            [
                'name'  => 'Commercial Clean — Kitchen / Break Room',
                'items' => [
                    ['description' => 'Wipe bench tops and splashback', 'is_required' => true],
                    ['description' => 'Clean sink and taps', 'is_required' => true],
                    ['description' => 'Wipe exterior of microwave, fridge, and coffee machine', 'is_required' => false],
                    ['description' => 'Clean inside microwave', 'is_required' => false],
                    ['description' => 'Mop floor', 'is_required' => true],
                    ['description' => 'Empty and clean bins', 'is_required' => true],
                ],
            ],
        ],

        JobType::PRESSURE => [
            [
                'name'  => 'Pressure Wash',
                'items' => [
                    ['description' => 'Clear area of furniture and obstacles', 'is_required' => true],
                    ['description' => 'Pre-wet surface with water', 'is_required' => false],
                    ['description' => 'Apply degreaser / detergent to heavy stains', 'is_required' => false],
                    ['description' => 'Pressure wash all designated surfaces', 'is_required' => true],
                    ['description' => 'Clear drain of debris after washing', 'is_required' => true],
                    ['description' => 'Rinse and inspect surface with client', 'is_required' => true],
                ],
            ],
        ],

        JobType::POOL => [
            [
                'name'  => 'Pool Clean',
                'items' => [
                    ['description' => 'Skim surface and remove floating debris', 'is_required' => true],
                    ['description' => 'Brush pool walls and floor', 'is_required' => true],
                    ['description' => 'Vacuum pool floor', 'is_required' => true],
                    ['description' => 'Empty and rinse skimmer basket and pump basket', 'is_required' => true],
                    ['description' => 'Backwash filter if pressure elevated', 'is_required' => false],
                    ['description' => 'Test water chemistry (pH, chlorine, alkalinity)', 'is_required' => true],
                    ['description' => 'Add chemicals as required and record dosage', 'is_required' => true],
                    ['description' => 'Inspect equipment and report faults to client', 'is_required' => false],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->templates as $jobType => $checklists) {
            foreach ($checklists as $index => $templateData) {
                $checklist = ProjectChecklist::firstOrCreate(
                    [
                        'name'        => $templateData['name'],
                        'is_template' => true,
                        'project_id'  => null,
                    ],
                    [
                        'job_type'   => $jobType,
                        'created_by' => null,
                    ]
                );

                // Only seed items if the checklist was just created
                if ($checklist->wasRecentlyCreated) {
                    foreach ($templateData['items'] as $order => $itemData) {
                        $checklist->items()->create([
                            'description' => $itemData['description'],
                            'is_required' => $itemData['is_required'],
                            'sort_order'  => $order,
                        ]);
                    }
                }
            }
        }
    }
}
