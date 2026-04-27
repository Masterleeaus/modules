<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('code', 20)->comment('e.g. 4000, 6100');
                $table->string('name', 191)->comment('e.g. Cleaning Revenue, Vehicle Expenses');
                $table->enum('type', ['revenue', 'expense', 'asset', 'liability', 'equity']);
                $table->boolean('is_system')->default(false)->comment('System accounts cannot be deleted');
                $table->string('xero_account_id', 191)->nullable();
                $table->timestamps();

                $table->unique(['organization_id', 'code'], 'accounts_org_code_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
