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
        Schema::create('status_setting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('create_by')->constrained()->on('users');
            $table->foreignId('current_status')->constrained()->on('status');
            $table->foreignId('next_status')->constrained()->on('status');
            $table->timestamp('create_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_setting');
    }
};
