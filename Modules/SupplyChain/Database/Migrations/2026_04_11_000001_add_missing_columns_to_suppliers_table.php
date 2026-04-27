<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'abn')) {
                $table->string('abn', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'website')) {
                $table->string('website', 255)->nullable()->after('abn');
            }
            if (!Schema::hasColumn('suppliers', 'notes')) {
                $table->text('notes')->nullable()->after('website');
            }
            if (!Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('notes');
            }
            if (!Schema::hasColumn('suppliers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('company_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $drop = array_filter(
                ['abn', 'website', 'notes', 'is_active', 'user_id'],
                fn($c) => Schema::hasColumn('suppliers', $c)
            );

            if ($drop) {
                if (Schema::hasColumn('suppliers', 'user_id')) {
                    $table->dropForeign(['user_id']);
                }
                $table->dropColumn(array_values($drop));
            }
        });
    }
};
