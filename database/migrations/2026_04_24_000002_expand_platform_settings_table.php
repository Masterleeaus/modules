<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('platform_settings', function (Blueprint $table) {
            $this->stringColumn($table, 'app_name');
            $this->stringColumn($table, 'site_name');
            $this->stringColumn($table, 'logo');
            $this->stringColumn($table, 'logo_path');
            $this->stringColumn($table, 'favicon');
            $this->stringColumn($table, 'favicon_path');
            $this->stringColumn($table, 'primary_color');
            $this->stringColumn($table, 'secondary_color');
            $this->stringColumn($table, 'accent_color');
            $this->stringColumn($table, 'support_email');
            $this->stringColumn($table, 'billing_email');
            $this->stringColumn($table, 'contact_phone');
            $this->textColumn($table, 'footer_text');
            $this->stringColumn($table, 'meta_title');
            $this->textColumn($table, 'meta_description');
            $this->stringColumn($table, 'landing_headline');
            $this->textColumn($table, 'landing_subheadline');
            $this->stringColumn($table, 'cta_label');
            $this->stringColumn($table, 'cta_url');
            if (! Schema::hasColumn('platform_settings', 'enable_registration')) {
                $table->boolean('enable_registration')->default(true);
            }
            $this->textColumn($table, 'maintenance_message');
            $this->textColumn($table, 'custom_css');
        });

        if (DB::table('platform_settings')->count() === 0) {
            DB::table('platform_settings')->insert([
                'app_name' => 'TITAN ZERO',
                'site_name' => 'TITAN ZERO',
                'logo_path' => 'platform/titan-zero-logo.png',
                'logo' => 'platform/titan-zero-logo.png',
                'primary_color' => '#2563eb',
                'secondary_color' => '#0f172a',
                'accent_color' => '#14b8a6',
                'support_email' => 'support@titanzero.pro',
                'footer_text' => 'Powered by Titan Zero.',
                'meta_title' => 'TITAN ZERO',
                'meta_description' => 'Titan Zero field operations platform.',
                'landing_headline' => 'Field operations, controlled from one hub.',
                'landing_subheadline' => 'Manage jobs, teams, invoices, dispatch, and SaaS tenants from Titan Zero.',
                'cta_label' => 'Get started',
                'cta_url' => '/register',
                'enable_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('platform_settings')->update([
            'app_name' => DB::raw("COALESCE(app_name, site_name, 'TITAN ZERO')"),
            'site_name' => DB::raw("COALESCE(site_name, app_name, 'TITAN ZERO')"),
            'logo_path' => DB::raw('COALESCE(logo_path, logo)'),
            'logo' => DB::raw('COALESCE(logo, logo_path)'),
            'favicon_path' => DB::raw('COALESCE(favicon_path, favicon)'),
            'favicon' => DB::raw('COALESCE(favicon, favicon_path)'),
        ]);
    }

    public function down(): void
    {
        // Non-destructive rollback: keep settings data intact.
    }

    private function stringColumn(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn('platform_settings', $column)) {
            $table->string($column)->nullable();
        }
    }

    private function textColumn(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn('platform_settings', $column)) {
            $table->text($column)->nullable();
        }
    }
};
