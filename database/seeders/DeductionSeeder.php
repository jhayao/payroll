<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('deductions')->delete();

        DB::table('deductions')->insert([
            ['id' => 1, 'description' => 'SSS'],
            ['id' => 2, 'description' => 'Cash Advance'],
            ['id' => 7, 'description' => 'PhilHealth'],
            ['id' => 8, 'description' => 'Pag-Ibig'],
        ]);
    }
}
