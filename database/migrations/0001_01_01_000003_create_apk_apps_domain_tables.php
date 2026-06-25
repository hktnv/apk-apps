<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('managed_applications', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('package_name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'created_at']);
        });

        Schema::create('apk_releases', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('application_id', 26);
            $table->unsignedBigInteger('version_code');
            $table->string('version_name');
            $table->text('release_notes');
            $table->string('original_filename');
            $table->string('storage_disk');
            $table->string('storage_path');
            $table->char('sha256', 64);
            $table->unsignedBigInteger('size_bytes');
            $table->char('uploaded_by_admin_id', 26);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('application_id')->references('id')->on('managed_applications')->restrictOnDelete();
            $table->foreign('uploaded_by_admin_id')->references('id')->on('admin_users')->restrictOnDelete();
            $table->unique(['application_id', 'version_code']);
            $table->index(['application_id', 'created_at']);
        });

        Schema::create('release_publications', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('application_id', 26);
            $table->char('release_id', 26);
            $table->string('channel', 16);
            $table->string('action', 16);
            $table->boolean('force_update');
            $table->unsignedBigInteger('minimum_supported_version_code')->default(0);
            $table->text('comment');
            $table->char('published_by_admin_id', 26);
            $table->timestamp('published_at');

            $table->foreign('application_id')->references('id')->on('managed_applications')->restrictOnDelete();
            $table->foreign('release_id')->references('id')->on('apk_releases')->restrictOnDelete();
            $table->foreign('published_by_admin_id')->references('id')->on('admin_users')->restrictOnDelete();
            $table->index(['application_id', 'channel', 'id']);
            $table->index(['release_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_publications');
        Schema::dropIfExists('apk_releases');
        Schema::dropIfExists('managed_applications');
    }
};
