<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->string('name');
            $table->string('agent_id')->unique();
            $table->string('secret_hash');
            $table->boolean('is_active')->default(true);
            $table->char('created_by_admin_id', 26);
            $table->timestamps();

            $table->foreign('created_by_admin_id')->references('id')->on('admin_users')->restrictOnDelete();
            $table->index(['is_active', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
