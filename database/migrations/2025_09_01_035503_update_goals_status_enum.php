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
        // Alter the status enum to include new values
        DB::statement("ALTER TABLE goals MODIFY COLUMN status ENUM('active', 'completed', 'paused', 'planned', 'in_progress', 'done') DEFAULT 'active'");
        
        // Update existing records from 'planned' to 'active'
        DB::table('goals')->where('status', 'planned')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE goals MODIFY COLUMN status ENUM('planned', 'in_progress', 'done') DEFAULT 'planned'");
        
        // Update records back
        DB::table('goals')->where('status', 'active')->update(['status' => 'planned']);
    }
};
