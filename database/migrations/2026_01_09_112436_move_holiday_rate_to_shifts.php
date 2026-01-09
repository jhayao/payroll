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
        Schema::table('shifts', function (Blueprint $table) {
            $table->double('rate_percentage')->default(100);
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('holiday_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->double('holiday_rate')->default(100);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('rate_percentage');
        });
    }
};
