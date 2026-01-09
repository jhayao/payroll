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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('lastname', 50)->nullable();
            $table->string('firstname', 50)->nullable();
            $table->string('middlename', 50)->nullable();
            $table->string('suffix', 50)->nullable();
            $table->string('sex', 25)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('mobile_no', 25)->nullable();
            $table->integer('position_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('password', 150)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->string('photo_2x2', 150)->nullable();
            $table->string('photo_lg', 150)->nullable();
            $table->string('photo_lg2', 150)->nullable();
            $table->string('photo_lg3', 150)->nullable();
        });

        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->integer('payroll_item_id')->nullable();
            $table->string('description', 50)->nullable();
            $table->double('amount')->nullable();
        });

        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->integer('payroll_item_id')->nullable();
            $table->string('description', 50)->nullable();
            $table->double('amount')->nullable();
        });

        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->nullable();
            $table->integer('shift_id')->nullable();
            $table->string('remarks', 25)->nullable();
        });

        Schema::create('employee_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('token')->nullable();
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('employee_allowances');
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('employee_shifts');
        Schema::dropIfExists('employee_password_reset_tokens');
    }
};
