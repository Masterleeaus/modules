<?php

namespace Modules\ProShots\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProShotsPresetsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $presets = [
            ['name' => 'Clean Home Kitchen',    'preset_key' => 'clean_home_kitchen',    'category' => 'residential', 'description' => 'Bright, modern kitchen with clean white surfaces and natural light.', 'is_default' => true],
            ['name' => 'Sparkling Bathroom',    'preset_key' => 'sparkling_bathroom',    'category' => 'residential', 'description' => 'Pristine bathroom with gleaming tiles and chrome fixtures.', 'is_default' => false],
            ['name' => 'Fresh Lounge Room',     'preset_key' => 'fresh_lounge_room',     'category' => 'residential', 'description' => 'Comfortable, freshly cleaned living room with neutral tones.', 'is_default' => false],
            ['name' => 'Clean Bedroom',         'preset_key' => 'clean_bedroom',         'category' => 'residential', 'description' => 'Neatly made bed, fresh linen, and clean carpeted floor.', 'is_default' => false],
            ['name' => 'Clean Office Space',    'preset_key' => 'clean_office_space',    'category' => 'commercial',  'description' => 'Modern open-plan office with polished floors and tidy desks.', 'is_default' => false],
            ['name' => 'Spotless Reception',    'preset_key' => 'spotless_reception',    'category' => 'commercial',  'description' => 'Gleaming reception area with marble floors and welcoming ambience.', 'is_default' => false],
            ['name' => 'Outdoor Clean Area',    'preset_key' => 'outdoor_clean_area',    'category' => 'outdoor',     'description' => 'Fresh outdoor patio or driveway after a high-pressure clean.', 'is_default' => false],
            ['name' => 'Clean Oven & Stove',    'preset_key' => 'clean_oven_stove',      'category' => 'residential', 'description' => 'Spotless oven interior or stovetop after deep clean.', 'is_default' => false],
            ['name' => 'Freshly Cleaned Carpet','preset_key' => 'freshly_cleaned_carpet','category' => 'residential', 'description' => 'Plush, steam-cleaned carpet with visible freshness.', 'is_default' => false],
        ];

        foreach ($presets as $preset) {
            DB::table('proshots_background_presets')->insertOrIgnore(array_merge($preset, [
                'thumbnail_url' => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]));
        }
    }
}
