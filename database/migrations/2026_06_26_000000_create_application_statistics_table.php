<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_statistics', function (Blueprint $table): void {
            $table->char('application_id', 26)->primary();
            $table->unsignedBigInteger('update_check_count')->default(0);
            $table->unsignedBigInteger('update_available_count')->default(0);
            $table->unsignedBigInteger('up_to_date_count')->default(0);
            $table->unsignedBigInteger('apk_download_count')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();

            $table->foreign('application_id')->references('id')->on('managed_applications')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_statistics');
    }
};
