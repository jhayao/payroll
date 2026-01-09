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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255)->nullable();
            $table->double('daily_rate')->nullable();
            $table->double('hourly_rate')->nullable();
            $table->double('minutely_rate')->nullable();
            $table->double('holiday_rate')->nullable();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('am_in', 25)->nullable();
            $table->string('am_out', 25)->nullable();
            $table->string('pm_in', 25)->nullable();
            $table->string('pm_out', 25)->nullable();
            $table->integer('in_out_interval')->default(0)->nullable();
            $table->integer('out_in_interval')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
        Schema::dropIfExists('shifts');
    }
};
