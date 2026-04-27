<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds compliance-tracking columns to `employee_details`.
     * These columns are shared with ProviderManagement and support
     * Australian field-service (cleaning) compliance requirements.
     *
     * Sensitive fields (TFN, bank account) are stored encrypted;
     * encryption is handled via $casts in the EmployeeDetails model.
     */
    public function up(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_details', 'police_check_date')) {
                $table->date('police_check_date')->nullable()->after('probation_end_date');
            }

            if (!Schema::hasColumn('employee_details', 'police_check_expiry')) {
                $table->date('police_check_expiry')->nullable()->after('police_check_date');
            }

            if (!Schema::hasColumn('employee_details', 'wwcc_expiry')) {
                $table->date('wwcc_expiry')->nullable()->after('police_check_expiry');
            }

            if (!Schema::hasColumn('employee_details', 'insurance_expiry')) {
                $table->date('insurance_expiry')->nullable()->after('wwcc_expiry');
            }

            if (!Schema::hasColumn('employee_details', 'abn')) {
                $table->string('abn')->nullable()->after('insurance_expiry');
            }

            if (!Schema::hasColumn('employee_details', 'is_subcontractor')) {
                $table->boolean('is_subcontractor')->default(false)->after('abn');
            }

            if (!Schema::hasColumn('employee_details', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('is_subcontractor');
            }

            if (!Schema::hasColumn('employee_details', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }

            if (!Schema::hasColumn('employee_details', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('emergency_contact_phone');
            }

            if (!Schema::hasColumn('employee_details', 'bank_bsb')) {
                $table->string('bank_bsb')->nullable()->after('bank_account_name');
            }

            if (!Schema::hasColumn('employee_details', 'bank_account_number')) {
                // Stored encrypted — see EmployeeDetails::$casts
                $table->text('bank_account_number')->nullable()->after('bank_bsb')->comment('encrypted');
            }

            if (!Schema::hasColumn('employee_details', 'tax_file_number')) {
                // Stored encrypted — see EmployeeDetails::$casts
                $table->text('tax_file_number')->nullable()->after('bank_account_number')->comment('encrypted');
            }

            if (!Schema::hasColumn('employee_details', 'induction_completed_date')) {
                $table->date('induction_completed_date')->nullable()->after('tax_file_number');
            }
        });
    }

    /**
     * Reverse the migrations — remove only recruit-owned compliance columns.
     */
    public function down(): void
    {
        $columns = [
            'police_check_date',
            'police_check_expiry',
            'wwcc_expiry',
            'insurance_expiry',
            'abn',
            'is_subcontractor',
            'emergency_contact_name',
            'emergency_contact_phone',
            'bank_account_name',
            'bank_bsb',
            'bank_account_number',
            'tax_file_number',
            'induction_completed_date',
        ];

        Schema::table('employee_details', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
