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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->integer('department_id')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('status', 25)->default('Current')->nullable();
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->integer('payroll_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->double('num_of_days')->nullable();
            $table->double('daily_rate')->nullable();
            $table->integer('overtime')->default(0)->nullable();
            $table->double('overtime_pay')->default(0)->nullable();
            $table->double('gross_pay')->nullable();
            $table->double('net_pay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('payroll_items');
    }
};
