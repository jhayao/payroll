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
        Schema::table('employees', function (Blueprint $table) {
            // Make email and password nullable (if not already)
            $table->string('email', 150)->nullable()->change();
            $table->string('password', 150)->nullable()->change();

            // Add new address fields
            $table->string('purok', 100)->nullable();
            $table->string('barangay', 100)->nullable();
            $table->string('city', 100)->nullable();

            // Add unique employee_id
            $table->string('employee_id')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert new fields
            $table->dropColumn(['purok', 'barangay', 'city', 'employee_id']);
            
            // Reverting nullable is risky if data has nulls, so we generally accept they remain nullable or do nothing.
            // But ideally we'd change them back to not null if we were sure.
            // For now, we'll just drop the new columns.
        });
    }
};
