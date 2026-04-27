<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('external_sync_records')) {
            Schema::create('external_sync_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('syncable_type', 100)->comment('e.g. App\\Models\\Invoice');
                $table->unsignedBigInteger('syncable_id')->index();
                $table->string('provider', 30)->default('xero')->comment('xero|myob');
                $table->string('external_id', 191)->nullable()->comment('Xero/MYOB assigned ID');
                $table->string('status', 20)->default('pending')->comment('pending|synced|failed');
                $table->text('last_error')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->unsignedSmallInteger('retry_count')->default(0);
                $table->timestamps();

                $table->unique(
                    ['organization_id', 'syncable_type', 'syncable_id', 'provider'],
                    'ext_sync_records_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('external_sync_records');
    }
};
