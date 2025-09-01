<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add missing columns
            $table->text('description')->nullable()->after('title');
            $table->date('due_date')->nullable()->after('description');
        });
        
        // Update status enum to include 'pending'
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'todo', 'doing', 'done') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['description', 'due_date']);
        });
        
        // Revert status enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo', 'doing', 'done') DEFAULT 'todo'");
    }
};
