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
        Schema::table('allowances', function (Blueprint $table) {
            $table->string('schedule')->default('every_payroll'); // every_payroll or specific_month
            $table->integer('target_month')->nullable(); // 1-12
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->string('schedule')->default('every_payroll');
            $table->integer('target_month')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->dropColumn(['schedule', 'target_month']);
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->dropColumn(['schedule', 'target_month']);
        });
    }
};
