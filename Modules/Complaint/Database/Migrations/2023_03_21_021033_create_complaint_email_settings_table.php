<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Complaint\Entities\ComplaintEmailSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('complaint_email_settings')) {
            Schema::create('complaint_email_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('mail_username')->nullable();
                $table->string('mail_password')->nullable();
                $table->string('mail_from_name')->nullable();
                $table->string('mail_from_email')->nullable();

                $table->string('imap_host')->nullable();
                $table->string('imap_port')->nullable();
                $table->string('imap_encryption')->nullable();

                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('verified')->default(0);
                $table->integer('sync_interval')->default(1);

                $table->timestamps();
            });
        }

        // Seed default settings for existing companies (install-safe)
        $companies = Company::query()->get();
        foreach ($companies as $company) {
            if (method_exists(ComplaintEmailSetting::class, 'createEmailSetting')) {
                ComplaintEmailSetting::createEmailSetting($company);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaint_email_settings');
    }
};
