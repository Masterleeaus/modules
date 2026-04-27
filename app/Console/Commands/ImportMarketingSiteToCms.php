<?php

namespace App\Console\Commands;

use App\Models\CmsPage;
use Database\Seeders\ImportMarketingSiteToCmsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ImportMarketingSiteToCms extends Command
{
    protected $signature = 'cms:import-existing-site {--force : Overwrite existing CMS homepage content}';

    protected $description = 'Imports the existing FieldOps/TitanZero marketing site into editable CMS blocks.';

    public function handle(): int
    {
        if (! Schema::hasTable('cms_pages')) {
            $this->error('cms_pages table does not exist. Run migrations first.');
            return self::FAILURE;
        }

        $page = CmsPage::firstOrNew(['slug' => 'home']);

        if ($page->exists && ! $this->option('force')) {
            $this->warn('CMS homepage already exists. Re-run with --force to overwrite it.');
            return self::SUCCESS;
        }

        $page->fill([
            'title' => 'Titan Zero FieldOps Hub',
            'summary' => 'Imported from the existing FieldOps Hub marketing homepage and converted into editable CMS blocks.',
            'meta_title' => 'Titan Zero — Field Service Management Platform',
            'meta_description' => 'Dispatch technicians, track jobs, send estimates, collect payments, and manage field service operations from one platform.',
            'status' => 'published',
            'published_at' => now(),
            'website_content' => ImportMarketingSiteToCmsSeeder::blocks(),
        ]);

        $page->save();

        $this->info('Imported the existing marketing homepage into editable CMS blocks.');
        $this->line('Edit it in: /admin/cms-pages');

        return self::SUCCESS;
    }
}
