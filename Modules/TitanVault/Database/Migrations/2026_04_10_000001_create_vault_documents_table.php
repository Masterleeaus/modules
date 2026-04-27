<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_documents')) {
            Schema::create('vault_documents', function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('company_id')->nullable()->index();
                $table->foreign('company_id')
                    ->references('id')->on('companies')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->string('title');
                $table->text('description')->nullable();
                $table->string('file_path')->nullable();
                $table->longText('content')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedInteger('version')->default(1);

                $table->unsignedBigInteger('parent_document_id')->nullable();
                $table->foreign('parent_document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->unsignedInteger('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->unsignedBigInteger('project_id')->nullable()->index();

                // client_id stores the user ID of the client (clients are users in WorkSuite).
                $table->unsignedInteger('client_id')->nullable()->index();
                $table->foreign('client_id')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->enum('status', ['draft', 'in_review', 'approved', 'rejected', 'archived'])
                    ->default('draft');

                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_documents');
    }
};
