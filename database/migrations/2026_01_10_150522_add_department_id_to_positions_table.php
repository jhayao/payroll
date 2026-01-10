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
        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');
            // Assuming departments table exists and has id column. If not, remove foreign key constraint or ensure order.
            // Based on previous reads, departments table exists.
            // Using set null on delete to avoid deleting positions if a department is deleted, or cascade? 
            // Let's stick to nullable for now as it's safer for existing data.
            // However, for strict integrity we might want to enforce it.
            // Given the user wants a hierarchy, nullable allows existing positions to exist without a department until assigned.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
};
