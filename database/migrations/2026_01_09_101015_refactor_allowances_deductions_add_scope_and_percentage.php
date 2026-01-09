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
        // Add scope to allowances and deductions
        Schema::table('allowances', function (Blueprint $table) {
            $table->enum('scope', ['all', 'position', 'employee'])->default('all')->after('type');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->enum('scope', ['all', 'position', 'employee'])->default('all')->after('type');
        });

        // Add percentage to pivot tables
        Schema::table('allowance_position', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('amount');
            $table->decimal('amount', 10, 2)->nullable()->change(); // Make nullable
        });

        Schema::table('deduction_position', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('amount');
            $table->decimal('amount', 10, 2)->nullable()->change(); // Make nullable
        });

        Schema::table('allowance_employee', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('amount');
        });

        Schema::table('deduction_employee', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('amount');
        });

        // Migrate Data
        \DB::table('allowances')->where('type', 'position_based')->update(['scope' => 'position', 'type' => 'fixed']); // Defaulting to fixed for now, logic will handle amounts
        \DB::table('allowances')->where('type', 'employee_based')->update(['scope' => 'employee', 'type' => 'fixed']); 
        
        \DB::table('deductions')->where('type', 'position_based')->update(['scope' => 'position', 'type' => 'fixed']);
        \DB::table('deductions')->where('type', 'employee_based')->update(['scope' => 'employee', 'type' => 'fixed']);
        
        // Note: For 'fixed' and 'percentage' types, scope remains default 'all'
    }

    public function down(): void
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('allowance_position', function (Blueprint $table) {
            $table->dropColumn('percentage');
            // Reverting amount nullable change is tricky without data loss risk, skipping strict revert
        });

        Schema::table('deduction_position', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });

        Schema::table('allowance_employee', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });

        Schema::table('deduction_employee', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
