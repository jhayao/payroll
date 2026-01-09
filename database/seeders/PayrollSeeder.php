<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payrolls')->delete();
        DB::table('payroll_items')->delete();

        // Payrolls
        DB::table('payrolls')->insert([
            ['id' => 14, 'department_id' => 2, 'date_from' => '2025-12-01', 'date_to' => '2025-12-16', 'status' => 'Current'],
            ['id' => 15, 'department_id' => 3, 'date_from' => '2025-12-01', 'date_to' => '2025-12-15', 'status' => 'Current'],
            ['id' => 16, 'department_id' => 6, 'date_from' => '2025-12-01', 'date_to' => '2025-12-15', 'status' => 'Current'],
        ]);

        // Payroll Items
        DB::table('payroll_items')->insert([
            [
                'id' => 10, 
                'payroll_id' => 14, 
                'employee_id' => 1761536897, 
                'num_of_days' => 6, 
                'daily_rate' => 900, 
                'overtime' => 229, 
                'overtime_pay' => 428.23, 
                'gross_pay' => 5828.23, 
                'net_pay' => 5828.23
            ],
        ]);
    }
}
