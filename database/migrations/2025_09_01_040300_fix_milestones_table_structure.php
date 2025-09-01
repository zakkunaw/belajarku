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
        Schema::table('milestones', function (Blueprint $table) {
            // Add missing columns
            $table->text('description')->nullable()->after('title');
            $table->string('status')->default('pending')->after('due_date');
            $table->integer('order_index')->default(0)->after('order_no');
            
            // Change due_date to target_date and make it nullable
            $table->date('target_date')->nullable()->after('description');
        });
        
        // Copy data from due_date to target_date
        DB::statement('UPDATE milestones SET target_date = due_date');
        
        // Drop old columns
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'order_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            // Add back old columns
            $table->date('due_date')->after('title');
            $table->integer('order_no')->default(0)->after('status');
        });
        
        // Copy data back
        DB::statement('UPDATE milestones SET due_date = target_date WHERE target_date IS NOT NULL');
        
        // Drop new columns
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn(['description', 'status', 'order_index', 'target_date']);
        });
    }
};
