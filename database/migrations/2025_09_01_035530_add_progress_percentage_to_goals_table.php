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
        Schema::table('goals', function (Blueprint $table) {
            $table->integer('progress_percentage')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('progress_percentage');
        });
    }
};
