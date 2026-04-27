<?php

use App\Helper\Files;
use Illuminate\Database\Migrations\Migration;
use Modules\Recruit\Entities\RecruitJobApplication;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // WithoutGlobalScopes
        $files = [
            [
                'model' => RecruitJobApplication::class,
                'columns' => [
                    [
                        'name' => 'photo',
                        'path' => 'avatar',
                    ],
                ],
            ],
        ];

        foreach ($files as $file) {
            $model = $file['model'];
            $columns = $file['columns'];

            Files::fixLocalUploadFiles($model, $columns);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
