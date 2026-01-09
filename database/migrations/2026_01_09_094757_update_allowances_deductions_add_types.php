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
            $table->string('type', 20)->default('fixed')->after('description'); // fixed, percentage, position_based
            $table->decimal('amount', 10, 2)->nullable()->after('type'); // for fixed amount
            $table->decimal('percentage', 5, 2)->nullable()->after('amount'); // for salary percentage
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->string('type', 20)->default('fixed')->after('description'); // fixed, percentage, position_based
            $table->decimal('amount', 10, 2)->nullable()->after('type'); // for fixed amount
            $table->decimal('percentage', 5, 2)->nullable()->after('amount'); // for salary percentage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->dropColumn(['type', 'amount', 'percentage']);
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->dropColumn(['type', 'amount', 'percentage']);
        });
    }
};
