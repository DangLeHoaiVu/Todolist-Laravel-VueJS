<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->on('task');
            $table->foreignId('implementer_id')->nullable()->constrained()->on('users');
            $table->foreignId('create_by')->constrained()->on('users');
            $table->foreignId('status_id')->constrained()->on('status');
            $table->string('log');
            $table->string('comment')->nullable();
            $table->string('type');
            $table->timestamp('update_at')->nullable();
            $table->timestamp('create_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_log');
    }
};
