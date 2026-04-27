<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if (!Schema::hasColumn('recruit_interview_comments', 'candidate_comment')) {
            if (Schema::hasTable('recruit_interview_comments')) {
        Schema::table('recruit_interview_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('recruit_interview_comments', 'candidate_comment')) {
                    $table->string('candidate_comment')->after('comment')->nullable();
                }
        });
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
        //
    }

};
