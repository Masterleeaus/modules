<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_document_versions')) {
            Schema::create('vault_document_versions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->unsignedInteger('version_number');
                $table->longText('content')->nullable();
                $table->string('file_path')->nullable();

                $table->unsignedInteger('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_document_versions');
    }
};
