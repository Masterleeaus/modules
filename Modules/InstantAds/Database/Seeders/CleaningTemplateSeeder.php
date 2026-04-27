<?php

namespace Modules\InstantAds\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleaningTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $templates = [
            [
                'key'              => 'spring_clean_special',
                'name'             => 'Spring Clean Special',
                'category'         => 'seasonal',
                'job_type'         => 'spring_clean',
                'prompt_template'  => 'A vibrant, professional advertisement for a spring cleaning special offer. '
                    . 'Bright, airy aesthetic with fresh green and yellow tones. '
                    . 'Show a spotless, sunlit home interior with flowers in the foreground. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Clean typography overlay with promotional text. High quality photorealistic style.',
                'negative_prompt'  => 'dirty, cluttered, dark, gloomy, blurry, low quality',
                'is_active'        => true,
                'sort_order'       => 10,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'key'              => 'end_of_lease_bond_clean',
                'name'             => 'End-of-Lease Bond Clean',
                'category'         => 'service_type',
                'job_type'         => 'bond_clean',
                'prompt_template'  => 'Professional real-estate style advertisement for an end-of-lease bond cleaning service. '
                    . 'Show a pristine, empty apartment with gleaming surfaces, sparkling oven, and clean carpets. '
                    . 'Neutral tones, white walls, warm lighting. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Emphasise guarantee and inspection readiness. Photorealistic, sharp details.',
                'negative_prompt'  => 'dirty, messy, worn, damaged, dark shadows, low quality',
                'is_active'        => true,
                'sort_order'       => 20,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'key'              => 'move_in_out_clean',
                'name'             => 'Move In/Out Clean',
                'category'         => 'service_type',
                'job_type'         => 'move_clean',
                'prompt_template'  => 'Uplifting advertisement showcasing a move-in / move-out cleaning service. '
                    . 'Split composition: left side — boxes being packed in a spotless room; right side — fresh new home interior gleaming. '
                    . 'Warm, welcoming colour palette. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Clean, modern typography. Photorealistic commercial photography style.',
                'negative_prompt'  => 'dirt, stains, clutter, dark, low quality',
                'is_active'        => true,
                'sort_order'       => 30,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'key'              => 'regular_home_clean',
                'name'             => 'Regular Home Clean',
                'category'         => 'evergreen',
                'job_type'         => 'regular_clean',
                'prompt_template'  => 'Warm and welcoming advertisement for a regular home cleaning subscription service. '
                    . 'Show a happy family in a beautifully clean living room, natural light, fresh flowers. '
                    . 'Soft, homely colour palette with {{primary_color}} accent. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Lifestyle photography feel, high resolution, trustworthy and professional.',
                'negative_prompt'  => 'clinical, cold, mess, clutter, low quality, cartoon',
                'is_active'        => true,
                'sort_order'       => 40,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'key'              => 'commercial_office_clean',
                'name'             => 'Commercial Office Clean',
                'category'         => 'service_type',
                'job_type'         => 'commercial_clean',
                'prompt_template'  => 'Corporate-style advertisement for a commercial office cleaning service. '
                    . 'Modern open-plan office environment, gleaming floors, polished desks, spotless windows. '
                    . 'Professional colour scheme using {{primary_color}} and {{secondary_color}}. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Conveys reliability, discretion, and professionalism. Photorealistic quality.',
                'negative_prompt'  => 'residential, dirty, cluttered, dated, low quality',
                'is_active'        => true,
                'sort_order'       => 50,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'key'              => 'carpet_steam_clean',
                'name'             => 'Carpet Steam Clean',
                'category'         => 'service_type',
                'job_type'         => 'carpet_clean',
                'prompt_template'  => 'Before-and-after style advertisement for a professional carpet steam cleaning service. '
                    . 'Dramatic split showing dirty stained carpet versus brilliantly clean, fluffy carpet after treatment. '
                    . 'Steam rising from clean carpet adds visual impact. Warm residential setting. '
                    . 'Brand: {{brand_name}}. {{tagline}} '
                    . 'Close-up texture detail that showcases the transformation. High quality photorealistic.',
                'negative_prompt'  => 'stains (after side), dirty (after side), low quality, blurry',
                'is_active'        => true,
                'sort_order'       => 60,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('instant_ads_templates')->insertOrIgnore($template);
        }
    }
}
