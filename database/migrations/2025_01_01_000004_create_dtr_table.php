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
        Schema::create('dtr', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->nullable();
            $table->date('log_date')->nullable();
            $table->datetime('am_in')->nullable();
            $table->datetime('am_out')->nullable();
            $table->datetime('pm_in')->nullable();
            $table->datetime('pm_out')->nullable();
            $table->datetime('ot_in')->nullable();
            $table->datetime('ot_out')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtr');
    }
};
