<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('summary')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('status')->default('draft')->index();
                $table->json('website_content')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            if (! Schema::hasColumn('cms_pages', 'summary')) {
                $table->text('summary')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'status')) {
                $table->string('status')->default('draft')->index();
            }
            if (! Schema::hasColumn('cms_pages', 'website_content')) {
                $table->json('website_content')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Keep CMS content safe on rollback.
    }
};
