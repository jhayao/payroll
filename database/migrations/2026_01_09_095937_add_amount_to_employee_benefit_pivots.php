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
        Schema::table('allowance_employee', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('allowance_id');
        });

        Schema::table('deduction_employee', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('deduction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowance_employee', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('deduction_employee', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
