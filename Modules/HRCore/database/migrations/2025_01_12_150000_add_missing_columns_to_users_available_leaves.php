<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_available_leaves', function (Blueprint $table) {
            // Add year column
            $table->integer('year')->after('leave_type_id');

            // Rename existing columns for clarity
            $table->renameColumn('total_leaves', 'entitled_leaves');
            $table->renameColumn('remaining_leaves', 'available_leaves');

            // Add new columns
            $table->decimal('carried_forward_leaves', 5, 2)->default(0)->after('entitled_leaves');
            $table->decimal('additional_leaves', 5, 2)->default(0)->after('carried_forward_leaves');
            $table->date('carry_forward_expiry_date')->nullable()->after('available_leaves');

            // Add unique index on user_id, leave_type_id, and year
            $table->unique(['user_id', 'leave_type_id', 'year'], 'user_leave_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_available_leaves', function (Blueprint $table) {
            // Drop the unique index
            $table->dropUnique('user_leave_year_unique');

            // Drop added columns
            $table->dropColumn(['year', 'carried_forward_leaves', 'additional_leaves', 'carry_forward_expiry_date']);

            // Rename columns back
            $table->renameColumn('entitled_leaves', 'total_leaves');
            $table->renameColumn('available_leaves', 'remaining_leaves');
        });
    }
};
