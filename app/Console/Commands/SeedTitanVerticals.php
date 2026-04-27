<?php

namespace App\Console\Commands;

use Database\Seeders\TitanVerticalCmsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SeedTitanVerticals extends Command
{
    protected $signature = 'titan:seed-verticals';
    protected $description = 'Seed Titan BOS vertical CMS pages and the public vertical registry.';

    public function handle(): int
    {
        if (! Schema::hasTable('cms_pages')) {
            $this->error('cms_pages table does not exist. Run migrations first.');
            return self::FAILURE;
        }

        $this->call(TitanVerticalCmsSeeder::class);
        $this->info('Titan BOS vertical CMS pages seeded.');

        return self::SUCCESS;
    }
}
