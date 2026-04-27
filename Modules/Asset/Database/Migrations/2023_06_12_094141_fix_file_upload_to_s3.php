<?php

use App\Helper\Files;
use Modules\Asset\Entities\Asset;
use Illuminate\Database\Migrations\Migration;

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
                'model' => Asset::class,
                'columns' => [
                    [
                        'name' => 'image',
                        'path' => 'assets',
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
