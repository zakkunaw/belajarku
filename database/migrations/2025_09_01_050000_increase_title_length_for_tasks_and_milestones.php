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
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('title')->change(); // Change from VARCHAR(255) to TEXT
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->string('title', 500)->change(); // Increase from VARCHAR(255) to VARCHAR(500)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('title')->change(); // Revert back to VARCHAR(255)
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->string('title')->change(); // Revert back to VARCHAR(255)
        });
    }
};
