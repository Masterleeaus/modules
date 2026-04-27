<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('recruit_jobs', 'currency_id')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {
                $table->dropForeign(['currency_id']);
                $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade')->change();
            });

        }

        if (! Schema::hasTable('recruit_job_alerts')) {
            Schema::create('recruit_job_alerts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('email');
                $table->unsignedBigInteger('recruit_work_experience_id')->nullable();
                $table->foreign('recruit_work_experience_id')->references('id')->on('recruit_work_experiences')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedBigInteger('recruit_job_type_id')->nullable();
                $table->foreign('recruit_job_type_id')->references('id')->on('recruit_job_types')->onUpdate('cascade')->onDelete('cascade');
                $table->integer('recruit_job_category_id')->unsigned();
                $table->foreign('recruit_job_category_id')->references('id')->on('recruit_job_categories')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedBigInteger('location_id')->nullable();
                $table->foreign('location_id')->references('id')->on('company_addresses')->onUpdate('cascade')->onDelete('cascade');
                $table->string('status');
                $table->string('hashname')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_custom_questions')) {
            Schema::create('recruit_custom_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->enum('category', ['job_application', 'job_offer'])->default('job_application');
                $table->string('question');
                $table->enum('status', ['enable', 'disable'])->default('disable');
                $table->enum('type', ['text', 'number', 'password', 'textarea', 'select', 'radio', 'date', 'checkbox', 'file'])->default('text');
                $table->enum('required', ['yes', 'no'])->default('no');
                $table->string('values', 5000)->nullable();
                $table->integer('column_priority');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_job_questions')) {
            Schema::create('recruit_job_questions', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('recruit_custom_question_id')->unsigned();
                $table->foreign('recruit_custom_question_id')->references('id')->on('recruit_custom_questions')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->bigInteger('recruit_job_id')->unsigned();
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_job_custom_answers')) {
            Schema::create('recruit_job_custom_answers', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('recruit_job_offer_letter_id')->unsigned()->nullable();
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')->onUpdate('cascade')->onDelete('cascade');
                $table->integer('recruit_job_application_id')->unsigned()->nullable();
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->bigInteger('recruit_job_id')->unsigned();
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('recruit_job_question_id')->unsigned();
                $table->foreign('recruit_job_question_id')->references('id')->on('recruit_custom_questions')->onUpdate('cascade')->onDelete('cascade');
                $table->string('answer')->nullable();
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_job_offer_questions')) {
            Schema::create('recruit_job_offer_questions', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('recruit_custom_question_id')->unsigned();
                $table->foreign('recruit_custom_question_id')->references('id')->on('recruit_custom_questions')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->integer('recruit_job_offer_letter_id')->unsigned();
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')->onUpdate('cascade')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
