<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_document_comments')) {
            Schema::create('vault_document_comments', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->unsignedInteger('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->string('client_token')->nullable();

                // Section/position reference within the document.
                $table->string('position')->nullable();

                $table->text('content');
                $table->timestamp('resolved_at')->nullable();

                $table->unsignedBigInteger('parent_comment_id')->nullable();
                $table->foreign('parent_comment_id')
                    ->references('id')->on('vault_document_comments')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_document_comments');
    }
};
