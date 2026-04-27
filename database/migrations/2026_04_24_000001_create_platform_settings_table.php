<?php

use App\Models\PlatformSetting;
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
                $table->string('app_name')->default('TITAN ZERO');
                $table->string('tagline')->nullable();
                $table->string('logo')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('favicon')->nullable();
                $table->string('favicon_path')->nullable();
                $table->string('primary_color')->nullable();
                $table->string('secondary_color')->nullable();
                $table->string('accent_color')->nullable();
                $table->string('background_color')->nullable();
                $table->string('support_email')->nullable();
                $table->string('sales_email')->nullable();
                $table->string('from_email')->nullable();
                $table->string('from_name')->nullable();
                $table->text('footer_text')->nullable();
                $table->string('copyright_text')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('marketing_headline')->nullable();
                $table->text('marketing_subheadline')->nullable();
                $table->string('login_headline')->nullable();
                $table->text('login_subheadline')->nullable();
                $table->string('default_plan')->nullable();
                $table->unsignedInteger('trial_days')->default(14);
                $table->boolean('enable_registration')->default(true);
                $table->boolean('stripe_enabled')->default(false);
                $table->text('maintenance_banner')->nullable();
                $table->text('custom_head_html')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('platform_settings', function (Blueprint $table) {
                foreach ([
                    'tagline' => fn () => $table->string('tagline')->nullable(),
                    'logo' => fn () => $table->string('logo')->nullable(),
                    'logo_path' => fn () => $table->string('logo_path')->nullable(),
                    'favicon' => fn () => $table->string('favicon')->nullable(),
                    'favicon_path' => fn () => $table->string('favicon_path')->nullable(),
                    'primary_color' => fn () => $table->string('primary_color')->nullable(),
                    'secondary_color' => fn () => $table->string('secondary_color')->nullable(),
                    'accent_color' => fn () => $table->string('accent_color')->nullable(),
                    'background_color' => fn () => $table->string('background_color')->nullable(),
                    'support_email' => fn () => $table->string('support_email')->nullable(),
                    'sales_email' => fn () => $table->string('sales_email')->nullable(),
                    'from_email' => fn () => $table->string('from_email')->nullable(),
                    'from_name' => fn () => $table->string('from_name')->nullable(),
                    'footer_text' => fn () => $table->text('footer_text')->nullable(),
                    'copyright_text' => fn () => $table->string('copyright_text')->nullable(),
                    'meta_title' => fn () => $table->string('meta_title')->nullable(),
                    'meta_description' => fn () => $table->text('meta_description')->nullable(),
                    'marketing_headline' => fn () => $table->string('marketing_headline')->nullable(),
                    'marketing_subheadline' => fn () => $table->text('marketing_subheadline')->nullable(),
                    'login_headline' => fn () => $table->string('login_headline')->nullable(),
                    'login_subheadline' => fn () => $table->text('login_subheadline')->nullable(),
                    'default_plan' => fn () => $table->string('default_plan')->nullable(),
                    'trial_days' => fn () => $table->unsignedInteger('trial_days')->default(14),
                    'enable_registration' => fn () => $table->boolean('enable_registration')->default(true),
                    'stripe_enabled' => fn () => $table->boolean('stripe_enabled')->default(false),
                    'maintenance_banner' => fn () => $table->text('maintenance_banner')->nullable(),
                    'custom_head_html' => fn () => $table->text('custom_head_html')->nullable(),
                ] as $column => $callback) {
                    if (! Schema::hasColumn('platform_settings', $column)) {
                        $callback();
                    }
                }
            });
        }

        if (Schema::hasTable('platform_settings') && DB::table('platform_settings')->count() === 0) {
            DB::table('platform_settings')->insert([
                ...PlatformSetting::defaults(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('platform_settings')) {
            $settings = DB::table('platform_settings')->first();

            if ($settings) {
                $defaults = PlatformSetting::defaults();
                $updates = [];

                foreach ($defaults as $key => $value) {
                    if (Schema::hasColumn('platform_settings', $key) && blank($settings->{$key} ?? null)) {
                        $updates[$key] = $value;
                    }
                }

                if (! empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('platform_settings')->where('id', $settings->id)->update($updates);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
