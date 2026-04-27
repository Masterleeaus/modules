<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vault_access_links')) {
            Schema::create('vault_access_links', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->string('token', 64)->unique();
                $table->string('password_hash')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('max_views')->nullable();
                $table->unsignedInteger('view_count')->default(0);

                $table->unsignedInteger('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_access_links');
    }
};
